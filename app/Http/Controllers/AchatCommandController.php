<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\AchatCommand;
use App\Models\AchatCommandProduct;
use App\Models\Fournisseur;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\FinancialTransaction;
use App\Models\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\HasIntelligentPagination;



class AchatCommandController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['CodeCommande', 'status'];
    protected array $sortable   = ['CodeCommande', 'status', 'montantTotal', 'created_at'];

    /**
     * Afficher la liste de toutes les commandes d'achat
     * Récupère toutes les commandes avec fournisseur, produits et créateur
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = AchatCommand::with(['fournisseur', 'products', 'creator']);

        // Filtre fournisseur par nom (relation)
        if ($request->filled('search_fournisseur')) {
            $search = $request->search_fournisseur;
            $query->whereHas('fournisseur', fn($q) => $q->where('name', 'like', "%{$search}%"));
        }

        $commands = $this->applyIntelligentPagination(
            $query, $request,
            $this->searchable,
            $this->sortable,
            15
        );

        return view('achats.index', [
            'commands' => $commands,
            'params'   => $this->getPaginationParams($request),
        ]);
    }
 
    /**
     * Afficher une commande d'achat spécifique
     * Récupère une commande avec toutes ses relations
     * 
     * @param int $id - ID de la commande
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id){
       
            // Récupérer la commande
            $achatCommand = AchatCommand::with(['fournisseur', 'products', 'creator'])->findOrFail($id);
            $items= $achatCommand->products;
            // Retourner la commande
            //return view("achats.show",["achatCommand"=>$achatCommand, "totalAmount"=>$achatCommand->montantTotal]);
            return view('achats.show', [
            "achatCommand" => $achatCommand,        // Commande spécifique
            "items" => $items,                      // Articles de la commande
            "itemCount" => $achatCommand->products->count(),           // Nombre d'articles
            "totalAmount"=>$achatCommand->montantTotal,   ]);       // Montant totaal
    }
 
    /**
     * Créer une nouvelle commande d'achat
     * Valide les données et crée une commande avec ses articles
     * IMPORTANT : Utilise CodeCommande (pas CodeCommand)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(){

    // Récupérer tous les fournisseurs et produits
    $fournisseurs =Fournisseur::orderBy('name')->get();
    $products = Product::orderBy('name')->get();

    // Retourner la vue avec les données injectées
    return view('achats.create', ['fournisseurs'=>$fournisseurs, 'products'=>$products]);
    }

    
    public function store(Request $request){

            // Valider les données
            $validator = Validator::make($request->all(), [
                'fournisseur_id' => 'required|exists:Fournisseurs,id',
                'products' => 'required|array|min:1',
                'products.*.product_id' => 'required|exists:products,id',
                'products.*.quantity' => 'required|numeric',
                'products.*.unit_price' => 'required|numeric|min:0',
                'paiement_method' =>'nullable|string',
            ]);
        
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
        // try {
                // Calculer le montant total
                $montantTotal = 0;
                foreach ($request->products as $item) {
                    $montantTotal += $item['quantity'] * $item['unit_price'];
                }
     
                // Générer un code de commande unique
        
                $codeCommande = 'ACH-' . date('YmdHis') . '-' . rand(1000, 9999);
                //$codeCommande = 'ACH-' . Str::upper(Str::random(10));

                // Créer la commande d'achat
                $command = AchatCommand::create([
                    'fournisseur_id' => $request->fournisseur_id,
                    'CodeCommande' => $codeCommande,  // 
                    'status' => 'EN ATTENTE',
                    'montantTotal' => $montantTotal,
                    'created_by' => auth()->id() ?? 1,
                ]);
 
                // Créer les articles de la commande
                foreach ($request->products as $item) {
                    AchatCommandProduct::create([
                        'achat_command_id' => $command->id,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unitPriceAtCommand' => $item['unit_price'],
                    ]);
                }
 
                // Vérifier si le montant est important (> 1 000 000 FCFA)
                if ($montantTotal > 1000000) {
                    // Créer une alerte de transaction importante
                    Alert::create([
                        'type' => 'grande_transaction',
                        'title' => 'Commande d\'achat importante',
                        'message' => 'Une commande d\'achat de ' . $montantTotal . ' FCFA a été créée',
                        'severity' => 'moyen',
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
 
                // Valider la transaction
            DB::commit();
  
                // Retourner la commande créée
            return to_route('achats.index')->with('success', 'Commande créée avec succès');
        
    }
 
    /**
     * Marquer une commande comme reçue et créer les mouvements de stock
     * Règle 6 : Création automatique de mouvements de stock à la réception
     * 
     * @param int $id - ID de la commande
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandRecu($id)
    {
        
            // Rechercher la commande
            $command = AchatCommand::findOrFail($id);
 
            // Vérifier que la commande est en attente
            if ($command->status !== 'EN ATTENTE') {
                return to_route('achats.index')->with('success', 'Seules les commandes en attente peuvent être marquées comme reçues');
            }
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
            try {
                // Mettre à jour le statut
                $command->update(['status' => 'RECU']);
 
                // Créer les mouvements de stock pour chaque article
                foreach ($command->products as $item) {
                    StockMovement::create([
                        'product_id' => $item->product_id,
                        'type' => 'IN',
                        'quantity' => $item->quantity,
                        'reason' => 'IN',
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
 
                // Valider la transaction
                DB::commit();
 
                // Retourner la commande mise à jour
                return view('achats.show', [
                    "achatCommand" => $command->load(['fournisseur', 'products']),
                    "items" => $command->products,
                    "itemCount" => $command->products->count(),
                    "totalAmount"=>$command->montantTotal,
                ]);
            } catch (\Exception $e) {
                // Annuler la transaction
                DB::rollBack();
                throw $e;
            }
    }
 
    /**
     * Marquer une commande comme payée et créer la transaction financière
     * Règle 7 : Enregistrement de la transaction financière
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id - ID de la commande
     * @return \Illuminate\Http\JsonResponse
     */
    public function commandPayer(Request $request, $id){
        
            // Valider les données
            $validator = Validator::make($request->all(), [
                'paiement_method' => 'required|',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
 
            // Rechercher la commande
            $command = AchatCommand::findOrFail($id);
 
            // Vérifier que la commande est reçue
            if ($command->status !== 'RECU') {
                return to_route('achats.index')->with('Seules les commandes reçues peuvent être marquées comme payées');
            }
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
            try {
             
                // Mettre à jour le statut
                $command->update(['status' => 'PAYER',
                'paiement_method' => $request->paiement_method]);
                $nouveauSolde = FinancialTransaction::calculerNouveauSolde('DEPENSE', $command->montantTotal);


 
                // Créer la transaction financière
                // IMPORTANT : Utiliser paiement_method (pas paiment_method)
                FinancialTransaction::create([
                    'type' => 'DEPENSE',
                    'description' => 'Paiement de la commande d\'achat ' . $command->CodeCommande,
                    'montant' => $command->montantTotal,
                    'paiement_method' => $request->paiement_method,
                    'categorie' => 'Achats',
                    'related_model_achat_or_vente' => 'AchatCommand',
                    'related_id_achat' => $command->id,
                    'created_by' => auth()->id() ?? 1,
                    'solde_actuel' => $nouveauSolde,
                ]);
 
                // Valider la transaction
                DB::commit();
 
                // Retourner la commande mise à jour
                return view('achats.show', [
                    "achatCommand" => $command->load(['fournisseur', 'products']),
                    "items" => $command->products,
                    "itemCount" => $command->products->count(),
                    "totalAmount"=>$command->montantTotal,
                ]);
            } catch (\Exception $e) {
                // Annuler la transaction
                DB::rollBack();
                throw $e;
        }
    }
 
    /**
     * Supprimer une commande d'achat (seulement si en attente)
     * Supprime la commande et ses articles
     * 
     * @param int $id - ID de la commande
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        try {
            // Rechercher la commande
            $command = AchatCommand::findOrFail($id);
 
            // Vérifier que la commande est en attente
            if ($command->status !== 'EN ATTENTE') {
                return to_route('achats.index')->with('Seules les commandes en attente peuvent être supprimées');
            }
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
            try {
                // Supprimer les articles de la commande
                AchatCommandProduct::where('achat_command_id', $command->id)->delete();
 
                // Supprimer la commande
                $command->delete();
 
                // Valider la transaction
                DB::commit();
 
                // Retourner le message de succès
                return response()->json([
                    'success' => true,
                    'message' => 'Commande supprimée avec succès'
                ], 200);
            } catch (\Exception $e) {
                // Annuler la transaction
                DB::rollBack();
                throw $e;
            }
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Commande non trouvée'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la commande',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
