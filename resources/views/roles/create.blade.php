@extends('layouts.app')

@section('page-title', 'Créer un Rôle')

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

input[type="text"],
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

.grid {
    background: #f3f4f6;
    padding: 15px;
    border-radius: 12px;
    max-height: 260px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
}

.grid::-webkit-scrollbar {
    width: 6px;
}

.grid::-webkit-scrollbar-thumb {
    background: #c4b5fd;
    border-radius: 10px;
}

input[type="checkbox"] {
    accent-color: #7C3AED;
    cursor: pointer;
}

label.flex {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px;
    border-radius: 10px;
    transition: all 0.2s ease;
    cursor: pointer;
}

label.flex:hover {
    background: rgba(124, 58, 237, 0.1);
    transform: translateX(3px);
}

label.flex p:first-child {
    font-weight: 600;
    font-size: 14px;
}

label.flex p:last-child {
    font-size: 12px;
    color: #6b7280;
}

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

@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr !important;
    }

    .flex.gap-4 {
        flex-direction: column;
    }
}
</style>

<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Créer un Nouveau Rôle</h2>

        <form action="{{ route('roles.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <label for="name" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-tag" style="color: #7C3AED;"></i> Nom du Rôle
                </label>
                <input type="text" id="name" name="name" value="{{ old('name') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-200"
                    placeholder="Administrateur" required>
                @error('name')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="slug" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-link" style="color: #7C3AED;"></i> Slug
                </label>
                <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    placeholder="admin" required>
                @error('slug')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="description" class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-align-left" style="color: #7C3AED;"></i> Description
                </label>
                <textarea id="description" name="description"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                    rows="3">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-4">
                    <i class="fas fa-lock" style="color: #7C3AED;"></i> Permissions
                </label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($permissions as $permission)
                        <label class="flex items-start">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                @if(in_array($permission->id, old('permissions', []))) checked @endif
                                class="w-4 h-4 text-purple-600 rounded">
                            <div class="ml-3">
                                <p class="text-gray-700 font-semibold">{{ $permission->name }}</p>
                                <p class="text-gray-500 text-sm">{{ $permission->slug }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex gap-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Créer le rôle</span>
                </button>
                <a href="{{ route('roles.index') }}"
                    class="flex-1 bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-lg text-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

@endsection