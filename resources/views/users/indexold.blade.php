@extends('layouts.app')

@section('page-title', 'Utilisateurs')

@section('content')
<div class="container mx-auto">
    <!-- En-tête avec bouton créer -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
         <h1 style="font-size: 2rem; font-weight: 700;">Gestion des Utilisateurs</h1>
        @can('create-user')
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                <span>Nouveau Utilisateur</span>
            </a>
        @endcan
    </div>

    <!-- Tableau des utilisateurs -->
    <div style="background: white; border-radius: 0.75rem; overflow-x:auto; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
        <table class="table">
            <thead>
                <tr class="bg-gray-100 border-b">
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th >Rôles</th>
                    <th >Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr class="border-b hover:bg-gray-50 transition duration-200">
                        <td class="px-6 py-4 text-sm text-gray-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-cyan-500 flex items-center justify-center text-white font-bold">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                {{ $user->name }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->email }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $user->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm">
                            @foreach($user->roles as $role)
                                <span class="inline-block bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-xs font-semibold mr-2 mb-1">
                                    {{ $role->name }}
                                </span>
                            @endforeach
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($user->is_active)
                                <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    Actif
                                </span>
                            @else
                                <span class="inline-block bg-red-100 text-red-800 px-3 py-1 rounded-full text-xs font-semibold">
                                    Inactif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('users.show', $user->id) }}" class="text-blue-600 hover:text-blue-800 transition duration-200" title="Voir">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @can('edit-user')
                                    <a href="{{ route('users.edit', $user->id) }}" class="text-yellow-600 hover:text-yellow-800 transition duration-200" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                @endcan
                                @can('delete-user') 
                                    @if($user->id !== auth()->user()->id)
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 transition duration-200" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-3 opacity-50"></i>
                            <p>Aucun utilisateur trouvé</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $users->links() }}
    </div>
</div>

@endsection

