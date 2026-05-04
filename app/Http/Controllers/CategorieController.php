<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Traits\HasIntelligentPagination;
use Illuminate\Http\Request;

class CategorieController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['name', 'description'];
    protected array $sortable   = ['name', 'created_at'];

    // ══════════════════════════════════════════════════════════════════
    // INDEX — liste toutes les catégories parentes + stats
    // Route : GET /categories
    // ══════════════════════════════════════════════════════════════════
    public function index(Request $request)
    {
        // Catégories parentes avec leurs sous-catégories et produits
        $query = Categorie::with(['children.products', 'products'])
            ->whereNull('parent_id');

        $categories = $this->applyIntelligentPagination(
            $query, $request,
            $this->searchable,
            $this->sortable,
            15
        );

        // Stats globales
        $totalCategories    = Categorie::whereNull('parent_id')->count();
        $totalSousCategories = Categorie::whereNotNull('parent_id')->count();

        return view('categories.index', [
            'categories'         => $categories,
            'totalCategories'    => $totalCategories,
            'totalSousCategories'=> $totalSousCategories,
            'params'             => $this->getPaginationParams($request),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // SHOW — détail d'une catégorie + ses sous-catégories + ses produits
    // Route : GET /categories/{id}
    // ══════════════════════════════════════════════════════════════════
    public function show(Request $request, $id)
    {
        $categorie = Categorie::with([
            'parent',
            'children.products',
            'products.category',
        ])->findOrFail($id);

        // Produits directs de cette catégorie (paginés)
        $produitsQuery = $categorie->products()->with('category');

        $produits = $this->applyIntelligentPagination(
            $produitsQuery, $request,
            ['name', 'code', 'description'],
            ['name', 'code', 'ventePrice', 'achatPrice', 'created_at'],
            10
        );

        return view('categories.show', [
            'categorie' => $categorie,
            'produits'  => $produits,
            'params'    => $this->getPaginationParams($request),
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // CREATE — formulaire de création
    // Route : GET /categories/create
    // ══════════════════════════════════════════════════════════════════
    public function create()
    {
        // Seules les catégories parentes peuvent être sélectionnées comme parent
        $parents = Categorie::whereNull('parent_id')->orderBy('name')->get();

        return view('categories.create', ['parents' => $parents]);
    }

    // ══════════════════════════════════════════════════════════════════
    // STORE — enregistrement nouvelle catégorie ou sous-catégorie
    // Route : POST /categories
    // ══════════════════════════════════════════════════════════════════
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string|max:1000',
            'parent_id'   => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.unique'   => 'Ce nom de catégorie existe déjà.',
            'parent_id.exists' => 'La catégorie parente sélectionnée est invalide.',
        ]);

        Categorie::create([
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id ?: null,
        ]);

        $type = $request->parent_id ? 'Sous-catégorie' : 'Catégorie';

        return to_route('categories.index')
            ->with('success', "{$type} « {$request->name} » créée avec succès.");
    }

    // ══════════════════════════════════════════════════════════════════
    // EDIT — formulaire d'édition
    // Route : GET /categories/{id}/edit
    // ══════════════════════════════════════════════════════════════════
    public function edit($id)
    {
        $categorie = Categorie::findOrFail($id);

        // Exclure la catégorie elle-même et ses enfants pour éviter une boucle
        $parents = Categorie::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', [
            'categorie' => $categorie,
            'parents'   => $parents,
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // UPDATE — mise à jour d'une catégorie
    // Route : PUT /categories/{id}
    // ══════════════════════════════════════════════════════════════════
    public function update(Request $request, $id)
    {
        $categorie = Categorie::findOrFail($id);

        $request->validate([
            'name'        => 'required|string|max:255|unique:categories,name,' . $id,
            'description' => 'nullable|string|max:1000',
            'parent_id'   => 'nullable|exists:categories,id',
        ], [
            'name.required' => 'Le nom est obligatoire.',
            'name.unique'   => 'Ce nom de catégorie existe déjà.',
        ]);

        // Empêcher une catégorie d'être son propre parent
        if ($request->parent_id == $id) {
            return back()->withErrors(['parent_id' => 'Une catégorie ne peut pas être son propre parent.']);
        }

        $categorie->update([
            'name'        => $request->name,
            'description' => $request->description,
            'parent_id'   => $request->parent_id ?: null,
        ]);

        return to_route('categories.show', $id)
            ->with('success', 'Catégorie mise à jour avec succès.');
    }

    // ══════════════════════════════════════════════════════════════════
    // DESTROY — suppression d'une catégorie
    // Route : DELETE /categories/{id}
    // ══════════════════════════════════════════════════════════════════
    public function destroy($id)
    {
        $categorie = Categorie::withCount(['products', 'children'])->findOrFail($id);

        // Bloquer si des produits sont liés
        if ($categorie->products_count > 0) {
            return back()->with('erreur',
                "Impossible de supprimer « {$categorie->name} » : {$categorie->products_count} produit(s) y sont rattachés.");
        }

        // Bloquer si des sous-catégories existent
        if ($categorie->children_count > 0) {
            return back()->with('erreur',
                "Impossible de supprimer « {$categorie->name} » : elle contient {$categorie->children_count} sous-catégorie(s).");
        }

        $nom = $categorie->name;
        $categorie->delete();

        return to_route('categories.index')
            ->with('success', "Catégorie « {$nom} » supprimée avec succès.");
    }
}
