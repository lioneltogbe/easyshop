@extends('layouts.app')

@section('title', 'Finances - easyShop')

@section('content')

 <div style="margin-bottom:2rem;">
        <a href="{{ route('finances.index') }}" class="breadcrumb">← Retour aux finances</a>
    </div>


<!-- Transactions Récentes -->
<!-- Transactions Récentes -->
<div style="margin-top: 2rem; background: white; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h2 style="font-size: 1.5rem; font-weight: 700;">Transactions Récentes</h2>
        <a href="{{ route('finances.transactions.create') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            ➕ Nouvelle Transaction
        </a>
    </div>

    <table class="table" style="overflow-x:auto;">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Type</th>
                <th>Montant</th>
                <th>Méthode</th>
                <th style="text-align: right;">Solde Actuel</th>
            </tr>
        </thead>
        <tbody>
            @php
                $soldeActuel = 0; // Initialiser le solde à 0
            @endphp

            @forelse($transactions ?? [] as $transaction)
                @php
                    // Calculer le solde actuel
                    if($transaction->type == 'REVENUE') {
                        $soldeActuel += $transaction->montant;
                    } else {
                        $soldeActuel -= $transaction->montant;
                    }
                @endphp
                <tr>
                    <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $transaction->description }}</td>
                    <td>
                        @if($transaction->type == 'REVENUE')
                            <span class="badge badge-success">Revenu</span>
                        @else
                            <span class="badge badge-danger">Dépense</span>
                        @endif
                    </td>
                    <td style="font-weight: 600; color: @if($transaction->type == 'REVENUE') var(--success) @else var(--danger) @endif;">
                        @if($transaction->type == 'REVENUE') + @else - @endif
                        {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                    </td>
                    <td>{{ ucfirst($transaction->paiement_method) }}</td>
                   <td style="text-align: right; font-weight: 700; font-size: 1rem; color: @if($transaction->solde_actuel >= 0) #10B981 @else #EF4444 @endif;">
                        {{ number_format($transaction->solde_actuel, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 2rem; color: #6B7280;">
                        Aucune transaction trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<style>
    .revenue-expense-chart {
        padding: 0;
    }

    .chart-legend-top {
        display: flex;
        justify-content: center;
        gap: 2rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #F3F4F6;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
    }

    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .revenue-color {
        background: linear-gradient(135deg, #10B981 0%, #059669 100%);
    }

    .expense-color {
        background: linear-gradient(135deg, #EF4444 0%, #DC2626 100%);
    }

    .chart-wrapper {
        position: relative;
        background: white;
        border-radius: 0.5rem;
        padding: 20px;
        border: 1px solid #E5E7EB;
    }

    .chart-grid {
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
        height: 300px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        pointer-events: none;
    }

    .grid-line {
        height: 1px;
        background: #F3F4F6;
        width: 100%;
    }

    .chart-container-dual {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        height: 300px;
        padding: 0 5px;
        gap: 12px;
        position: relative;
    }

    .chart-bar-group {
        flex: 1;
        height: 100%;
        display: flex;
        align-items: flex-end;
        justify-content: center;
    }

    .chart-bar-dual {
        display: flex;
        gap: 6px;
        align-items: flex-end;
        height: 100%;
        width: 100%;
        justify-content: center;
    }

    .chart-bar {
        width: 45%;
        min-height: 5px;
        border-radius: 6px 6px 0 0;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding-top: 8px;
    }

    .revenue-bar {
        background: linear-gradient(180deg, #10B981 0%, #059669 100%);
    }

    .expense-bar {
        background: linear-gradient(180deg, #EF4444 0%, #DC2626 100%);
    }

    .bar-value {
        font-size: 0.7rem;
        font-weight: 700;
        color: white;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    .chart-bar:hover {
        opacity: 0.85;
        transform: translateY(-8px) scale(1.05);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .chart-bar::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 110%;
        left: 50%;
        transform: translateX(-50%) scale(0);
        background: rgba(0, 0, 0, 0.9);
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        white-space: nowrap;
        pointer-events: none;
        transition: all 0.2s ease;
        z-index: 10;
        font-weight: 600;
    }

    .chart-bar:hover::after {
        transform: translateX(-50%) scale(1);
    }

    .chart-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 12px;
        padding: 0 25px;
        font-size: 0.75rem;
        color: #6B7280;
        font-weight: 600;
    }

    .chart-labels span {
        flex: 1;
        text-align: center;
        text-transform: uppercase;
    }

    .chart-summary {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-top: 1.5rem;
        padding: 1.25rem;
        background: #F9FAFB;
        border-radius: 0.5rem;
        border: 1px solid #E5E7EB;
    }

    .summary-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .summary-label {
        font-size: 0.75rem;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .summary-value {
        font-size: 1.125rem;
        font-weight: 700;
    }

    .revenue-text {
        color: #10B981;
    }

    .expense-text {
        color: #EF4444;
    }

    .profit-text {
        color: #3B82F6;
    }

    .summary-divider {
        width: 1px;
        height: 40px;
        background: #D1D5DB;
    }

    /* Responsive */
    @media (max-width: 968px) {
        .chart-container-dual {
            height: 250px;
            gap: 8px;
        }

        .chart-grid {
            height: 250px;
        }

        .chart-labels span {
            font-size: 0.65rem;
        }

        .chart-bar {
            width: 42%;
        }

        .bar-value {
            font-size: 0.6rem;
        }

        .chart-summary {
            flex-direction: column;
            gap: 1rem;
        }

        .summary-divider {
            width: 100%;
            height: 1px;
        }

         .table th:last-child,
    .table td:last-child {
        background: #F9FAFB;
        font-weight: 700;
    }

    .table tbody tr:hover td:last-child {
        background: #F3F4F6;
    }
    }

    @media (max-width: 640px) {
        .chart-bar-dual {
            gap: 4px;
        }

        .bar-value {
            display: none;
        }
    }
</style>
@endsection
