<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VenteCommand;
use App\Models\VenteCommandProduct;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommandeClientController extends Controller
{
    // ──────────────────────────────────────────────
    // GET /api/commandes
    // Retourne les commandes du client connecté
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $user   = $request->user();
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Profil client introuvable.',
            ], 404);
        }

        $commandes = VenteCommand::with(['products.product'])
            ->where('client_id', $client->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($c) => $this->formatCommande($c));

        return response()->json([
            'success' => true,
            'data'    => $commandes,
        ]);
    }

    // ──────────────────────────────────────────────
    // GET /api/commandes/{id}
    // ──────────────────────────────────────────────

    public function show(Request $request, $id)
    {
        $user   = $request->user();
        $client = Client::where('email', $user->email)->first();

        $commande = VenteCommand::with(['products.product', 'invoice'])
            ->where('client_id', $client?->id)
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatCommande($commande, true),
        ]);
    }

    // ──────────────────────────────────────────────
    // POST /api/commandes
    // Corps attendu :
    // {
    //   "articles": [
    //     { "product_id": 1, "quantity": 5 },
    //     { "product_id": 3, "quantity": 2 }
    //   ],
    //   "notes": "Livraison urgente"
    // }
    // ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'articles'               => 'required|array|min:1',
            'articles.*.product_id'  => 'required|integer|exists:products,id',
            'articles.*.quantity'    => 'required|numeric|min:0.01',
            'notes'                  => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user   = $request->user();
        $client = Client::where('email', $user->email)->first();

        if (!$client) {
            return response()->json([
                'success' => false,
                'message' => 'Profil client introuvable. Contactez l\'administrateur.',
            ], 404);
        }

        DB::beginTransaction();

        try {
            $montantTotal = 0;
            $articlesData = [];

            // Vérifier le stock et calculer les montants
            foreach ($request->articles as $article) {
                $product  = Product::findOrFail($article['product_id']);
                $quantity = (float) $article['quantity'];
                $stock    = $product->getCurrentStock();

                if ($stock < $quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour « {$product->name} ». Disponible : {$stock} {$product->unitofmeasure}.",
                    ], 409);
                }

                $prixUnitaire = (float) $product->ventePrice;
                $totalLigne   = round($prixUnitaire * $quantity, 2);
                $montantTotal += $totalLigne;

                $articlesData[] = [
                    'product'      => $product,
                    'quantity'     => $quantity,
                    'prixUnitaire' => $prixUnitaire,
                    'totalLigne'   => $totalLigne,
                ];
            }

            // Créer la commande — statut EN ATTENTE (validation admin requise)
            $codeCommand = 'CLI-' . date('YmdHis') . '-' . rand(1000, 9999);

            $commande = VenteCommand::create([
                'client_id'    => $client->id,
                'codeCommand'  => $codeCommand,
                'status'       => 'EN ATTENTE',  // ← attente validation admin
                'montantTotal' => $montantTotal,
                'montantPayer' => 0,
                'created_by'   => $user->id,
            ]);

            // Créer les articles
            foreach ($articlesData as $data) {
                VenteCommandProduct::create([
                    'vente_command_id'   => $commande->id,
                    'product_id'         => $data['product']->id,
                    'quantity'           => $data['quantity'],
                    'unitPriceAtCommand' => $data['prixUnitaire'],
                    'total'              => $data['totalLigne'],
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Commande créée. En attente de validation par notre équipe.',
                'data'    => [
                    'id'           => $commande->id,
                    'code'         => $commande->codeCommand,
                    'status'       => 'EN ATTENTE',
                    'montant'      => $montantTotal,
                    'articles'     => count($articlesData),
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création : ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────
    // DELETE /api/commandes/{id}
    // Annuler une commande (EN ATTENTE seulement)
    // ──────────────────────────────────────────────

    public function cancel(Request $request, $id)
    {
        $user   = $request->user();
        $client = Client::where('email', $user->email)->first();

        $commande = VenteCommand::where('client_id', $client?->id)->findOrFail($id);

        if ($commande->status !== 'EN ATTENTE') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les commandes en attente peuvent être annulées.',
            ], 409);
        }

        $commande->update(['status' => 'ANNULER']);

        return response()->json([
            'success' => true,
            'message' => 'Commande annulée.',
        ]);
    }

    // ──────────────────────────────────────────────
    // Helper — formater une commande pour l'API
    // ──────────────────────────────────────────────

    private function formatCommande(VenteCommand $c, bool $detail = false): array
    {
        $base = [
            'id'          => $c->id,
            'code'        => $c->codeCommand,
            'status'      => $c->status,
            'montant'     => (float) $c->montantTotal,
            'montant_paye'=> (float) $c->montantPayer,
            'created_at'  => $c->created_at?->format('Y-m-d H:i'),
        ];

        if ($detail) {
            $base['articles'] = $c->products->map(fn ($p) => [
                'product_id' => $p->product_id,
                'nom'        => $p->product?->name,
                'quantite'   => (float) $p->quantity,
                'prix'       => (float) $p->unitPriceAtCommand,
                'total'      => round($p->quantity * $p->unitPriceAtCommand, 0),
            ]);

            if ($c->invoice) {
                $base['facture'] = [
                    'id'             => $c->invoice->id,
                    'numero'         => $c->invoice->invoice_number,
                    'pdf_disponible' => !empty($c->invoice->pdf_path),
                ];
            }
        }

        return $base;
    }
}
