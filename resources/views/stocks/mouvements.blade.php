@extends('layouts.app')

@section('title', 'Mouvements de stock')

@section('content')
<style>
:root{--primary:#6366F1;--success:#10B981;--danger:#EF4444;--gray-50:#F9FAFB;--gray-100:#F3F4F6;--gray-200:#E5E7EB;--gray-700:#374151;--gray-900:#111827;--radius:.75rem;}
.product-box{display:flex;flex-direction:column;}
.product-name{font-weight:600;color:var(--gray-900);}
.product-code{font-size:.75rem;color:var(--gray-700);}
.qty-in{font-weight:700;color:var(--success);}
.qty-out{font-weight:700;color:var(--danger);}
</style>

<div style="margin-bottom:2rem;">
    <a href="{{ route('stocks.index') }}" style="color:var(--primary);text-decoration:none;font-weight:500;">← Retour à la gestion des stocks</a>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <div>
        <h1 style="font-size:1.8rem;font-weight:700;color:var(--gray-900);">Mouvements de stock</h1>
        <p style="color:var(--gray-700);font-size:.9rem;">Historique complet des entrées et sorties</p>
    </div>
</div>

<x-pagination-controls
    :action="route('stocks.mouvements')"
    :params="$params"
    searchPlaceholder="Rechercher par raison..."
    :sortOptions="[
        ['value'=>'created_at', 'label'=>'Date'],
        ['value'=>'type',       'label'=>'Type'],
        ['value'=>'quantity',   'label'=>'Quantité'],
    ]"
>
    {{-- Filtre type --}}
    <div>
        <select name="filter_type" class="form-select" style="margin-bottom:0;">
            <option value="">-- Type --</option>
            <option value="IN"        @selected(($params['filter_type'] ?? '') === 'IN')>Entrée</option>
            <option value="OUT"       @selected(($params['filter_type'] ?? '') === 'OUT')>Sortie</option>
            <option value="AJUSTMENT" @selected(($params['filter_type'] ?? '') === 'AJUSTMENT')>Ajustement</option>
        </select>
    </div>
</x-pagination-controls>

@if($movements->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $movements->firstItem() }}–{{ $movements->lastItem() }} sur {{ $movements->total() }} mouvements
</p>
@endif

<div style="background:white;border-radius:var(--radius);box-shadow:0 10px 20px rgba(0,0,0,.08);overflow:hidden;">
    @if($movements->isEmpty())
        <div style="padding:3rem;text-align:center;color:var(--gray-700);">Aucun mouvement de stock enregistré.</div>
    @else
        <table style="width:100%;border-collapse:collapse;">
            <thead style="background:var(--gray-100);">
                <tr>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Date</th>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Produit</th>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Type</th>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Quantité</th>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Raison</th>
                    <th style="padding:1rem;text-align:left;font-size:.75rem;font-weight:600;color:var(--gray-700);text-transform:uppercase;letter-spacing:.05em;border-bottom:1px solid var(--gray-200);">Utilisateur</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $movement)
                    <tr style="border-bottom:1px solid var(--gray-200);">
                        <td style="padding:1rem;font-size:.9rem;">
                            {{ $movement->created_at->format('d/m/Y') }}<br>
                            <small>{{ $movement->created_at->format('H:i') }}</small>
                        </td>
                        <td style="padding:1rem;">
                            <div class="product-box">
                                <span class="product-name">{{ $movement->product->name ?? '—' }}</span>
                                <span class="product-code">{{ $movement->product->code ?? '' }}</span>
                            </div>
                        </td>
                        <td style="padding:1rem;">
                            @if($movement->type === 'IN')
                                <span style="background:var(--success);color:white;padding:.3rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;">ENTRÉE</span>
                            @elseif($movement->type === 'OUT')
                                <span style="background:var(--danger);color:white;padding:.3rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;">SORTIE</span>
                            @else
                                <span style="background:#6366F1;color:white;padding:.3rem .75rem;border-radius:999px;font-size:.7rem;font-weight:700;">AJUST.</span>
                            @endif
                        </td>
                        <td style="padding:1rem;" class="{{ $movement->type === 'IN' ? 'qty-in' : 'qty-out' }}">
                            {{ $movement->type === 'IN' ? '+' : '' }}{{ $movement->quantity }}
                        </td>
                        <td style="padding:1rem;font-size:.9rem;">{{ $movement->reason ?? '—' }}</td>
                        <td style="padding:1rem;font-size:.9rem;">{{ $movement->createdBy->name ?? 'Inconnu' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        @if($movements->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--gray-200);">
            {{ $movements->links() }}
        </div>
        @endif
    @endif
</div>
@endsection
