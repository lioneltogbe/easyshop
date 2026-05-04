@extends('layouts.app')

@section('title', 'Produits - easyShop')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <h1 style="font-size:2rem;font-weight:700;">Gestion des Produits</h1>
    @can('create-product')
        <a href="{{ route('produits.create') }}" class="btn btn-primary">➕ Nouveau Produit</a>
    @endcan
</div>

<x-pagination-controls
    :action="route('produits.index')"
    :params="$params"
    searchPlaceholder="Rechercher par nom, code, description..."
    :sortOptions="[
        ['value'=>'name',       'label'=>'Nom'],
        ['value'=>'code',       'label'=>'Code'],
        ['value'=>'achatPrice', 'label'=>'Prix achat'],
        ['value'=>'ventePrice', 'label'=>'Prix vente'],
        ['value'=>'created_at', 'label'=>'Date création'],
    ]"
>
    {{-- Filtre catégorie --}}
    <div>
        <select name="filter_category_id" class="form-select" style="margin-bottom:0;">
            <option value="">Toutes les catégories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(($params['filter_category_id'] ?? '') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
    </div>
</x-pagination-controls>

@if($products->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $products->firstItem() }}–{{ $products->lastItem() }} sur {{ $products->total() }} produits
</p>
@endif

<div style="background:white;border-radius:.75rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    <table class="table">
        <thead>
            <tr>
                <th>Code</th>
                <th>Nom</th>
                <th>Catégorie</th>
                <th>Prix Achat</th>
                <th>Prix Vente</th>
                <th>Stock</th>
                <th>Marge</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        @forelse($products as $product)
            <tr>
                <td><code style="background:#F3F4F6;padding:.25rem .5rem;border-radius:.25rem;">{{ $product->code }}</code></td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name ?? 'N/A' }}</td>
                <td>{{ number_format($product->achatPrice, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($product->ventePrice, 0, ',', ' ') }} FCFA</td>
                <td>
                    @if($product->isOutOfStock())
                        <span class="badge badge-danger">Rupture</span>
                    @elseif($product->isLowStock())
                        <span class="badge badge-warning">Faible</span>
                    @else
                        <span class="badge badge-success">Bon</span>
                    @endif
                </td>
                <td>{{ $product->getMarge() }}%</td>
                <td style="display:flex;align-items:center;gap:.5rem;">
                    @can('view-product')
                        <a href="{{ route('produits.show', $product->id) }}" class="btn btn-secondary btn-sm">Voir</a>
                    @endcan
                    @can('edit-product')
                        <a href="{{ route('produits.edit', $product->id) }}" class="btn btn-primary btn-sm">Éditer</a>
                    @endcan
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="8" style="text-align:center;padding:2rem;color:#6B7280;">Aucun produit trouvé</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    @if($products->hasPages())
    <div style="padding:1rem 1.5rem; background:white;border-top:1px solid #E5E7EB;">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
