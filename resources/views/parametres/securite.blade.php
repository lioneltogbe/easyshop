@extends('layouts.app')

@section('page-title', 'Sécurité')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    .settings-layout {
        display: grid;
        grid-template-columns: 220px 1fr;
        gap: 2rem;
        max-width: 1280px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .settings-sidebar {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .settings-nav {
        padding: 0.9rem 1rem;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
        color: #6B7280;
        font-weight: 600;
        border-left: 3px solid transparent;
        border-radius: 0.75rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .settings-nav:hover {
        color: #7C3AED;
        background-color: #F3F4F6;
    }

    .settings-nav.active {
        color: #7C3AED;
        background-color: #F3E8FF;
        border-left-color: #7C3AED;
    }

    .settings-content {
        background: white;
        border-radius: 1rem;
        padding: 2rem;
        box-shadow: 0 15px 40px rgba(15, 23, 42, 0.08);
        border: 1px solid #E5E7EB;
    }

    .settings-section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .settings-description {
        color: #4B5563;
        margin-bottom: 2rem;
        line-height: 1.75;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
        font-size: 0.94rem;
    }

    .form-input {
        width: 100%;
        padding: 0.85rem 1rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.75rem;
        background: #FFFFFF;
        font-size: 0.95rem;
    }

    .form-input:focus {
        outline: none;
        border-color: #7C3AED;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.12);
    }

    .form-help {
        font-size: 0.82rem;
        color: #6B7280;
        margin-top: 0.45rem;
    }

    .button-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.75rem;
    }

    .btn {
        padding: 0.9rem 1.5rem;
        border: none;
        border-radius: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .btn-primary {
        background: linear-gradient(135deg, #7C3AED 0%, #A78BFA 100%);
        color: white;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 18px 40px rgba(124, 58, 237, 0.18);
    }

    .btn-secondary {
        background: #F3F4F6;
        color: #111827;
        border: 1px solid #E5E7EB;
    }

    .btn-secondary:hover {
        background: #E5E7EB;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #7C3AED;
        text-decoration: none;
        font-weight: 700;
        margin-bottom: 1.5rem;
    }

    .back-link:hover {
        color: #5B21B6;
    }

    @media (max-width: 768px) {
        .settings-layout {
            grid-template-columns: 1fr;
        }

        .settings-sidebar {
            position: static;
            max-height: none;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1280px; margin: 0 auto; padding: 2rem 0;">
    <a href="{{ route('parametres.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Retour aux paramètres
    </a>

    <div class="settings-layout">
        <!-- <div class="settings-sidebar">
            <a href="{{ route('parametres.index') }}" class="settings-nav">🏠 Général</a>
            <a href="{{ route('parametres.profil') }}" class="settings-nav">👤 Profil</a>
            <a href="{{ route('parametres.securite') }}" class="settings-nav active">🔐 Sécurité</a>
            <a href="{{ route('parametres.notifications') }}" class="settings-nav">🔔 Notifications</a>
            <a href="{{ route('parametres.entreprise') }}" class="settings-nav">🏢 Entreprise</a>
        </div> -->

        

      
    </div>

    <div class="settings-content" style="margin-bottom: 2rem;" >
            <div class="settings-section"  >
                <div class="settings-section-title">
                    <i class="fas fa-lock"></i>
                    Sécurité du compte
                </div>
                <p class="settings-description">
                    Changez votre mot de passe en toute sécurité. Assurez-vous d'utiliser un mot de passe unique et difficile à deviner.
                </p>

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="current_password">Mot de passe actuel</label>
                        <input id="current_password" type="password" name="current_password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Nouveau mot de passe</label>
                        <input id="password" type="password" name="password" class="form-input" placeholder="••••••••" required>
                        <p class="form-help">Minimum 8 caractères, avec une majuscule et un chiffre.</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirmez le mot de passe</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-shield-alt"></i>
                            Mettre à jour le mot de passe
                        </button>
                        <a href="{{ route('parametres.profil') }}" class="btn btn-secondary">Retour au profil</a>
                    </div>
                </form>
            </div>

            
        </div>

      <div class="settings-content">
            <div class="settings-section">
                <div class="settings-section-title">
                    <i class="fas fa-user-lock"></i>
                    Vérification et sécurité
                </div>
                <p class="settings-description">
                    Pour renforcer la protection de votre compte, activez l’authentification à deux facteurs et vérifiez que vos informations de récupération sont à jour.
                </p>

                <div class="form-group">
                    <label class="form-label">Méthode de connexion</label>
                    <input type="text" class="form-input" value="Email + mot de passe" readonly>
                </div>

                <div class="form-group">
                    <label class="form-label">Dernière connexion</label>
                    <input type="text" class="form-input" value="{{ optional(auth()->user()->last_login_at)->format('d/m/Y H:i') ?? 'Aucune donnée' }}" readonly>
                </div>
            </div>
        </div>
</div>
@endsection