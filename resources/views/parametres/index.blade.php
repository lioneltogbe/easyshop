@extends('layouts.app')

@section('title', 'Paramètres - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <h1 style="font-size: 2rem; font-weight: 700;">Paramètres</h1>
</div>

<!-- Onglets Paramètres -->
<div class="tabs">
    <a href="{{ route('parametres.index') }}" class="tab-link active">⚙️ Accueil</a>
    <a href="{{ route('parametres.profil') }}" class="tab-link">👤 Profil</a>
    <a href="{{ route('parametres.securite') }}" class="tab-link">🔒 Sécurité</a>
    <a href="{{ route('parametres.notifications') }}" class="tab-link">🔔 Notifications</a>
    <a href="{{ route('parametres.entreprise') }}" class="tab-link">🏢 Entreprise</a>
</div>

<!-- Cartes Paramètres -->
<div class="grid grid-2">
    <div class="kpi-card" style="cursor: pointer; transition: all 0.3s ease;">
        <div style="font-size: 2rem; margin-bottom: 1rem;">👤</div>
        <h3 style="font-weight: 600; margin-bottom: 0.5rem;">Profil Utilisateur</h3>
        <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
            Gérez vos informations personnelles
        </p>
        <a href="{{ route('parametres.profil') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            Accéder →
        </a>
    </div>

    <div class="kpi-card" style="cursor: pointer; transition: all 0.3s ease;">
        <div style="font-size: 2rem; margin-bottom: 1rem;">🔒</div>
        <h3 style="font-weight: 600; margin-bottom: 0.5rem;">Sécurité</h3>
        <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
            Changez votre mot de passe et gérez la sécurité
        </p>
        <a href="{{ route('parametres.securite') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            Accéder →
        </a>
    </div>

    <div class="kpi-card" style="cursor: pointer; transition: all 0.3s ease;">
        <div style="font-size: 2rem; margin-bottom: 1rem;">🔔</div>
        <h3 style="font-weight: 600; margin-bottom: 0.5rem;">Notifications</h3>
        <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
            Configurez vos préférences de notifications
        </p>
        <a href="{{ route('parametres.notifications') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            Accéder →
        </a>
    </div>

    <div class="kpi-card" style="cursor: pointer; transition: all 0.3s ease;">
        <div style="font-size: 2rem; margin-bottom: 1rem;">🏢</div>
        <h3 style="font-weight: 600; margin-bottom: 0.5rem;">Informations Entreprise</h3>
        <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
            Gérez les informations de votre entreprise
        </p>
        <a href="{{ route('parametres.entreprise') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.875rem;">
            Accéder →
        </a>
    </div>
</div>
@endsection
