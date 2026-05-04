@extends('layouts.app')
 
@section('content')
<style>
    :root {
        --color-primary: #7C3AED;
        --color-primary-light: #A78BFA;
        --color-primary-dark: #6D28D9;
        --color-gray-50: #F9FAFB;
        --color-gray-100: #F3F4F6;
        --color-gray-200: #E5E7EB;
        --color-gray-600: #4B5563;
        --color-gray-700: #374151;
        --color-gray-900: #111827;
        --color-success: #10B981;
        --color-warning: #F59E0B;
        --color-danger: #EF4444;
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --spacing-2xl: 3rem;
        --radius-lg: 0.75rem;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --transition-base: 200ms ease-in-out;
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        background-color: var(--color-gray-50);
        color: var(--color-gray-900);
    }
    
    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 var(--spacing-md);
    }
    
    .header-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
        gap: var(--spacing-lg);
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-gray-900);
    }
    
    .breadcrumb {
        color: var(--color-primary);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: var(--spacing-md);
    }
    
    .breadcrumb:hover {
        text-decoration: underline;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1.5rem;
        border: none;
        border-radius: var(--radius-lg);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all var(--transition-base);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
        color: white;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
    }
    
    .btn-success {
        background-color: var(--color-success);
        color: white;
    }
    
    .btn-success:hover {
        background-color: #059669;
    }
    
    .btn-warning {
        background-color: var(--color-warning);
        color: white;
    }
    
    .btn-warning:hover {
        background-color: #D97706;
    }
    
    .card {
        background-color: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-gray-200);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-bottom: var(--spacing-2xl);
    }
    
    .card-header {
        padding: var(--spacing-xl);
        border-bottom: 1px solid var(--color-gray-200);
        background-color: var(--color-gray-50);
    }
    
    .card-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--color-gray-900);
    }
    
    .card-body {
        padding: var(--spacing-xl);
    }
    
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }
    
    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .info-item {
        display: flex;
        flex-direction: column;
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 500;
        color: var(--color-gray-900);
    }
    
    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .badge.pending {
        background-color: rgba(249, 115, 22, 0.1);
        color: var(--color-warning);
    }
    
    .badge.received {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--color-success);
    }
    
    .badge.paid {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3B82F6;
    }
    
    .table-container {
        overflow-x: auto;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background-color: var(--color-gray-50);
        border-bottom: 1px solid var(--color-gray-200);
    }
    
    th {
        padding: var(--spacing-md);
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        color: var(--color-gray-700);
        text-transform: uppercase;
    }
    
    td {
        padding: var(--spacing-md);
        border-bottom: 1px solid var(--color-gray-200);
    }
    
    tbody tr:hover {
        background-color: var(--color-gray-50);
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }
    
    @media (max-width: 768px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .summary-item {
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        text-align: center;
        border: 1px solid;
    }
    
    .summary-item.items {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.1) 0%, rgba(59, 130, 246, 0.05) 100%);
        border-color: rgba(59, 130, 246, 0.2);
    }
    
    .summary-item.total {
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(124, 58, 237, 0.05) 100%);
        border-color: rgba(124, 58, 237, 0.2);
    }
    
    .summary-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-gray-600);
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    
    .summary-value {
        font-size: 1.5rem;
        font-weight: 700;
    }
    
    .summary-value.items {
        color: #3B82F6;
    }
    
    .summary-value.total {
        color: var(--color-primary);
    }
    
    .action-buttons {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-xl);
        flex-wrap: wrap;
    }
</style>
 
<div class="container" style="padding-top: var(--spacing-2xl);">
    <div class="header-section">
        <div>
            <a href="{{ route('achats.index') }}" class="breadcrumb">← Retour aux commandes</a>
            <h1 class="page-title">{{ $achatCommand->CodeCommande }}</h1>
        </div>
    </div>
 
    <!-- INFORMATIONS GÉNÉRALES -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informations Générales</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Fournisseur</span>
                    <span class="info-value">{{ $achatCommand->fournisseur->name ?? 'N/A' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Statut</span>
                    <span class="badge {{ strtolower($achatCommand->status) }}">
                            {{$achatCommand->status}}
                    </span>
                </div>
                 <div class="info-item">
                    <span class="info-label">Méthode de payement</span>
                    <span class="badge {{ strtolower($achatCommand->status) }}">
                    @if($achatCommand->status== 'EN ATTENTE')
                        En attente de réception
                    @elseif($achatCommand->status== 'RECU')
                        Non payée
                    @elseif($achatCommand->status== 'PAYER')
                        Payée
                    @else
                        En attente de réception
                    @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Date de Commande</span>
                    <span class="info-value">{{ $achatCommand->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Montant Total</span>
                    <span class="info-value">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>
 
    <!-- ARTICLES -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Articles ({{ $achatCommand->products->count() ?? 0 }})</h2>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Produit</th>
                            <th>Quantité</th>
                            <th>Prix Unitaire</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>{{ $item->product->name ?? 'N/A' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->unitPriceAtCommand, 0, ',', ' ') }} FCFA</td>
                            <td><strong>{{ number_format($item->quantity * $item->unitPriceAtCommand, 0, ',', ' ') }} FCFA</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
 
            <div class="summary-grid">
                <div class="summary-item items">
                    <div class="summary-label">Articles</div>
                    <div class="summary-value items">{{ $itemCount ?? 0 }}</div>
                </div>
                <div class="summary-item total">
                    <div class="summary-label">Montant Total</div>
                    <div class="summary-value total">{{ number_format($totalAmount, 0, ',', ' ') }} FCFA</div>
                </div>
            </div>
        </div>
    </div>
 
    <!-- ACTIONS -->
    <div class="action-buttons">
    @can('edit-achat')
        @if($achatCommand->status === 'EN ATTENTE')
            <form action="{{ route('achats.commandrecu', $achatCommand->id) }}" method="POST" style="display: inline;">
                @csrf
                <!-- @method('PATCH') -->
                <button type="submit" class="btn btn-success">Marquer comme Reçu</button>
            </form>
        @endif
 
        @if($achatCommand->status === 'RECU')
            <form action="{{ route('achats.commandpayer', $achatCommand->id) }}" method="POST" style="display: inline;">
                @csrf
            <div>
                <select name="paiement_method" required class="form-input" style="margin-bottom:10px">
                    <option value="paiement_method">-- Choisir un mode de paiement --</option>
                    <option value="caisse">Caisse</option>
                    <option value="cheque">Chèque</option>
                    <option value="transfert">Transfert</option>
                    <option value="credit">Crédit</option>
                </select>
            </div>
                <!-- @method('PATCH') -->
                <button type="submit" class="btn btn-success">Marquer comme Payé</button>
                <a href="{{ route('achats.index') }}" class="btn btn-primary">Retour</a>
            </form>
        @endif
 
    @endcan
    </div>
</div>
 

@endsection