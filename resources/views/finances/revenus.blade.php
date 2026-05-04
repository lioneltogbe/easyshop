@extends('layouts.app')

@section('title', 'Revenus - easyShop')

@section('content')

<div style="margin-bottom:2rem;">
    <a href="{{ route('finances.index') }}" class="breadcrumb">← Retour aux finances</a>
</div>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;">
    <h1 style="font-size:2rem;font-weight:700;color:#111827;">Revenus</h1>
</div>

<x-pagination-controls
    :action="route('finances.revenus')"
    :params="$params"
    searchPlaceholder="Rechercher par description, catégorie..."
    :sortOptions="[
        ['value'=>'created_at',     'label'=>'Date'],
        ['value'=>'montant',        'label'=>'Montant'],
        ['value'=>'paiement_method','label'=>'Méthode'],
        ['value'=>'categorie',      'label'=>'Catégorie'],
    ]"
>
    <div>
        <select name="filter_paiement_method" class="form-select" style="margin-bottom:0;">
            <option value="">-- Méthode --</option>
            @foreach(['caisse','cheque','transfert','credit'] as $m)
                <option value="{{ $m }}" @selected(($params['filter_paiement_method'] ?? '') === $m)>{{ ucfirst($m) }}</option>
            @endforeach
        </select>
    </div>
</x-pagination-controls>

@if($transactions->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} sur {{ $transactions->total() }} revenus
</p>
@endif

<div style="background:white;border-radius:.75rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    @include('finances._transactions_table')
</div>
@endsection
