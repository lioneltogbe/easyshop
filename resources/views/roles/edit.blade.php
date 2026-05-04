@extends('layouts.app')

@section('page-title', 'Modifier le Rôle')

@section('content')
<div class="container" style="padding-top: 2rem;">
    <div class="card" style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.8rem; font-weight: 700; margin: 0 0 0.5rem;">Modifier {{ $role->name }}</h2>
                <p style="margin: 0; color: var(--gray-600);">Ajustez le nom, le slug et les permissions du rôle.</p>
            </div>
            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-secondary" style="white-space: nowrap;">← Retour au rôle</a>
        </div>

        <form action="{{ route('roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-2" style="gap: 1.5rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label for="name" class="form-label">Nom du rôle</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $role->name) }}"
                        class="form-input"
                        required
                    >
                    @error('name')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="slug" class="form-label">Slug</label>
                    <input
                        type="text"
                        id="slug"
                        name="slug"
                        value="{{ old('slug', $role->slug) }}"
                        class="form-input"
                        required
                    >
                    @error('slug')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description" class="form-label">Description</label>
                <textarea
                    id="description"
                    name="description"
                    class="form-textarea"
                    rows="4"
                >{{ old('description', $role->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label class="form-label">Permissions</label>
                <div class="grid grid-2" style="gap: 1rem;">
                    @foreach($permissions as $permission)
                        <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                @if($role->permissions->contains($permission->id)) checked @endif
                                style="width: 1.1rem; height: 1.1rem; accent-color: var(--primary); margin-top: 0.35rem;"
                            >
                            <div>
                                <span style="display: block; font-weight: 600; color: var(--gray-800);">{{ $permission->name }}</span>
                                <span style="display: block; color: var(--gray-500); font-size: 0.9rem;">{{ $permission->slug }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('permissions')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 180px;">Enregistrer</button>
                <a href="{{ route('roles.show', $role->id) }}" class="btn btn-secondary" style="flex: 1; min-width: 180px; text-align: center;">Annuler</a>
            </div>
        </form>
    </div>
</div>
@endsection
