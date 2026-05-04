@extends('layouts.app')

@section('page-title', 'Mon Profil')

@section('styles')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* ===== SETTINGS LAYOUT ===== */
    .settings-layout {
        display: grid;
        grid-template-columns: 200px 1fr;
        gap: 2rem;
        max-width: 1280px;
        margin: 0 auto;
    }

    .settings-sidebar {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        position: sticky;
        top: 100px;
        max-height: calc(100vh - 120px);
        overflow-y: auto;
    }

    .settings-nav {
        padding: 0.75rem 1rem;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
        color: #6B7280;
        font-weight: 500;
        border-left: 3px solid transparent;
        transition: all 0.2s;
        border-radius: 0.5rem;
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
        border-radius: 0.75rem;
        padding: 2rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        border: 1px solid #F3F4F6;
    }

    .settings-section {
        margin-bottom: 2rem;
    }

    .settings-section:last-child {
        margin-bottom: 0;
    }

    .settings-section-title {
        font-size: 1.25rem;
        font-weight: bold;
        color: #111827;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    /* ===== PROFILE HEADER ===== */
    .profile-header {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-bottom: 2rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #E5E7EB;
    }

    .profile-avatar {
        width: 5rem;
        height: 5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, #7C3AED 0%, #06B6D4 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2rem;
        font-weight: bold;
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2);
    }

    .profile-info h2 {
        font-size: 1.5rem;
        color: #111827;
        margin-bottom: 0.25rem;
    }

    .profile-info p {
        color: #6B7280;
        font-size: 0.875rem;
    }

    /* ===== FORM GROUPS ===== */
    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group:last-child {
        margin-bottom: 0;
    }

    .form-label {
        display: block;
        font-weight: 600;
        color: #111827;
        margin-bottom: 0.5rem;
        font-size: 0.875rem;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #D1D5DB;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-family: inherit;
        background: white;
    }

    .form-input:focus,
    .form-select:focus {
        outline: none;
        border-color: #7C3AED;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
    }

    .form-help {
        font-size: 0.75rem;
        color: #6B7280;
        margin-top: 0.25rem;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    /* ===== BUTTONS ===== */
    .btn {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
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
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
        transform: translateY(-2px);
    }

    .btn-secondary {
        background-color: #F3F4F6;
        color: #111827;
        border: 1px solid #E5E7EB;
    }

    .btn-secondary:hover {
        background-color: #E5E7EB;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    .back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #7C3AED;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .back-link:hover {
        gap: 0.75rem;
        color: #6D28D9;
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 768px) {
        .settings-layout {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .settings-sidebar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.5rem;
            position: static;
            max-height: none;
        }

        .settings-nav {
            border-left: none;
            border-bottom: 3px solid transparent;
            padding: 0.75rem;
            text-align: center;
        }

        .settings-nav.active {
            border-left: none;
            border-bottom-color: #7C3AED;
            background-color: #F3E8FF;
        }

        .settings-content {
            padding: 1.5rem;
        }

        .profile-header {
            flex-direction: column;
            text-align: center;
        }

        .profile-info h2 {
            font-size: 1.25rem;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .settings-sidebar {
            grid-template-columns: repeat(2, 1fr);
        }

        .settings-content {
            padding: 1rem;
        }

        .settings-section-title {
            font-size: 1rem;
        }

        .form-label {
            font-size: 0.8rem;
        }

        .form-input,
        .form-select {
            padding: 0.6rem 0.8rem;
            font-size: 0.8rem;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('content')
<div style="max-width: 1280px; margin: 0 auto; padding: 2rem 1rem;">
    <a href="{{ route('parametres.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Retour aux paramètres
    </a>

    <div class="settings-layout">
        <!-- Sidebar Navigation -->
        <div class="settings-sidebar">
            <button class="settings-nav active" onclick="showSection('profile')">👤 Profil</button>
            <button class="settings-nav" onclick="showSection('personal')">📋 Personnel</button>
            <button class="settings-nav" onclick="showSection('security')">🔐 Sécurité</button>
            <button class="settings-nav" onclick="showSection('notifications')">🔔 Notifications</button>
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            <!-- Profile Section -->
            <div id="profile" class="settings-section" style="display: block;">
                <div class="profile-header">
                    <div class="profile-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                    <div class="profile-info">
                        <h2>{{ auth()->user()->name }}</h2>
                        <p>{{ auth()->user()->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div id="personal" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-user"></i>
                    Informations Personnelles
                </h3>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">Nom complet</label>
                            <input type="text" name="name" class="form-input" value="{{ auth()->user()->name }}" placeholder="Votre nom complet" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="{{ auth()->user()->email }}" placeholder="votre@email.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input type="tel" name="phone" class="form-input" value="{{ auth()->user()->phone ?? '' }}" placeholder="+229 97 00 00 00">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse</label>
                        <input type="text" name="address" class="form-input" value="{{ auth()->user()->address ?? '' }}" placeholder="Votre adresse">
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer les modifications
                        </button>
                        <a href="{{ route('parametres.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>

            <!-- Security Section -->
            <div id="security" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-lock"></i>
                    Sécurité & Mot de Passe
                </h3>

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="current_password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="password" class="form-input" placeholder="••••••••" required>
                        <p class="form-help">Au minimum 8 caractères avec majuscules, minuscules et chiffres</p>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-input" placeholder="••••••••" required>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-shield-alt"></i>
                            Changer le mot de passe
                        </button>
                        <a href="{{ route('parametres.index') }}" class="btn btn-secondary">Annuler</a>
                    </div>
                </form>
            </div>

            <!-- Notifications Section -->
            <div id="notifications" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-bell"></i>
                    Préférences de Notifications
                </h3>

                <form action="" method="POST">
                    @csrf

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            <span>Alertes de stock faible</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            <span>Notifications de commande</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" style="width: 18px; height: 18px; cursor: pointer;">
                            <span>Rapports financiers</span>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 12px; cursor: pointer; font-weight: 600;">
                            <input type="checkbox" checked style="width: 18px; height: 18px; cursor: pointer;">
                            <span>Alertes d'expiration des lots</span>
                        </label>
                    </div>

                    <div class="button-group">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Enregistrer les préférences
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function showSection(sectionId) {
        // Hide all sections
        document.querySelectorAll('.settings-section').forEach(section => {
            section.style.display = 'none';
        });

        // Remove active state from all nav buttons
        document.querySelectorAll('.settings-nav').forEach(btn => {
            btn.classList.remove('active');
        });

        // Show selected section
        document.getElementById(sectionId).style.display = 'block';

        // Add active state to clicked button
        event.target.classList.add('active');
    }
</script>
@endsection