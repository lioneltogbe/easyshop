@extends('layouts.app')

@section('title', 'Mouvements de stock')

@section('content')
<style>
:root {
    --primary: #6366F1;
    --success: #10B981;
    --danger: #EF4444;
    --gray-50: #F9FAFB;
    --gray-100: #F3F4F6;
    --gray-200: #E5E7EB;
    --gray-700: #374151;
    --gray-900: #111827;
    --radius: 0.75rem;
    --shadow: 0 10px 20px rgba(0,0,0,0.08);
}

.container-stock {
    max-width: 1200px;
    margin: auto;
    padding: 2rem 1rem;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: var(--gray-900);
}

.page-subtitle {
    color: var(--gray-700);
    font-size: 0.9rem;
}

.cardmov {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    overflow: hidden;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table thead {
    background: var(--gray-100);
}

.table th,
.table td {
    padding: 1rem;
    text-align: left;
    font-size: 0.9rem;
    border-bottom: 1px solid var(--gray-200);
}

.table th {
    font-weight: 600;
    color: var(--gray-700);
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
}

.badge {
    padding: 0.3rem 0.75rem;
    border-radius: 999px;
    font-size: 0.7rem;
    font-weight: 700;
    color: white;
}

.badge-in {
    background-color: var(--success);
}

.badge-out {
    background-color: var(--danger);
}

.product-box {
    display: flex;
    flex-direction: column;
}

.product-name {
    font-weight: 600;
    color: var(--gray-900);
}

.product-code {
    font-size: 0.75rem;
    color: var(--gray-700);
}

.quantity {
    font-weight: 700;
}

.quantity.in {
    color: var(--success);
}

.quantity.out {
    color: var(--danger);
}

.empty {
    padding: 3rem;
    text-align: center;
    color: var(--gray-700);
}
</style>

<div class="container-stock">

    <div style="margin-bottom: 2rem;">
        <a href="{{ route('stocks.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la gestion des stocks</a>
    </div>

    <div class="page-header">
        <div>
            <h1 class="page-title">Mouvements de stock</h1>
            <p class="page-subtitle">
                Historique complet des entrées et sorties de stock
            </p>
        </div>
    </div>

    <div class="cardmov">
        @if($movements->isEmpty())
            <div class="empty">
                Aucun mouvement de stock enregistré.
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Produit</th>
                        <th>Type</th>
                        <th>Quantité</th>
                        <th>Raison</th>
                        <th>Utilisateur</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($movements as $movement)
                        <tr>
                            <td>
                                {{ $movement->created_at->format('d/m/Y') }} <br>
                                <small>{{ $movement->created_at->format('H:i') }}</small>
                            </td>

                            <td>
                                <div class="product-box">
                                    <span class="product-name">
                                        {{ $movement->product->name ?? '—' }}
                                    </span>
                                    <span class="product-code">
                                        {{ $movement->product->code ?? '' }}
                                    </span>
                                </div>
                            </td>

                            <td>
                                @if($movement->type === 'IN')
                                    <span class="badge badge-in">ENTRÉE</span>
                                @else
                                    <span class="badge badge-out">SORTIE</span>
                                @endif
                            </td>

                            <td class="quantity {{ strtolower($movement->type) }}">
                                {{ $movement->type === 'IN' ? '+' : '-' }}
                                {{ $movement->quantity }}
                            </td>

                            <td>
                                {{ $movement->reason ?? '—' }}
                            </td>

                            <td>
                                {{ $movement->createdBy->name ?? 'Inconnu' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
