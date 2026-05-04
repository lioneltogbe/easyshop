<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            background: white;
        }

        .page {
            width: 90%;
            padding:36px;
        }

        /* ═══════════════════════════════════════
           EN-TÊTE : logo gauche / FACTURE droite
        ═══════════════════════════════════════ */
        .header-table {
            width: 100%;
            margin-bottom: 6px;
        }
        .header-table td { vertical-align: middle; }

        .logo-text {
            font-size: 28px;
            font-weight: bold;
            color: {{ $company->primary_color }};
        }
        .logo-text span { color: {{ $company->secondary_color }}; }
        .logo-tagline {
            font-size: 10px;
            color: #888;
            margin-top: 2px;
        }
        .facture-title {
            font-size: 34px;
            font-weight: bold;
            color: {{ $company->primary_color }};
            text-align: right;
        }

        /* Ligne de séparation sous l'en-tête */
        .divider {
            border: none;
            border-top: 2px solid #ddd;
            margin: 10px 0 16px 0;
        }

        /* ═══════════════════════════════════════
           BLOC : entreprise gauche / N° + date droite
        ═══════════════════════════════════════ */
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
        }
        .meta-table td { vertical-align: top; }
        .company-name {
            font-size: 13px;
            font-weight: bold;
            color: #111;
            margin-bottom: 2px;
        }
        .company-detail {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
        }
        .invoice-ref {
            text-align: right;
            font-size: 12px;
            color: #444;
            line-height: 1.9;
        }
        .invoice-ref strong {
            color: {{ $company->primary_color }};
        }

        /* Petite ligne sous meta */
        .divider-light {
            border: none;
            border-top: 1px solid #ddd;
            margin: 0 0 16px 0;
        }

        /* ═══════════════════════════════════════
           BLOC CLIENT
        ═══════════════════════════════════════ */
        .client-label {
            font-size: 12px;
            font-weight: bold;
            color: {{ $company->primary_color }};
            margin-bottom: 6px;
        }
        .client-name {
            font-size: 13px;
            font-weight: bold;
            color: #111;
            margin-bottom: 2px;
        }
        .client-detail {
            font-size: 11px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* ═══════════════════════════════════════
           TABLEAU ARTICLES
        ═══════════════════════════════════════ */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }
        /* En-tête : fond violet plein — background-color obligatoire pour DomPDF */
        .items-table thead tr {
            background-color: {{ $company->primary_color }};
            color: white;
        }
        .items-table th {
            padding: 10px 14px;
            font-weight: 600;
            font-size: 11px;
            text-align: left;
            letter-spacing: 0.3px;
        }
        .items-table th.right { text-align: right; }
        .items-table td {
            padding: 10px 14px;
            border-bottom: 1px solid #eee;
            color: #333;
            vertical-align: middle;
        }
        .items-table td.right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        .product-name { font-weight: 600; color: #111; }
        .product-code { font-size: 10px; color: #aaa; margin-top: 1px; }

        /* ═══════════════════════════════════════
           TOTAUX (alignés à droite)
        ═══════════════════════════════════════ */
        .totals-wrapper {
            width: 100%;
            margin-bottom: 20px;
        }
        .totals-wrapper td { vertical-align: top; }
        .totals-spacer { width: 55%; }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .totals-table td {
            padding: 7px 10px;
            border-bottom: 1px solid #eee;
            color: #444;
        }
        .totals-table td.label { text-align: left; }
        .totals-table td.amount {
            text-align: right;
            font-weight: 600;
            color: #111;
        }
        .totals-table tr.grand-total td {
            background-color: {{ $company->secondary_color }};
            color: white;
            font-weight: bold;
            font-size: 14px;
            border: none;
            padding: 11px 14px;
        }
        .totals-table tr.grand-total td.label {
            border-radius: 4px 0 0 4px;
        }
        .totals-table tr.grand-total td.amount {
            color: white;
            border-radius: 0 4px 4px 0;
        }

        /* ═══════════════════════════════════════
           STATUT : bande pleine largeur dégradé
           DomPDF ne supporte pas les dégradés CSS,
           on utilise background-color avec 2 colonnes
           pour simuler l'effet violet → cyan
        ═══════════════════════════════════════ */
        .status-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            border-radius: 4px;
            overflow: hidden;
        }
        .status-table td {
            background-color: {{ $company->primary_color }};
            color: white;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            padding: 14px;
            text-transform: uppercase;
        }

        /* ═══════════════════════════════════════
           QR CODE : aligné à droite
        ═══════════════════════════════════════ */
        .qr-table {
            width: 100%;
            margin-bottom: 16px;
        }
        .qr-table td { vertical-align: top; }
        .qr-cell {
            text-align: right;
            width: 100%;
        }
        .qr-cell svg {
            width: 100px;
            height: 100px;
        }

        /* ═══════════════════════════════════════
           FOOTER : une seule ligne horizontale
        ═══════════════════════════════════════ */
        .footer-table {
            width: 100%;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .footer-table td {
            font-size: 10px;
            color: #555;
            text-align: center;
            vertical-align: middle;
            white-space: nowrap;
        }
        .footer-table td strong { color: {{ $company->primary_color }}; }
        .footer-sep {
            color: #bbb;
            padding: 0 6px;
        }
    </style>
</head>
<body>
<div class="page">

     <!-- EN-TÊTE -->
    <table class="header-table">
        <tr>
            <td style="width:50%;">
                <div class="logo-text">easy<span>Shop</span></div>
                <div class="logo-tagline">Logiciel de Gestion Commerciale</div>
            </td>
            <td style="width:50%;">
                <div class="facture-title">FACTURE</div>
            </td>
        </tr>
    </table>

    <hr class="divider">

            <!-- INFORMATIONS ENTREPRISE ET CLIENT -->
    <table class="meta-table">
        <tr>
            <td style="width:50%;">
                <div class="company-name">{{ $company->name }}</div>
                <div class="company-detail">
                    {{ $company->address }}<br>
                    @if($company->tax_id)
                        <p  style="margin-top: 8px; font-size: 12px; color: #999;">
                            N° IFU : {{ $company->tax_id }}
                        </p>
                    @endif
                    @if($calculations['regime'] === 'normal')
                        ✓ RÉGIME NORMAL (TVA 18%)
                    @else
                        ✓ RÉGIME TPS (Pas de TVA)
                    @endif
                </div>
            </td>
            <td style="width:50%;">
                <div class="invoice-ref">
                    <strong>N° FACTURE :</strong> {{ $invoice->invoice_number }}<br>
                    <strong>DATE :</strong> {{ $invoice->invoice_date->format('d/m/Y') }}<br>
                    <strong>ÉCHÉANCE :</strong> {{ $invoice->due_date->format('d/m/Y') }}
                </div>
            </td>
        </tr>
    </table>

    <hr class="divider-light">

      <!-- CLIENT -->
    <div class="client-label">Client :</div>
    <div class="client-name">{{ $client->name }}</div>
    <div class="client-detail">
        {{ $client->address ?? '' }}
        @if($client->city ?? false) — {{ $client->city }} @endif
        @if($client->phone ?? false)<br>Tél : {{ $client->phone }} @endif
        @if($client->email ?? false)<br>{{ $client->email }}@endif
    </div>

    <!--  ARTICLES -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:45%;">Description</th>
                <th class="right" style="width:15%;">Quantité</th>
                <th class="right" style="width:20%;">Prix Unitaire</th>
                <th class="right" style="width:20%;">Remise</th>
                <th class="right" style="width:20%;">Montant HT</th>
                 @if($calculations['is_tva_applicable'])
                    <th class="text-right">TVA (18%) </th>
                    <th class="text-right">Montant TTC</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
                <tr>
                    <td>
                        <div class="product-name">{{ $item->product->name ?? 'Produit #' . $item->product_id }}</div>
                        @if(isset($item->product->code))
                            <div class="product-code">{{ $item->product->code }}</div>
                        @endif
                    </td>
                    <td class="right">
                        {{ number_format($item->quantity, 0, ',', ' ') }}
                        @if(isset($item->product->unitofmeasure))
                            <span style="font-size:10px;color:#aaa;"> {{ $item->product->unitofmeasure }}</span>
                        @endif
                    </td>
                    <td class="right">{{ number_format($item->unitPriceAtCommand, 0, ',', ' ') }} {{ $company->currency }}</td>
                    <td class="right">
                        @if($item->remise_montant > 0)
                            -{{ number_format($item->remise_montant, 0, ',', ' ') }} {{ $company->currency }}
                        @endif
                
                    <td class="right">
                        <strong>{{ number_format($item->quantity * $item->unitPriceAtCommand, 0, ',', ' ') }} {{ $company->currency }}</strong>
                    </td>

                      @if($calculations['is_tva_applicable'])
                            <td class="text-right">
                                @if($item->product->is_taxable)
                                    {{ number_format($item->montant_tva, 0, ',', ' ') }} FCFA
                                @else
                                    0 {{ $company->currency }}
                                @endif
                            </td>
 
                            <!-- Montant TTC -->
                            <td class="text-right amount">
                                {{ number_format($item->subtotal_ttc, 0, ',', ' ') }} {{ $company->currency }}
                            </td>
                        @endif
                </tr>

            @endforeach
        </tbody>
    </table>

        <!-- RÉSUMÉ FINANCIER -->
    <table class="totals-wrapper">
        <tr>
            <td class="totals-spacer"></td>
            <td style="width:45%;">
                <table class="totals-table">
                    <tr>
                        <td class="label">Montant Total HT :</td>
                        <td class="amount">{{ number_format($calculations['montant_ht'], 0, ',', ' ') }} {{ $company->currency }}</td>
                    </tr>
                    @if($calculations['remise'] > 0)
                        <tr>
                            <td class="label">Remise Total :</td>
                            <td class="amount"  style="color:#e53e3e;">-{{ number_format($calculations['remise'], 0, ',', ' ') }} {{ $company->currency }}</td>
                        </tr>
                        <tr>
                            <td class="label">Montant Total (après remise) :</td>
                            <td class="amount">{{ number_format($calculations['montant_ht_remise'], 0, ',', ' ') }} {{ $company->currency }}</td>
                        </tr>
                    @endif


                    @if($calculations['is_tva_applicable'] && $calculations['montant_tva'] > 0)
                        <tr>
                            <td class="label">TVA (18%) :</td>
                            <td class="amount">{{ number_format($calculations['montant_tva'], 0, ',', ' ') }} {{ $company->currency }}</td>
                        </tr>
                    @else
                       -
                    @endif
                    <tr class="grand-total">
                        <td class="label">Total TTC :</td>
                        <td class="amount">{{ number_format($calculations['montant_ttc'], 0, ',', ' ') }} {{ $company->currency }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

        <!-- CODE QR -->
    <table class="qr-table">
        <tr>
            <!-- <td class="qr-cell">
                {!! $qr_code_svg ?? '' !!}
            </td> -->

            <td class="qr-cell">
            @if(!empty($qr_code_svg))
                <img src="data:image/svg+xml;base64,{{ $qr_code_svg }}"
                    width="100" height="100"
                    style="display:block;margin-left:auto;">
            @endif
        </td>
        </tr>
    </table>
       <!-- STATUT DE PAIEMENT -->
    <table class="status-table">
        <tr>
            <td>{{ $invoice->status === 'PAYER' ? 'PAYÉE' : 'EN ATTENTE DE PAIEMENT' }}</td>
        </tr>
    </table>
    
        <!-- PIED DE PAGE -->
    <table class="footer-table">
        <tr>
            <td>
                <strong>{{ $company->name }}</strong>
                <span class="footer-sep">|</span>
                Tél : {{ $company->phone }}
                <span class="footer-sep">|</span>
                Email : {{ $company->email }}
                @if($company->website)
                    <span class="footer-sep">|</span>
                    {{ $company->website }}
                @endif
                @if($company->tax_id)
                    <span class="footer-sep">|</span>
                    N° IFU : {{ $company->tax_id }}
                @endif
            </td>
        </tr>
    </table>

</div>
</body>
</html>
