@extends('layouts.app')

@section('title', 'Créer une transaction - easyShop')

@section('content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('finances.transactions') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour aux transactions</a>
</div>

<div style="background: white; border-radius: 0.75rem; padding: 2rem; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1); max-width: 720px;">
    <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 1.5rem;">Créer une transaction financière</h1>

    <form action="{{ route('finances.transactions.store') }}" method="POST">
        @csrf

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="type">Type de transaction *</label>
            <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                <option value="">Sélectionner un type</option>
                <option value="REVENUE" @selected(old('type') === 'REVENUE')>Revenu</option>
                <option value="DEPENSE" @selected(old('type') === 'DEPENSE')>Dépense</option>
            </select>
            @error('type')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="description">Description *</label>
            <input id="description" type="text" name="description" class="form-input @error('description') is-invalid @enderror" value="{{ old('description') }}" placeholder="Ex : Paiement facture électricité" required>
            @error('description')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="montant">Montant (FCFA) *</label>
            <input id="montant" type="number" step="0.01" min="0" name="montant" class="form-input @error('montant') is-invalid @enderror" value="{{ old('montant') }}" placeholder="0.00" required>
            @error('montant')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 1.25rem;">
            <label class="form-label" for="paiement_method">Méthode de paiement *</label>
            <select id="paiement_method" name="paiement_method" class="form-select @error('paiement_method') is-invalid @enderror" required>
                <option value="">Sélectionner une méthode</option>
                <option value="caisse" @selected(old('paiement_method') === 'caisse')>Caisse</option>
                <option value="cheque" @selected(old('paiement_method') === 'cheque')>Chèque</option>
                <option value="transfert" @selected(old('paiement_method') === 'transfert')>Transfert</option>
                <option value="credit" @selected(old('paiement_method') === 'credit')>Crédit</option>
            </select>
            @error('paiement_method')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="margin-bottom: 1.5rem;">
            <label class="form-label" for="categorie">Catégorie</label>
            <input id="categorie" type="text" name="categorie" class="form-input @error('categorie') is-invalid @enderror" value="{{ old('categorie') }}" placeholder="Ex : Fournitures, Services, Salaire">
            @error('categorie')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <button type="submit" class="btn btn-primary" style="flex: 1; min-width: 160px;">✅ Enregistrer</button>
            <a href="{{ route('finances.transactions') }}" class="btn btn-secondary" style="flex: 1; min-width: 160px; text-align: center;">❌ Annuler</a>
        </div>
    </form>
</div>
@endsection
