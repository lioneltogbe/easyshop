@extends('layouts.app')

@section('title', 'Éditer - ' . $categorie->name)

@section('content')
<style>
    .form-card { background:white; border-radius:.75rem; border:1px solid #E5E7EB; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,.06); max-width:680px; }
    .form-label { display:block; font-weight:600; color:#111827; margin-bottom:.4rem; font-size:.875rem; }
    .form-input,.form-select,.form-textarea { width:100%; padding:.75rem 1rem; border:1px solid #D1D5DB; border-radius:.5rem; font-size:.875rem; font-family:inherit; transition:border-color .2s,box-shadow .2s; }
    .form-input:focus,.form-select:focus,.form-textarea:focus { outline:none; border-color:#7C3AED; box-shadow:0 0 0 3px rgba(124,58,237,.1); }
    .form-input.is-invalid,.form-select.is-invalid { border-color:#EF4444; }
    .form-error { font-size:.75rem; color:#EF4444; margin-top:.25rem; }
    .form-group { margin-bottom:1.25rem; }
    .info-row { display:flex; justify-content:space-between; padding:.5rem 0; border-bottom:1px solid #F3F4F6; font-size:.875rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('categories.show', $categorie->id) }}" style="color:#7C3AED;text-decoration:none;font-weight:600;font-size:.875rem;">
        ← Retour à la catégorie
    </a>
</div>

<h1 style="font-size:2rem;font-weight:700;margin-bottom:.5rem;">Éditer la catégorie</h1>
<p style="color:#6B7280;margin-bottom:1.75rem;">{{ $categorie->name }}</p>

@if($errors->any())
<div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;">
    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 300px;gap:1.5rem;align-items:start;">

    {{-- Formulaire --}}
    <div class="form-card">
        <form action="{{ route('categories.update', $categorie->id) }}" method="POST">
            @csrf @method('PUT')

            {{-- Nom --}}
            <div class="form-group">
                <label class="form-label" for="name">Nom <span style="color:#EF4444;">*</span></label>
                <input type="text" id="name" name="name"
                    class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                    value="{{ old('name', $categorie->name) }}"
                    required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            {{-- Description --}}
            <div class="form-group">
                <label class="form-label" for="description">Description</label>
                <textarea id="description" name="description" rows="3"
                    class="form-textarea">{{ old('description', $categorie->description) }}</textarea>
            </div>

            {{-- Catégorie parente --}}
            <div class="form-group">
                <label class="form-label" for="parent_id">Catégorie parente</label>
                <select id="parent_id" name="parent_id"
                    class="form-select {{ $errors->has('parent_id') ? 'is-invalid' : '' }}">
                    <option value="">-- Aucune (Catégorie principale) --</option>
                    @foreach($parents as $parent)
                        <option value="{{ $parent->id }}" @selected(old('parent_id', $categorie->parent_id) == $parent->id)>
                            {{ $parent->name }}
                        </option>
                    @endforeach
                </select>
                <p style="font-size:.75rem;color:#6B7280;margin-top:.25rem;">Laisser vide pour une catégorie principale.</p>
                @error('parent_id')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;gap:1rem;padding-top:1.25rem;border-top:1px solid #E5E7EB;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Enregistrer
                </button>
                <a href="{{ route('categories.show', $categorie->id) }}" class="btn btn-secondary">Annuler</a>
            </div>
        </form>
    </div>

    {{-- Infos en lecture seule --}}
    <div>
        <div style="background:white;border-radius:.75rem;border:1px solid #E5E7EB;padding:1.25rem;margin-bottom:1rem;">
            <p style="font-weight:700;margin-bottom:.75rem;font-size:.9rem;">Informations</p>
            <div class="info-row"><span style="color:#6B7280;">ID</span><span>{{ $categorie->id }}</span></div>
            <div class="info-row"><span style="color:#6B7280;">Sous-catégories</span>
                <span style="background:#F3E8FF;color:#6D28D9;padding:.1rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;">
                    {{ $categorie->children->count() ?? 0 }}
                </span>
            </div>
            <div class="info-row"><span style="color:#6B7280;">Produits</span>
                <span style="background:#D1FAE5;color:#065F46;padding:.1rem .6rem;border-radius:999px;font-size:.75rem;font-weight:600;">
                    {{ $categorie->products->count() ?? 0 }}
                </span>
            </div>
            <div class="info-row" style="border:none;"><span style="color:#6B7280;">Créée le</span>
                <span>{{ $categorie->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        {{-- Danger zone --}}
        <div style="background:#FEF2F2;border:1px solid #FECACA;border-radius:.75rem;padding:1.25rem;">
            <p style="font-weight:700;color:#991B1B;margin-bottom:.5rem;font-size:.875rem;">Zone dangereuse</p>
            <p style="font-size:.8rem;color:#7F1D1D;margin-bottom:.75rem;">
                La suppression est irréversible. Impossible si des produits ou sous-catégories y sont rattachés.
            </p>
            <form action="{{ route('categories.destroy', $categorie->id) }}" method="POST"
                  onsubmit="return confirm('Supprimer définitivement « {{ $categorie->name }} » ?')">
                @csrf @method('DELETE')
                <button type="submit" style="width:100%;padding:.75rem;background:#EF4444;color:white;border:none;border-radius:.5rem;font-weight:600;cursor:pointer;">
                    🗑️ Supprimer la catégorie
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
