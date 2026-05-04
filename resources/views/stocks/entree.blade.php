@extends('layouts.app')

@section('title', 'Enregistrer une Entrée - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('stocks.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la gestion des stocks</a>
    <h1 style="font-size: 2rem; font-weight: 700; margin-top: 1rem;">📥 Enregistrer une Entrée de Stock</h1>
</div>

<div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 600px;">
    <form action="{{ route('stocks.entree.store') }}" method="POST">
        @csrf

        <!-- Produit -->
        <div class="form-group">
            <label class="form-label">Produit *</label>
            <select name="product_id" class="form-select @error('product_id') is-invalid @enderror" required>
                <option value="">Sélectionner un produit</option>
                @foreach(\App\Models\Product::all() as $product)
                    <option value="{{ $product->id }}" @if(old('product_id') == $product->id) selected @endif>
                        {{ $product->name }} (Stock actuel: {{ $product->getCurrentStock() }} {{ $product->unitofmeasure }})
                    </option>
                @endforeach
            </select>
            @error('product_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Quantité -->
        <div class="form-group">
            <label class="form-label">Quantité *</label>
            <input type="number" name="quantity" class="form-input @error('quantity') is-invalid @enderror" 
                   placeholder="0.00" step="0.01" required value="{{ old('quantity') }}">
            @error('quantity')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Raison -->
        <div class="form-group">
            <label class="form-label">Raison *</label>
            <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                <option value="">Sélectionner une raison</option>
                <option value="IN" @if(old('reason') == 'IN') selected @endif>Réception Commande</option>
                <!-- <option value="OUT" @if(old('reason') == 'OUT') selected @endif>Retour Client</option>
                <option value="AJUSTMENT" @if(old('reason') == 'AJUSTMENT') selected @endif>Ajustement</option> -->
            </select>
            @error('reason')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Boutons -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">✅ Enregistrer l'Entrée</button>
            <a href="{{ route('stocks.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">❌ Annuler</a>
        </div>
    </form>
</div>
@endsection
