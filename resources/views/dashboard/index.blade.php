@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')

<!-- Dashboard Header -->
<div class="dashboard-header">
    <div>
        <h1>Bienvenue dans easyShop 👋</h1>
        <!-- <p>Tableau de bord de gestion commerciale</p> -->
    </div>
    <div class="dashboard-date">
        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span>{{ \Carbon\Carbon::now()->locale('fr')->isoFormat('dddd, D MMMM YYYY') }}</span>
    </div>
</div>


<!-- DASHBOARD ADMIN -->
@if($userRole === 'admin')
    <style>
        /* ========== CARTES DE STATISTIQUES ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background-color: var(--white);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            /* border-top: 4px solid #3b82f6; */
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card.blue {
            border:3px solid #3b82f6;
        }

        .stat-card.green {
            border:3px solid #10b981;
        }

        .stat-card.purple {
            border:3px solid #a855f7;
        }

        .stat-card.orange {
            border:3px solid #f97316;
        }

        .stat-card-content h4 {
            color: #4b5563;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .stat-card-value {
            font-size: 20px;
            font-weight: bold;
            margin: 12px 0;
        }

        .stat-card.blue .stat-card-value {
            color: #2563eb;
        }

        .stat-card.green .stat-card-value {
            color: #059669;
        }

        .stat-card.purple .stat-card-value {
            color: #9333ea;
        }

        .stat-card.orange .stat-card-value {
            color: #ea580c;
        }

        .stat-card-subtitle {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 8px;
        }

        .stat-card-icon {
            font-size: 32px;
        }

        .stat-card.blue .stat-card-icon {
            color: #3b82f6;
        }

        .stat-card.green .stat-card-icon {
            color: #10b981;
        }

        .stat-card.purple .stat-card-icon {
            color: #a855f7;
        }

        .stat-card.orange .stat-card-icon {
            color: #f97316;
        }

        /* ========== GRAPHIQUES ========== */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .chart-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
        }

        .chart-card h3 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 16px 0;
        }

        .chart-card canvas {
            max-height: 300px;
        }

        /* ========== TOP VENDEURS ========== */
        .top-vendeurs-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .vendeur-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .vendeur-item:last-child {
            border-bottom: none;
        }

        .vendeur-info h5 {
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 4px 0;
            font-size: 14px;
        }

        .vendeur-info p {
            font-size: 12px;
            color: #6b7280;
            margin: 0;
        }

        .vendeur-montant {
            font-weight: bold;
            color: #059669;
            font-size: 14px;
        }

        /* ========== TABLE ========== */
        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            overflow-x: auto;
        }

        .table-container h3 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-livree {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-en_cours {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-default {
            background-color: #f3f4f6;
            color: #374151;
        }

        .text-bold {
            font-weight: 600;
        }

        .text-muted {
            color: #6b7280;
        }
    </style>

    <!-- Cartes de Statistiques -->
    <div class="stats-grid">
        
        <!-- Utilisateurs -->
        <div class="stat-card blue">
            <div class="stat-card-content">
                <h4>Utilisateurs Totaux</h4>
                <div class="stat-card-value">{{ $stats['totalUsers'] ?? 0 }}</div>
                <div class="stat-card-subtitle">
                    <i class="fas fa-check-circle" style="color: #10b981;"></i> {{ $stats['activeUsers'] ?? 0 }} actifs
                </div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <!-- Ventes -->
        <div class="stat-card green">
            <div class="stat-card-content">
                <h4>Ventes Totales</h4>
                <div class="stat-card-value">{{ $stats['totalVentes'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Ce mois : {{ $stats['ventesThisMonth'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>

        <!-- Montants -->
        <div class="stat-card purple">
            <div class="stat-card-content">
                <h4>Montant Total</h4>
                <div class="stat-card-value">{{ number_format($stats['totalMontant'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Ce mois : {{ number_format($stats['montantThisMonth'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Factures -->
        <div class="stat-card orange">
            <div class="stat-card-content">
                <h4>Factures Générées</h4>
                <div class="stat-card-value">{{ $stats['totalFactures'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Ce mois : {{ $stats['facturesThisMonth'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- Graphiques -->
    <div class="charts-grid">
        <!-- Ventes par mois -->
        <div class="chart-card">
            <h3><i class="fas fa-chart-bar"></i> Ventes par Mois</h3>
            <canvas id="ventesChart"></canvas>
        </div>

        <!-- Top Vendeurs -->
        <div class="chart-card">
            <h3><i class="fas fa-star"></i> Top Vendeurs</h3>
            <div class="top-vendeurs-list">
                @forelse($stats['topVendeurs'] ?? [] as $vendeur)
                    <div class="vendeur-item">
                        <div class="vendeur-info">
                            <h5>{{ $vendeur->creator->name ?? 'N/A' }}</h5>
                            <p>{{ $vendeur->total_ventes ?? 0 }} ventes</p>
                        </div>
                        <div class="vendeur-montant">{{ number_format($vendeur->montant_total ?? 0, 0, ',', ' ') }} FCFA</div>
                    </div>
                @empty
                    <p style="text-align: center; color: #6b7280; padding: 16px 0;">Aucun vendeur</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Ventes récentes -->
    <div class="table-container">
        <h3><i class="fas fa-history"></i> Ventes Récentes</h3>
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recentVentes'] ?? [] as $vente)
                    <tr>
                        <td>#{{ $vente->id }}</td>
                        <td>{{ $vente->client->name ?? 'N/A' }}</td>
                        <td>{{ $vente->creator->name ?? 'N/A' }}</td>
                        <td class="text-bold">{{ number_format($vente->montantTotal, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge badge-info">
                              {{ $vente->status}}
                            </span>
                        </td>
                        <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6b7280; padding: 24px;">Aucune vente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Graphique Ventes par Mois
        const ventesCtx = document.getElementById('ventesChart').getContext('2d');
        new Chart(ventesCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($stats['ventesParMois']['labels'] ?? []) !!},
                datasets: [{
                    label: 'Nombre de Ventes',
                    data: {!! json_encode($stats['ventesParMois']['data'] ?? []) !!},
                    backgroundColor: 'rgba(124, 58, 237, 0.5)',
                    borderColor: 'rgba(124, 58, 237, 1)',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    }
                }
            }
        });
    </script>
@endif

<!-- DASHBOARD MANAGER -->
@if($userRole === 'manager')
    <style>
        /* ========== CARTES DE STATISTIQUES ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            border-left: 4px solid #3b82f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card.blue { border-left-color: #3b82f6; }
        .stat-card.green { border-left-color: #10b981; }
        .stat-card.purple { border-left-color: #a855f7; }
        .stat-card.orange { border-left-color: #f97316; }

        .stat-card-content h4 {
            color: #4b5563;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: bold;
            margin: 12px 0;
        }

        .stat-card.blue .stat-card-value { color: #2563eb; }
        .stat-card.green .stat-card-value { color: #059669; }
        .stat-card.purple .stat-card-value { color: #9333ea; }
        .stat-card.orange .stat-card-value { color: #ea580c; }

        .stat-card-subtitle {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 8px;
        }

        .stat-card-icon {
            font-size: 32px;
          
        }

        .stat-card.blue .stat-card-icon { color: #3b82f6; }
        .stat-card.green .stat-card-icon { color: #10b981; }
        .stat-card.purple .stat-card-icon { color: #a855f7; }
        .stat-card.orange .stat-card-icon { color: #f97316; }

        /* ========== TABLE ========== */
        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            overflow-x: auto;
        }

        .table-container h3 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-livree { background-color: #d1fae5; color: #065f46; }
        .status-en_cours { background-color: #dbeafe; color: #1e40af; }
        .status-default { background-color: #f3f4f6; color: #374151; }

        .text-bold { font-weight: 600; }
    </style>

    <!-- Cartes de Statistiques -->
    <div class="stats-grid">
        <!-- Ventes Ce Mois -->
        <div class="stat-card green">
            <div class="stat-card-content">
                <h4>Ventes Ce Mois</h4>
                <div class="stat-card-value">{{ $stats['ventesThisMonth'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Total : {{ $stats['totalVentes'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>

        <!-- Montant Ce Mois -->
        <div class="stat-card purple">
            <div class="stat-card-content">
                <h4>Montant Ce Mois</h4>
                <div class="stat-card-value">{{ number_format($stats['montantThisMonth'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Total : {{ number_format($stats['totalMontant'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Factures En Attente -->
        <div class="stat-card orange">
            <div class="stat-card-content">
                <h4>Factures En Attente</h4>
                <div class="stat-card-value">{{ $stats['facturesEnAttente'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Générées : {{ $stats['totalFactures'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>

        <!-- Vendeurs Actifs -->
        <div class="stat-card blue">
            <div class="stat-card-content">
                <h4>Vendeurs Actifs</h4>
                <div class="stat-card-value">{{ $stats['totalVendeurs'] ?? 0 }}</div>
                <div class="stat-card-subtitle">En activité</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-user-tie"></i>
            </div>
        </div>
    </div>

    <!-- Ventes récentes -->
    <div class="table-container">
        <h3><i class="fas fa-history"></i> Ventes Récentes</h3>
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Client</th>
                    <th>Vendeur</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recentVentes'] ?? [] as $vente)
                    <tr>
                        <td>#{{ $vente->id }}</td>
                        <td>{{ $vente->client->name ?? 'N/A' }}</td>
                        <td>{{ $vente->creator->name ?? 'N/A' }}</td>
                        <td class="text-bold">{{ number_format($vente->montantTotal, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="badge badge-info" >
                               {{ $vente->status }}
                            </span>
                        </td>
                        <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: #6b7280; padding: 24px;">Aucune vente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<!-- DASHBOARD VENDEUR -->
@if($userRole === 'vendeur')
    <style>
        /* ========== CARTES DE STATISTIQUES ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            border-left: 4px solid #3b82f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card.green { border-left-color: #10b981; }
        .stat-card.purple { border-left-color: #a855f7; }
        .stat-card.orange { border-left-color: #f97316; }

        .stat-card-content h4 {
            color: #4b5563;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: bold;
            margin: 12px 0;
        }

        .stat-card.green .stat-card-value { color: #059669; }
        .stat-card.purple .stat-card-value { color: #9333ea; }
        .stat-card.orange .stat-card-value { color: #ea580c; }

        .stat-card-subtitle {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 8px;
        }

        .stat-card-icon {
            font-size: 32px;
        }

        .stat-card.green .stat-card-icon { color: #10b981; }
        .stat-card.purple .stat-card-icon { color: #a855f7; }
        .stat-card.orange .stat-card-icon { color: #f97316; }

        /* ========== TABLE ========== */
        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            overflow-x: auto;
        }

        .table-container h3 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-livree { background-color: #d1fae5; color: #065f46; }
        .status-en_cours { background-color: #dbeafe; color: #1e40af; }
        .status-default { background-color: #f3f4f6; color: #374151; }

        .text-bold { font-weight: 600; }
    </style>

    <!-- Cartes de Statistiques -->
    <div class="stats-grid">
        <!-- Mes Ventes -->
        <div class="stat-card green">
            <div class="stat-card-content">
                <h4>Mes Ventes</h4>
                <div class="stat-card-value">{{ $stats['mesVentes'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Ce mois : {{ $stats['mesVentesThisMonth'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>

        <!-- Mon Montant -->
        <div class="stat-card purple">
            <div class="stat-card-content">
                <h4>Mon Montant</h4>
                <div class="stat-card-value">{{ number_format($stats['monMontantTotal'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Ce mois : {{ number_format($stats['monMontantThisMonth'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Mes Factures -->
        <div class="stat-card orange">
            <div class="stat-card-content">
                <h4>Mes Factures</h4>
                <div class="stat-card-value">{{ $stats['mesFactures'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Générées</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- Mes ventes récentes -->
    <div class="table-container">
        <h3><i class="fas fa-list"></i> Mes Ventes Récentes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['mesVentesRecentes'] ?? [] as $vente)
                    <tr>
                        <td>#{{ $vente->id }}</td>
                        <td>{{ $vente->client->name ?? 'N/A' }}</td>
                        <td class="text-bold">{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="status-badge 
                                @if($vente->statut == 'livrer')
                                    status-livree
                                @elseif($vente->statut == 'en_cours')
                                    status-en_cours
                                @else
                                    status-default
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $vente->statut)) }}
                            </span>
                        </td>
                        <td>{{ $vente->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; padding: 24px;">Aucune vente</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<!-- DASHBOARD COMPTABLE -->
@if($userRole === 'comptable')
    <style>
            /* ========== CARTES DE STATISTIQUES ========== */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 24px;
                margin-bottom: 32px;
            }

            .stat-card {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                padding: 24px;
                border-left: 4px solid #3b82f6;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .stat-card.orange { border-left-color: #f97316; }
            .stat-card.purple { border-left-color: #a855f7; }
            .stat-card.red { border-left-color: #ef4444; }

            .stat-card-content h4 {
                color: #4b5563;
                font-size: 14px;
                font-weight: 600;
                margin: 0 0 12px 0;
            }

            .stat-card-value {
                font-size: 32px;
                font-weight: bold;
                margin: 12px 0;
            }

            .stat-card.orange .stat-card-value { color: #ea580c; }
            .stat-card.purple .stat-card-value { color: #9333ea; }
            .stat-card.red .stat-card-value { color: #dc2626; }

            .stat-card-subtitle {
                color: #9ca3af;
                font-size: 12px;
                margin-top: 8px;
            }

            .stat-card-icon {
                font-size: 32px;
                opacity: 0.2;
            }

            .stat-card.orange .stat-card-icon { color: #f97316; }
            .stat-card.purple .stat-card-icon { color: #a855f7; }
            .stat-card.red .stat-card-icon { color: #ef4444; }

            /* ========== TABLE ========== */
            .table-container {
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                padding: 24px;
                overflow-x: auto;
            }

            .table-container h3 {
                font-size: 18px;
                font-weight: bold;
                color: #1f2937;
                margin: 0 0 16px 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table thead {
                background-color: #f3f4f6;
                border-bottom: 1px solid #e5e7eb;
            }

            table th {
                padding: 12px 16px;
                text-align: left;
                font-size: 14px;
                font-weight: 600;
                color: #374151;
            }

            table td {
                padding: 12px 16px;
                font-size: 14px;
                border-bottom: 1px solid #f3f4f6;
            }

            table tbody tr:hover {
                background-color: #f9fafb;
            }

            .text-bold { font-weight: 600; }
    </style>

    <!-- Cartes de Statistiques -->
    <div class="stats-grid">
        <!-- Factures Générées -->
        <div class="stat-card orange">
            <div class="stat-card-content">
                <h4>Factures Générées</h4>
                <div class="stat-card-value">{{ $stats['totalFactures'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Ce mois : {{ $stats['facturesThisMonth'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>

        <!-- Montant Total -->
        <div class="stat-card purple">
            <div class="stat-card-content">
                <h4>Montant Total</h4>
                <div class="stat-card-value">{{ number_format($stats['montantTotal'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Ce mois : {{ number_format($stats['montantThisMonth'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- TVA Ce Mois -->
        <div class="stat-card red">
            <div class="stat-card-content">
                <h4>TVA Ce Mois</h4>
                <div class="stat-card-value">{{ number_format($stats['tvaThisMonth'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Année : {{ number_format($stats['tvaThisYear'] ?? 0, 0, ',', ' ') }} FCFA</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-percent"></i>
            </div>
        </div>
    </div>

    <!-- Factures récentes -->
    <div class="table-container">
        <h3><i class="fas fa-file-invoice"></i> Factures Récentes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Client</th>
                    <th>Montant</th>
                    <th>TVA</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['recentFactures'] ?? [] as $facture)
                    <tr>
                        <td>#{{ $facture->id }}</td>
                        <td>{{ $facture->client->name ?? 'N/A' }}</td>
                        <td class="text-bold">{{ number_format($facture->montant_total, 0, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($facture->montant_tva, 0, ',', ' ') }} FCFA</td>
                        <td>{{ $facture->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; padding: 24px;">Aucune facture</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<!-- DASHBOARD CLIENT -->
@if($userRole === 'client')
    
    <style>
        /* ========== CARTES DE STATISTIQUES ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }

        .stat-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            border-left: 4px solid #3b82f6;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .stat-card.green { border-left-color: #10b981; }
        .stat-card.purple { border-left-color: #a855f7; }
        .stat-card.orange { border-left-color: #f97316; }

        .stat-card-content h4 {
            color: #4b5563;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 12px 0;
        }

        .stat-card-value {
            font-size: 32px;
            font-weight: bold;
            margin: 12px 0;
        }

        .stat-card.green .stat-card-value { color: #059669; }
        .stat-card.purple .stat-card-value { color: #9333ea; }
        .stat-card.orange .stat-card-value { color: #ea580c; }

        .stat-card-subtitle {
            color: #9ca3af;
            font-size: 12px;
            margin-top: 8px;
        }

        .stat-card-icon {
            font-size: 32px;
            opacity: 0.2;
        }

        .stat-card.green .stat-card-icon { color: #10b981; }
        .stat-card.purple .stat-card-icon { color: #a855f7; }
        .stat-card.orange .stat-card-icon { color: #f97316; }

        /* ========== TABLE ========== */
        .table-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            padding: 24px;
            overflow-x: auto;
        }

        .table-container h3 {
            font-size: 18px;
            font-weight: bold;
            color: #1f2937;
            margin: 0 0 16px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table thead {
            background-color: #f3f4f6;
            border-bottom: 1px solid #e5e7eb;
        }

        table th {
            padding: 12px 16px;
            text-align: left;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
        }

        table td {
            padding: 12px 16px;
            font-size: 14px;
            border-bottom: 1px solid #f3f4f6;
        }

        table tbody tr:hover {
            background-color: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-livree { background-color: #d1fae5; color: #065f46; }
        .status-en_cours { background-color: #dbeafe; color: #1e40af; }
        .status-default { background-color: #f3f4f6; color: #374151; }

        .facture-status {
            font-weight: 600;
        }

        .facture-available { color: #059669; }
        .facture-unavailable { color: #6b7280; }

        .text-bold { font-weight: 600; }
    </style>

    <!-- Cartes de Statistiques -->
    <div class="stats-grid">
        <!-- Mes Commandes -->
        <div class="stat-card green">
            <div class="stat-card-content">
                <h4>Mes Commandes</h4>
                <div class="stat-card-value">{{ $stats['mesCommandes'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Livrées : {{ $stats['mesCommandesLivrees'] ?? 0 }}</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
        </div>

        <!-- Montant Total -->
        <div class="stat-card purple">
            <div class="stat-card-content">
                <h4>Montant Total</h4>
                <div class="stat-card-value">{{ number_format($stats['monMontantTotal'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-card-subtitle">Dépensé</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
        </div>

        <!-- Mes Factures -->
        <div class="stat-card orange">
            <div class="stat-card-content">
                <h4>Mes Factures</h4>
                <div class="stat-card-value">{{ $stats['mesFactures'] ?? 0 }}</div>
                <div class="stat-card-subtitle">Disponibles</div>
            </div>
            <div class="stat-card-icon">
                <i class="fas fa-file-invoice"></i>
            </div>
        </div>
    </div>

    <!-- Mes commandes récentes -->
    <div class="table-container">
        <h3><i class="fas fa-box"></i> Mes Commandes</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Facture</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stats['mesCommandesRecentes'] ?? [] as $commande)
                    <tr>
                        <td>#{{ $commande->id }}</td>
                        <td class="text-bold">{{ number_format($commande->montant_total, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <span class="status-badge 
                                @if($commande->statut == 'livree')
                                    status-livree
                                @elseif($commande->statut == 'en_cours')
                                    status-en_cours
                                @else
                                    status-default
                                @endif
                            ">
                                {{ ucfirst(str_replace('_', ' ', $commande->statut)) }}
                            </span>
                        </td>
                        <td>
                            <span class="facture-status 
                                @if($commande->lien_facture)
                                    facture-available
                                @else
                                    facture-unavailable
                                @endif
                            ">
                                @if($commande->lien_facture)
                                    ✅ Disponible
                                @else
                                    ❌ Non générée
                                @endif
                            </span>
                        </td>
                        <td>{{ $commande->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: #6b7280; padding: 24px;">Aucune commande</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

@endsection