@extends('layouts.app')

@section('title', $categorie->name . ' - easyShop')

@section('content')
<style>
    .info-card { background:white; border-radius:.75rem; border:1px solid #E5E7EB; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,.06); }
    .sub-row { display:flex; align-items:center; justify-content:space-between; padding:.75rem 1rem; border-radius:.5rem; background:#F9FAFB; border:1px solid #F3F4F6; margin-bottom:.5rem; }
    .stat-pill { display:inline-block; padding:.2rem .75rem; border-radius:999px; font-size:.75rem; font-weight:700; }
    .breadcrumb-link { color:#7C3AED; text-decoration:none; font-weight:600; font-size:.875rem; }
    .breadcrumb-link:hover { color:#6D28D9; }
</style>

{{-- Breadcrumb --}}
<div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1.5rem;font-size:.875rem;">
    <a href="{{ route('categories.index') }}" class="breadcrumb-link">Catégories</a>
    @if($categorie->parent)
        <span style="color:#D1D5DB;">›</span>
        <a href="{{ route('categories.show', $categorie->parent->id) }}" class="breadcrumb-link">{{ $categorie->parent->name }}</a>
    @endif
    <span style="color:#D1D5DB;">›</span>
    <span style="color:#6B7280;">{{ $categorie->name }}</span>
</div>

{{-- Flash --}}
@if(session('success'))
<div style="background:#D1FAE5;color:#065F46;border:1px solid #A7F3D0;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;font-weight:500;">
    ✅ {{ session('success') }}
</div>
@endif
@if(session('erreur'))
<div style="background:#FEE2E2;color:#991B1B;border:1px solid #FECACA;padding:.875rem 1.25rem;border-radius:.5rem;margin-bottom:1.25rem;font-weight:500;">
    ❌ {{ session('erreur') }}
</div>
@endif

{{-- Header --}}
<div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;">
    <div>
        <h1 style="font-size:2rem;font-weight:700;">{{ $categorie->name }}</h1>
        @if($categorie->parent)
            <p style="color:#6B7280;font-size:.875rem;margin-top:.25rem;">
                Sous-catégorie de <strong>{{ $categorie->parent->name }}</strong>
            </p>
        @else
            <p style="color:#6B7280;font-size:.875rem;margin-top:.25rem;">Catégorie principale</p>
        @endif
        @if($categorie->description)
            <p style="color:#374151;margin-top:.5rem;max-width:600px;">{{ $categorie->description }}</p>
        @endif
    </div>
    <div style="display:flex;gap:.75rem;">
        <a href="{{ route('categories.edit', $categorie->id) }}" class="btn btn-primary">✏️ Éditer</a>
        <a href="{{ route('categories.create') }}?parent_id={{ $categorie->id }}" class="btn btn-secondary">+ Sous-catégorie</a>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;margin-bottom:2rem;">
    <div class="info-card" style="text-align:center;">
        <div style="font-size:1.75rem;font-weight:700;color:#7C3AED;">{{ $categorie->children->count() }}</div>
        <div style="font-size:.75rem;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:.5px;">Sous-catégories</div>
    </div>
    <div class="info-card" style="text-align:center;">
        <div style="font-size:1.75rem;font-weight:700;color:#10B981;">{{ $produits->total() }}</div>
        <div style="font-size:.75rem;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:.5px;">Produits directs</div>
    </div>
    <div class="info-card" style="text-align:center;">
        @php
            $totalProduits = $produits->total() + $categorie->children->sum(fn($c) => $c->products->count());
        @endphp
        <div style="font-size:1.75rem;font-weight:700;color:#F59E0B;">{{ $totalProduits }}</div>
        <div style="font-size:.75rem;font-weight:600;color:#6B7280;text-transform:uppercase;letter-spacing:.5px;">Total (avec sous-cat.)</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;">

    {{-- ── Sous-catégories ── --}}
    <div class="info-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-size:1.1rem;font-weight:700;">Sous-catégories</h2>
            <a href="{{ route('categories.create') }}?parent_id={{ $categorie->id }}" style="font-size:.8rem;color:#7C3AED;text-decoration:none;font-weight:600;">+ Ajouter</a>
        </div>

        @forelse($categorie->children as $child)
        <div class="sub-row">
            <div>
                <p style="font-weight:600;font-size:.9rem;">{{ $child->name }}</p>
                @if($child->description)
                    <p style="font-size:.75rem;color:#6B7280;">{{ Str::limit($child->description, 40) }}</p>
                @endif
            </div>
            <div style="display:flex;align-items:center;gap:.75rem;">
                <span class="stat-pill" style="background:#D1FAE5;color:#065F46;">{{ $child->products->count() }} prod.</span>
                <a href="{{ route('categories.show', $child->id) }}" style="font-size:.8rem;color:#7C3AED;font-weight:600;text-decoration:none;">Voir →</a>
            </div>
        </div>
        @empty
        <p style="color:#9CA3AF;font-size:.875rem;text-align:center;padding:1rem 0;font-style:italic;">Aucune sous-catégorie</p>
        @endforelse
    </div>

    {{-- ── Produits directs ── --}}
    <div class="info-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
            <h2 style="font-size:1.1rem;font-weight:700;">Produits de cette catégorie</h2>
        </div>

        {{-- Recherche produits --}}
        <form method="GET" style="margin-bottom:1rem;">
            <input type="text" name="search" class="form-input"
                   placeholder="Rechercher un produit..."
                   value="{{ request('search') }}"
                   style="margin-bottom:0;">
        </form>

        @if($produits->total() > 0)
        <p style="font-size:.75rem;color:#6B7280;margin-bottom:.5rem;">
            {{ $produits->firstItem() }}–{{ $produits->lastItem() }} sur {{ $produits->total() }} produits
        </p>
        @endif

        @forelse($produits as $produit)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:.65rem .75rem;border-radius:.5rem;background:#F9FAFB;border:1px solid #F3F4F6;margin-bottom:.4rem;">
            <div>
                <p style="font-weight:600;font-size:.875rem;">{{ $produit->name }}</p>
                <p style="font-size:.75rem;color:#6B7280;">
                    <code style="background:#E5E7EB;padding:.1rem .35rem;border-radius:.25rem;">{{ $produit->code }}</code>
                    · {{ number_format($produit->ventePrice, 0, ',', ' ') }} FCFA
                </p>
            </div>
            <a href="{{ route('produits.show', $produit->id) }}"
               style="font-size:.8rem;color:#7C3AED;font-weight:600;text-decoration:none;">Voir →</a>
        </div>
        @empty
        <p style="color:#9CA3AF;font-size:.875rem;text-align:center;padding:1rem 0;font-style:italic;">Aucun produit dans cette catégorie</p>
        @endforelse

        @if($produits->hasPages())
        <div style="margin-top:.75rem;">{{ $produits->links() }}</div>
        @endif

        @if($produits->total() === 0)
        <div style="margin-top:.75rem;text-align:center;">
            <a href="{{ route('produits.create') }}" style="font-size:.8rem;color:#7C3AED;font-weight:600;text-decoration:none;">
                + Ajouter un produit dans cette catégorie
            </a>
        </div>
        @endif
    </div>

</div>
@endsection
