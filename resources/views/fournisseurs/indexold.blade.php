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
        --color-success: #10B981;
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
        max-width: 1400px;
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
        box-shadow: 0 4px 6px rgba(124, 58, 237, 0.2);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 12px rgba(124, 58, 237, 0.3);
    }
    
    .card {
        background-color: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-gray-200);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
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
        padding: ;
    }
    
    .stat-card {
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        text-align: center;
        border: 1px solid;
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.1) 0%, rgba(124, 58, 237, 0.05) 100%);
        border-color: rgba(124, 58, 237, 0.2);
        margin-bottom: var(--spacing-2xl);
    }
    
    .stat-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--color-gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .stat-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--color-primary);
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
        font-size: 0.9rem;
        font-weight: 700;
        color: var(--color-gray-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    td {
        padding: var(--spacing-md);
        border-bottom: 1px solid var(--color-gray-200);
    }
    
    tbody tr:hover {
        background-color: var(--color-gray-50);
    }
    
    .action-links {
        display: flex;
        gap: 0.5rem;
    }
    
    .action-link {
        padding: 0.25rem 0.75rem;
        border-radius: var(--radius-lg);
        font-size: 0.75rem;
        text-decoration: none;
        transition: all var(--transition-base);
    }
    
    .action-link.edit {
        background-color: rgba(59, 130, 246, 0.1);
        color: #3B82F6;
    }
    
    .action-link.edit:hover {
        background-color: rgba(59, 130, 246, 0.2);
    }
    
    .action-link.delete {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--color-danger);
    }
    
    .action-link.delete:hover {
        background-color: rgba(239, 68, 68, 0.2);
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
</style>
 
<div class="container" style="padding-top: var(--spacing-2xl);">
    <div class="header-section">
        <div>
            <h1 class="page-title">Fournisseurs</h1>
        </div>
        @can('create-fournisseur')
         <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">+ Nouveau Fournisseur</a>
        @endcan
    </div>
 
    <!-- STATISTIQUES -->
    <div class="stat-card">
        <div class="stat-label">Total Fournisseurs</div>
        <div class="stat-value">{{ $totalFournisseurs ?? 0 }}</div>
    </div>
 
    <!-- TABLEAU DES FOURNISSEURS -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Liste des Fournisseurs</h2>
        </div>
        <div class="card-body">
            @if($fournisseurs && count($fournisseurs) > 0)
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Adresse</th>
                                <th>Ville</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fournisseurs as $fournisseur)
                            <tr>
                                <td><strong>{{ $fournisseur->name }}</strong></td>
                                <td>{{ $fournisseur->email ?? 'N/A' }}</td>
                                <td>{{ $fournisseur->phone ?? 'N/A' }}</td>
                                <td>{{ $fournisseur->address ?? 'N/A' }}</td>
                                <td>{{ $fournisseur->city ?? 'Inconnu' }}</td>
                                <td>
                                    <div class="action-links">
                                        @can('edit-fournisseur')
                                            <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="action-link edit">Éditer</a>
                                        @endcan
                                        
                                        @can('delete-fournisseur')
                                            <form action="{{ route('fournisseurs.delete', $fournisseur->id) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-link delete" onclick="return confirm('Êtes-vous sûr ?')">Supprimer</button>
                                            </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">🏢</div>
                    <p>Aucun fournisseur enregistré</p>
                    <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary" style="margin-top: var(--spacing-lg);">
                        Créer un Fournisseur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
 
@endsection