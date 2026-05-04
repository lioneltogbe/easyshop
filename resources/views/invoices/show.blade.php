@extends('layouts.app')

@section('title', 'Facture ' . $invoice->invoice_number . ' - easyShop')

@section('content')

{{-- Flash messages --}}
@if(session('success'))
    <div style="background:#D1FAE5;border:1px solid #10B981;color:#065F46;padding:0.875rem 1rem;border-radius:0.5rem;margin-bottom:1.25rem;">
        ✅ {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#FEE2E2;border:1px solid #EF4444;color:#991B1B;padding:0.875rem 1rem;border-radius:0.5rem;margin-bottom:1.25rem;">
        ❌ {{ session('error') }}
    </div>
@endif

 <a href="{{ url()->previous() }}" class="btn btn-secondary" style="padding:0.5rem 1rem;font-size:0.875rem;">
            ← Retour
</a>

{{-- ── BARRE D'ACTIONS ─────────────────────────────────── --}}
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:0.75rem;">
    <div style="display:flex;align-items:center;gap:0.75rem;">
       
        <h1 style="font-size:1.5rem;font-weight:700;color:#1F2937;">
            Facture <span style="color:#6366F1;">{{ $invoice->invoice_number }}</span>
        </h1>
        {{-- Badge statut --}}
        @php
            $statusColor = match($invoice->status) {
                'GENERATED' => ['bg'=>'#EEF2FF','text'=>'#4338CA','label'=>'Générée'],
                'SENT'      => ['bg'=>'#FEF3C7','text'=>'#92400E','label'=>'Envoyée'],
                'PAID'      => ['bg'=>'#D1FAE5','text'=>'#065F46','label'=>'Payée'],
                'DRAFT'     => ['bg'=>'#F3F4F6','text'=>'#374151','label'=>'Brouillon'],
                default     => ['bg'=>'#F3F4F6','text'=>'#374151','label'=>$invoice->status],
            };
        @endphp
        <span style="background:{{ $statusColor['bg'] }};color:{{ $statusColor['text'] }};padding:0.3rem 0.85rem;border-radius:999px;font-size:0.8rem;font-weight:600;">
            {{ $statusColor['label'] }}
        </span>
    </div>

    <div style="display:flex;gap:0.5rem;flex-wrap:wrap;">
        {{-- Télécharger PDF --}}
        @can('download-invoice')
            @if($invoice->pdf_path)
                <a href="{{ route('invoices.download', $invoice->id) }}"
                   class="btn btn-primary"
                   style="padding:0.5rem 1.25rem;font-size:0.875rem;">
                    ⬇️ Télécharger PDF
                </a>
            @endif
        @endcan

        {{-- Lien vers la commande --}}
        @can('view-vente')
            <a href="{{ route('ventes.show', $invoice->vente_command_id) }}"
               class="btn btn-secondary"
               style="padding:0.5rem 1.25rem;font-size:0.875rem;">
                🧾 Voir la commande
            </a>
        @endcan
    </div>
</div>

{{-- ── CORPS DE LA FACTURE ─────────────────────────────── --}}
<div style="background:white;border-radius:1rem;box-shadow:0 4px 24px rgba(0,0,0,0.08);overflow:hidden;max-width:900px;margin:0 auto;">

    {{-- En-tête coloré --}}
    <div style="background:linear-gradient(135deg,{{ $company->primary_color }},{{ $company->secondary_color }});padding:2rem 2.5rem;display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
        <div>
            <div style="font-size:1.75rem;font-weight:800;color:white;letter-spacing:-0.5px;">
                easy<span style="opacity:0.75;">Shop</span>
            </div>
            <div style="color:rgba(255,255,255,0.85);font-size:0.875rem;margin-top:0.25rem;">
                {{ $company->name }}
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:2rem;font-weight:800;color:white;letter-spacing:2px;">FACTURE</div>
            <div style="color:rgba(255,255,255,0.9);font-size:1rem;font-weight:600;margin-top:0.25rem;">
                {{ $invoice->invoice_number }}
            </div>
        </div>
    </div>

    <div style="padding:2rem 2.5rem;">

        {{-- Infos entreprise / client --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;margin-bottom:2rem;">
            {{-- Entreprise --}}
            <div>
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9CA3AF;margin-bottom:0.5rem;">
                    Vendeur
                </div>
                <div style="font-weight:700;color:#111827;margin-bottom:0.2rem;">{{ $company->name }}</div>
                <div style="font-size:0.875rem;color:#6B7280;line-height:1.7;">
                    {{ $company->address }}<br>
                    {{ $company->phone }}<br>
                    {{ $company->email }}<br>
                    @if($company->tax_id)
                        N° fiscal : {{ $company->tax_id }}
                    @endif
                    @if($calculations['regime'] === 'normal')
                        ✓ RÉGIME NORMAL (TVA 18%)
                    @else
                        ✓ RÉGIME TPS (Pas de TVA)
                    @endif
                </div>
            </div>

            {{-- Client --}}
            <div>
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1.5px;color:#9CA3AF;margin-bottom:0.5rem;">
                   Client 
                </div>
                <div style="font-weight:700;color:#111827;margin-bottom:0.2rem;">{{ $client->name }}</div>
                <div style="font-size:0.875rem;color:#6B7280;line-height:1.7;">
                    {{ $client->address ?? 'Adresse non renseignée' }}<br>
                    {{ $client->phone ?? '' }}<br>
                    {{ $client->email ?? '' }}
                </div>
            </div>
        </div>

        {{-- Dates --}}
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1rem;background:#F9FAFB;border-radius:0.75rem;padding:1.25rem;margin-bottom:2rem;">
            <div style="text-align:center;">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9CA3AF;margin-bottom:0.25rem;">Date de facture</div>
                <div style="font-weight:600;color:#111827;">{{ $invoice->invoice_date->format('d/m/Y') }}</div>
            </div>
            <div style="text-align:center;border-left:1px solid #E5E7EB;border-right:1px solid #E5E7EB;">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9CA3AF;margin-bottom:0.25rem;">Échéance</div>
                <div style="font-weight:600;color:#111827;">{{ $invoice->due_date ? $invoice->due_date->format('d/m/Y') : '—' }}</div>
            </div>
            <div style="text-align:center;">
                <div style="font-size:0.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:#9CA3AF;margin-bottom:0.25rem;">Commande liée</div>
                <div style="font-weight:600;color:#6366F1;">#{{ $invoice->vente_command_id }}</div>
            </div>
        </div>

        {{-- Tableau des articles --}}
        <table style="width:100%;border-collapse:collapse;margin-bottom:1.5rem;font-size:0.875rem;">
            <thead>
                <tr style="background:linear-gradient(135deg,{{ $company->primary_color }},{{ $company->secondary_color }});color:white;">
                    <th style="padding:0.875rem 1rem;text-align:left;font-weight:600;border-radius:0.5rem 0 0 0;">Désignation</th>
                    <th style="padding:0.875rem 1rem;text-align:center;font-weight:600;">Qté</th>
                    <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;">Prix unitaire</th>
                    <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;">Remise</th>
                    <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;">Montant HT</th>
                    @if($calculations['is_tva_applicable'])
                        <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;">TVA (18%)</th>
                        <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;border-radius:0 0.5rem 0.5rem 0;">Montant TTC</th>
                    @else
                        <th style="padding:0.875rem 1rem;text-align:right;font-weight:600;border-radius:0 0.5rem 0.5rem 0;">Total</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach($items as $index => $item)
                    <tr style="background:{{ $index % 2 === 0 ? '#FAFAFA' : 'white' }};border-bottom:1px solid #F3F4F6;">
                        <td style="padding:0.875rem 1rem;">
                            <div style="font-weight:600;color:#111827;">{{ $item->product->name ?? 'Produit #' . $item->product_id }}</div>
                            @if($item->product && $item->product->code)
                                <div style="font-size:0.75rem;color:#9CA3AF;">{{ $item->product->code }}</div>
                            @endif
                        </td>
                        <td style="padding:0.875rem 1rem;text-align:center;color:#374151;">
                            {{ number_format($item->quantity, 0, ',', ' ') }}
                            @if($item->product) <span style="font-size:0.75rem;color:#9CA3AF;">{{ $item->product->unitofmeasure }}</span> @endif
                        </td>
                        <td style="padding:0.875rem 1rem;text-align:right;color:#374151;">
                            {{ number_format($item->unitPriceAtCommand, 0, ',', ' ') }} {{ $company->currency }}
                        </td>
                        <td style="padding:0.875rem 1rem;text-align:right;color:#e53e3e;">
                            @if($item->remise_montant > 0)
                                -{{ number_format($item->remise_montant, 0, ',', ' ') }} {{ $company->currency }}
                            @else
                                -
                            @endif
                        </td>
                        <td style="padding:0.875rem 1rem;text-align:right;color:#374151;">
                            {{ number_format($item->quantity * $item->unitPriceAtCommand, 0, ',', ' ') }} {{ $company->currency }}
                        </td>
                        @if($calculations['is_tva_applicable'])
                            <td style="padding:0.875rem 1rem;text-align:right;color:#374151;">
                                @if($item->product->is_taxable ?? true)
                                    {{ number_format($item->montant_tva, 0, ',', ' ') }} {{ $company->currency }}
                                @else
                                    0 {{ $company->currency }}
                                @endif
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:right;font-weight:700;color:#111827;">
                                {{ number_format($item->subtotal_ttc, 0, ',', ' ') }} {{ $company->currency }}
                            </td>
                        @else
                            <td style="padding:0.875rem 1rem;text-align:right;font-weight:700;color:#111827;">
                                {{ number_format($item->quantity * $item->unitPriceAtCommand - $item->remise_montant, 0, ',', ' ') }} {{ $company->currency }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Résumé financier + QR code --}}
        <div style="display:grid;grid-template-columns:1fr auto;gap:2rem;align-items:end;flex-wrap:wrap;">

            {{-- QR code --}}
            <div style="display:flex;flex-direction:column;align-items:flex-start;gap:0.5rem;">
                @if($invoice->qr_code_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->qr_code_path))
                    <div style="border:2px solid #E5E7EB;border-radius:0.75rem;padding:0.75rem;background:white;display:inline-block;">
                        {!! Storage::disk('public')->get($invoice->qr_code_path) !!}

                    </div>
                @endif
                <div style="font-size:0.7rem;color:#9CA3AF;text-align:center;max-width:120px;">
                    Scanner pour vérifier l'authenticité
                </div>
            </div>

            {{-- Totaux --}}
            <div style="min-width:280px;">
                <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid #F3F4F6;font-size:0.875rem;">
                    <span style="color:#6B7280;">Montant Total HT</span>
                    <span style="font-weight:600;color:#111827;">{{ number_format($calculations['montant_ht'], 0, ',', ' ') }} {{ $company->currency }}</span>
                </div>
                @if($calculations['remise'] > 0)
                    <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid #F3F4F6;font-size:0.875rem;">
                        <span style="color:#6B7280;">Remise Total</span>
                        <span style="font-weight:600;color:#EF4444;">-{{ number_format($calculations['remise'], 0, ',', ' ') }} {{ $company->currency }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid #F3F4F6;font-size:0.875rem;">
                        <span style="color:#6B7280;">Montant HT après remise</span>
                        <span style="font-weight:600;color:#111827;">{{ number_format($calculations['montant_ht_remise'], 0, ',', ' ') }} {{ $company->currency }}</span>
                    </div>
                @endif
                @if($calculations['is_tva_applicable'] && $calculations['montant_tva'] > 0)
                    <div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid #F3F4F6;font-size:0.875rem;">
                        <span style="color:#6B7280;">TVA (18%)</span>
                        <span style="font-weight:600;color:#111827;">{{ number_format($calculations['montant_tva'], 0, ',', ' ') }} {{ $company->currency }}</span>
                    </div>
                @endif
                {{-- Total final --}}
                <div style="display:flex;justify-content:space-between;padding:1rem 1.25rem;margin-top:0.5rem;background:linear-gradient(135deg,{{ $company->primary_color }},{{ $company->secondary_color }});border-radius:0.75rem;color:white;">
                    <span style="font-weight:700;font-size:1rem;">Total TTC</span>
                    <span style="font-weight:800;font-size:1.1rem;">{{ number_format($calculations['montant_ttc'], 0, ',', ' ') }} {{ $company->currency }}</span>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        @if($invoice->notes)
            <div style="margin-top:2rem;background:#FFFBEB;border:1px solid #FDE68A;border-radius:0.75rem;padding:1rem 1.25rem;">
                <div style="font-size:0.75rem;font-weight:700;color:#92400E;text-transform:uppercase;letter-spacing:1px;margin-bottom:0.4rem;">Notes</div>
                <p style="font-size:0.875rem;color:#78350F;">{{ $invoice->notes }}</p>
            </div>
        @endif

        {{-- Pied de page --}}
        <div style="margin-top:2rem;padding-top:1.5rem;border-top:2px solid #F3F4F6;text-align:center;">
            <div style="display:flex;justify-content:center;gap:2rem;flex-wrap:wrap;font-size:0.8rem;color:#6B7280;margin-bottom:0.5rem;">
                <span>📞 {{ $company->phone }}</span>
                <span>✉️ {{ $company->email }}</span>
                @if($company->website)<span>🌐 {{ $company->website }}</span>@endif
            </div>
            <div style="font-size:0.7rem;color:#9CA3AF;margin-top:0.5rem;">
                Facture générée automatiquement par easyShop · 
                <a href="{{ route('invoices.verify', $invoice->invoice_number) }}" 
                   style="color:#6366F1;text-decoration:none;">
                    Vérifier l'authenticité
                </a>
            </div>
        </div>

    </div>{{-- fin padding --}}
</div>{{-- fin carte --}}

@endsection
