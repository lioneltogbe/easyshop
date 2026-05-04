<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\Product;
use App\Models\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\HasIntelligentPagination;

class StockMovementController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['reason', 'type'];
    protected array $sortable   = ['type', 'quantity', 'reason', 'created_at'];

    /**
     * Afficher la liste de tous les mouvements de stock
     * Récupère tous les mouvements avec les produits associés
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(\Illuminate\Http\Request $request)
    {
        try {
            $query = StockMovement::with('product');

            $movements = $this->applyIntelligentPagination(
                $query, $request,
                $this->searchable,
                $this->sortable,
                20
            );

            return view('stocks.mouvements', [
                'movements' => $movements,
                'params'    => $this->getPaginationParams($request),
            ]);
        } catch (\Exception $e) {
            return to_route('stocks.index')->with('error', 'Erreur lors de la récupération des mouvements');
        }
    }
 
    /**
     * Enregistrer une entrée de stock (IN)
     * Crée un mouvement d'entrée et met à jour les alertes si nécessaire
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordInbound(Request $request)
    {
        try {
            // Valider les données
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:0.01',
                'reason' => 'required|in:IN,OUT,AJUSTMENT',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
            try {
                // Créer le mouvement de stock (entrée)
                $movement = StockMovement::create([
                    'product_id' => $request->product_id,
                    'type' => 'IN',
                    'quantity' => $request->quantity,
                    'reason' => $request->reason,
                    'created_by' => auth()->id() ?? 1,
                ]);
 
                // Récupérer le produit
                $product = Product::find($request->product_id);
 
                // Calculer le nouveau stock
                $newStock = $product->getCurrentStock();
 
                // Vérifier si le stock est maintenant au-dessus du seuil d'alerte
                if ($newStock > $product->alertStockLevel) {
                    // Marquer les alertes de stock faible comme lues
                    Alert::where('product_id', $product->id)
                        ->where('type', 'peu_stock')
                        ->whereNull('read_at')
                        ->update(['read_at' => now()]);
                }
 
                // Valider la transaction
                DB::commit();
 
                // Retourner le mouvement créé
                // return response()->json([
                //     'success' => true,
                //     'message' => 'Entrée de stock enregistrée avec succès',
                //     'data' => [
                //         'movement' => $movement,
                //         'new_stock' => $newStock,
                //         'product_name' => $product->name
                //     ]
                // ], 201);

                return to_route('stocks.index')->with('success', 'Entrée de stock enregistrée avec succès');

            } catch (\Exception $e) {
                // Annuler la transaction en cas d'erreur
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Erreur lors de l\'enregistrement de l\'entrée de stock',
            //     'error' => $e->getMessage()
            // ], 500);

            return to_route('stocks.index')->with('erreur', 'Erreur lors de l\'enregistrement de l\'entrée de stock');
        }
    }
 
    /**
     * Enregistrer une sortie de stock (OUT)
     * Crée un mouvement de sortie avec vérification du stock disponible
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recordOutbound(Request $request)
    {
        try {
            // Valider les données
            $validator = Validator::make($request->all(), [
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|numeric|min:0.01',
                'reason' => 'required|in:IN,OUT,AJUSTMENT',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
 
            // Commencer une transaction DB
            DB::beginTransaction();
 
            try {
                // Récupérer le produit
                $product = Product::find($request->product_id);
 
                // Calculer le stock actuel
                $currentStock = $product->getCurrentStock();
 
                // Vérifier si le stock est suffisant
                if ($currentStock < $request->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuffisant',
                        'data' => [
                            'current_stock' => $currentStock,
                            'requested_quantity' => $request->quantity,
                            'shortage' => $request->quantity - $currentStock
                        ]
                    ], 409);
                }
 
                // Créer le mouvement de stock (sortie)
                $movement = StockMovement::create([
                    'product_id' => $request->product_id,
                    'type' => 'OUT',
                    'quantity' => $request->quantity,
                    'reason' => $request->reason,
                    'created_by' => auth()->id() ?? 1,
                ]);
 
                // Calculer le nouveau stock
                $newStock = $product->getCurrentStock();
 
                // Vérifier si le stock est maintenant faible
                if ($newStock <= $product->alertStockLevel && $newStock > 0) {
                    // Créer une alerte de stock faible
                    Alert::create([
                        'type' => 'peu_stock',
                        'title' => 'Stock faible pour ' . $product->name,
                        'message' => 'Le stock du produit ' . $product->name . ' est maintenant faible (' . $newStock . ' unités)',
                        'severity' => 'moyen',
                        'product_id' => $product->id,
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
 
                // Vérifier si le stock est maintenant en rupture
                if ($newStock == 0) {
                    // Créer une alerte de rupture de stock
                    Alert::create([
                        'type' => 'sortie_de_stock',
                        'title' => 'Rupture de stock pour ' . $product->name,
                        'message' => 'Le produit ' . $product->name . ' est maintenant en rupture de stock',
                        'severity' => 'critique',
                        'product_id' => $product->id,
                        'created_by' => auth()->id() ?? 1,
                    ]);
                }
 
                // Valider la transaction
                DB::commit();
 
                // Retourner le mouvement créé
                // return response()->json([
                //     'success' => true,
                //     'message' => 'Sortie de stock enregistrée avec succès',
                //     'data' => [
                //         'movement' => $movement,
                //         'new_stock' => $newStock,
                //         'product_name' => $product->name
                //     ]
                // ], 201);

                return to_route('stocks.index')->with('success', 'Sortie de stock enregistrée avec succès');
            } catch (\Exception $e) {
                // Annuler la transaction
                DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Erreur lors de l\'enregistrement de la sortie de stock',
            //     'error' => $e->getMessage()
            // ], 500);

            return to_route('stocks.index')->with('erreur', 'Erreur lors de l\'enregistrement de la sortie de stock');
        }
    }


      /**
    * Afficher le formulaire d'ajustement de stock
    */   
    public function showAdjustment(){
        $products = Product::with('category')->orderBy('name')->get();

        $lastAdjustments = StockMovement::with(['product', 'createdBy'])
            ->where('type', 'AJUSTMENT')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('stocks.ajustement', ['products'=>$products, 'lastAdjustments'=>$lastAdjustments]);    
        }
 
    /**
     * Enregistrer un ajustement de stock
     * Crée un mouvement d'ajustement (peut être positif ou négatif)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    // public function recordAdjustment(Request $request)
    // {
    //     try {
    //         // Valider les données
    //         $validator = Validator::make($request->all(), [
    //             'product_id' => 'required|exists:products,id',
    //             'quantity' => 'required|numeric',
    //             'reason' => 'required|in:IN,OUT,AJUSTMENT',
    //         ]);
 
    //         // Si la validation échoue
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Erreur de validation',
    //                 'errors' => $validator->errors()
    //             ], 422);
    //         }
 
    //         // Créer le mouvement d'ajustement
    //         $movement = StockMovement::create([
    //             'product_id' => $request->product_id,
    //             'type' => 'AJUSTMENT',
    //             'quantity' => $request->quantity,
    //             'reason' => $request->reason,
    //             'created_by' => auth()->id() ?? 1,
    //         ]);
 
    //         // Récupérer le produit et le nouveau stock
    //         $product = Product::find($request->product_id);
    //         $newStock = $product->getCurrentStock();
 
    //         // Retourner le mouvement créé
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Ajustement de stock enregistré avec succès',
    //             'data' => [
    //                 'movement' => $movement,
    //                 'new_stock' => $newStock,
    //                 'product_name' => $product->name
    //             ]
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Erreur lors de l\'enregistrement de l\'ajustement',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }

      public function recordAdjustment(Request $request)
    {
        $validated = $request->validate([
           'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|numeric|min:0.01',
            'direction'  => 'required|in:positif,negatif',
            'reason'     => 'required|string|max:255',
            'note'       => 'nullable|string|max:500',
        ], [
            'product_id.required' => 'Veuillez sélectionner un produit.',
            'quantity.required'   => 'La quantité est obligatoire.',
            'quantity.min'        => 'La quantité doit être supérieure à 0.',
            'direction.required'  => 'Le type d\'ajustement est obligatoire.',
            'reason.required'     => 'Le motif est obligatoire.',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);

            // Quantité signée selon la direction
            $signedQuantity = $validated['direction'] === 'negatif'
                ? -abs($validated['quantity'])
                : abs($validated['quantity']);

            // Vérifier qu'un ajustement négatif ne met pas le stock sous zéro
            $stockActuel = $product->getCurrentStock();
            if ($signedQuantity < 0 && ($stockActuel + $signedQuantity) < 0) {
                return back()
                    ->withInput()
                    ->withErrors(['quantity' => 'Stock insuffisant. Stock actuel : ' . $stockActuel . ' ' . $product->unitofmeasure . '. Vous ne pouvez pas retirer 				plus que ce qui est disponible.']);
            }

            // Raison complète (motif + note)
            $reasonFull = $validated['reason'];
            if (!empty($validated['note'])) {
                $reasonFull .= ' — ' . $validated['note'];
            }

            // Créer le mouvement
            StockMovement::create([
                'product_id' => $product->id,
                'type'       => 'AJUSTMENT',
                'quantity'   => $signedQuantity,
                'reason'     => $reasonFull,
                'created_by' => auth()->id() ?? 1,
            ]);
            $newStock = $product->getCurrentStock();

            // Gérer les alertes
            if ($newStock <= $product->alertStockLevel && $newStock > 0) {
                Alert::firstOrCreate(
                    ['product_id' => $product->id, 'type' => 'peu_stock', 'read_at' => null],
                    [
                        'title'      => 'Stock faible — ' . $product->name,
                        'message'    => 'Après ajustement, le stock de ' . $product->name . ' est faible (' . $newStock . ' ' . $product->unitofmeasure . ').',
                        'severity'   => 'moyen',
                        'created_by' => auth()->id() ?? 1,
                    ]
                );
            } elseif ($newStock == 0) {
                Alert::firstOrCreate(
                    ['product_id' => $product->id, 'type' => 'sortie_de_stock', 'read_at' => null],
                    [
                        'title'      => 'Rupture de stock — ' . $product->name,
                        'message'    => 'Après ajustement, le produit ' . $product->name . ' est en rupture totale.',
                        'severity'   => 'critique',
                        'created_by' => auth()->id() ?? 1,
                    ]
                );
            } else {
                Alert::where('product_id', $product->id)
                    ->whereIn('type', ['peu_stock', 'sortie_de_stock'])
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }

            DB::commit();

            $sens = $signedQuantity >= 0 ? '+' . $signedQuantity : (string)$signedQuantity;

            return redirect()->route('stocks.ajustement')
                ->with('success', "Ajustement enregistré ({$sens} {$product->unitofmeasure}) pour « {$product->name} ». Nouveau stock : {$newStock} {$product->				unitofmeasure}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Erreur lors de l\'enregistrement : ' . $e->getMessage());
       }
    } 
 
    /**
     * Récupérer l'historique des mouvements d'un produit
     * Affiche tous les mouvements d'un produit avec le stock actuel
     * 
     * @param int $productId - ID du produit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProductHistory($productId)
    {
        try {
            // Récupérer l'historique des mouvements
            $movements = StockMovement::where('product_id', $productId)
                ->orderBy('created_at', 'desc')
                ->get();
 
            // Récupérer le produit
            $product = Product::find($productId);
 
            // Retourner l'historique avec le stock actuel
             return view('stocks.produit', [
                'product'       => $product,
                'movements'     => $movements,
                'currentStock'  => $product->getCurrentStock(),
            ]);
        } catch (\Exception $e) {
            return to_route('stocks.index')->with('error', 'Erreur lors de la récupération de l\'historique');
        }
    }

    

}
