@extends('layouts.app')

@section('page-title', 'Rôles et Permissions')

@section('content')
<div class="container mx-auto">
    <!-- En-tête avec bouton créer -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1 style="font-size: 2rem; font-weight: 700;">Gestion des Rôles</h1>
        @can('manage-roles')
            <a href="{{ route('roles.create') }}" class="btn btn-primry" style="text-decoration:none;">
                <span> ➕ Nouveau Rôle</span>
            </a>
        @endcan
    </div>

    <!-- Tableau des rôles -->
    
    <div style="background: white; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <table class="table">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th>Nom</th>
                    <th >Slug</th>
                    <th>Description</th>
                    <th>Permissions</th>
                    <th >Utilisateurs</th>
                    <th >Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr class="border-b hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 text-sm font-semibold text-gray-800">{{ $role->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $role->slug }}</code>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $role->description ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $role->permissions->count() }} permissions
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $role->users->count() }} utilisateurs
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex justify-center gap-2">
                                @can('manage-roles')  
                                    <a href="{{ route('roles.show', $role->id) }}" class="text-blue-600 hover:text-blue-800 transition duration-200" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                  
                                    <a href="{{ route('roles.edit', $role->id) }}" class="btn-sm text-yellow-600 hover:text-yellow-800 transition duration-200" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm text-red-600 hover:text-red-800 transition duration-200" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p>Aucun rôle trouvé</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
   

    <!-- Pagination -->
    <div class="mt-6">
        {{ $roles->links() }}
    </div>
</div>
@endsection

