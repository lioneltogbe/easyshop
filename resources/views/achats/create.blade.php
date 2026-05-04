
@extends('layouts.app')

@section('title', 'Créer une Commande d\'Achat - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('achats.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la liste</a> 
    <h1 style="font-size: 2rem; font-weight: 700; margin-top: 1rem;">Créer une Commande d'Achat</h1>
</div>

<div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
    <form action="{{ route('achats.store') }}" method="POST" id="achatForm">
        @csrf

        <!-- Fournisseur -->
        <div class="form-group">
            <label class="form-label">Fournisseur *</label>
            <select name="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                <option value="">Sélectionner un fournisseur</option>
                @foreach($fournisseurs as $fournisseur)
                    <option value="{{ $fournisseur->id }}" @if(old('fournisseur_id') == $fournisseur->id) selected @endif>
                        {{ $fournisseur->name }}
                    </option>
                @endforeach
            </select>
            @error('fournisseur_id')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <!-- Articles -->
        <div style="margin-top: 2rem; margin-bottom: 2rem;">
            <h3 style="font-weight: 600; margin-bottom: 1rem;">Articles de la Commande</h3>

            <div id="productsContainer">
                <div class="product-item" style="background: #F9FAFB; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem;">
                    <div class="grid grid-3">
                        <div class="form-group">
                            <label class="form-label">Produit *</label>
                            <select name="products[0][product_id]" class="form-select" required>
                                <option value="">Sélectionner un produit</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} ({{ $product->unitofmeasure }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Quantité *</label>
                            <input type="number" name="products[0][quantity]" class="form-input" 
                                   placeholder="0.00" step="0.01" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Prix Unitaire *</label>
                            <input type="number" name="products[0][unit_price]" class="form-input" 
                                   placeholder="0.00" step="0.01" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-secondary" onclick="addProduct()" style="margin-top: 1rem;">
                ➕ Ajouter un Article
            </button>
        </div>

        <!-- Boutons -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">✅ Créer la Commande</button>
            <a href="{{ route('achats.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">❌ Annuler</a>
        </div>
    </form>
</div>

<script>
let productIndex = 1;

function addProduct() {
    const container = document.getElementById('productsContainer');
    const newProduct = document.createElement('div');
    newProduct.className = 'product-item';
    newProduct.style.cssText = 'background: #F9FAFB; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 1rem;';
    
    newProduct.innerHTML = `
        <div class="grid grid-3">
            <div class="form-group">
                <label class="form-label">Produit *</label>
                <select name="products[${productIndex}][product_id]" class="form-select" required>
                    <option value="">Sélectionner un produit</option>
                    @foreach(\App\Models\Product::all() as $product)
                        <option value="{{ $product->id }}">
                            {{ $product->name }} ({{ $product->unitofmeasure }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Quantité *</label>
                <input type="number" name="products[${productIndex}][quantity]" class="form-input" 
                       placeholder="0.00" step="0.01" required>
            </div>

            <div class="form-group">
                <label class="form-label">Prix Unitaire *</label>
                <input type="number" name="products[${productIndex}][unit_price]" class="form-input" 
                       placeholder="0.00" step="0.01" required>
            </div>
        </div>
        <button type="button" class="btn btn-danger" onclick="this.parentElement.remove()" style="margin-top: 1rem;">
            🗑️ Supprimer
        </button>
    `;

    container.appendChild(newProduct);
    productIndex++;
}
</script>
@endsection