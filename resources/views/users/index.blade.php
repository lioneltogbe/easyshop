@extends('layouts.app')

@section('page-title', 'Utilisateurs')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;">
    <h1 style="font-size:2rem;font-weight:700;">Gestion des Utilisateurs</h1>
    @can('create-user')
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouveau Utilisateur
        </a>
    @endcan
</div>

<x-pagination-controls
    :action="route('users.index')"
    :params="$params"
    searchPlaceholder="Rechercher par nom, email, téléphone..."
    :sortOptions="[
        ['value'=>'name',       'label'=>'Nom'],
        ['value'=>'email',      'label'=>'Email'],
        ['value'=>'created_at', 'label'=>'Date création'],
        ['value'=>'is_active',  'label'=>'Statut'],
    ]"
>
    {{-- Filtre statut actif --}}
    <div>
        <select name="filter_is_active" class="form-select" style="margin-bottom:0;">
            <option value="">-- Statut --</option>
            <option value="1" @selected(($params['filter_is_active'] ?? '') === '1')>Actif</option>
            <option value="0" @selected(($params['filter_is_active'] ?? '') === '0')>Inactif</option>
        </select>
    </div>
</x-pagination-controls>

@if($users->total() > 0)
<p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
    Affichage {{ $users->firstItem() }}–{{ $users->lastItem() }} sur {{ $users->total() }} utilisateurs
</p>
@endif

<div style="background:white;border-radius:.75rem;overflow-x:auto;box-shadow:0 1px 3px rgba(0,0,0,.1);">
    <table class="table">
        <thead>
            <tr>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôles</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:.75rem;">
                            <div style="width:2.5rem;height:2.5rem;border-radius:50%;background:linear-gradient(135deg,#7C3AED,#06B6D4);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;flex-shrink:0;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            {{ $user->name }}
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone ?? '-' }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span style="display:inline-block;background:#EDE9FE;color:#5B21B6;padding:.2rem .65rem;border-radius:999px;font-size:.75rem;font-weight:600;margin:.1rem;">
                                {{ $role->name }}
                            </span>
                        @endforeach
                    </td>
                    <td>
                        @if($user->is_active)
                            <span style="display:inline-block;background:#D1FAE5;color:#065F46;padding:.2rem .65rem;border-radius:999px;font-size:.75rem;font-weight:600;">Actif</span>
                        @else
                            <span style="display:inline-block;background:#FEE2E2;color:#991B1B;padding:.2rem .65rem;border-radius:999px;font-size:.75rem;font-weight:600;">Inactif</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:.5rem;align-items:center;">
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-secondary" style="padding:.35rem .75rem;font-size:.8rem;">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('edit-user')
                                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary" style="padding:.35rem .75rem;font-size:.8rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan
                            @can('delete-user')
                                @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding:.35rem .75rem;font-size:.8rem;">
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
                    <td colspan="6" style="text-align:center;padding:2rem;color:#6B7280;">
                        <i class="fas fa-inbox" style="font-size:2rem;display:block;margin-bottom:.5rem;opacity:.4;"></i>
                        Aucun utilisateur trouvé
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div style="padding:1rem 1.5rem;border-top:1px solid #E5E7EB;">
        {{ $users->links() }}
    </div>
    @endif
</div>
@endsection
