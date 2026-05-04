@extends('layouts.app')

@section('title', 'Alertes de Stock - easyShop')

@section('content')
<style>
:root {
    --primary:   #6366F1;
    --success:   #10B981;
    --warning:   #F59E0B;
    --danger:    #EF4444;
    --gray-50:   #F9FAFB;
    --gray-100:  #F3F4F6;
    --gray-200:  #E5E7EB;
    --gray-500:  #6B7280;
    --gray-700:  #374151;
    --gray-900:  #111827;
    --radius:    0.75rem;
    --shadow:    0 4px 16px rgba(0,0,0,0.07);
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
    gap: 1rem;
}

/* ── KPI cards ── */
.kpi-row {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}
.kpi-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}
.kpi-icon {
    font-size: 2rem;
    width: 3rem;
    text-align: center;
    flex-shrink: 0;
}
.kpi-value { font-size: 1.75rem; font-weight: 700; line-height: 1; }
.kpi-label { font-size: 0.8rem; color: var(--gray-500); margin-top: 0.2rem; }

/* ── Filters bar ── */
.filters {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.filter-btn {
    padding: 0.4rem 1rem;
    border-radius: 999px;
    border: 2px solid var(--gray-200);
    background: white;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: all .15s;
    color: var(--gray-700);
}
.filter-btn:hover, .filter-btn.active { border-color: var(--primary); background: var(--primary); color: white; }
.filter-btn.f-critique.active  { border-color: var(--danger);  background: var(--danger); }
.filter-btn.f-moyen.active     { border-color: var(--warning); background: var(--warning); }
.filter-btn.f-peu.active       { border-color: var(--success); background: var(--success); }

/* ── Alert cards ── */
.alert-list { display: flex; flex-direction: column; gap: 0.75rem; }

.alert-card {
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-left: 5px solid var(--gray-200);
    transition: opacity .2s;
}
.alert-card.severity-critique  { border-left-color: var(--danger); }
.alert-card.severity-moyen     { border-left-color: var(--warning); }
.alert-card.severity-peu       { border-left-color: var(--success); }
/* .alert-card.is-read             { opacity: 0.55; } */

.alert-icon { font-size: 1.5rem; flex-shrink: 0; width: 2.5rem; text-align: center; }

.alert-body { flex: 1; min-width: 0; }
.alert-title {
    font-weight: 600;
    font-size: 0.95rem;
    color: var(--gray-900);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.alert-meta {
    font-size: 0.78rem;
    color: var(--gray-500);
    margin-top: 0.2rem;
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}
.alert-message {
    font-size: 0.85rem;
    color: var(--gray-700);
    margin-top: 0.35rem;
}

.badge-severity {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 700;
    color: white;
    flex-shrink: 0;
}
.bs-critique { background: var(--danger); }
.bs-moyen    { background: var(--warning); color: #1f2937; }
.bs-peu      { background: var(--success); }

.badge-read {
    padding: 0.2rem 0.6rem;
    border-radius: 999px;
    font-size: 0.68rem;
    font-weight: 600;
    background: var(--gray-100);
    color: var(--gray-500);
    flex-shrink: 0;
}

.actions { display: flex; gap: 0.5rem; flex-shrink: 0; }
.btn-icon {
    background: none;
    border: 1px solid var(--gray-200);
    border-radius: 0.5rem;
    padding: 0.35rem 0.65rem;
    font-size: 0.8rem;
    cursor: pointer;
    color: var(--gray-700);
    transition: all .15s;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
}
.btn-icon:hover { background: var(--gray-100); }
.btn-icon.danger:hover { background: #FEE2E2; border-color: var(--danger); color: var(--danger); }
.btn-icon.success:hover { background: #D1FAE5; border-color: var(--success); color: var(--success); }

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    color: var(--gray-500);
}
.empty-state .icon { font-size: 3rem; margin-bottom: 1rem; }

.toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
.toolbar-title { font-size: 0.85rem; color: var(--gray-500); }
</style>

{{-- ── HEADER ── --}}
<div class="page-header">
    <div>

        <h1 style="font-size:1.8rem;font-weight:700;margin-top:0.5rem;color:var(--gray-900);">⚠️ Alertes système</h1>
    </div>

    {{-- Bouton "Tout marquer lu" --}}
    @php $hasUnread = $alerts->whereNull('read_at')->count(); @endphp
    @if($hasUnread)
        <form action="{{ route('alertes.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-secondary" style="font-size:0.85rem;">
                ✅ Tout marquer comme lu ({{ $hasUnread }})
            </button>
        </form>
    @endif
</div>



{{-- Flash messages --}}
@if(session('success'))
    <div style="background:#D1FAE5;border:1px solid #10B981;color:#065F46;padding:0.875rem 1rem;border-radius:0.5rem;margin-bottom:1.25rem;">
        ✅ {{ session('success') }}
    </div>
@endif
@if($errors->any())
    <div style="background:#FEE2E2;border:1px solid #EF4444;color:#991B1B;padding:0.875rem 1rem;border-radius:0.5rem;margin-bottom:1.25rem;">
        ❌ {{ $errors->first() }}
    </div>
@endif

{{-- ── KPI ── --}}
@php
    $total     = $alerts->count();
    $unread    = $alerts->whereNull('read_at')->count();
    $critiques = $alerts->where('severity', 'critique')->count();
    $moyens    = $alerts->where('severity', 'moyen')->count();
@endphp

<div class="kpi-row">
    <div class="kpi-card">
        <div class="kpi-icon">🔔</div>
        <div>
            <div class="kpi-value" style="color:var(--primary);">{{ $total }}</div>
            <div class="kpi-label">Total alertes</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon">📬</div>
        <div>
            <div class="kpi-value" style="color:var(--warning);">{{ $unread }}</div>
            <div class="kpi-label">Non lues</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon">🚨</div>
        <div>
            <div class="kpi-value" style="color:var(--danger);">{{ $critiques }}</div>
            <div class="kpi-label">Critiques</div>
        </div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon">⚠️</div>
        <div>
            <div class="kpi-value" style="color:var(--warning);">{{ $moyens }}</div>
            <div class="kpi-label">Avertissements</div>
        </div>
    </div>
</div>

{{-- ── FILTRES ── --}}
<div class="filters" id="filterBar">
    <button class="filter-btn active"      onclick="filterAlerts('all', this)">Toutes ({{ $total }})</button>
    <button class="filter-btn"             onclick="filterAlerts('unread', this)">Non lues ({{ $unread }})</button>
    <button class="filter-btn f-critique"  onclick="filterAlerts('critique', this)">🚨 Critique ({{ $critiques }})</button>
    <button class="filter-btn f-moyen"     onclick="filterAlerts('moyen', this)">⚠️ Moyen ({{ $moyens }})</button>
    <button class="filter-btn f-peu"       onclick="filterAlerts('peu', this)">ℹ️ Information ({{ $alerts->where('severity','peu')->count() }})</button>
</div>

{{-- ── LISTE ── --}}
<div class="toolbar">
    <span class="toolbar-title" id="countLabel">{{ $total }} alerte(s) affichée(s)</span>
</div>

@if($alerts->isEmpty())
    <div class="empty-state">
        <div class="icon">🎉</div>
        <div style="font-size:1.1rem;font-weight:600;color:var(--gray-700);margin-bottom:0.5rem;">Aucune alerte</div>
        <div>Tout est en ordre. Aucune alerte de stock détectée.</div>
    </div>
@else
    <div class="alert-list" id="alertList">
        @foreach($alerts->sortByDesc(function($a){ return [$a->read_at === null ? 1 : 0, $a->created_at]; }) as $alert)
            @php
                $isRead = !is_null($alert->read_at);

                $icon = match($alert->type) {
                    'sortie_de_stock'    => '🔴',
                    'peu_stock'          => '🟡',
                    'expiration'         => '⏰',
                    'grande_transaction' => '💰',
                    'anomalie financier' => '⚠️',
                    default              => '🔔',
                };
            @endphp

            <div class="alert-card severity-{{ $alert->severity }} {{ $isRead ? 'is-read' : '' }}"
                 data-severity="{{ $alert->severity }}"
                 data-read="{{ $isRead ? '1' : '0' }}">

                <div class="alert-icon">{{ $icon }}</div>

                <div class="alert-body">
                    <div class="alert-title">{{ $alert->title }}</div>
                    <div class="alert-meta">
                        <span>{{ $alert->getTypeLabel() }}</span>
                        <span>·</span>
                        <span>{{ $alert->created_at?->diffForHumans() ?? '—' }}</span>
                        @if($alert->product)
                            <span>·</span>
                            <a href="{{ route('stocks.produit', $alert->product_id) }}"
                               style="color:var(--primary);text-decoration:none;">
                                {{ $alert->product->name }}
                            </a>
                        @endif
                        @if($alert->creator)
                            <span>·</span>
                            <span>Par {{ $alert->creator->name }}</span>
                        @endif
                    </div>
                    <div class="alert-message">{{ $alert->message }}</div>
                </div>

                {{-- Badges --}}
                <span class="badge-severity bs-{{ $alert->severity }}">
                    {{ $alert->getSeverityLabel() }}
                </span>

                @if($isRead)
                    <span class="badge-read">✔ Lu</span>
                @endif

                {{-- Actions --}}
                <div class="actions">
                    @if(!$isRead)
                        <form action="{{ route('alertes.markAsRead', $alert->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-icon success" title="Marquer comme lu">
                                ✅ Lu
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('alertes.delete', $alert->id) }}" method="POST"
                          onsubmit="return confirm('Supprimer cette alerte ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-icon danger" title="Supprimer">
                            🗑
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

<script>
function filterAlerts(filter, btn) {
    // Mise à jour des boutons actifs
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    const cards = document.querySelectorAll('#alertList .alert-card');
    let visible = 0;

    cards.forEach(card => {
        let show = false;
        if (filter === 'all')               show = true;
        else if (filter === 'unread')       show = card.dataset.read === '0';
        else                                show = card.dataset.severity === filter;

        card.style.display = show ? '' : 'none';
        if (show) visible++;
    });

    document.getElementById('countLabel').textContent = visible + ' alerte(s) affichée(s)';
}
</script>
@endsection
