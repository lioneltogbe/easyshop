@extends('layouts.app')

@section('title', 'Créer une Commande de Vente - easyShop')

@section('content')

<div style="margin-bottom:2rem;">
    <a href="{{ route('ventes.index') }}" style="color:var(--primary); font-weight:500;">
        ← Retour à la liste
    </a>
        <h1 style="font-size:2rem; font-weight:700; margin-top:1rem;">
            Créer une Commande de Vente
        </h1>
</div>

<div style="background:#fff; border-radius:.75rem; padding:2rem; box-shadow:0 1px 3px rgba(0,0,0,.1);">

    <form action="{{ route('ventes.store') }}" method="POST" id="venteForm">
        @csrf

        <!-- Client -->
        <div class="form-group">
            <label class="form-label">Client *</label>
            <select name="client_id" class="form-select" required>
                <option value="">Sélectionner un client</option>
                @foreach($clients as $client)
                    <option value="{{ $client->id }}">
                        {{ $client->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Produits -->
        <h3 style="margin-top:2rem; font-weight:600;">Articles vendus</h3>

        <div id="productsContainer"></div>

        <button type="button" class="btn btn-secondary" onclick="addProduct()" style="margin-top:1rem;">
            ➕ Ajouter un article
        </button>

        <!-- montant payer -->
        <!-- <div class="form-group" style="margin-top:2rem;">
            <label class="form-label">Montant Payer *</label>
                <input type="number" name="montantPayer"  class="form-input" step="0.01" min="0"
                required>
        </div> -->

        <!-- Total -->
        <div style="margin-top:2rem; text-align:right;">
            <strong>Total :</strong>
            <span id="montantTotal">0</span> FCFA
            <input type="hidden" name="montantTotal" id="montantTotalInput">
        </div>
        
        <!-- Actions -->
        <div style="display:flex; gap:1rem; margin-top:2rem;">
            <button class="btn btn-primary" style="flex:1;">✅ Créer la Vente</button>
            <a href="{{ route('ventes.index') }}" class="btn btn-secondary" style="flex:1; text-align:center;">
                ❌ Annuler
            </a>
        </div>
    </form>
</div>
<script>
    let index = 0;
    const products = @json($products);

    function addProduct() {
        const container = document.getElementById('productsContainer');

        let options = `<option value="">Sélectionner un produit</option>`;
        products.forEach(p => {
            options += `<option value="${p.id}" data-price="${p.ventePrice}">
                ${p.name} (${p.unitofmeasure})
            </option>`;
        });

        const div = document.createElement('div');
        div.className = 'product-item';
        div.style.cssText = 'background:#F9FAFB; padding:1.5rem; border-radius:.5rem; margin-top:1rem;';

        div.innerHTML = `
            <div class="grid grid-4">
                <div class="form-group">
                    <label>Produit *</label>
                    <select name="products[${index}][product_id]"
                            class="form-select product-select"
                            onchange="updatePrice(this)"
                            required>
                        ${options}
                    </select>
                </div>

                <div class="form-group">
                    <label>Quantité *</label>
                    <input type="number"
                        name="products[${index}][quantity]"
                        class="form-input qty-input"
                        step="0.01" min="0"
                        oninput="calculateTotal()"
                        required>
                </div>

                <div class="form-group">
                    <label>Prix unitaire</label>
                    <input type="number"
                        name="products[${index}][prix_unitaire]"
                        class="form-input unit-price"
                        step="0.01" min="0"
                        oninput="calculateTotal()">
                </div>

                <div class="form-group">
                    <label>Remise (FCFA)</label>
                    <input type="number"
                        name="products[${index}][remise_montant]"
                        class="form-input remise-input"
                        step="1" min="0" value="0"
                        oninput="calculateTotal()">
                </div>
            @if($isTvaApplicable)
                <div class="form-group">
                    <label>TVA (%)</label>
                    <input type="number"
                        name="products[${index}][tva]"
                        class="form-input tva-input"
                        step="0.01" min="0" value="{{$tvaRate}}%"
                        oninput="calculateTotal()" disabled>
                </div>
            @else
                <div class="form-group">
                    <label>TVA (%)</label>
                    <input type="number"
                        name="products[${index}][taux_tva]"
                        class="form-input tva-input"
                        step="0.01" min="0" value=0
                        oninput="calculateTotal()" disabled>
                </div>
            @endif

                <div class="form-group">
                    <label>Total pour ce produit (FCFA)</label>
                    <input type="text"
                        class="form-input line-total"
                        disabled>
                </div>
            </div>

            <button type="button"
                    class="btn btn-danger"
                    style="margin-top:.5rem;"
                    onclick="this.parentElement.remove(); calculateTotal();">
                🗑️ Supprimer
            </button>
        `;

        container.appendChild(div);
        index++;
    }

    function updatePrice(select) {
        const price = select.selectedOptions[0]?.dataset.price || 0;
        const container = select.closest('.product-item');
        container.querySelector('.unit-price').value = parseFloat(price).toFixed(2);
        calculateTotal();
    }

    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('.product-item').forEach(item => {
            const qty          = parseFloat(item.querySelector('.qty-input')?.value)   || 0;
            const price        = parseFloat(item.querySelector('.unit-price')?.value)  || 0;
            const remise       = parseFloat(item.querySelector('.remise-input')?.value)|| 0;
            const tva          = parseFloat(item.querySelector('.tva-input')?.value)   || 0;


            const baseImposable  = parseFloat(((price * qty) - remise).toFixed(2));
            const montantTva     = parseFloat((baseImposable * (tva / 100)).toFixed(2));
            const totalLigne     = parseFloat((baseImposable + montantTva).toFixed(2));

            item.querySelector('.line-total').value = totalLigne.toFixed(2);
            total += totalLigne;
        });

        document.getElementById('montantTotal').innerText     = total.toFixed(2);
        document.getElementById('montantTotalInput').value    = total.toFixed(2);
    }
</script>

@endsection
