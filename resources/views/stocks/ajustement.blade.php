@extends('layouts.app')

@section('title', 'Ajustement de Stock - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('stocks.index') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la gestion des stocks</a>
    <h1 style="font-size: 2rem; font-weight: 700; margin-top: 1rem;">⚙️ Ajustement de Stock</h1>
    <p style="color: var(--gray-500); margin-top: 0.5rem;">Corrigez le stock réel après un inventaire, une casse, une perte ou un écart constaté.</p>
</div>

{{-- Messages flash --}}
@if(session('success'))
    <div style="background: #D1FAE5; border: 1px solid #10B981; color: #065F46; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background: #FEE2E2; border: 1px solid #EF4444; color: #991B1B; padding: 1rem; border-radius: 0.5rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
        ❌ {{ session('error') }}
    </div>
@endif

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: start;">

    {{-- FORMULAIRE --}}
    <div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--gray-800);">Nouvel ajustement</h2>

        <form action="{{ route('stocks.ajustement.store') }}" method="POST" id="adjustForm">
            @csrf

            {{-- Produit --}}
            <div class="form-group">
                <label class="form-label">Produit *</label>
                <select name="product_id" id="productSelect"
                    class="form-select @error('product_id') is-invalid @enderror"
                    required onchange="updateStockInfo(this)">
                    <option value="">Sélectionner un produit</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                            data-stock="{{ $product->getCurrentStock() }}"
                            data-unit="{{ $product->unitofmeasure }}"
                            data-seuil="{{ $product->alertStockLevel }}"
                            @if(old('product_id') == $product->id) selected @endif>
                            {{ $product->name }} — Stock actuel : {{ $product->getCurrentStock() }} {{ $product->unitofmeasure }}
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Info stock actuel --}}
            <div id="stockInfo" style="display:none; background: #F0F9FF; border: 1px solid #BAE6FD; border-radius: 0.5rem; padding: 0.75rem 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #0369A1;">
                📦 Stock actuel : <strong id="currentStockDisplay">—</strong>
                &nbsp;|&nbsp; Seuil d'alerte : <strong id="seuilDisplay">—</strong>
            </div>

            {{-- Type d'ajustement --}}
            <div class="form-group">
                <label class="form-label">Type d'ajustement *</label>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-top: 0.25rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 2px solid var(--gray-200); border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;" id="labelPositif">
                        <input type="radio" name="direction" value="positif" onchange="toggleDirection()" {{ old('direction','positif') == 'positif' ? 'checked' : '' }} required>
                        <span> Ajustement <strong>+</strong> (hausse)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem; border: 2px solid var(--gray-200); border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;" id="labelNegatif">
                        <input type="radio" name="direction" value="negatif" onchange="toggleDirection()" {{ old('direction') == 'negatif' ? 'checked' : '' }}>
                        <span> Ajustement <strong>−</strong> (baisse)</span>
                    </label>
                </div>
            </div>

            {{-- Quantité --}}
            <div class="form-group">
                <label class="form-label">Quantité à ajuster *</label>
                <input type="number" name="quantity"
                    class="form-input @error('quantity') is-invalid @enderror"
                    placeholder="Entrez la quantité d'écart (ex: 3)"
                    step="0.01" min="0.01" required
                    value="{{ old('quantity') }}">
                <small style="color: var(--gray-500); font-size: 0.8rem; margin-top: 0.25rem; display:block;">
                    Saisissez la valeur <em>absolue</em> de l'écart. La direction est définie ci-dessus.
                </small>
                @error('quantity')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Motif --}}
            <div class="form-group">
                <label class="form-label">Motif *</label>
                <select name="reason" class="form-select @error('reason') is-invalid @enderror" required>
                    <option value="">Sélectionner un motif</option>
                    <option value="Inventaire physique"    @if(old('reason') == 'Inventaire physique') selected @endif>📋 Inventaire physique</option>
                    <option value="Casse / détérioration" @if(old('reason') == 'Casse / détérioration') selected @endif>💥 Casse / détérioration</option>
                    <option value="Vol / démarque"        @if(old('reason') == 'Vol / démarque') selected @endif>🔒 Vol / démarque inconnue</option>
                    <option value="Erreur de saisie"      @if(old('reason') == 'Erreur de saisie') selected @endif>✏️ Correction d'erreur de saisie</option>
                    <option value="Péremption"            @if(old('reason') == 'Péremption') selected @endif>⏰ Produit périmé / retiré</option>
                    <option value="Retour fournisseur"    @if(old('reason') == 'Retour fournisseur') selected @endif>🔄 Retour fournisseur</option>
                    <option value="Autre"                 @if(old('reason') == 'Autre') selected @endif>📝 Autre</option>
                </select>
                @error('reason')
                    <div class="form-error">{{ $message }}</div>
                @enderror
            </div>

            {{-- Note libre --}}
            <div class="form-group">
                <label class="form-label">Note complémentaire <span style="color:var(--gray-400)">(facultatif)</span></label>
                <textarea name="note" class="form-input" rows="2"
                    placeholder="Précisez si nécessaire..."
                    style="resize: vertical;">{{ old('note') }}</textarea>
            </div>

            {{-- Boutons --}}
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    ✅ Enregistrer l'ajustement
                </button>
                <a href="{{ route('stocks.index') }}" class="btn btn-secondary" style="flex: 1; text-align: center;">
                    ❌ Annuler
                </a>
            </div>
        </form>
    </div>

    {{-- HISTORIQUE DES DERNIERS AJUSTEMENTS --}}
    <div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <h2 style="font-size: 1.1rem; font-weight: 600; margin-bottom: 1.5rem; color: var(--gray-800);">
            🕐 Derniers ajustements
        </h2>

        @if($lastAdjustments->isEmpty())
            <div style="text-align: center; padding: 2rem; color: var(--gray-400);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">📭</div>
                Aucun ajustement enregistré pour l'instant.
            </div>
        @else
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($lastAdjustments as $adj)
                    @php $qty = $adj->quantity; $isPos = $qty >= 0; @endphp
                    <div style="display: flex; justify-content: space-between; align-items: center;
                                padding: 0.75rem 1rem; border-radius: 0.5rem;
                                background: {{ $isPos ? '#F0FDF4' : '#FFF7F7' }};
                                border-left: 4px solid {{ $isPos ? '#10B981' : '#EF4444' }};">
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem; color: var(--gray-800);">
                                {{ $adj->product->name ?? '—' }}
                            </div>
                            <div style="font-size: 0.78rem; color: var(--gray-500); margin-top: 0.2rem;">
                                {{ $adj->reason }}
                                &nbsp;·&nbsp;
                                {{ $adj->createdBy->name ?? 'Système' }}
                                &nbsp;·&nbsp;
                                {{ $adj->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div style="font-size: 1.1rem; font-weight: 700; color: {{ $isPos ? '#10B981' : '#EF4444' }};">
                            {{ $isPos ? '+' : '' }}{{ number_format($qty, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div style="margin-top: 1rem; text-align: right;">
                <a href="{{ route('stocks.mouvements') }}" style="color: var(--primary); font-size: 0.875rem; text-decoration: none;">
                    Voir tous les mouvements →
                </a>
            </div>
        @endif
    </div>

</div>

<script>
function updateStockInfo(select) {
    const opt = select.options[select.selectedIndex];
    const stock = opt.dataset.stock;
    const unit  = opt.dataset.unit;
    const seuil = opt.dataset.seuil;
    const info  = document.getElementById('stockInfo');

    if (select.value) {
        document.getElementById('currentStockDisplay').textContent = stock + ' ' + unit;
        document.getElementById('seuilDisplay').textContent = seuil + ' ' + unit;
        info.style.display = 'block';
    } else {
        info.style.display = 'none';
    }
}

function toggleDirection() {
    const pos = document.querySelector('input[value="positif"]').checked;
    document.getElementById('labelPositif').style.borderColor = pos ? '#10B981' : 'var(--gray-200)';
    document.getElementById('labelPositif').style.background  = pos ? '#F0FDF4' : 'white';
    document.getElementById('labelNegatif').style.borderColor = !pos ? '#EF4444' : 'var(--gray-200)';
    document.getElementById('labelNegatif').style.background  = !pos ? '#FFF7F7' : 'white';
}

// Init sur chargement (si old() présent)
document.addEventListener('DOMContentLoaded', function() {
    toggleDirection();
    const sel = document.getElementById('productSelect');
    if (sel.value) updateStockInfo(sel);
});
</script>
@endsection