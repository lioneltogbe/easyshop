@extends('layouts.app')

@section('content')
<style>
/* === STYLE IDENTIQUE À ACHATS/SHOW === */
:root {
    --color-primary:#7C3AED;
    --color-gray-50:#F9FAFB;
    --color-gray-200:#E5E7EB;
    --color-gray-600:#4B5563;
    --color-gray-900:#111827;
    --color-success:#10B981;
    --color-warning:#F59E0B;
    --color-danger:#EF4444;
    --radius-lg:.75rem;
}

.container { max-width:1200px; margin:auto; padding:2rem 1rem; }
.page-title { font-size:2rem; font-weight:700; }
.breadcrumb { color:var(--color-primary); font-weight:500; text-decoration:none; }

.card {
    background:#fff;
    border-radius:var(--radius-lg);
    border:1px solid var(--color-gray-200);
    margin-bottom:2rem;
}

.card-header {
    padding:1.5rem;
    background:var(--color-gray-50);
    border-bottom:1px solid var(--color-gray-200);
}

.card-body { padding:1.5rem; }

.info-grid {
    display:grid;
    grid-template-columns:repeat(2,1fr);
    gap:1.5rem;
}

.info-label {
    font-size:.75rem;
    color:var(--color-gray-600);
    font-weight:600;
    text-transform:uppercase;
}

.info-value { font-weight:500; }

.badge {
    padding:.25rem .75rem;
    border-radius:999px;
    font-size:.75rem;
    font-weight:600;
}

.badge.en { background:rgba(245,158,11,.1); color:var(--color-warning); }
.badge.confirmer { background:rgba(59,130,246,.1); color:#3B82F6; }
.badge.livrer { background:rgba(16,185,129,.1); color:var(--color-success); }
.badge.payer { background:rgba(124,58,237,.1); color:var(--color-primary); }

table { width:100%; border-collapse:collapse; }
th,td { padding:1rem; border-bottom:1px solid var(--color-gray-200); }
th { text-transform:uppercase; font-size:.75rem; color:var(--color-gray-600); }

.action-buttons { display:flex; gap:1rem; flex-wrap:wrap; }
.btn { padding:.5rem 1.5rem; border-radius:.75rem; border:none; cursor:pointer; font-weight:500; }
.btn-success { background:var(--color-success); color:#fff; }
.btn-warning { background:var(--color-warning); color:#fff; }
.btn-danger { background:var(--color-danger); color:#fff; }
.btn-primary { background:var(--color-primary); color:#fff; }
</style>

<div class="container">

    <!-- HEADER -->
    <div style="margin-bottom:2rem;">
        <a href="{{ route('ventes.index') }}" class="breadcrumb">← Retour aux ventes</a>
        <h1 class="page-title">{{ $vente->codeCommand }}</h1>
    </div>

    <!-- INFOS GÉNÉRALES -->
    <div class="card">
        <div class="card-header">
            <h2>Informations générales</h2>
        </div>
        <div class="card-body">
            <div class="info-grid">
                <div>
                    <span class="info-label">Client</span>
                    <span class="info-value">{{ $vente->client->name ?? 'N/A' }}</span>
                </div>

                <div>
                    <span class="info-label">Statut</span>
                    <span class="badge {{ strtolower($vente->status) }}">
                        {{ $vente->getStatusLabel() }}
                    </span>
                </div>

                <div>
                    <span class="info-label">Date</span>
                    <span class="info-value">{{ $vente->created_at->format('d/m/Y H:i') }}</span>
                </div>

                <div>
                    <span class="info-label">Montant Total</span>
                    <span class="info-value">
                        {{ number_format($vente->montantTotal,0,',',' ') }} FCFA
                    </span>
                </div>

                <div>
                    <span class="info-label">Montant Payé</span>
                    <span class="info-value">
                        {{ number_format($vente->montantPayer ?? 0,0,',',' ') }} FCFA
                    </span>
                </div>

                <div>
                    <span class="info-label">Créé par</span>
                    <span class="info-value">{{ $vente->creator->name ?? 'Système' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ARTICLES -->
    <div class="card">
        <div class="card-header">
            <h2>Articles ({{ $itemCount }})</h2>
        </div>
        <div class="card-body">
            <table>
                <thead>
                    <tr>
                        <th>Produit</th>
                        <th>Quantité</th>
                        <th>Prix unitaire</th>
                        <th>Remise</th>
                        <th>TVA</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format($item->unitPriceAtCommand,0,',',' ') }} FCFA</td>
                        <td>{{ number_format($item->remise_montant, 0, ',', ' ') }} FCFA</td>
                        <td>{{ number_format($item->montant_tva, 0, ',', ' ') }} FCFA</td>
                        <td>
                            <strong>
                                {{ number_format($item->getmontantTotal(),0,',',' ') }} FCFA
                            </strong>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- ACTIONS -->
    <div class="action-buttons">

        @if($vente->status === 'EN ATTENTE')
            <form method="POST" action="{{ route('ventes.confirmer', $vente->id) }}">
                @csrf
                
                <button class="btn btn-success"> Confirmer</button>
            </form>

            <form method="POST" action="{{ route('ventes.delete', $vente->id) }}"
                  onsubmit="return confirm('Annuler définitivement cette vente ?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger"> Annuler</button>
            </form>
        @endif

        @if($vente->status === 'CONFIRMER')
            <form method="POST" action="{{ route('ventes.livrer', $vente->id) }}">
                @csrf
                <button class="btn btn-warning">🚚 Livrer</button>
            </form>
        @endif
    
        @if($vente->status === 'LIVRER')
            <form method="POST" action="{{ route('ventes.payer', $vente->id) }}" style="display: inline;">
                
                @csrf
                <select name="paiement_method" required class="form-input"  style="margin-bottom:10px">
                    <option value="">-- Choisir Mode de paiement --</option>
                    <option value="caisse">Caisse</option>
                    <option value="cheque">Chèque</option>
                    <option value="transfert">Transfert</option>
                    <option value="credit">Crédit</option>
                </select>

                     <!-- Montant payé -->
                <div class="col-md-6">
                    <div class="form-group">
                    
                        <input type="number" name="montantPayer" id="montantPayer" class="form-input"  readonly style="background: white" value="{{$vente->montantTotal}}" >
                        <!-- <label class="form-label" for="montantPayer">Montant payé</label> -->
                    </div>
        
                    <button class="btn btn-success">💰 Marquer comme payé</button>
                </div>
          </form>
    
        @endif

    </div>
</div>
@endsection
