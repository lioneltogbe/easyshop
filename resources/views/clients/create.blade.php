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

.page-subtitle {
    font-size: 0.875rem;
    color: var(--color-gray-600);
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

.form-label .required {
    color: var(--color-danger);
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--color-gray-200);
    border-radius: var(--radius-lg);
    font-size: 0.875rem;
    transition: all var(--transition-base);
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--color-primary);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.form-control.is-invalid {
    border-color: var(--color-danger);
}

.form-control.is-invalid:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.form-error {
    color: var(--color-danger);
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: block;
}

.form-help {
    color: var(--color-gray-600);
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

.btn-primary:active {
    transform: translateY(0);
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
    border-left: 4px solid;
}

.alert-danger {
    background-color: rgba(239, 68, 68, 0.1);
    border-left-color: var(--color-danger);
    color: var(--color-danger);
}

.alert-success {
    background-color: rgba(16, 185, 129, 0.1);
    border-left-color: var(--color-success);
    color: var(--color-success);
}

.alert ul {
    margin-top: 0.5rem;
    margin-left: 1.5rem;
}

.alert li {
    margin-bottom: 0.25rem;
}

.section-divider {
  
}

.section-divider:last-child {
    border-bottom: none;
}

.section-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--color-gray-900);
    margin-bottom: var(--spacing-lg);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: var(--color-primary);
}
</style>

<div class="container" style="padding-top: var(--spacing-2xl);">
    <div class="header-section">
        <h1 class="page-title">Nouveau Client</h1>
        <p class="page-subtitle">Ajouter un nouveau client à votre base de données</p>
    </div>

    @if($errors->any())
    <div class="alert alert-danger">
        <strong>Erreur de validation !</strong>
        <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Informations du Client</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('clients.store') }}" method="POST" id="clientForm">
                @csrf

                <!-- Section Informations Personnelles -->
                <div class="section-divider">
                    <h3 class="section-title">Informations Personnelles</h3>
                    
                    <div class="form-grid">
                        <!-- Nom du Client (Requis) -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nom du Client <span class="required">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                class="form-control @error('name') is-invalid @enderror" 
                                value="{{ old('name') }}" 
                                required
                                placeholder="Ex: Jean Dupont"
                                maxlength="255">
                            @error('name')
                            <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Email (Optionnel, Unique) -->
                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input 
                                type="email" 
                                name="email" 
                                id="email" 
                                class="form-control @error('email') is-invalid @enderror" 
                                value="{{ old('email') }}"
                                placeholder="Ex: jean@example.com">
                            @error('email')
                            <span class="form-error">{{ $message }}</span>
                            @enderror
                            <span class="form-help">L'email doit être unique dans la base de données</span>
                        </div>

                        <!-- Téléphone (Optionnel) -->
                        <div class="form-group">
                            <label for="phone" class="form-label">Téléphone</label>
                            <input 
                                type="tel" 
                                name="phone" 
                                id="phone" 
                                class="form-control @error('phone') is-invalid @enderror" 
                                value="{{ old('phone') }}"
                                placeholder="Ex: +33 6 12 34 56 78"
                                maxlength="20">
                            @error('phone')
                            <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section Adresse -->
                <div class="section-divider">
                    <h3 class="section-title">Adresse</h3>

                    <div class="form-group">
                        <label for="address" class="form-label">Adresse</label>
                        <input 
                            type="text" 
                            name="address" 
                            id="address" 
                            class="form-control @error('address') is-invalid @enderror" 
                            value="{{ old('address') }}"
                            placeholder="Ex: 123 Rue de la Paix">
                        @error('address')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-grid">
                        <!-- Ville (Optionnel) -->
                        <div class="form-group">
                            <label for="city" class="form-label">Ville</label>
                            <input 
                                type="text" 
                                name="city" 
                                id="city" 
                                class="form-control @error('city') is-invalid @enderror" 
                                value="{{ old('city') }}"
                                placeholder="Ex: Paris">
                            @error('city')
                            <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Pays (Optionnel) -->
                        <div class="form-group">
                            <label for="country" class="form-label">Pays</label>
                            <input 
                                type="text" 
                                name="country" 
                                id="country" 
                                class="form-control @error('country') is-invalid @enderror" 
                                value="{{ old('country') }}"
                                placeholder="Ex: France">
                            @error('country')
                            <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section Limite de Crédit -->
                 @ @role(['admin', 'manager','comptable'])
                <div class="section-divider">
                    <h3 class="section-title">Gestion du Crédit</h3>

                    <div class="form-group">
                        <label for="creditLimit" class="form-label">Limite de Crédit</label>
                        <input 
                            type="number" 
                            name="creditLimit" 
                            id="creditLimit" 
                            class="form-control @error('creditLimit') is-invalid @enderror" 
                            value="{{ old('creditLimit', 0) }}"
                            placeholder="0.00"
                            min="0"
                            step="0.01"
                            inputmode="decimal">
                        @error('creditLimit')
                        <span class="form-error">{{ $message }}</span>
                        @enderror
                        <span class="form-help">Montant maximum que le client peut dépenser. Le crédit actuel sera initialisé à 0.</span>
                    </div>
                </div>
                @endrole

                <!-- Boutons d'Action -->
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Créer le Client</button>
                    <a href="{{ route('clients.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Validation côté client avant soumission
    document.getElementById('clientForm').addEventListener('submit', function(e) {
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const creditLimit = parseFloat(document.getElementById('creditLimit').value) || 0;

        // Vérifier que le nom n'est pas vide
        if (!name) {
            e.preventDefault();
            alert('Le nom du client est obligatoire');
            document.getElementById('name').focus();
            return false;
        }

        // Vérifier que la limite de crédit est positive
        if (creditLimit < 0) {
            e.preventDefault();
            alert('La limite de crédit ne peut pas être négative');
            document.getElementById('creditLimit').focus();
            return false;
        }

        // Vérifier le format de l'email si rempli
        if (email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Veuillez entrer une adresse email valide');
                document.getElementById('email').focus();
                return false;
            }
        }
    });

    // Formatage automatique du champ de crédit
    document.getElementById('creditLimit').addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
</script>

@endsection
