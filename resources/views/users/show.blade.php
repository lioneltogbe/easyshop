@extends('layouts.app')

@section('page-title', 'Profil Utilisateur')

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

    .profile-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        margin-top: 8px;
    }

    .status-active {
        background: #dcfce7;
        color: #15803d;
    }

    .status-inactive {
        background: #fee2e2;
        color: #991b1b;
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

    .form-input:disabled,
    .form-select:disabled {
        background: #F9FAFB;
        color: #9ca3af;
        cursor: not-allowed;
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

    .btn-danger {
        background-color: #FEE2E2;
        color: #DC2626;
    }

    .btn-danger:hover {
        background-color: #FECACA;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
    }

    /* ===== ROLES & PERMISSIONS ===== */
    .roles-container {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, rgba(147, 51, 234, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
        border: 1px solid #c4b5fd;
        color: #7c3aed;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 600;
    }

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1rem;
    }

    .permission-item {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%);
        border: 1px solid #dbeafe;
        border-left: 4px solid #3b82f6;
        border-radius: 0.5rem;
        padding: 1rem;
        transition: all 0.3s ease;
    }

    .permission-item:hover {
        border-left-color: #7c3aed;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12) 0%, rgba(147, 51, 234, 0.12) 100%);
        box-shadow: 0 2px 8px rgba(124, 58, 237, 0.1);
    }

    .permission-name {
        font-size: 0.875rem;
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 0.25rem;
    }

    .permission-slug {
        font-size: 0.75rem;
        color: #3b82f6;
        font-family: monospace;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        padding: 1rem;
        background: #F9FAFB;
        border-radius: 0.5rem;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .info-value {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
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

        .permissions-grid {
            grid-template-columns: 1fr;
        }

        .info-grid {
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
    <a href="{{ route('users.index') }}" class="back-link">
        <i class="fas fa-arrow-left"></i>
        Retour à la liste
    </a>

    <div class="settings-layout">
        <!-- Sidebar Navigation -->
        <div class="settings-sidebar">
            <button class="settings-nav active" onclick="showSection('profile')">👤 Profil</button>
            <button class="settings-nav" onclick="showSection('infos')">📋 Infos</button>
            <button class="settings-nav" onclick="showSection('roles')">👥 Rôles</button>
            <button class="settings-nav" onclick="showSection('permissions')">🔐 Permissions</button>
            @can('edit-user')
                <button class="settings-nav" onclick="showSection('actions')">⚙️ Actions</button>
            @endcan
        </div>

        <!-- Main Content -->
        <div class="settings-content">
            <!-- Profile Section -->
            <div id="profile" class="settings-section" style="display: block;">
                <div class="profile-header">
                    <div class="profile-avatar">{{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}</div>
                    <div class="profile-info">
                        <h2>{{ $user->name }}</h2>
                        <p>{{ $user->email }}</p>
                        @if($user->is_active)
                            <div class="profile-status status-active">
                                <i class="fas fa-check-circle"></i>
                                Compte actif
                            </div>
                        @else
                            <div class="profile-status status-inactive">
                                <i class="fas fa-times-circle"></i>
                                Compte inactif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Information Section -->
            <div id="infos" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-information-circle"></i>
                    Détails de l'Utilisateur
                </h3>

                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">📧 Email</span>
                        <span class="info-value">{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📱 Téléphone</span>
                        <span class="info-value">{{ $user->phone ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📍 Adresse</span>
                        <span class="info-value">{{ $user->address ?? '—' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">📅 Inscrit depuis</span>
                        <span class="info-value">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">🔄 Mis à jour</span>
                        <span class="info-value">{{ $user->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">⚡ Statut</span>
                        <span class="info-value">
                            @if($user->is_active)
                                <span style="color: #15803d;">✓ Actif</span>
                            @else
                                <span style="color: #991b1b;">✗ Inactif</span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>

            <!-- Roles Section -->
            <div id="roles" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-shield-alt"></i>
                    Rôles Assignés
                </h3>

                <div class="roles-container">
                    @forelse($user->roles as $role)
                        <span class="role-badge">
                            <i class="fas fa-tag"></i>
                            {{ $role->name }}
                        </span>
                    @empty
                        <p style="color: #9ca3af; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i>
                            Aucun rôle assigné à cet utilisateur
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Permissions Section -->
            <div id="permissions" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-lock"></i>
                    Permissions Efficaces
                </h3>

                <div class="permissions-grid">
                    @php
                        $permissions = $user->roles()
                            ->with('permissions')
                            ->get()
                            ->pluck('permissions')
                            ->flatten()
                            ->unique('id');
                    @endphp
                    @forelse($permissions as $permission)
                        <div class="permission-item">
                            <div class="permission-name">{{ $permission->name }}</div>
                            <div class="permission-slug">{{ $permission->slug }}</div>
                        </div>
                    @empty
                        <p style="color: #9ca3af; grid-column: 1 / -1; font-size: 0.875rem;">
                            <i class="fas fa-info-circle"></i>
                            Aucune permission assignée
                        </p>
                    @endforelse
                </div>
            </div>

            <!-- Actions Section -->
            @can('edit-user')
            <div id="actions" class="settings-section" style="display: none;">
                <h3 class="settings-section-title">
                    <i class="fas fa-cog"></i>
                    Actions
                </h3>

                <div class="form-group">
                    <label class="form-label">Modifier l'utilisateur</label>
                    <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
                        Accédez à la page de modification pour éditer les informations de cet utilisateur.
                    </p>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Modifier l'utilisateur
                    </a>
                </div>

                @can('delete-user')
                    @if($user->id !== auth()->user()->id)
                    <div class="form-group">
                        <h3 class="settings-section-title" style="color: #DC2626;">
                            <i class="fas fa-exclamation-triangle"></i>
                            Zone Dangereuse
                        </h3>
                        <p style="color: #6B7280; font-size: 0.875rem; margin-bottom: 1rem;">
                            Cette action est irréversible. L'utilisateur et tous ses enregistrements seront supprimés de façon permanente.
                        </p>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                                Supprimer Définitivement
                            </button>
                        </form>
                    </div>
                    @endif
                @endcan
            </div>
            @endcan
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

