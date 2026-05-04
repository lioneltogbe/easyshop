@extends('layouts.app')

@section('page-title', 'Détails du Rôle')

@section('content')
<div class="container mx-auto max-w-3xl">
    <div class="bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">{{ $role->name }}</h2>
            <div class="flex gap-2">
                @can('manage-roles')
                    <a href="{{ route('roles.edit', $role->id) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200">
                        <i class="fas fa-edit"></i>
                        <span>Modifier</span>
                    </a>
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg flex items-center gap-2 transition duration-200">
                            <i class="fas fa-trash"></i>
                            <span>Supprimer</span>
                        </button>
                    </form>
                @endcan
            </div>
        </div>

        <!-- Info rôle -->
        <div class="mb-8">
            <p class="text-gray-600 text-sm">Slug</p>
            <p class="text-gray-800 font-semibold mb-4"><code class="bg-gray-100 px-2 py-1 rounded">{{ $role->slug }}</code></p>
            
            <p class="text-gray-600 text-sm">Description</p>
            <p class="text-gray-800 font-semibold">{{ $role->description ?? '-' }}</p>
        </div>

        <!-- Permissions -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-lock"></i> Permissions ({{ $role->permissions->count() }})
            </h3>
            <div class="grid grid-cols-2 gap-3">
                @forelse($role->permissions as $permission)
                    <div class="bg-blue-50 border-l-4 border-blue-500 p-3">
                        <p class="text-blue-800 font-semibold text-sm">{{ $permission->name }}</p>
                        <p class="text-blue-600 text-xs">{{ $permission->slug }}</p>
                    </div>
                @empty
                    <p class="text-gray-500 col-span-2">Aucune permission assignée</p>
                @endforelse
            </div>
        </div>

        <!-- Utilisateurs avec ce rôle -->
        <div class="mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-users"></i> Utilisateurs ({{ $role->users->count() }})
            </h3>
            <div class="space-y-2">
                @forelse($role->users as $user)
                    <div class="bg-green-50 border-l-4 border-green-500 p-3 flex justify-between items-center">
                        <div>
                            <p class="text-green-800 font-semibold text-sm">{{ $user->name }}</p>
                            <p class="text-green-600 text-xs">{{ $user->email }}</p>
                        </div>
                        <a href="{{ route('users.show', $user->id) }}" class="text-green-600 hover:text-green-800">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500">Aucun utilisateur avec ce rôle</p>
                @endforelse
            </div>
        </div>

        <!-- Bouton retour -->
        <a href="{{ route('roles.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">
            ← Retour à la liste
        </a>
    </div>
</div>
@endsection

