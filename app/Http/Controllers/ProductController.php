<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Categorie;
use App\Models\StockMovement;
use App\Models\ProductBatche;
use App\Models\Alert;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\HasIntelligentPagination;


class ProductController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['name', 'code', 'description'];
    protected array $sortable   = ['name', 'code', 'achatPrice', 'ventePrice', 'created_at', 'category_id'];

      
    // Affiche la liste de tous les produits avec pagination

    public function index(Request $request)
    {
        $categories = \App\Models\Categorie::orderBy('name')->get();

        $query = Product::with('category');

        // Filtre catégorie via filter_category_id
        $products = $this->applyIntelligentPagination(
            $query, $request,
            $this->searchable,
            $this->sortable,
            15
        );

        return view('produits.index', [
            'products'      => $products,
            'totalProducts' => $products->total(),
            'categories'    => $categories,
            'params'        => $this->getPaginationParams($request),
        ]);
    }

    public function create(){
        $categories=Categorie::all();
        return view('produits.create', ['categories'=>$categories]);
    }
    
    /** Afficher un produit spécifique
     * Récupère un produit avec toutes ses relations (catégorie, mouvements de stock, lots) **/

        public function show($id){

        // try {
            // Rechercher le produit par ID avec ses relations
            $product = Product::with(['category', 'stockMovements', 'batches'])->findOrFail($id);
 
            // Ajouter des informations calculées
            $product->currentStock = $product->getCurrentStock();
            $product->marge = $product->getMarge();
            $product->isLowStock = $product->isLowStock();
            $product->isOutOfStock = $product->isOutOfStock();
            $movements = $product->stockMovements->sortByDesc('created_at');
            
            // Retourner le produit avec ses informations complètes
            return view('produits.show', [
                'product' => $product,
                'currentStock' => $product->currentStock,
                'movements' => $movements,
                ]);
          
    }

    /**
     * Créer un nouveau produit
     * Valide les données et crée un produit avec tous ses attributs
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */

       public function store(Request $request){

        try {
            // Valider les données entrantes
            $validator = Validator::make($request->all(), [
                // 'code' => 'required|string|unique:products,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:categories,id',
                'unitofmeasure' => 'required|in:unit,kg,m,m2,m3,liter',
                'achatPrice' => 'required|numeric|min:0',
                'ventePrice' => 'required|numeric|min:0',
                'alertStockLevel' => 'required|numeric|min:0',
                'alertDaybeforeExpiration' => 'nullable|integer|min:1',
            ]);
 
            // Si la validation échoue, retourner les erreurs
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
            $code= 'PRD-' . date('YmdHis') . '-' . rand(1000, 9999);

            // Créer le produit avec les données validées
            $product = Product::create([
                'code' => $code,
                'name' => $request->name,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'unitofmeasure' => $request->unitofmeasure,
                'achatPrice' => $request->achatPrice,
                'ventePrice' => $request->ventePrice,
                'alertStockLevel' => $request->alertStockLevel,
                'alertDaybeforeExpiration' => $request->alertDaybeforeExpiration,
            ]);


            return to_route("produits.index")->with('success', 'Produit bien enregistrer');
        
            } catch (\Exception $e) {
    
            return to_route("produits.index")->with('erreur', "Erreur lors de la création du produit");

        }
    }
    

     /**
     * Mettre à jour un produit existant
     * Valide et met à jour les attributs d'un produit
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id - ID du produit
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){

            // Rechercher le produit
            $product = Product::findOrFail($id);
 
            // Valider les données
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'category_id' => 'sometimes|exists:categories,id',
                'unitofmeasure' => 'sometimes|in:unit,kg,m,m2,m3,liter',
                'achatPrice' => 'sometimes|numeric|min:0',
                'ventePrice' => 'sometimes|numeric|min:0',
                'alertStockLevel' => 'sometimes|numeric|min:0',
                'alertDayBeforeExpiration' => 'nullable|integer|min:1',
            ]);
 
            // Si la validation échoue
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur de validation',
                    'errors' => $validator->errors()
                ], 422);
            }
 
            // Mettre à jour le produit
            $product->update($request->only([
                'name',
                'description',
                'category_id',
                'unitofmeasure',
                'achatPrice',
                'ventePrice',
                'alertStockLevel',
                'alertDayBeforeExpiration',
            ]));
           $movements=$product->movements;
            return view('produits.show', [
                'product' =>$product,
                'currentStock'=>$product->currentStock,
                'movements'=>$movements,
                ]);
        //    Retourner le produit mis à jour
        //     return response()->json([
        //         'success' => true,
        //         'message' => 'Produit mis à jour avec succès',
        //         'data' => $product
        //     ], 200);
        
        
    }

    /**
     * Enregistrer un nouveau lot pour un produit
     */
    public function storeBatch(Request $request, Product $product)
    {
        $validated = $request->validate([
            'batchNumber' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.01',
            'dayExpiration' => 'nullable|date|after_or_equal:today',
        ]);

        DB::beginTransaction();
        try {
            ProductBatche::create([
                'product_id' => $product->id,
                'batchNumber' => $validated['batchNumber'],
                'quantity' => $validated['quantity'],
                'dayExpiration' => $validated['dayExpiration'] ?? null,
            ]);

            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'IN',
                'quantity' => $validated['quantity'],
                'reason' => 'Ajout lot ' . $validated['batchNumber'],
                'created_by' => auth()->id() ?? 1,
            ]);

            DB::commit();

            return redirect()->route('produits.show', $product)
                ->with('success', 'Lot ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Erreur lors de l’ajout du lot : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un lot de produit
     */
    public function destroyBatch(Product $product, ProductBatche $batch)
    {
        if ($batch->product_id !== $product->id) {
            abort(404);
        }

        DB::beginTransaction();
        try {
            // Créer un mouvement de sortie pour retirer la quantité du lot
            StockMovement::create([
                'product_id' => $product->id,
                'type' => 'OUT',
                'quantity' => $batch->quantity,
                'reason' => 'Suppression lot ' . $batch->batchNumber,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Créer une alerte d'anomalie financière
            Alert::create([
                'type' => 'anomalie financier',
                'title' => 'Suppression de lot détectée',
                'message' => 'Le lot ' . $batch->batchNumber . ' du produit "' . $product->name . '" a été supprimé, entraînant une sortie de stock de ' . $batch->quantity . ' ' . $product->unitofmeasure . '.',
                'severity' => 'moyen',
                'product_id' => $product->id,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Supprimer le lot
            $batch->delete();

            DB::commit();

            return redirect()->route('produits.show', $product)
                ->with('success', 'Lot supprimé avec succès. Une sortie de stock et une alerte ont été enregistrées.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('produits.show', $product)
                ->with('error', 'Erreur lors de la suppression du lot : ' . $e->getMessage());
        }
    }

     /**
     * Supprimer un produit
     * Supprime un produit de la base de données
     * 
     * @param int $id - ID du produit
     * @return \Illuminate\Http\JsonResponse
     */

       public function destroy($id){

        try {
            // Rechercher et supprimer le produit
            $product = Product::findOrFail($id);
            $product->delete();
 
            // Retourner le message de succès
            return response()->json([
                'success' => true,
                'message' => 'Produit supprimé avec succès'
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du produit',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les produits en stock faible
     * Utilise la méthode isLowStock() du model pour filtrer les produits
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowStockProducts(){

        try {
            // Récupérer les produits avec stock faible
            $products = Product::with('category')
                ->get()
                ->filter(function ($product) {
                    return $product->isLowStock();
                });
 
            // Retourner les produits en stock faible
            return response()->json([
                'success' => true,
                'message' => 'Produits en stock faible récupérés',
                'data' => $products->values()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits en stock faible',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les produits en rupture de stock
     * Utilise la méthode isOutOfStock() du model pour filtrer les produits
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOutOfStockProducts(){

        try {
            // Récupérer les produits en rupture
            $products = Product::with('category')
                ->get()
                ->filter(function ($product) {
                    return $product->isOutOfStock();
                });
 
            // Retourner les produits en rupture
            return response()->json([
                'success' => true,
                'message' => 'Produits en rupture de stock récupérés',
                'data' => $products->values()
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des produits en rupture',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer le stock actuel d'un produit
     * Calcule le stock en temps réel à partir des mouvements
     * 
     * @param int $id - ID du produit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCurrentStock($id){

        try {
            // Rechercher le produit
            $product = Product::findOrFail($id);
 
            // Calculer le stock actuel en utilisant la méthode du model
            $currentStock = $product->getCurrentStock();
 
            // Retourner le stock
            return response()->json([
                'success' => true,
                'message' => 'Stock actuel récupéré',
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'current_stock' => $currentStock,
                    'alert_level' => $product->alertStockLevel,
                    'is_low_stock' => $currentStock <= $product->alertStockLevel,
                    'is_out_of_stock' => $currentStock == 0
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du stock',
                'error' => $e->getMessage()
            ], 500);
        }
    }

     /**
     * Récupérer la marge bénéficiaire d'un produit
     * Calcule le pourcentage de marge entre prix d'achat et prix de vente
     * 
     * @param int $id - ID du produit
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMarge($id){

        try {
            // Rechercher le produit
            $product = Product::findOrFail($id);
 
            // Calculer la marge en utilisant la méthode du model
            $marge = $product->getMarge();
 
            // Retourner la marge
            return response()->json([
                'success' => true,
                'message' => 'Marge bénéficiaire récupérée',
                'data' => [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'achatPrice' => $product->achatPrice,
                    'ventePrice' => $product->ventePrice,
                    'marge_percentage' => $marge,
                    'marge_absolute' => $product->ventePrice - $product->achatPrice
                ]
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Produit non trouvé'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul de la marge',
                'error' => $e->getMessage()
            ], 500);
        }
    }
 
}
