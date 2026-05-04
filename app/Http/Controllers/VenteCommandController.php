<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VenteCommand;
use App\Models\VenteCommandProduct;
use App\Models\Client;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\FinancialTransaction;
use App\Models\Alert;
use App\Models\Invoice;
use App\Models\Company;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\HasIntelligentPagination;

class VenteCommandController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['codeCommand', 'status'];
    protected array $sortable   = ['codeCommand', 'status', 'montantTotal', 'created_at'];

    // ──────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $query = VenteCommand::with(['client', 'products', 'creator']);

            // Filtre client par nom (relation)
            if ($request->filled('search_client')) {
                $search = $request->search_client;
                $query->whereHas('client', fn($q) => $q->where('name', 'like', "%{$search}%"));
            }

            $commands = $this->applyIntelligentPagination(
                $query, $request,
                $this->searchable,
                $this->sortable,
                15
            );

            return view('ventes.index', [
                'commands' => $commands,
                'params'   => $this->getPaginationParams($request),
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erreur lors du chargement des ventes : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────

    public function show($id)
    {
        try {
            $vente = VenteCommand::with([
                'client',
                'products.product',
                'creator',
                
            ])->findOrFail($id);

            $montantTotal = $vente->products->sum(fn ($item) => $item->quantity * $item->unitPriceAtCommand);

            return view('ventes.show', [
                'vente'        => $vente,
                'montantTotal' => $montantTotal,
                'itemCount'    => $vente->products->count(),
                'items'        => $vente->products,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('ventes.index')->with('erreur', 'Commande de vente introuvable.');
        } catch (\Exception $e) {
            return to_route('ventes.index')->with('erreur', 'Erreur lors de l\'affichage de la commande.');
        }
    }

    // ──────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────

    public function create()
    {
        $clients  = Client::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        // Récupérer l'entreprise (configuration générale)
        $company = Company::where('is_active', true)->first();

        //Récupérer le régime fiscal de l'entreprise
        $regime=$company->getRegimeFiscal();
        $isTvaApplicable=$company->isTvaApplicable();
        $tvaRate= config('invoice.tva_rate', 18);

        return view('ventes.create', compact('clients', 'products', 'company', 'regime', 'isTvaApplicable', 'tvaRate'));
    }

    // ──────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────

    public function store(Request $request)
    {

         
        // Récupérer l'entreprise (configuration générale)
        $company = Company::where('is_active', true)->first();

        //Récupérer le régime fiscal de l'entreprise
        $regime= $company->getRegimeFiscal();
        $isTvaApplicable=$company->isTvaApplicable();
        $tvaRate= config('invoice.tva_rate', 18);

        
        $validator = Validator::make($request->all(), [
            'client_id'                      => 'required|exists:clients,id',
            'products'                       => 'required|array|min:1',
            'products.*.product_id'          => 'required|exists:products,id',
            'products.*.quantity'            => 'required|numeric|min:0.01',
            'products.*.prix_unitaire'       => 'nullable|numeric|min:0',
            'products.*.tva'                => 'nullable|numeric|min:0',
            'products.*.remise_montant'     => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $montantTotal = 0;
            $totalRemise = 0;
            $totalHt = 0;
            $totalTva = 0;
            $totalHtRemise = 0;
            $productsData = [];

            foreach ($request->products as $item) {
                $product        = Product::findOrFail($item['product_id']);
                $quantity       = floatval($item['quantity']);
                $prix_unitaire  = isset($item['prix_unitaire']) ? floatval($item['prix_unitaire']) : floatval($product->ventePrice);
                $remise_montant  = floatval($item['remise_montant']);
                
                $ligne_ht = round($prix_unitaire * $quantity, 2);
                $base_imposable  = round($ligne_ht - $remise_montant, 2);

                if($isTvaApplicable && ($product->is_taxable ?? true )){
                    $montant_tva     = round($base_imposable * ($tvaRate / 100), 2);
                }
                else{
                     $montant_tva = 0;
                }

                $total_ligne     = round($base_imposable + $montant_tva, 2);

                // Accumulate totals
                $totalHt += $ligne_ht;
                $totalRemise += $remise_montant;
                $totalHtRemise += $base_imposable;
                $totalTva += $montant_tva;
                $montantTotal += $total_ligne;

                // Vérifier le stock
                if ($product->getCurrentStock() < $quantity) {
                    DB::rollBack();
                    return to_route('ventes.create')
                        ->withInput()
                        ->with('erreur', 'Stock insuffisant pour le produit « ' . $product->name . ' ». Stock disponible : ' . $product->getCurrentStock() . ' ' . $product->unitofmeasure);
                }

                $productsData[] = [
                    'product'        => $product,
                    'quantity'       => $quantity,
                    'prix_unitaire'  => $prix_unitaire,
                    'remise_montant' => $remise_montant,
                    'montant_tva'    => $montant_tva,
                    'total_ligne'    => $total_ligne,
                ];
            }

            $codeCommand = 'VEN-' . date('YmdHis') . '-' . rand(1000, 9999);

            $command = VenteCommand::create([
                'client_id'      => $request->client_id,
                'codeCommand'    => $codeCommand,
                'status'         => 'EN ATTENTE',
                'montantTotal'   => $montantTotal,
                'montantPayer'   => $request->montantPayer ?? 0,
                'montant_ht'     => $totalHt,
                'montant_tva'    => $totalTva,
                'montant_ht_remise' => $totalHtRemise,
                'remise'         => $totalRemise,
                'regime_fiscal'  => $regime,
                'lien_facture'   => $request->lien_facture ?? null,
                'created_by'     => auth()->id() ?? 1,
            ]);

            foreach ($productsData as $data) {
                VenteCommandProduct::create([
                    'vente_command_id'  => $command->id,
                    'product_id'        => $data['product']->id,
                    'quantity'          => $data['quantity'],
                    'unitPriceAtCommand'=> $data['prix_unitaire'],
                    'subtotal_ht'        => $data['quantity'] * $data['prix_unitaire'],
                    'remise_montant'    => $data['remise_montant'],
                    'montant_tva'       => $data['montant_tva'],
                    'subtotal_ttc'      => $data['total_ligne'],
                ]);
            }

            DB::commit();

            return to_route('ventes.index')->with('success', 'Vente enregistrée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Erreur lors de la création : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // CONFIRM
    // ──────────────────────────────────────────────

    public function confirm($id)
    {
        try {
            $command = VenteCommand::findOrFail($id);

            if ($command->status !== 'EN ATTENTE') {
                return to_route('ventes.index')
                    ->with('erreur', 'Seules les commandes en attente peuvent être confirmées.');
            }

            $command->update(['status' => 'CONFIRMER']);

            return to_route('ventes.index')->with('success', 'Vente confirmée avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return to_route('ventes.index')->with('erreur', 'Commande introuvable.');
        } catch (\Exception $e) {
            return to_route('ventes.index')->with('erreur', 'Erreur : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // MARK AS DELIVERED
    // ──────────────────────────────────────────────

    public function markAsDelivered($id)
    {
        DB::beginTransaction();

        try {
            $command = VenteCommand::with('products')->findOrFail($id);

            if ($command->status !== 'CONFIRMER') {
                return to_route('ventes.index')
                    ->with('erreur', 'Seules les commandes confirmées peuvent être marquées comme livrées.');
            }

            $command->update(['status' => 'LIVRER']);

            foreach ($command->products as $item) {
                StockMovement::create([
                    'product_id' => $item->product_id,
                    'type'       => 'OUT',
                    'quantity'   => $item->quantity,
                    'reason'     => 'Livraison commande ' . $command->codeCommand,
                    'created_by' => auth()->id() ?? 1,
                ]);
            }

            DB::commit();

            return to_route('ventes.index')->with('success', 'Vente livrée et stock mis à jour.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Commande introuvable.');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Erreur : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // MARK AS PAID
    // ──────────────────────────────────────────────

    public function markAsPaid(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'paiement_method' => 'required|in:caisse,cheque,transfert,credit',
            'montantPayer'    => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $command = VenteCommand::findOrFail($id);

            if ($command->status !== 'LIVRER') {
                return to_route('ventes.index')
                    ->with('erreur', 'Seules les commandes livrées peuvent être marquées comme payées.');
            }

            $client = Client::findOrFail($command->client_id);

            // Vérification crédit
            if ($request->paiement_method === 'credit' && $client->creditLimit > 0) {
                $creditDisponible = $client->creditLimit - $client->current_credit;
                if (($client->current_credit + $command->montantTotal) > $client->creditLimit) {
                    DB::rollBack();
                    return to_route('ventes.index')
                        ->with('erreur', 'Limite de crédit dépassée. Disponible : ' . number_format($creditDisponible, 0, ',', ' ') . ' FCFA');
                }
            }

            // Mise à jour crédit client
            $client->update([
                'current_credit' => $client->current_credit + $command->montantTotal,
            ]);

            // Mise à jour commande
            $command->update([
                'status'       => 'PAYER',
                'montantPayer' => $request->montantPayer,
            ]);

            // Transaction financière
            $nouveauSolde = FinancialTransaction::calculerNouveauSolde('REVENUE', $request->montantPayer);

            FinancialTransaction::create([
                'type'                           => 'REVENUE',
                'description'                    => 'Paiement commande ' . $command->codeCommand,
                'montant'                        => $request->montantPayer,
                'paiement_method'                => $request->paiement_method,
                'categorie'                      => 'Ventes',
                'related_model_achat_or_vente'   => 'VenteCommand',
                'related_id_vente'               => $command->id,
                'created_by'                     => auth()->id() ?? 1,
                'solde_actuel'                   => $nouveauSolde,
            ]);

            DB::commit();

            return to_route('ventes.index')->with('success', 'Vente marquée comme payée.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Commande introuvable.');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Erreur : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // DELETE
    // ──────────────────────────────────────────────

    public function delete($id)
    {
        DB::beginTransaction();

        try {
            $vente = VenteCommand::findOrFail($id);

            if ($vente->status !== 'EN ATTENTE') {
                return to_route('ventes.index')
                    ->with('erreur', 'Seules les commandes en attente peuvent être supprimées.');
            }

            VenteCommandProduct::where('vente_command_id', $vente->id)->delete();
            $vente->delete();

            DB::commit();

            // ✅ Redirige vers index (et non vers ventes.delete qui est une route DELETE)
            return to_route('ventes.index')->with('success', 'Vente supprimée avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Commande introuvable.');
        } catch (\Exception $e) {
            DB::rollBack();
            return to_route('ventes.index')->with('erreur', 'Erreur lors de la suppression.');
        }
    }
}
