@extends('layouts.app')

@section('title', 'Commandes de Vente - easyShop')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Commandes de Vente</h1>
    @can("create-vente")
        <a href="{{ route('ventes.create') }}" class="btn btn-primary">
            ➕ Nouvelle Commande
        </a>
    @endcan
</div>

<!-- Filtres -->
<form method="GET" action="{{ route('ventes.index') }}">
<div style="background: white; padding: 1.5rem; border-radius: 0.75rem; margin-bottom: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <div class="grid grid-3">
        <div class="form-group">
            <select class="form-select" name="status">
                <option value="">-- Statut --</option>
                <option value="EN ATTENTE" {{ request('status') == 'EN ATTENTE' ? 'selected' : '' }}>En attente</option>
                <option value="CONFIRMER" {{ request('status') == 'CONFIRMER' ? 'selected' : '' }}>Confirmer</option>
                <option value="LIVRER" {{ request('status') == 'LIVRER' ? 'selected' : '' }}>Livrer</option>
                <option value="PAYER" {{ request('status') == 'PAYER' ? 'selected' : '' }}>Payer</option>
                <option value="ANNULER" {{ request('status') == 'ANNULER' ? 'selected' : '' }}>Annulée</option>
            </select>
        </div>

        <div class="form-group">
            <input type="text" name="client" class="form-input"
                   placeholder="Rechercher par client..."
                   value="{{ request('client') }}">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" style="width: 100%;">🔍 Filtrer</button>
        </div>
    </div>
</div>
</form>

<!-- Tableau -->
<div style="background: white; border-radius: 0.75rem; overflow-x:auto; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <table class="table" style="overflow-x: auto;">
        <thead>
            <tr>
                <th>Code Vente</th>
                <th>Client</th>
                <th>Montant</th>
                <th>Statut</th>
                <th>Date</th>
                <th>Vendeur</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($commands as $command)
                <tr>
                    <td><strong>{{ $command->codeCommand }}</strong></td>
                    <td>{{ $command->client->name ?? 'N/A' }}</td>
                    <td>{{ number_format($command->montantTotal, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($command->status == 'EN ATTENTE')
                            <span class="badge badge-warning">En Attente</span>
                        @elseif($command->status == 'CONFIRMER')
                            <span class="badge badge-secondary" style="background-color: #1b609c; color: white;">Confirmé</span>
                        @elseif($command->status == 'LIVRER')
                            <span class="badge badge-secondary" style="background-color: #f1077c; color: white;">Livré</span>
                        @elseif($command->status == 'RECU')
                            <span class="badge badge-info">Reçu</span>
                        @elseif($command->status == 'ANNULER')
                            <span class="badge badge-danger">Annulée</span>
                        @else
                            <span class="badge badge-success">Payé</span>
                        @endif
                    </td>
                    <td>{{ $command->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $command->creator->name ?? 'N/A' }}</td>
                    <td>
                    
                    
                        @if( Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager') || Auth::user()->hasRole('vendeur'))
                            @can('view-vente')
                                <a href="{{ route('ventes.show', $command->id) }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
                                     Voir
                                </a>
                            @endcan
                        @endif
                           
                        @if(Auth::user()->hasRole('manager') || Auth::user()->hasRole('admin'))
                            @can('delete-vente')
                                <form action="{{ route('ventes.delete', $command->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"  style="padding: 0.5rem ; font-size: 0.875rem;" onclick="return confirm('Êtes-vous sûr ?')">
                                        Supprimer
                                    </button>
                                </form>
                            @endcan
                        @endif
                    
                            <!-- Afficher le bouton "Générer Facture" seulement si l'utilisateur a la permission -->
                            @if( Auth::user()->hasRole('admin') || Auth::user()->hasRole('manager') || Auth::user()->hasRole('vendeur') || Auth::user()->hasRole('comptable') )
                                @can('download-invoice')
                                    <form action="{{ route('invoices.generate', $command->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn" style="background-color: var(--success); color: white; padding: 0.5rem;"
                                        onclick="return confirm('Générer la facture pour cette commande ?')">
                                            Facture
                                        </button>
                                    </form>

                                @endcan
                            @endif                      
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">
                        Aucune commande de vente trouvée
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
