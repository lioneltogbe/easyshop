@extends('layouts.app')

@section('title', 'Catégories - easyShop')

@section('content')
<style>
    .cat-card { background:white; border-radius:.75rem; border:1px solid #E5E7EB; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); transition:box-shadow .2s; }
    .cat-card:hover { box-shadow:0 4px 16px rgba(124,58,237,.12); }
    .cat-header { padding:1rem 1.25rem; background:linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color:white; display:flex; justify-content:space-between; align-items:center; }
    .cat-body   { padding:1rem 1.25rem; }
    .sub-badge  { display:inline-flex; align-items:center; gap:.35rem; background:#F3E8FF; color:#6D28D9; padding:.2rem .65rem; border-radius:999px; font-size:.75rem; font-weight:600; }
    .prod-badge { display:inline-flex; align-items:center; gap:.35rem; background:#D1FAE5; color:#065F46; padding:.2rem .65rem; border-radius:999px; font-size:.75rem; font-weight:600; }
    .stat-box   { background:white; border-radius:.75rem; border:1px solid #E5E7EB; padding:1.25rem 1.5rem; text-align:center; }
    .stat-num   { font-size:2rem; font-weight:700; color:#7C3AED; }
    .stat-lbl   { font-size:.75rem; font-weight:600; color:#6B7280; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.5rem; }
    .sub-row    { display:flex; align-items:center; justify-content:space-between; padding:.5rem .75rem; border-radius:.5rem; background:#FAFAFA; border:1px solid #F3F4F6; margin-bottom:.4rem; font-size:.875rem; }
</style>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <div>
        <h1 style="font-size:2rem;font-weight:700;">Catégories</h1>
        <!-- <p style="color:#6B7280;font-size:.875rem;">Gérez les catégories et sous-catégories de produits</p> -->
    </div>
    @can('create-product')
    <a href="{{ route('categories.create') }}" class="btn btn-primary">➕ Nouvelle Catégorie</a>
    @endcan
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;font-weight:500;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('erreur'))
    <div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;font-weight:500;">
        ❌ {{ session('erreur') }}
    </div>
@endif

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
    <div class="stat-box">
        <div class="stat-lbl">Catégories principales</div>
        <div class="stat-num">{{ $totalCategories }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-lbl">Sous-catégories</div>
        <div class="stat-num">{{ $totalSousCategories }}</div>
    </div>
    <div class="stat-box">
        <div class="stat-lbl">Total</div>
        <div class="stat-num">{{ $totalCategories + $totalSousCategories }}</div>
    </div>
</div>

{{-- Barre de recherche --}}
<x-pagination-controls
    :action="route('categories.index')"
    :params="$params"
    searchPlaceholder="Rechercher une catégorie..."
    :sortOptions="[
        ['value'=>'name',       'label'=>'Nom'],
        ['value'=>'created_at', 'label'=>'Date création'],
    ]"
/>

@if($categories->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $categories->firstItem() }}–{{ $categories->lastItem() }} sur {{ $categories->total() }} catégories
</p>
@endif

{{-- Grille des catégories --}}
@forelse($categories as $categorie)
<div class="cat-card" style="margin-bottom:1.25rem;">

    {{-- En-tête de la catégorie --}}
    <div class="cat-header">
        <div>
            <div style="font-size:1.1rem;font-weight:700;">{{ $categorie->name }}</div>
            @if($categorie->description)
                <div style="font-size:.8rem;opacity:.85;margin-top:.2rem;">{{ Str::limit($categorie->description, 60) }}</div>
            @endif
        </div>
        <div style="display:flex;gap:.5rem;align-items:center;">
            <span class="sub-badge">{{ $categorie->children->count() }} sous-cat.</span>
            <span class="prod-badge">{{ $categorie->products->count() }} produits</span>
            <a href="{{ route('categories.show', $categorie->id) }}" class="btn btn-secondary" style="padding:.35rem .9rem;font-size:.8rem;background:rgba(255,255,255,.2);color:white;border:1px solid rgba(255,255,255,.4);">Voir</a>
            <a href="{{ route('categories.edit', $categorie->id) }}" class="btn" style="padding:.35rem .9rem;font-size:.8rem;background:white;color:#7C3AED;">✏️ Éditer</a>
            <form action="{{ route('categories.destroy', $categorie->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cette catégorie ?')">
                @csrf @method('DELETE')
                <button type="submit" style="padding:.35rem .9rem;font-size:.8rem;background:rgba(239,68,68,.2);color:white;border:none;border-radius:.5rem;cursor:pointer;">🗑️</button>
            </form>
        </div>
    </div>

    {{-- Sous-catégories --}}
    @if($categorie->children->count() > 0)
    <div class="cat-body">
        <p style="font-size:.75rem;font-weight:700;color:#6B7280;text-transform:uppercase;letter-spacing:.5px;margin-bottom:.6rem;">Sous-catégories</p>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:.5rem;">
            @foreach($categorie->children as $child)
            <div class="sub-row">
                <div>
                    <span style="font-weight:600;">{{ $child->name }}</span>
                    <span style="color:#6B7280;font-size:.75rem;margin-left:.5rem;">{{ $child->products->count() }} produit(s)</span>
                </div>
                <div style="display:flex;gap:.35rem;">
                    <a href="{{ route('categories.show', $child->id) }}" style="font-size:.75rem;color:#7C3AED;text-decoration:none;font-weight:600;">Voir</a>
                    <span style="color:#D1D5DB;">|</span>
                    <a href="{{ route('categories.edit', $child->id) }}" style="font-size:.75rem;color:#3B82F6;text-decoration:none;font-weight:600;">Éditer</a>
                </div>
            </div>
            @endforeach
        </div>
        {{-- Ajouter sous-catégorie rapide --}}
        <div style="margin-top:.75rem;">
            <a href="{{ route('categories.create') }}?parent_id={{ $categorie->id }}"
               style="font-size:.8rem;color:#7C3AED;text-decoration:none;font-weight:600;">
                + Ajouter une sous-catégorie
            </a>
        </div>
    </div>
    @else
    <div class="cat-body" style="display:flex;align-items:center;justify-content:space-between;">
        <span style="color:#9CA3AF;font-size:.875rem;font-style:italic;">Aucune sous-catégorie</span>
        <a href="{{ route('categories.create') }}?parent_id={{ $categorie->id }}"
           style="font-size:.8rem;color:#7C3AED;text-decoration:none;font-weight:600;">
            + Ajouter une sous-catégorie
        </a>
    </div>
    @endif

</div>
@empty
<div style="background:white;border-radius:.75rem;padding:3rem;text-align:center;color:#6B7280;">
    <div style="font-size:3rem;margin-bottom:1rem;">📂</div>
    <p style="font-size:1.1rem;margin-bottom:1rem;">Aucune catégorie trouvée</p>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">Créer la première catégorie</a>
</div>
@endforelse

@if($categories->hasPages())
<div style="background:white;border-radius:.75rem;padding:1rem 1.5rem;box-shadow:0 1px 3px rgba(0,0,0,.06);">
    {{ $categories->links() }}
</div>
@endif

@endsection
