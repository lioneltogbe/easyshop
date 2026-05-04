@extends('layouts.app')

@section('title', 'Gestion des Stocks - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Gestion des Stocks</h1>
</div>

<!-- Onglets Stocks -->
<div class="tabs">
    <a href="{{ route('stocks.index') }}" class="tab-link active">📊 Vue Générale</a>
    <a href="{{ route('stocks.mouvements') }}" class="tab-link">📝 Mouvements</a>
    <a href="{{ route('stocks.entree') }}" class="tab-link">📥 Entrée</a>
    <a href="{{ route('stocks.sortie') }}" class="tab-link">📤 Sortie</a>
    <a href="{{ route('stocks.ajustement') }}" class="tab-link">⚙️ Ajustement</a>
    <a href="{{ route('alertes.index') }}" class="tab-link">⚠️ Alertes</a>
</div>

<!-- Cartes de Stock -->
<div class="grid grid-3">
    @foreach(\App\Models\Product::with('category')->get() as $product)
        <div class="kpi-card" style="margin-top:0.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <div class="kpi-label">{{ $product->name }}</div>
                    <div style="font-size: 0.875rem; color: #6B7280;">{{ $product->category->name ?? 'N/A' }}</div>
                </div>
                @if($product->isOutOfStock())
                    <span class="badge badge-danger">Rupture</span>
                @elseif($product->isLowStock())
                    <span class="badge badge-warning">Faible</span>
                @else
                    <span class="badge badge-success">Bon</span>
                @endif
            </div>

            <div class="kpi-value">{{ $product->getCurrentStock() }} {{ $product->unitofmeasure }}</div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #E5E7EB;">
                <div style="font-size: 0.875rem; color: #6B7280;">
                    Seuil: {{ $product->alertStockLevel }} {{ $product->unitofmeasure }}
                </div>
                <a href="{{ route('stocks.produit', $product->id) }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">
                    Voir historique →
                </a>
            </div>
        </div>
    @endforeach
</div>
@endsection
