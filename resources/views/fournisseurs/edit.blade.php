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
        --spacing-md: 1rem;
        --spacing-lg: 1.5rem;
        --spacing-xl: 2rem;
        --spacing-2xl: 3rem;
        --radius-lg: 0.75rem;
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
        max-width: 900px;
        margin: 0 auto;
        padding: 0 var(--spacing-md);
    }
    
    .header-section {
        margin-bottom: var(--spacing-2xl);
    }
    
    .page-title {
        font-size: 2rem;
        font-weight: 700;
        color: var(--color-gray-900);
        margin-bottom: 0.5rem;
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
    
    .card {
        background-color: white;
        border-radius: var(--radius-lg);
        border: 1px solid var(--color-gray-200);
        box-shadow: var(--shadow-md);
        overflow: hidden;
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
    }
    
    .card-body {
        padding: var(--spacing-xl);
    }
    
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }
    
    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .form-group {
        margin-bottom: var(--spacing-lg);
    }
    
    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--color-gray-700);
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid var(--color-gray-200);
        border-radius: var(--radius-lg);
        font-size: 0.875rem;
        transition: all var(--transition-base);
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--color-primary);
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }
    
    .form-control.is-invalid {
        border-color: var(--color-danger);
    }
    
    .form-error {
        color: var(--color-danger);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }
    
    .form-actions {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-2xl);
    }
    
    .btn {
        flex: 1;
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
    
    .alert {
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        margin-bottom: var(--spacing-lg);
    }
    
    .alert-danger {
        background-color: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: var(--color-danger);
    }
</style>
 
<div class="container" style="padding-top: var(--spacing-2xl);">
    <div class="header-section">
        <a href="{{ route('fournisseurs.index') }}" class="breadcrumb">← Retour aux fournisseurs</a>
        <h1 class="page-title">Éditer le Fournisseur</h1>
    </div>
 
    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Erreur !</strong>
        <ul style="margin-top: 0.5rem; margin-left: 1.5rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
 
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informations du Fournisseur</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('fournisseurs.update', $fournisseur->id) }}" method="POST">
                @csrf
                @method('PUT')
 
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name" class="form-label">Nom du Fournisseur *</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $fournisseur->name) }}" required>
                        @error('name')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="contact_person" class="form-label">Personne de Contact</label>
                        <input type="text" name="contact_person" id="contact_person" class="form-control" 
                               value="{{ old('contact_person', $fournisseur->contact_person) }}">
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $fournisseur->email) }}">
                        @error('email')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="tel" name="phone" id="phone" class="form-control" 
                               value="{{ old('phone', $fournisseur->phone) }}">
                    </div>
                </div>
 
                <div class="form-group">
                    <label for="address" class="form-label">Adresse</label>
                    <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $fournisseur->address) }}</textarea>
                </div>
 
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                    <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
 
@endsection