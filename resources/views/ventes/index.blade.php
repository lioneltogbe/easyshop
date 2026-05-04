@extends('layouts.app')

@section('title', 'Commandes de Vente - easyShop')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <h1 style="font-size:2rem;font-weight:700;">Commandes de Vente</h1>
    @can('create-vente')
        <a href="{{ route('ventes.create') }}" class="btn btn-primary">➕ Nouvelle Commande</a>
    @endcan
</div>

<x-pagination-controls
    :action="route('ventes.index')"
    :params="$params"
    searchPlaceholder="Rechercher par code commande..."
    :sortOptions="[
        ['value'=>'codeCommand',  'label'=>'Code'],
        ['value'=>'status',       'label'=>'Statut'],
        ['value'=>'montantTotal', 'label'=>'Montant'],
        ['value'=>'created_at',   'label'=>'Date'],
    ]"
>
    {{-- Filtre statut --}}
    <div>
        <select name="filter_status" class="form-select" style="margin-bottom:0;">
            <option value="">-- Statut --</option>
            @foreach(['EN ATTENTE','CONFIRMER','LIVRER','PAYER','ANNULER'] as $s)
                <option value="{{ $s }}" @selected(($params['filter_status'] ?? '') == $s)>{{ ucfirst(strtolower($s)) }}</option>
            @endforeach
        </select>
    </div>
    {{-- Filtre client --}}
    <div>
        <input type="text" name="search_client" class="form-input"
               placeholder="Filtrer par client..."
               value="{{ request('search_client') }}"
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
                <th>Code Vente</th><th>Client</th><th>Montant</th>
                <th>Statut</th><th>Date</th><th>Vendeur</th><th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commands as $command)
                <tr>
                    <td><strong>{{ $command->codeCommand }}</strong></td>
                    <td>{{ $command->client->name ?? 'N/A' }}</td>
                    <td>{{ number_format($command->montantTotal, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @php $badges=['EN ATTENTE'=>'warning','CONFIRMER'=>'secondary','LIVRER'=>'secondary','RECU'=>'info','ANNULER'=>'danger']; @endphp
                        @if($command->status==='CONFIRMER')
                            <span class="badge badge-secondary" style="background:#EDE9FE;color:#5B21B6;">Confirmé</span>
                        @elseif($command->status==='LIVRER')
                            <span class="badge badge-secondary" style="background:#f1077c;color:white;">Livré</span>
                        @elseif($command->status==='EN ATTENTE')
                            <span class="badge badge-warning">En Attente</span>
                        @elseif($command->status==='ANNULER')
                            <span class="badge badge-danger">Annulée</span>
                        @elseif($command->status==='RECU')
                            <span class="badge badge-info">Reçu</span>
                        @else
                            <span class="badge badge-success">Payé</span>
                        @endif
                    </td>
                    <td style="font-size:.75rem;">{{ $command->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $command->creator->name ?? 'N/A' }}</td>
                    <td style="display:flex; justify-content:space-beteween; gap:.5rem; margin-top:1.5rem;">
                        @if(Auth::user()->hasRole('admin')||Auth::user()->hasRole('manager')||Auth::user()->hasRole('vendeur'))
                            @can('view-vente')
                                <a href="{{ route('ventes.show', $command->id) }}" class="btn btn-secondary" style="padding:.5rem 1rem;font-size:.875rem;">Voir</a>
                            @endcan
                        @endif
                        @if(Auth::user()->hasRole('manager')||Auth::user()->hasRole('admin'))
                            @can('delete-vente')
                                <form action="{{ route('ventes.delete', $command->id) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:.5rem;font-size:.875rem;" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                                </form>
                            @endcan
                        @endif
                        @if(Auth::user()->hasRole('admin')||Auth::user()->hasRole('manager')||Auth::user()->hasRole('vendeur')||Auth::user()->hasRole('comptable'))
                            @can('download-invoice')
                                <form action="{{ route('invoices.generate', $command->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <button type="submit" class="btn" style="background:var(--success);color:white;padding:.5rem;" onclick="return confirm('Générer la facture ?')">Facture</button>
                                </form>
                            @endcan
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align:center;padding:2rem;">Aucune commande de vente trouvée</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($commands->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid #E5E7EB;">{{ $commands->links() }}</div>
    @endif
</div>
@endsection
