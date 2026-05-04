@extends('layouts.app')

@section('title', 'Commande reçue - ' . $command->CodeCommande)

@section('content')

<div style="margin-bottom: 2rem;">
    <a href="{{ route('achats.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">
        ← Retour aux commandes
    </a>
    <h1 style="font-size: 2rem; font-weight: 700; margin-top: 1rem;">
        Commande reçue ✅
    </h1>
</div>

{{-- Carte principale --}}
<div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">

    {{-- Infos commande --}}
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <div>
            <strong>Code commande :</strong><br>
            {{ $command->CodeCommande }}
        </div>

        <div>
            <strong>Statut :</strong><br>
            <span style="color: green; font-weight: 600;">
                {{ $command->status }}
            </span>
        </div>

        <div>
            <strong>Montant total :</strong><br>
            {{ number_format($command->montantTotal, 0, ',', ' ') }} FCFA
        </div>

        <div>
            <strong>Méthode de paiement :</strong><br>
            {{ $command->paiement_method }}
        </div>

        <div>
            <strong>Date de création :</strong><br>
            {{ $command->created_at->format('d/m/Y H:i') }}
        </div>

        <div>
            <strong>Dernière mise à jour :</strong><br>
            {{ $command->updated_at->format('d/m/Y H:i') }}
        </div>
    </div>

    {{-- Fournisseur --}}
    <div style="margin-bottom: 2rem;">
        <h3 style="font-weight: 600; margin-bottom: 0.5rem;">🏭 Fournisseur</h3>
        <div style="background: #F9FAFB; padding: 1rem; border-radius: 0.5rem;">
            <strong>{{ $command->fournisseur->name }}</strong>
        </div>
    </div>

    {{-- Produits --}}
    <div>
        <h3 style="font-weight: 600; margin-bottom: 1rem;">📦 Produits reçus</h3>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #F3F4F6;">
                    <th style="padding: 0.75rem; text-align: left;">Produit</th>
                    <th style="padding: 0.75rem; text-align: right;">Quantité</th>
                    <th style="padding: 0.75rem; text-align: right;">Prix unitaire</th>
                    <th style="padding: 0.75rem; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($command->products as $item)
                    <tr style="border-bottom: 1px solid #E5E7EB;">
                        <td style="padding: 0.75rem;">
                            {{ $item->product->name ?? 'Produit #' . $item->product_id }}
                        </td>
                        <td style="padding: 0.75rem; text-align: right;">
                            {{ number_format($item->quantity, 2, ',', ' ') }}
                        </td>
                        <td style="padding: 0.75rem; text-align: right;">
                            {{ number_format($item->unitPriceAtCommand, 0, ',', ' ') }} FCFA
                        </td>
                        <td style="padding: 0.75rem; text-align: right; font-weight: 600;">
                            {{ number_format($item->quantity * $item->unitPriceAtCommand, 0, ',', ' ') }} FCFA
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Actions --}}
    <div style="display: flex; gap: 1rem; margin-top: 2rem;">
        <a href="{{ route('achats.index') }}" class="btn btn-primary" style="flex: 1; text-align: center;">
            📄 Voir toutes les commandes
        </a>
        <button onclick="window.print()" class="btn btn-secondary" style="flex: 1;">
            🖨️ Imprimer
        </button>
    </div>

</div>

@endsection
