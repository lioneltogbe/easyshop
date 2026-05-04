@extends('layouts.app')

@section('title', $product->name . ' - easyShop')

@section('content')

    <style>
        /* Page Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .page-header-content {
            flex: 1;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 500;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .back-link:hover {
            gap: 0.75rem;
            color: var(--primary-dark);
        }

        .back-link svg {
            width: 16px;
            height: 16px;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-600);
            font-size: 0.875rem;
        }

        .page-subtitle svg {
            width: 16px;
            height: 16px;
        }

        .code-badge {
            font-family: 'Courier New', monospace;
            background: var(--gray-100);
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .page-header-actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .inline-form {
            display: inline-block;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .btn svg {
            width: 18px;
            height: 18px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Product Grid Layout */
        .product-grid {
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 1.5rem;
        }

        .product-main {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        /* Info Card */
        .info-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
        }

        .card-header svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }

        .card-header h2,
        .card-header h3 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--gray-900);
            margin: 0;
        }

        .badge-count {
            margin-left: auto;
            background: var(--primary);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Info Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .info-item label {
            display: block;
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .info-value {
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-900);
        }

        .category-badge {
            display: inline-block;
            background: linear-gradient(135deg, var(--primary-light) 0%, var(--primary) 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .description-section {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--gray-200);
        }

        .description-section label {
            display: block;
            font-size: 0.875rem;
            color: var(--gray-600);
            font-weight: 500;
            margin-bottom: 0.75rem;
        }

        .description-text {
            color: var(--gray-700);
            line-height: 1.6;
        }

        /* Pricing Grid */
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .price-card {
            background: var(--price-bg);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            border: 2px solid var(--price-border);
            position: relative;
            overflow: hidden;
        }

        .price-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--price-color);
        }

        .price-card.purchase {
            --price-bg: #eff6ff;
            --price-border: #bfdbfe;
            --price-color: #3b82f6;
        }

        .price-card.sale {
            --price-bg: #f0fdf4;
            --price-border: #bbf7d0;
            --price-color: #10b981;
        }

        .price-card.margin {
            --price-bg: #faf5ff;
            --price-border: #e9d5ff;
            --price-color: #a855f7;
        }

        .price-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--price-color);
            border-radius: 12px;
            color: white;
        }

        .price-icon svg {
            width: 24px;
            height: 24px;
        }

        .price-card label {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-600);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .price-value {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--price-color);
            line-height: 1;
        }

        .price-currency {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
            font-weight: 600;
        }

        .margin-percentage {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--price-color);
            padding: 0.25rem 0.75rem;
            background: white;
            border-radius: 9999px;
            display: inline-block;
        }

        /* Alerts Grid */
        .alerts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .alert-box {
            background: var(--alert-bg);
            border: 2px solid var(--alert-border);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .alert-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--alert-color);
        }

        .alert-box.warning {
            --alert-bg: #fffbeb;
            --alert-border: #fde68a;
            --alert-color: #f59e0b;
        }

        .alert-box.danger {
            --alert-bg: #fef2f2;
            --alert-border: #fecaca;
            --alert-color: #ef4444;
        }

        .alert-icon {
            width: 40px;
            height: 40px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--alert-color);
            border-radius: 10px;
            color: white;
        }

        .alert-icon svg {
            width: 20px;
            height: 20px;
        }

        .alert-box label {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-600);
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .alert-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--alert-color);
        }

        .alert-unit {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
        }

        /* Movements List */
        .movements-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .movement-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--gray-50);
            border-radius: 12px;
            border: 1px solid var(--gray-200);
            transition: all 0.2s;
        }

        .movement-item:hover {
            background: white;
            border-color: var(--primary);
            box-shadow: var(--shadow);
        }

        .batch-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }

        .batch-table th,
        .batch-table td {
            padding: 0.9rem 1rem;
            border: 1px solid var(--gray-100);
            text-align: left;
            font-size: 0.95rem;
            color: var(--gray-700);
        }

        .batch-table th {
            background: var(--gray-50);
            color: var(--gray-600);
            font-weight: 700;
        }

        .batch-status {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .batch-status.expired {
            background: #fee2e2;
            color: #b91c1c;
        }

        .batch-status.soon {
            background: #fffbeb;
            color: #92400e;
        }

        .batch-status.ok {
            background: #ecfdf5;
            color: #047857;
        }

        .batch-form {
            display: grid;
            grid-template-columns: repeat(3, minmax(180px, 1fr));
            gap: 1rem;
            align-items: end;
            margin-top: 1rem;
        }

        .batch-form input {
            width: 100%;
            padding: 0.85rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 0.75rem;
            background: white;
            color: var(--gray-900);
        }

        .batch-form button {
            border: none;
            border-radius: 0.75rem;
            padding: 0.95rem 1rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            font-weight: 700;
            cursor: pointer;
        }

        .batch-form button:hover {
            transform: translateY(-1px);
        }

        .batch-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .batch-card .card-header {
            background: var(--gray-50);
        }

        .movement-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .movement-icon svg {
            width: 20px;
            height: 20px;
            color: white;
        }

        .movement-icon.in {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .movement-icon.out {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .movement-icon.adjust {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        }

        .movement-content {
            flex: 1;
        }

        .movement-type {
            margin-bottom: 0.25rem;
        }

        .type-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .type-badge.in {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .type-badge.out {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .type-badge.adjust {
            background: rgba(99, 102, 241, 0.1);
            color: #6366f1;
        }

        .movement-reason {
            font-size: 0.875rem;
            color: var(--gray-700);
            margin-bottom: 0.25rem;
        }

        .movement-date {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .movement-quantity {
            text-align: right;
        }

        .quantity-value {
            display: block;
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .quantity-value.positive {
            color: #10b981;
        }

        .quantity-value.negative {
            color: #ef4444;
        }

        .quantity-value.neutral {
            color: var(--gray-700);
        }

        .quantity-unit {
            display: block;
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        /* Stock Card */
        .stock-card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-100);
            overflow: hidden;
        }

        .stock-card.sticky {
            position: sticky;
            top: 100px;
        }

        .stock-indicator {
            padding: 2rem 1.5rem;
        }

        .stock-badge {
            background: var(--stock-bg);
            border: 2px solid var(--stock-border);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stock-badge::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--stock-color);
        }

        .stock-badge.outofstock {
            --stock-bg: #fef2f2;
            --stock-border: #fecaca;
            --stock-color: #ef4444;
        }

        .stock-badge.lowstock {
            --stock-bg: #fffbeb;
            --stock-border: #fde68a;
            --stock-color: #f59e0b;
        }

        .stock-badge.normalstock {
            --stock-bg: #f0fdf4;
            --stock-border: #bbf7d0;
            --stock-color: #10b981;
        }

        .stock-icon {
            width: 48px;
            height: 48px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--stock-color);
            border-radius: 12px;
            color: white;
        }

        .stock-icon svg {
            width: 24px;
            height: 24px;
        }

        .stock-value {
            font-size: 3rem;
            font-weight: 700;
            color: var(--stock-color);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .stock-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--stock-color);
        }

        /* Stock Details */
        .stock-details {
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .stock-detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }

        .stock-detail-item.divider {
            padding-top: 1rem;
            border-top: 1px solid var(--gray-200);
        }

        .detail-label {
            color: var(--gray-600);
        }

        .detail-value {
            font-weight: 700;
            color: var(--gray-900);
        }

        .detail-value.positive {
            color: #10b981;
        }

        .detail-value.negative {
            color: #ef4444;
        }

        /* Stock Actions */
        .stock-actions {
            padding: 1.5rem;
            border-top: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .stock-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.3s;
        }

        .stock-btn svg {
            width: 18px;
            height: 18px;
        }

        .stock-btn.entry {
            background: #10b981;
            color: white;
        }

        .stock-btn.entry:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .stock-btn.exit {
            background: #ef4444;
            color: white;
        }

        .stock-btn.exit:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .product-grid {
                grid-template-columns: 1fr;
            }

            .stock-card.sticky {
                position: relative;
                top: 0;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
            }

            .page-header-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="container">
        <!-- Header Section -->
        <div class="page-header">
            <div class="page-header-content">
                <a href="{{ route('produits.index') }}" class="back-link">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à la liste
                </a>
                <h1 class="page-title">{{ $product->name }}</h1>
                <p class="page-subtitle">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                    </svg>
                    Code: <span class="code-badge">{{ $product->code }}</span>
                </p>
            </div>
            <div class="page-header-actions">
                @if(session('success'))
                    <div style="width:100%;margin-bottom:1rem;padding:1rem;border-radius:0.75rem;background:#ecfdf5;border:1px solid #d1fae5;color:#054f31;">
                        ✅ {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="width:100%;margin-bottom:1rem;padding:1rem;border-radius:0.75rem;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;">
                        ❌ {{ session('error') }}
                    </div>
                @endif
                @can('edit-product')
                    <a href="{{ route('produits.edit', $product->id) }}" class="btn btn-primary">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Éditer
                    </a>
                @endcan

       
                <form action="{{ route('produits.destroy', $product->id) }}" method="POST" class="inline-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                    @csrf
                    @method('DELETE')
                    @can('delete-product')
                        <button type="submit" class="btn btn-danger">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Supprimer
                        </button>
                    @endcan
                </form>
             
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="product-grid">
            <!-- Left Column -->
            <div class="product-main">
                <!-- General Information Card -->
                <div class="info-card">
                    <div class="card-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2>Informations Générales</h2>
                    </div>
                    <div class="card-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <label>Catégorie</label>
                                <div class="info-value">
                                    <span class="category-badge">{{ $product->category->name ?? 'Non catégorisé' }}</span>
                                </div>
                            </div>
                            <div class="info-item">
                                <label>Unité de Mesure</label>
                                <div class="info-value">{{ $product->unitofmeasure }}</div>
                            </div>
                        </div>
                        @if($product->description)
                            <div class="description-section">
                                <label>Description</label>
                                <p class="description-text">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Pricing Card -->
                <div class="info-card">
                    <div class="card-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h2>Tarification</h2>
                    </div>
                    <div class="card-body">
                        <div class="pricing-grid">
                            <div class="price-card purchase">
                                <div class="price-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                    </svg>
                                </div>
                                <label>Prix d'Achat</label>
                                <div class="price-value">{{ number_format($product->achatPrice, 0, ',', ' ') }}</div>
                                <span class="price-currency">FCFA</span>
                            </div>
                            <div class="price-card sale">
                                <div class="price-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <label>Prix de Vente</label>
                                <div class="price-value">{{ number_format($product->ventePrice, 0, ',', ' ') }}</div>
                                <span class="price-currency">FCFA</span>
                            </div>
                            <div class="price-card margin">
                                <div class="price-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <label>Marge Bénéficiaire</label>
                                <div class="price-value">{{ number_format($product->ventePrice - $product->achatPrice, 0, ',', ' ') }}</div>
                                <span class="price-currency">FCFA</span>
                                <div class="margin-percentage">
                                    {{ round(((($product->ventePrice - $product->achatPrice) / $product->achatPrice) * 100), 2) }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerts and Thresholds Card -->
                <div class="info-card">
                    <div class="card-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h2>Alertes et Seuils</h2>
                    </div>
                    <div class="card-body">
                        <div class="alerts-grid">
                            <div class="alert-box warning">
                                <div class="alert-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <label>Seuil d'Alerte Stock</label>
                                <div class="alert-value">{{ $product->alertStockLevel }}</div>
                                <span class="alert-unit">{{ $product->unitofmeasure }}</span>
                            </div>
                            @if($product->alertDaybeforeExpiration)
                                <div class="alert-box danger">
                                    <div class="alert-icon">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <label>Alerte Expiration</label>
                                    <div class="alert-value">{{ $product->alertDaybeforeExpiration }}</div>
                                    <span class="alert-unit">jours avant</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Lots / Batches Card -->
                <div class="info-card batch-card">
                    <div class="card-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/>
                        </svg>
                        <h2>Lots / Batches</h2>
                        <span class="badge-count">{{ $product->batches->count() }}</span>
                    </div>
                    <div class="card-body">
                        <p style="margin-bottom:1rem;color:var(--gray-600);">Un produit reste unique, mais chaque entrée peut être enregistrée par lot avec quantité et expiration.</p>

                        @if($product->batches->isEmpty())
                            <div style="padding:1.25rem;border-radius:0.75rem;background:var(--gray-50);color:var(--gray-600);">
                                Aucun lot n’a encore été créé pour ce produit.
                            </div>
                        @else
                            <table class="batch-table">
                                <thead>
                                    <tr>
                                        <th>Lot</th>
                                        <th>Quantité</th>
                                        <th>Expiration</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->batches->sortBy('dayExpiration') as $batch)
                                        <tr>
                                            <td>{{ $batch->batchNumber }}</td>
                                            <td>{{ $batch->quantity }} {{ $product->unitofmeasure }}</td>
                                            <td>{{ $batch->dayExpiration ? $batch->dayExpiration->format('d/m/Y') : 'Aucune' }}</td>
                                            <td>
                                                @if($batch->checkExpiration())
                                                    <span class="batch-status expired">Expiré</span>
                                                @elseif($batch->isExpiringWithin($product->alertDaybeforeExpiration ?? 30))
                                                    <span class="batch-status soon">Bientôt expiré</span>
                                                @else
                                                    <span class="batch-status ok">Valide</span>
                                                @endif
                                            </td>
                                            <td>
                                                <form action="{{ route('produits.batches.destroy', ['product' => $product->id, 'batch' => $batch->id]) }}" method="POST" onsubmit="return confirm('Supprimer ce lot ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" style="padding:0.55rem 0.85rem;font-size:0.8rem;">Supprimer</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                        <form action="{{ route('produits.batches.store', $product) }}" method="POST" class="batch-form">
                            @csrf
                            <div>
                                <label for="batchNumber" style="display:block;font-size:0.85rem;color:var(--gray-600);margin-bottom:0.35rem;">Référence de lot</label>
                                <input type="text" id="batchNumber" name="batchNumber" value="{{ old('batchNumber') }}" placeholder="LOT-2026-001" required>
                            </div>
                            <div>
                                <label for="quantity" style="display:block;font-size:0.85rem;color:var(--gray-600);margin-bottom:0.35rem;">Quantité</label>
                                <input type="number" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="100" step="0.01" required>
                            </div>
                            <div>
                                <label for="dayExpiration" style="display:block;font-size:0.85rem;color:var(--gray-600);margin-bottom:0.35rem;">Date d’expiration</label>
                                <input type="date" id="dayExpiration" name="dayExpiration" value="{{ old('dayExpiration') }}">
                            </div>
                            <button type="submit">Ajouter un lot</button>
                        </form>
                    </div>
                </div>

                <!-- Movement History Card -->
                @if($movements && count($movements) > 0)
                    <div class="info-card">
                        <div class="card-header">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <h2>Historique des Mouvements</h2>
                            <span class="badge-count">{{ count($movements->take(5)) }}</span>
                        </div>
                        <div class="card-body">
                            <div class="movements-list">
                                @foreach($movements->take(5) as $movement)
                                    <div class="movement-item">
                                        <div class="movement-icon {{ strtolower($movement->type) }}">
                                            @if($movement->type == 'IN')
                                                <svg fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                                                </svg>
                                            @elseif($movement->type == 'OUT')
                                                <svg fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 4l1.41 1.41L7.83 11H20v2H7.83l5.58 5.59L12 20l-8-8z"/>
                                                </svg>
                                            @else
                                                <svg fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="movement-content">
                                            <div class="movement-type">
                                                @if($movement->type == 'IN')
                                                    <span class="type-badge in">Entrée Stock</span>
                                                @elseif($movement->type == 'OUT')
                                                    <span class="type-badge out">Sortie Stock</span>
                                                @else
                                                    <span class="type-badge adjust">Ajustement</span>
                                                @endif
                                            </div>
                                            <p class="movement-reason">{{ $movement->reason }}</p>
                                            <p class="movement-date">{{ $movement->created_at->format('d/m/Y à H:i') }}</p>
                                        </div>
                                        <div class="movement-quantity">
                                            <span class="quantity-value {{ $movement->type == 'IN' ? 'positive' : ($movement->type == 'OUT' ? 'negative' : 'neutral') }}">
                                                {{ $movement->type == 'IN' ? '+' : ($movement->type == 'OUT' ? '-' : '') }}{{ $movement->quantity }}
                                            </span>
                                            <span class="quantity-unit">{{ $product->unitofmeasure }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Sidebar -->
            <div class="product-sidebar">
                <div class="stock-card sticky">
                    <div class="card-header">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <h3>Stock Actuel</h3>
                    </div>

                    <!-- Stock Status Indicator -->
                    <div class="stock-indicator">
                        @if($product->isOutOfStock())
                            <div class="stock-badge outofstock">
                                <div class="stock-icon">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                                    </svg>
                                </div>
                                <div class="stock-value">0</div>
                                <div class="stock-label">Rupture de Stock</div>
                            </div>
                        @elseif($product->isLowStock())
                            <div class="stock-badge lowstock">
                                <div class="stock-icon">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z"/>
                                    </svg>
                                </div>
                                <div class="stock-value">{{ $currentStock }}</div>
                                <div class="stock-label">Stock Faible</div>
                            </div>
                        @else
                            <div class="stock-badge normalstock">
                                <div class="stock-icon">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M9 16.2L4.8 12l-1.4 1.4L9 19 21 7l-1.4-1.4L9 16.2z"/>
                                    </svg>
                                </div>
                                <div class="stock-value">{{ $currentStock }}</div>
                                <div class="stock-label">Stock Normal</div>
                            </div>
                        @endif
                    </div>

                    <!-- Stock Details -->
                    <div class="stock-details">
                        <div class="stock-detail-item">
                            <span class="detail-label">Stock Actuel</span>
                            <span class="detail-value">{{ $currentStock }} {{ $product->unitofmeasure }}</span>
                        </div>
                        <div class="stock-detail-item">
                            <span class="detail-label">Seuil d'Alerte</span>
                            <span class="detail-value">{{ $product->alertStockLevel }} {{ $product->unitofmeasure }}</span>
                        </div>
                        <div class="stock-detail-item divider">
                            <span class="detail-label">Différence</span>
                            <span class="detail-value {{ ($currentStock - $product->alertStockLevel) < 0 ? 'negative' : 'positive' }}">
                                {{ $currentStock - $product->alertStockLevel }} {{ $product->unitofmeasure }}
                            </span>
                        </div>
                    </div>

                    <!-- Stock Actions -->
                    <div class="stock-actions">
                        @can('view-stock')
                            <a href="{{ route('stocks.entree') }}?product_id={{ $product->id }}" class="stock-btn entry">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                </svg>
                                Entrée Stock
                            </a>
                            <a href="{{ route('stocks.sortie') }}?product_id={{ $product->id }}" class="stock-btn exit">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                </svg>
                                Sortie Stock
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection