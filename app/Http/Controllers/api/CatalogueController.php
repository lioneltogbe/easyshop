<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Categorie;
use Illuminate\Http\Request;

class CatalogueController extends Controller
{
    // ──────────────────────────────────────────────
    // GET /api/catalogue/produits
    // Paramètres optionnels :
    //   ?categorie_id=3
    //   ?search=ciment
    //   ?min_prix=0&max_prix=50000
    //   ?dispo=1  (en stock uniquement)
    //   ?per_page=12
    //   ?sort=prix_asc|prix_desc|nom
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::with('category')
            ->select([
                'id', 'code', 'name', 'description',
                'category_id', 'unitofmeasure',
                'ventePrice',          // ✅ prix de vente uniquement
                // 'achatPrice' jamais exposé
                'alertStockLevel',
            ]);

        // Filtre catégorie
        if ($request->filled('categorie_id')) {
            $query->where('category_id', $request->categorie_id);
        }

        // Recherche textuelle
        if ($request->filled('search')) {
            $search = '%' . $request->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('code', 'like', $search)
                  ->orWhere('description', 'like', $search);
            });
        }

        // Filtre prix
        if ($request->filled('min_prix')) {
            $query->where('ventePrice', '>=', $request->min_prix);
        }
        if ($request->filled('max_prix')) {
            $query->where('ventePrice', '<=', $request->max_prix);
        }

        // Tri
        match ($request->sort) {
            'prix_asc'  => $query->orderBy('ventePrice', 'asc'),
            'prix_desc' => $query->orderBy('ventePrice', 'desc'),
            'nom'       => $query->orderBy('name', 'asc'),
            default     => $query->orderBy('name', 'asc'),
        };

        $perPage  = min((int) ($request->per_page ?? 12), 50);
        $produits = $query->paginate($perPage);

        // Enrichir chaque produit avec le stock calculé
        $items = $produits->getCollection()->map(function ($product) {
            $stock = $product->getCurrentStock();
            return [
                'id'           => $product->id,
                'code'         => $product->code,
                'name'         => $product->name,
                'description'  => $product->description,
                'categorie'    => $product->category?->name,
                'categorie_id' => $product->category_id,
                'unite'        => $product->unitofmeasure,
                'prix'         => (float) $product->ventePrice,
                'stock'        => (float) $stock,
                'stock_status' => $this->stockStatus($stock, $product->alertStockLevel),
                // achatPrice intentionnellement absent
            ];
        });

        return response()->json([
            'success'    => true,
            'data'       => $items,
            'pagination' => [
                'total'        => $produits->total(),
                'per_page'     => $produits->perPage(),
                'current_page' => $produits->currentPage(),
                'last_page'    => $produits->lastPage(),
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    // GET /api/catalogue/produits/{id}
    // ──────────────────────────────────────────────

    public function show($id)
    {
        $product = Product::with('category')
            ->select([
                'id', 'code', 'name', 'description',
                'category_id', 'unitofmeasure',
                'ventePrice', 'alertStockLevel',
            ])
            ->findOrFail($id);

        $stock = $product->getCurrentStock();

        return response()->json([
            'success' => true,
            'data'    => [
                'id'           => $product->id,
                'code'         => $product->code,
                'name'         => $product->name,
                'description'  => $product->description,
                'categorie'    => $product->category?->name,
                'categorie_id' => $product->category_id,
                'unite'        => $product->unitofmeasure,
                'prix'         => (float) $product->ventePrice,
                'stock'        => (float) $stock,
                'stock_status' => $this->stockStatus($stock, $product->alertStockLevel),
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    // GET /api/catalogue/categories
    // ──────────────────────────────────────────────

    public function categories()
    {
        $categories = Categorie::withCount('products')
            ->orderBy('name')
            ->get()
            ->map(fn ($c) => [
                'id'              => $c->id,
                'name'            => $c->name,
                'nombre_produits' => $c->products_count,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $categories,
        ]);
    }

    // ──────────────────────────────────────────────
    // Helper — statut de stock
    // ──────────────────────────────────────────────

    private function stockStatus(float $stock, $seuil): string
    {
        if ($stock <= 0)          return 'rupture';
        if ($stock <= $seuil)     return 'faible';
        return 'disponible';
    }
}
