@extends('layouts.app')

@section('content')
<style>
    :root {
        --color-primary:#7C3AED;--color-primary-light:#A78BFA;
        --color-gray-50:#F9FAFB;--color-gray-100:#F3F4F6;--color-gray-200:#E5E7EB;
        --color-gray-600:#4B5563;--color-gray-700:#374151;--color-gray-900:#111827;
        --color-success:#10B981;--color-danger:#EF4444;
        --radius-lg:0.75rem;--shadow-sm:0 1px 2px 0 rgba(0,0,0,.05);
    }
</style>

<div style="padding-top:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
        <h1 style="font-size:2rem;font-weight:700;">Fournisseurs</h1>
        @can('create-fournisseur')
            <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary">+ Nouveau Fournisseur</a>
        @endcan
    </div>

    <!-- {{-- Stat --}}
    <div style="padding:1rem 1.5rem;border-radius:var(--radius-lg);text-align:center;border:1px solid rgba(124,58,237,.2);background:linear-gradient(135deg,rgba(124,58,237,.1),rgba(124,58,237,.05));margin-bottom:2rem;">
        <div style="font-size:.75rem;font-weight:600;color:var(--color-gray-600);text-transform:uppercase;letter-spacing:.5px;margin-bottom:.5rem;">Total Fournisseurs</div>
        <div style="font-size:1.875rem;font-weight:700;color:var(--color-primary);">{{ $fournisseurs->total() }}</div>
    </div> -->

    {{-- Barre recherche --}}
    <x-pagination-controls
        :action="route('fournisseurs.index')"
        :params="$params"
        searchPlaceholder="Rechercher par nom, email, téléphone, ville..."
        :sortOptions="[
            ['value'=>'name',       'label'=>'Nom'],
            ['value'=>'email',      'label'=>'Email'],
            ['value'=>'city',       'label'=>'Ville'],
            ['value'=>'created_at', 'label'=>'Date création'],
        ]"
    />

    @if($fournisseurs->total() > 0)
    <p style="margin-bottom:.75rem;color:#6B7280;font-size:.875rem;">
        Affichage {{ $fournisseurs->firstItem() }}–{{ $fournisseurs->lastItem() }} sur {{ $fournisseurs->total() }} fournisseurs
    </p>
    @endif

    <div style="background:white;border-radius:var(--radius-lg);border:1px solid var(--color-gray-200);box-shadow:var(--shadow-sm);overflow:hidden;">
        <div style="padding:1.5rem;border-bottom:1px solid var(--color-gray-200);background:var(--color-gray-50);">
            <h2 style="font-size:1.125rem;font-weight:700;">Liste des Fournisseurs</h2>
        </div>

        @if($fournisseurs->count() > 0)
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;">
                <thead style="background:var(--color-gray-50);border-bottom:1px solid var(--color-gray-200);">
                    <tr>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Nom</th>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Email</th>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Téléphone</th>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Adresse</th>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Ville</th>
                        <th style="padding:1rem;text-align:left;font-size:.9rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($fournisseurs as $fournisseur)
                    <tr style="border-bottom:1px solid var(--color-gray-200);">
                        <td style="padding:1rem;"><strong>{{ $fournisseur->name }}</strong></td>
                        <td style="padding:1rem;">{{ $fournisseur->email ?? 'N/A' }}</td>
                        <td style="padding:1rem;">{{ $fournisseur->phone ?? 'N/A' }}</td>
                        <td style="padding:1rem;">{{ $fournisseur->address ?? 'N/A' }}</td>
                        <td style="padding:1rem;">{{ $fournisseur->city ?? 'Inconnu' }}</td>
                        <td style="padding:1rem;">
                            <div style="display:flex;gap:.5rem;">
                                @can('edit-fournisseur')
                                    <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" style="padding:.25rem .75rem;border-radius:var(--radius-lg);font-size:.75rem;text-decoration:none;background:rgba(59,130,246,.1);color:#3B82F6;">Éditer</a>
                                @endcan
                                @can('delete-fournisseur')
                                    <form action="{{ route('fournisseurs.delete', $fournisseur->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" onclick="return confirm('Êtes-vous sûr ?')" style="padding:.25rem .75rem;border-radius:var(--radius-lg);font-size:.75rem;border:none;cursor:pointer;background:rgba(239,68,68,.1);color:var(--color-danger);">Supprimer</button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($fournisseurs->hasPages())
        <div style="padding:1rem 1.5rem;border-top:1px solid var(--color-gray-200);">
            {{ $fournisseurs->links() }}
        </div>
        @endif
        @else
            <div style="text-align:center;padding:3rem;color:var(--color-gray-600);">
                <div style="font-size:3rem;margin-bottom:1rem;">🏢</div>
                <p>Aucun fournisseur enregistré</p>
                <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary" style="margin-top:1rem;">Créer un Fournisseur</a>
            </div>
        @endif
    </div>
</div>
@endsection
