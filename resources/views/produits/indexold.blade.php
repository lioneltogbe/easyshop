@extends('layouts.app')

@section('title', 'Produits - easyShop')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Gestion des Produits</h1>
    @can('create-product')
        <a href="{{ route('produits.create') }}" class="btn btn-primary">
            ➕ Nouveau Produit
        </a>
    @endcan
</div>

<!-- Barre de Recherche et Filtres -->
<div style="background: white; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <div class="grid grid-3">
        <div class="form-group">
            <input type="text" class="form-input" placeholder="Rechercher un produit..." style="margin-bottom: 0;">
        </div>
        <div class="form-group">
            <select class="form-select" style="margin-bottom: 0;">
                <option value="">Toutes les catégories</option>
                <option value="1">Matériaux</option>
                <option value="2">Outils</option>
            </select>
        </div>
        <div class="form-group">
            <button class="btn btn-primary" style="width: 100%; margin-bottom: 0;">🔍 Rechercher</button>
        </div>
    </div>
</div>

<!-- Tableau des Produits -->
<div style="background: white; border-radius: 0.75rem; overflow-x:auto;  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
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
        @if($products->count())
            @foreach($products as $product)
                <tr>
                    <td><code style="background: #F3F4F6; padding: 0.25rem 0.5rem; border-radius: 0.25rem;">{{ $product->code }}</code></td>
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
                    <td style="display: flex; align-items: center; gap: 0.5rem;">
                        @can('view-product')
                            <a href="{{ route('produits.show', $product->id) }}" class="btn btn-secondary btn-sm" style="display: flex; align-items: center; gap: 0.15rem;">
                                <span>👁️</span> Voir
                            </a>
                        @endcan

                        @can('edit-product')
                            <a href="{{ route('produits.edit', $product->id) }}" style="display: flex; align-items: center; gap: 0.15rem;" class="btn btn-primary btn-sm">
                                <span>✏️</span> Éditer
                            </a>
                        @endcan
                    </td>

                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="8" style="text-align: center; padding: 2rem; color: #6B7280;">
                    Aucun produit trouvé
                </td>
            </tr>
        @endif
        </tbody>
      
    </table>

    <div style="padding: 1rem 1.5rem; background: white; border-top: 1px solid #E5E7EB;">
        {{ $products->links() }}
    </div>
</div>
@endsection
