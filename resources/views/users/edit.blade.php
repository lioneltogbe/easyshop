
@extends('layouts.app')

@section('page-title', 'Modifier l\'Utilisateur')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <div class="card" style="max-width: 900px; margin: 0 auto;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 0.5rem;">Modifier {{ $user->name }}</h2>
                <p style="margin: 0; color: var(--gray-600);">Mettez à jour les informations du profil et les rôles de l'utilisateur.</p>
            </div>
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary" style="white-space: nowrap;">← Retour au profil</a>
        </div>

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="name" class="form-label">Nom complet</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $user->name) }}"
                        class="form-input"
                        required
                    >
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $user->email) }}"
                        class="form-input"
                        required
                    >
                    @error('email')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="phone" class="form-label">Téléphone</label>
                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="{{ old('phone', $user->phone) }}"
                        class="form-input"
                    >
                    @error('phone')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">Adresse</label>
                    <input
                        type="text"
                        id="address"
                        name="address"
                        value="{{ old('address', $user->address) }}"
                        class="form-input"
                    >
                    @error('address')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Statut</label>
                <label style="display: inline-flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input
                        type="checkbox"
                        name="is_active"
                        value="1"
                        @if(old('is_active', $user->is_active)) checked @endif
                        style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);"
                    >
                    <span style="font-weight: 600; color: var(--gray-800);">Utilisateur actif</span>
                </label>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Rôles</label>
                <div class="grid grid-2" style="gap: 1rem;">
                    @foreach($roles as $role)
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                            <input
                                type="checkbox"
                                name="roles[]"
                                value="{{ $role->id }}"
                                @if($user->roles->contains($role->id)) checked @endif
                                style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary);"
                            >
                            <span style="color: var(--gray-800);">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 180px;">Enregistrer</button>
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary" style="flex: 1; min-width: 180px; text-align: center;">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection

