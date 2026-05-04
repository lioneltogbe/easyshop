@extends('layouts.app')

@section('title', 'Clients - easyShop')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <h1 style="font-size:2rem;font-weight:700;">Gestion des Clients</h1>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">➕ Nouveau Client</a>
</div>

{{-- Barre de recherche / tri / pagination --}}
<x-pagination-controls
    :action="route('clients.index')"
    :params="$params"
    searchPlaceholder="Rechercher par nom, email, téléphone, ville..."
    :sortOptions="[
        ['value'=>'name',        'label'=>'Nom'],
        ['value'=>'email',       'label'=>'Email'],
        ['value'=>'city',        'label'=>'Ville'],
        ['value'=>'creditLimit', 'label'=>'Limite crédit'],
        ['value'=>'created_at',  'label'=>'Date création'],
    ]"
/>

{{-- Infos résultats --}}
@if($clients->total() > 0)
<p style="margin-bottom:0.75rem;color:#6B7280;font-size:0.875rem;">
    Affichage {{ $clients->firstItem() }}–{{ $clients->lastItem() }} sur {{ $clients->total() }} clients
</p>
@endif

<div style="background:white;border-radius:0.75rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Limite Crédit</th>
                <th>Crédit Utilisé</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td><strong>{{ $client->name }}</strong></td>
                    <td>{{ $client->email ?? 'N/A' }}</td>
                    <td>{{ $client->phone ?? 'N/A' }}</td>
                    <td>{{ number_format($client->creditLimit, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($client->current_credit, 0, ',', ' ') }} FCFA</td>
                    <td>
                        @if($client->isOverCreditLimit())
                            <span class="badge badge-danger">Dépassé</span>
                        @else
                            <span class="badge badge-success">OK</span>
                        @endif
                    </td>
                    <td style="display:flex; justify-content:between; gap:10px">
                        <a href="{{ route('clients.show', $client->id) }}" class="btn btn-primary" style="padding:.5rem 1rem;font-size:.875rem;"> Voir</a>
                        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-success" style="padding:.5rem 1rem;font-size:.875rem; background-color:green;"> Éditer</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:2rem;color:#6B7280;">
                        Aucun client trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($clients->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid #E5E7EB;">
        {{ $clients->links() }}
    </div>
    @endif
</div>
@endsection
