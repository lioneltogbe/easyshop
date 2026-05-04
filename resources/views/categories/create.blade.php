@extends('layouts.app')

@section('title', 'Nouvelle Catégorie - easyShop')

@section('content')
<style>
    .form-card { background:white; border-radius:.75rem; border:1px solid #E5E7EB; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,.06); max-width:680px; }
    .form-label { display:block; font-weight:600; color:#111827; margin-bottom:.4rem; font-size:.875rem; }
    .form-input,.form-select,.form-textarea { width:100%; padding:.75rem 1rem; border:1px solid #D1D5DB; border-radius:.5rem; font-size:.875rem; font-family:inherit; transition:border-color .2s,box-shadow .2s; }
    .form-input:focus,.form-select:focus,.form-textarea:focus { outline:none; border-color:#7C3AED; box-shadow:0 0 0 3px rgba(124,58,237,.1); }
    .form-input.is-invalid,.form-select.is-invalid { border-color:#EF4444; }
    .form-error { font-size:.75rem; color:#EF4444; margin-top:.25rem; }
    .form-help  { font-size:.75rem; color:#6B7280; margin-top:.25rem; }
    .form-group { margin-bottom:1.25rem; }
    .type-btn { flex:1; padding:.875rem; border:2px solid #E5E7EB; border-radius:.75rem; cursor:pointer; text-align:center; transition:all .2s; background:white; }
    .type-btn.selected { border-color:#7C3AED; background:#F5F3FF; }
    .type-btn .icon { font-size:1.75rem; display:block; margin-bottom:.4rem; }
    .type-btn .label { font-weight:700; font-size:.9rem; color:#111827; }
    .type-btn .desc  { font-size:.75rem; color:#6B7280; margin-top:.2rem; }
</style>

<div style="margin-bottom:1.5rem;">
    <a href="{{ route('categories.index') }}" style="color:#7C3AED;text-decoration:none;font-weight:600;font-size:.875rem;">
        ← Retour aux catégories
    </a>
</div>

<h1 style="font-size:2rem;font-weight:700;margin-bottom:1.75rem;">Nouvelle Catégorie</h1>

@if($errors->any())
<div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;">
    @foreach($errors->all() as $error)<p>• {{ $error }}</p>@endforeach
</div>
@endif

<div class="form-card">
    <form action="{{ route('categories.store') }}" method="POST" id="cat-form">
        @csrf

        {{-- ── Choix du type ── --}}
        <div class="form-group">
            <label class="form-label">Type</label>
            <div style="display:flex;gap:1rem;" id="type-selector">
                <button type="button" class="type-btn selected" id="btn-principale" onclick="setType('principale')">
                    <span class="icon">📁</span>
                    <span class="label">Catégorie principale</span>
                    <p class="desc">Niveau racine, sans parent</p>
                </button>
                <button type="button" class="type-btn" id="btn-sous" onclick="setType('sous')">
                    <span class="icon">📂</span>
                    <span class="label">Sous-catégorie</span>
                    <p class="desc">Rattachée à une catégorie existante</p>
                </button>
            </div>
        </div>

        {{-- ── Catégorie parente (visible si sous-catégorie) ── --}}
        <div class="form-group" id="parent-group" style="display:none;">
            <label class="form-label" for="parent_id">Catégorie parente <span style="color:#EF4444;">*</span></label>
            <select id="parent_id" name="parent_id"
                class="form-select {{ $errors->has('parent_id') ? 'is-invalid' : '' }}">
                <option value="">-- Sélectionner une catégorie --</option>
                @foreach($parents as $parent)
                    <option value="{{ $parent->id }}" @selected(old('parent_id', request('parent_id')) == $parent->id)>
                        {{ $parent->name }}
                    </option>
                @endforeach
            </select>
            @error('parent_id')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- ── Nom ── --}}
        <div class="form-group">
            <label class="form-label" for="name">Nom <span style="color:#EF4444;">*</span></label>
            <input type="text" id="name" name="name"
                class="form-input {{ $errors->has('name') ? 'is-invalid' : '' }}"
                value="{{ old('name') }}"
                placeholder="Ex : Matériaux de Construction"
                required>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- ── Description ── --}}
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" rows="3"
                class="form-textarea {{ $errors->has('description') ? 'is-invalid' : '' }}"
                placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
            <p class="form-help">Optionnel — 1000 caractères max.</p>
            @error('description')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        {{-- ── Boutons ── --}}
        <div style="display:flex;gap:1rem;padding-top:1.25rem;border-top:1px solid #E5E7EB;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Créer la catégorie
            </button>
            <a href="{{ route('categories.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>

<script>
// Pré-sélectionner sous-catégorie si parent_id passé en query string
const preParentId = "{{ request('parent_id') }}";
if (preParentId) {
    setType('sous');
    document.getElementById('parent_id').value = preParentId;
}

// Pré-sélectionner si old('parent_id') présent après erreur
@if(old('parent_id'))
    setType('sous');
@endif

function setType(type) {
    const pg   = document.getElementById('parent-group');
    const bP   = document.getElementById('btn-principale');
    const bS   = document.getElementById('btn-sous');
    const sel  = document.getElementById('parent_id');

    if (type === 'sous') {
        pg.style.display = 'block';
        sel.required = true;
        bS.classList.add('selected');
        bP.classList.remove('selected');
    } else {
        pg.style.display = 'none';
        sel.required = false;
        sel.value = '';
        bP.classList.add('selected');
        bS.classList.remove('selected');
    }
}
</script>
@endsection
