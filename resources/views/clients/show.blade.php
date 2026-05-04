@extends('layouts.app')

@section('content')
<style>
:root {
    --color-primary: #7C3AED;
    --color-primary-light: #A78BFA;
    --color-gray-50: #F9FAFB;
    --color-gray-100: #F3F4F6;
    --color-gray-200: #E5E7EB;
    --color-gray-600: #4B5563;
    --color-gray-700: #374151;
    --color-gray-900: #111827;
    --color-danger: #EF4444;
    --color-success: #10B981;
    --color-warning: #F59E0B;
    --color-info: #3B82F6;
    --spacing-md: 1rem;
    --spacing-lg: 1.5rem;
    --spacing-xl: 2rem;
    --spacing-2xl: 3rem;
    --radius-lg: 0.75rem;
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
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

.header-content h1 {
    font-size: 2rem;
    font-weight: 700;
    color: var(--color-gray-900);
    margin-bottom: 0.5rem;
}

.header-content p {
    font-size: 0.875rem;
    color: var(--color-gray-600);
}

.header-actions {
    display: flex;
    gap: var(--spacing-md);
}

.badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.badge-success {
    background-color: rgba(16, 185, 129, 0.1);
    color: var(--color-success);
}

.badge-danger {
    background-color: rgba(239, 68, 68, 0.1);
    color: var(--color-danger);
}

.badge-warning {
    background-color: rgba(245, 158, 11, 0.1);
    color: var(--color-warning);
}

.card {
    background-color: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--color-gray-200);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    margin-bottom: var(--spacing-lg);
}

.card-header {
    padding: var(--spacing-xl);
    border-bottom: 1px solid var(--color-gray-200);
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(124, 58, 237, 0.05) 100%);
}

.card-title {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--color-primary);
    margin: 0;
}

.card-body {
    padding: var(--spacing-xl);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--spacing-lg);
}

.info-item {
    display: flex;
    flex-direction: column;
}

.info-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--color-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.info-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--color-gray-900);
    word-break: break-word;
}

.info-value.text-muted {
    color: var(--color-gray-600);
    font-weight: 400;
}

.info-value.text-danger {
    color: var(--color-danger);
}

.info-value.text-success {
    color: var(--color-success);
}

.info-value.text-warning {
    color: var(--color-warning);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: var(--spacing-lg);
    margin-bottom: var(--spacing-lg);
}

.stat-card {
    background: linear-gradient(135deg, rgba(124, 58, 237, 0.05) 0%, rgba(124, 58, 237, 0.02) 100%);
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    padding: var(--spacing-lg);
    text-align: center;
}

.stat-card.danger {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.05) 0%, rgba(239, 68, 68, 0.02) 100%);
    border-color: rgba(239, 68, 68, 0.2);
}

.stat-card.success {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.05) 0%, rgba(16, 185, 129, 0.02) 100%);
    border-color: rgba(16, 185, 129, 0.2);
}

.stat-card.warning {
    background: linear-gradient(135deg, rgba(245, 158, 11, 0.05) 0%, rgba(245, 158, 11, 0.02) 100%);
    border-color: rgba(245, 158, 11, 0.2);
}

.stat-label {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--color-gray-600);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--color-gray-900);
}

.stat-value.danger {
    color: var(--color-danger);
}

.stat-value.success {
    color: var(--color-success);
}

.stat-value.warning {
    color: var(--color-warning);
}

.table {
    width: 100%;
    border-collapse: collapse;
    margin-top: var(--spacing-lg);
}

.table thead {
    background-color: var(--color-gray-50);
    border-bottom: 2px solid var(--color-gray-200);
}

.table th {
    padding: var(--spacing-md);
    text-align: left;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--color-gray-700);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.table td {
    padding: var(--spacing-md);
    border-bottom: 1px solid var(--color-gray-200);
}

.table tbody tr:hover {
    background-color: var(--color-gray-50);
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.empty-state {
    text-align: center;
    padding: var(--spacing-2xl);
    color: var(--color-gray-600);
}

.empty-state-icon {
    font-size: 3rem;
    margin-bottom: var(--spacing-lg);
}

.empty-state-text {
    font-size: 0.95rem;
}

.btn {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: all var(--transition-base);
}

.btn-primary {
    background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-light) 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 12px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
    background-color: var(--color-gray-200);
    color: var(--color-gray-700);
}

.btn-secondary:hover {
    background-color: var(--color-gray-300);
}

.btn-danger {
    background-color: var(--color-danger);
    color: white;
}

.btn-danger:hover {
    background-color: #DC2626;
}

.btn-sm {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
}

.section-divider {
    margin: var(--spacing-2xl) 0;
    padding: var(--spacing-xl) 0;
    border-bottom: 1px solid var(--color-gray-200);
}

.section-divider:last-child {
    border-bottom: none;
}

.alert {
    padding: var(--spacing-lg);
    border-radius: var(--radius-lg);
    margin-bottom: var(--spacing-lg);
    border-left: 4px solid;
}

.alert-info {
    background-color: rgba(59, 130, 246, 0.1);
    border-left-color: var(--color-info);
    color: var(--color-info);
}

.breadcrumb {
    display: flex;
    gap: 0.5rem;
    margin-bottom: var(--spacing-lg);
    font-size: 0.875rem;
}

.breadcrumb a {
    color: var(--color-primary);
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.breadcrumb span {
    color: var(--color-gray-600);
}

@media (max-width: 768px) {
    .header-section {
        flex-direction: column;
        align-items: flex-start;
    }

    .header-actions {
        width: 100%;
    }

    .header-actions .btn {
        flex: 1;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .table {
        font-size: 0.85rem;
    }

    .table th,
    .table td {
        padding: 0.5rem;
    }
}
</style>

<div class="container" style="padding-top: var(--spacing-2xl);">
    <!-- Breadcrumb -->
    <div class="breadcrumb">
        <a href="{{ route('clients.index') }}">Clients</a>
        <span>/</span>
        <span>{{ $client->name }}</span>
    </div>

    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <h1>{{ $client->name }}</h1>
            <p>Détails et informations du client</p>
        </div>
        <div class="header-actions">
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">Modifier</a>
            <a href="{{ route('clients.index') }}" class="btn btn-secondary">Retour</a>
        </div>
    </div>

    <!-- Status Alert -->
    @if($client->is_over_credit_limit)
    <div class="alert alert-info">
        <strong>⚠️ Attention :</strong> Ce client a dépassé sa limite de crédit. Montant dû : <strong>{{ number_format($client->available_credit, 2, ',', ' ') }} €</strong>
    </div>
    @endif

    <!-- Informations Personnelles -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informations Personnelles</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Nom du Client</span>
                    <span class="info-value">{{ $client->name }}</span>
                </div>

                <div class="info-item">
                    <span class="info-label">Email</span>
                    <span class="info-value">
                        @if($client->email)
                            <a href="mailto:{{ $client->email }}" style="color: var(--color-primary); text-decoration: none;">
                                {{ $client->email }}
                            </a>
                        @else
                            <span class="text-muted">Non renseigné</span>
                        @endif
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Téléphone</span>
                    <span class="info-value">
                        @if($client->phone)
                            <a href="tel:{{ $client->phone }}" style="color: var(--color-primary); text-decoration: none;">
                                {{ $client->phone }}
                            </a>
                        @else
                            <span class="text-muted">Non renseigné</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Adresse -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Adresse</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Adresse</span>
                    <span class="info-value">
                        @if($client->address)
                            {{ $client->address }}
                        @else
                            <span class="text-muted">Non renseignée</span>
                        @endif
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Ville</span>
                    <span class="info-value">
                        @if($client->city)
                            {{ $client->city }}
                        @else
                            <span class="text-muted">Non renseignée</span>
                        @endif
                    </span>
                </div>

                <div class="info-item">
                    <span class="info-label">Pays</span>
                    <span class="info-value">
                        @if($client->country)
                            {{ $client->country }}
                        @else
                            <span class="text-muted">Non renseigné</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion du Crédit -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Gestion du Crédit</h2>
        </div>
        <div class="card-body">
            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Limite de Crédit</div>
                    <div class="stat-value">{{ number_format($client->creditLimit, 2, ',', ' ') }} </div>
                </div>

                <div class="stat-card {{ $client->available_credit < 0 ? 'danger' : 'success' }}">
                    <div class="stat-label">Crédit Disponible</div>
                    <div class="stat-value {{ $client->available_credit < 0 ? 'danger' : 'success' }}">
                        {{ number_format($client->available_credit, 2, ',', ' ') }} 
                    </div>
                </div>

                <div class="stat-card {{ $client->is_over_credit_limit ? 'danger' : 'success' }}">
                    <div class="stat-label">Crédit Actuel</div>
                    <div class="stat-value {{ $client->is_over_credit_limit ? 'danger' : 'success' }}">
                        {{ number_format($client->current_credit, 2, ',', ' ') }} FCFA
                    </div>
                </div>

                <div class="stat-card {{ $client->is_over_credit_limit ? 'danger' : 'success' }}">
                    <div class="stat-label">Statut</div>
                    <div class="stat-value">
                        @if($client->is_over_credit_limit)
                            <span class="badge badge-danger">Dépassé</span>
                        @else
                            <span class="badge badge-success">Bon</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Credit Info -->
            <div class="info-grid" style="margin-top: var(--spacing-lg);">
                <div class="info-item">
                    <span class="info-label">Statut du Crédit</span>
                    <span class="info-value">
                        @if($client->is_over_credit_limit)
                            <span class="text-danger">⚠️ Limite dépassée</span>
                        @else
                            <span class="text-success">✓ Crédit disponible</span>
                        @endif
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques des Commandes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Statistiques des Commandes</h2>
        </div>
        <div class="card-body">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Nombre de Commandes</div>
                    <div class="stat-value">{{ $client->getCommandCount() }}</div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Montant Total des Commandes</div>
                    <div class="stat-value">{{ number_format($client->getTotalMontant(), 2, ',', ' ') }} </div>
                </div>

                <div class="stat-card">
                    <div class="stat-label">Montant Moyen par Commande</div>
                    <div class="stat-value">
                        @if($client->getCommandCount() > 0)
                            {{ number_format($client->getTotalMontant() / $client->getCommandCount(), 2, ',', ' ') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique des Ventes -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Historique des Ventes</h2>
        </div>
        <div class="card-body">
            @if($client->ventes && $client->ventes->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>N° Vente</th>
                            <th>Date</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($client->ventes as $vente)
                        <tr>
                            <td>
                                <strong>#{{ $vente->id }}</strong>
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($vente->created_at)->format('d/m/Y') }}
                            </td>
                            <td>
                                <strong>{{ number_format($vente->montantTotal, 2, ',', ' ') }} </strong>
                            </td>
                            <td>
                                @if($vente->status === 'payée')
                                    <span class="badge badge-success">Payée</span>
                                @elseif($vente->status === 'en attente')
                                    <span class="badge badge-warning">En attente</span>
                                @else
                                    <span class="badge badge-danger">{{ ucfirst($vente->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if(isset($vente->id))
                                    <a href="{{ route('ventes.show', $vente->id) }}" class="btn btn-primary btn-sm">
                                        Voir
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">📦</div>
                    <div class="empty-state-text">
                        <p>Aucune vente pour ce client</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Actions Footer -->
    <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-2xl); margin-bottom: var(--spacing-2xl);">
        <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-primary">Modifier le Client</a>
        <a href="{{ route('clients.index') }}" class="btn btn-secondary">Retour à la Liste</a>
    </div>
</div>

@endsection
