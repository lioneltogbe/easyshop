{{-- Partial réutilisable : tableau de transactions --}}
{{-- Variables attendues : $transactions (LengthAwarePaginator), $params --}}
<table class="table" style="overflow-x:auto;">
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Méthode</th>
            <th style="text-align:right;">Solde Actuel</th>
        </tr>
    </thead>
    <tbody>
        @forelse($transactions as $transaction)
            <tr>
                <td>{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                <td>{{ $transaction->description }}</td>
                <td>
                    @if($transaction->type === 'REVENUE')
                        <span class="badge badge-success">Revenu</span>
                    @else
                        <span class="badge badge-danger">Dépense</span>
                    @endif
                </td>
                <td style="font-weight:600;color:{{ $transaction->type === 'REVENUE' ? 'var(--success)' : 'var(--danger)' }};">
                    {{ $transaction->type === 'REVENUE' ? '+' : '-' }}
                    {{ number_format($transaction->montant, 0, ',', ' ') }} FCFA
                </td>
                <td>{{ ucfirst($transaction->paiement_method) }}</td>
                <td style="text-align:right;font-weight:700;font-size:1rem;color:{{ ($transaction->solde_actuel ?? 0) >= 0 ? '#10B981' : '#EF4444' }};">
                    {{ number_format($transaction->solde_actuel ?? 0, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:#6B7280;">Aucune transaction trouvée</td>
            </tr>
        @endforelse
    </tbody>
</table>
<!-- @if($transactions->hasPages())
<div style="padding:1rem 1.5rem;border-top:1px solid #E5E7EB;">
    {{ $transactions->links() }}
</div>
@endif -->
