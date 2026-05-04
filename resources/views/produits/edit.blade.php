@extends('layouts.app')
 
@section('title', 'Éditer le Produit - easyShop')
 
@section('content')

<div style="margin-bottom: 2rem;">
        <a href="{{ route('produits.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la liste</a>
</div>
 <div style="margin-bottom: 2rem;">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Éditer le Produit</h1>
    <p style="text-gray-600">Modifiez les informations du produit</h1>
</div>
    <!-- Formulaire -->
    
    <div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 600px;">
        <form action="{{ route('produits.update', $product->id) }}" method="POST">
            @csrf
            @method('PUT')
 
            <!-- Code Produit (lecture seule) -->
            <div class="form-group">
                <label for="code" class="form-label">
                    Code Produit
                </label>
                <input 
                    type="text" 
                    id="code" 
                    value="{{ $product->code }}"
                    class="form-input"
                    disabled
                >
                <p class="form-error">Le code ne peut pas être modifié</p>
            </div>
 
            <!-- Nom Produit -->
            <div class="form-group">
                <label for="name" class="form-label @error('name') is-invalid @enderror">
                    Nom du Produit *
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name', $product->name) }}"
                    class="form-textarea"
                    required
                >
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
 
            <!-- Description -->
            <div>
                <label for="description" class="form-label">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4"
                    class="form-textarea @error('description') border-red-500 @enderror"
                >{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
 
            <!-- Catégorie -->
            <div class="form-group">
                <label for="category_id" class="form-label">
                    Catégorie *
                </label>
                <select 
                    id="category_id" 
                    name="category_id"
                    class="form-input @error('category_id') border-red-500 @enderror"
                    required
                >
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
 
            <!-- Unité de Mesure -->
            <div class="form-group">
                <label for="unitofmeasure" class="form-label">
                    Unité de Mesure *
                </label>
                <select 
                    id="unitofmeasure" 
                    name="unitofmeasure"
                    class="form-input @error('unitofmeasure') border-red-500 @enderror"
                    required
                >
                    <option value="kg" @selected(old('unitofmeasure', $product->unitofmeasure) == 'kg')>Kilogramme (kg)</option>
                    <option value="unit" @selected(old('unitofmeasure', $product->unitofmeasure) == 'unité')>Unité</option>
                    <option value="m2" @selected(old('unitofmeasure', $product->unitofmeasure) == 'm2')>Mètre Carré (m³)</option>
                    <option value="m3" @selected(old('unitofmeasure', $product->unitofmeasure) == 'm3')>Mètre Cube (m³)</option>
                    <option value="litre" @selected(old('unitofmeasure', $product->unitofmeasure) == 'litre')>Litre (L)</option>
                    <option value="m" @selected(old('unitofmeasure', $product->unitofmeasure) == 'm')>Mètre (m)</option>
                </select>
                @error('unitofmeasure')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
 
            <!-- Grille Prix et Seuils -->
            <div class="">
                <!-- Prix d'Achat -->
                <div class="form-group">
                    <label for="achatPrice" class="form-label">
                        Prix d'Achat (FCFA) *
                    </label>
                    <input 
                        type="number" 
                        id="achatPrice" 
                        name="achatPrice" 
                        value="{{ old('achatPrice', $product->achatPrice) }}"
                        step="0.01"
                        class="form-input @error('achatPrice') border-red-500 @enderror"
                        required
                    >
                    @error('achatPrice')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- Prix de Vente -->
                <div class="form-group">
                    <label for="ventePrice" class="form-label">
                        Prix de Vente (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="ventePrice" 
                        name="ventePrice" 
                        value="{{ old('ventePrice', $product->ventePrice) }}"
                        step="0.01"
                        class="form-input @error('ventePrice') border-red-500 @enderror"
                        required
                    >
                    @error('ventePrice')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- Seuil d'Alerte Stock -->
                <div class="form-group">
                    <label for="alertStockLevel" class="form-label">
                        Seuil d'Alerte Stock <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="number" 
                        id="alertStockLevel" 
                        name="alertStockLevel" 
                        value="{{ old('alertStockLevel', $product->alertStockLevel) }}"
                        class="form-input @error('alertStockLevel') border-red-500 @enderror"
                        required
                    >
                    @error('alertStockLevel')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
 
                <!-- Jours avant Expiration -->
                <div class="form-group">
                    <label for="alertDaybeforeExpiration" class="form-label">
                        Jours avant Expiration
                    </label>
                    <input 
                        type="number" 
                        id="alertDaybeforeExpiration" 
                        name="alertDayBeforeExpiration" 
                        value="{{ old('alertDayBeforeExpiration', $product->alertDayBeforeExpiration) }}"
                        class="form-input @error('alertDaybeforeExpiration') border-red-500 @enderror"
                    >
                    @error('alertDayBeforeExpiration')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>
 
            <!-- Boutons -->
            <div class="flex gap-4 pt-6 border-t border-gray-200" style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button  
                    type="submit" 
                   class="btn btn-primary" style="flex: 1;"
                >
                    Mettre à Jour
                </button>
                <a 
                    href="{{ route('produits.show', $product->id) }}" 
                    class="btn btn-secondary" style="flex: 1; text-align: center;"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
 
<style>
    .container {
        max-width: 1200px;
    }
</style>
@endsection