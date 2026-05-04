@extends('layouts.app')

@section('page-title', 'Créer un Utilisateur')

@section('content')

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    background: linear-gradient(135deg, #eef2ff, #f9fafb);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1f2937;
}

.container {
    padding: 50px 20px;
}

.bg-white {
    background: #ffffff;
    border-radius: 18px;
    padding: 30px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
    position: relative;
    overflow: hidden;
}

.bg-white::before {
    content: "";
    position: absolute;
    top: -50px;
    right: -50px;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, #7C3AED, transparent);
    opacity: 0.2;
}

h2 {
    font-size: 24px;
    font-weight: bold;
    margin-bottom: 25px;
    position: relative;
}

h2::after {
    content: "";
    width: 50px;
    height: 4px;
    background: #7C3AED;
    display: block;
    margin-top: 8px;
    border-radius: 10px;
}

label {
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

input,
textarea {
    width: 100%;
    padding: 12px 14px;
    border-radius: 10px;
    border: 1px solid #d1d5db;
    background: #f9fafb;
    font-size: 14px;
    transition: all 0.3s ease;
}

input:focus,
textarea:focus {
    border-color: #7C3AED;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    outline: none;
}

::placeholder {
    color: #9ca3af;
}

.text-red-600 {
    background: #fee2e2;
    color: #b91c1c;
    padding: 6px 10px;
    border-radius: 6px;
    font-size: 12px;
    margin-top: 5px;
    display: inline-block;
}

/* ROLES BOX */
.space-y-2 {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
}

/* CHECKBOX */
input[type="checkbox"] {
    accent-color: #7C3AED;
    cursor: pointer;
}

/* ROLE ITEM */
.space-y-2 label {
    display: flex;
    align-items: center;
    padding: 10px;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.space-y-2 label:hover {
    background: rgba(124, 58, 237, 0.1);
    transform: translateX(3px);
}

/* BUTTONS */
.flex.gap-4 {
    margin-top: 20px;
}

.bg-blue-600 {
    background: linear-gradient(135deg, #7C3AED, #4F46E5);
    color: #fff;
    border-radius: 10px;
    padding: 12px;
    font-weight: 600;
    box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
    transition: all 0.3s ease;
}

.bg-blue-600:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(124, 58, 237, 0.4);
}

.bg-gray-300 {
    background: #e5e7eb;
    border-radius: 10px;
    padding: 12px;
    transition: 0.3s;
}

.bg-gray-300:hover {
    background: #d1d5db;
}

i {
    margin-right: 5px;
}

/* RESPONSIVE */
@media (max-width: 768px) {
    .flex.gap-4 {
        flex-direction: column;
    }
}
</style>

<div class="container mx-auto max-w-2xl">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Créer un Nouvel Utilisateur</h2>

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name">
                    <i class="fas fa-user" style="color: #7C3AED;"></i> Nom Complet
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Jean Dupont" required>
                @error('name')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="email">
                    <i class="fas fa-envelope" style="color: #7C3AED;"></i> Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="jean@example.com" required>
                @error('email')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password">
                    <i class="fas fa-lock" style="color: #7C3AED;"></i> Mot de passe
                </label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
                @error('password')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password_confirmation">
                    <i class="fas fa-lock" style="color: #7C3AED;"></i> Confirmer le mot de passe
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
            </div>

            <div class="mb-6">
                <label for="phone">
                    <i class="fas fa-phone" style="color: #7C3AED;"></i> Téléphone
                </label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+237 652 345 678">
                @error('phone')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="address">
                    <i class="fas fa-map-marker-alt" style="color: #7C3AED;"></i> Adresse
                </label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" placeholder="Douala, Cameroun">
                @error('address')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label>
                    <i class="fas fa-shield-alt" style="color: #7C3AED;"></i> Rôles
                </label>
                <div class="space-y-2">
                    @foreach($roles as $role)
                        <label>
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                @if(in_array($role->id, old('roles', []))) checked @endif>
                            <span class="ml-3">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>
                @error('roles')
                    <p class="text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit" class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Créer l'utilisateur</span>
                </button>
                <a href="{{ route('users.index') }}" class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection