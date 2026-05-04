@extends('layouts.app')

@section('title', 'Créer un Produit - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('produits.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la liste</a>
    <h1 style="font-size: 2rem; font-weight: 700; margin-top: 1rem;">Créer un Nouveau Produit</h1>
</div>

<div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 600px;">
    <form action="{{ route('produits.store') }}" method="POST">
        @csrf

        <!-- Nom -->
        <div class="form-group">
            <label class="form-label">Nom du Produit *</label>
            <input type="text" name="name" class="form-input @error('name') is-invalid @enderror" 
                   placeholder="ex: Ciment Portland" required value="{{ old('name') }}">
            @error('name')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-textarea" placeholder="Description du produit..." rows="3">{{ old('description') }}</textarea>
        </div>
        @error('name')

         <div class="form-textarea">{{ $message }} </div>
        @enderror
            <!-- Catégorie -->
        <div class="form-group">
            <label class="form-label">Catégorie *</label>
            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                <option value="">Sélectionner une catégorie</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @if(old('category_id') == $cat->id) selected @endif>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Unité de Mesure -->
        <div class="form-group">
            <label class="form-label">Unité de Mesure *</label>
            <select name="unitofmeasure" class="form-select @error('unitofmeasure') is-invalid @enderror" required>
                <option value="">Sélectionner une unité</option>
                <option value="unit" @if(old('unitofmeasure') == 'unit') selected @endif>Unité</option>
                <option value="kg" @if(old('unitofmeasure') == 'kg') selected @endif>Kilogramme (kg)</option>
                <option value="m" @if(old('unitofmeasure') == 'm') selected @endif>Mètre (m)</option>
                <option value="m2" @if(old('unitofmeasure') == 'm2') selected @endif>Mètre carré (m²)</option>
                <option value="m3" @if(old('unitofmeasure') == 'm3') selected @endif>Mètre cube (m³)</option>
                <option value="liter" @if(old('unitofmeasure') == 'liter') selected @endif>Litre (L)</option>
            </select>
            @error('unitofmeasure')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Prix d'Achat -->
        <div class="form-group">
            <label class="form-label">Prix d'Achat (FCFA) *</label>
            <input type="number" name="achatPrice" class="form-input @error('achatPrice') is-invalid @enderror" 
                   placeholder="0.00" step="0.01" required value="{{ old('achatPrice') }}">
            @error('achatPrice')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Prix de Vente -->
        <div class="form-group">
            <label class="form-label">Prix de Vente (FCFA) *</label>
            <input type="number" name="ventePrice" class="form-input @error('ventePrice') is-invalid @enderror" 
                   placeholder="0.00" step="0.01" required value="{{ old('ventePrice') }}">
            @error('ventePrice')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Seuil d'Alerte Stock -->
        <div class="form-group">
            <label class="form-label">Seuil d'Alerte Stock *</label>
            <input type="number" name="alertStockLevel" class="form-input @error('alertStockLevel') is-invalid @enderror" 
                   placeholder="10" step="0.01" required value="{{ old('alertStockLevel') }}">
            @error('alertStockLevel')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Alerte Expiration -->
        <div class="form-group">
            <label class="form-label">Alerte Expiration (jours avant)</label>
            <input type="number" name="alertDaybeforeExpiration" class="form-input @error('alertDaybeforeExpiration') is-invalid @enderror" 
                   placeholder="30" step="1" value="{{ old('alertDaybeforeExpiration') }}">
            @error('alertDaybeforeExpiration')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Boutons -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">✅ Créer le Produit</button>
            <a href="{{ route('produits.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">❌ Annuler</a>
        </div>
    </form>
</div>
<style>
    .container {
        max-width: 1200px;
        
    }
</style>
@endsection
