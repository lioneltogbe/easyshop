@extends('layouts.app')

@section('title', "Commandes d'Achat - easyShop")

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <h1 style="font-size:2rem;font-weight:700;">Commandes d'Achat</h1>
    <a href="{{ route('achats.create') }}" class="btn btn-primary">➕ Nouvelle Commande</a>
</div>

<x-pagination-controls
    :action="route('achats.index')"
    :params="$params"
    searchPlaceholder="Rechercher par code commande..."
    :sortOptions="[
        ['value'=>'CodeCommande',  'label'=>'Code'],
        ['value'=>'status',        'label'=>'Statut'],
        ['value'=>'montantTotal',  'label'=>'Montant'],
        ['value'=>'created_at',    'label'=>'Date'],
    ]"
>
    {{-- Filtre statut --}}
    <div>
        <select name="filter_status" class="form-select" style="margin-bottom:0;">
            <option value="">-- Statut --</option>
            @foreach(['EN ATTENTE','RECU','PAYER'] as $s)
                <option value="{{ $s }}" @selected(($params['filter_status'] ?? '') == $s)>{{ ucfirst(strtolower($s)) }}</option>
            @endforeach
        </select>
    </div>
    {{-- Filtre fournisseur --}}
    <div>
        <input type="text" name="search_fournisseur" class="form-input"
               placeholder="Filtrer par fournisseur..."
               value="{{ request('search_fournisseur') }}"
               style="margin-bottom:0;">
    </div>
</x-pagination-controls>

@if($commands->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $commands->firstItem() }}–{{ $commands->lastItem() }} sur {{ $commands->total() }} commandes
</p>
@endif

<div style="background:white;border-radius:.75rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    <table class="table">
        <thead>
            <tr>
                <th>Code Commande</th>
                <th>Fournisseur</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commands as $command)
                <tr>
                    <td><strong>{{ $command->CodeCommande }}</strong></td>
                    <td>{{ $command->fournisseur->name ?? 'N/A' }}</td>
                    <td>{{ number_format($command->montantTotal, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($command->status === 'EN ATTENTE')
                            <span class="badge badge-warning">En Attente</span>
                        @elseif($command->status === 'RECU')
                            <span class="badge badge-info">Reçu</span>
                        @else
                            <span class="badge badge-success">Payé</span>
                        @endif
                    </td>
                    <td>{{ $command->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('achats.show', $command->id) }}" class="btn btn-secondary" style="padding:.5rem 1rem;font-size:.875rem;">👁️ Voir</a>
                        @if($command->status === 'EN ATTENTE')
                            @can('delete-achat')
                            <form action="{{ route('achats.destroy', $command->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding:.5rem 1rem;font-size:.875rem;">Supprimer</button>
                            </form>
                            @endcan
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:2rem;color:#6B7280;">Aucune commande d'achat trouvée</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($commands->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid #E5E7EB;">
        {{ $commands->links() }}
    </div>
    @endif
</div>
@endsection
