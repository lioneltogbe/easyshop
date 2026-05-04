<?php
 
namespace App\Services;
 
use App\Models\Company;
use App\Models\VenteCommand;
use Illuminate\Support\Collection;
 
/**
 * Service principal pour calculer les montants d'une facture
 * 
 * Flux :
 * 1. Déterminer le régime
 * 2. Calculer HT par ligne
 * 3. Calculer TVA par ligne
 * 4. Appliquer remise
 * 5. Calculer TTC final
 */
class InvoiceCalculationService
{
    protected InvoiceRegimeService $regimeService;
    protected InvoiceTvaService $tvaService;
    protected InvoiceRemiseService $remiseService;
 
    public function __construct(
        InvoiceRegimeService $regimeService,
        InvoiceTvaService $tvaService,
        InvoiceRemiseService $remiseService
    ) {
        $this->regimeService = $regimeService;
        $this->tvaService = $tvaService;
        $this->remiseService = $remiseService;
    }
 
    /**
     * Calculer tous les montants d'une commande
     * 
     * @param VenteCommand $command
     * @param Collection $items [['product' => Product, 'quantity' => int, 'unit_price' => float, 'remise_montant'=>float,...]
     * @param float $remise
     * @param Company $company
     * @return array
     */
    public function calculateInvoice(
        VenteCommand $command,
        Collection $items,
        float $remise,
        Company $company
    ): array
    {
        // 1. Déterminer le régime
        // $regime = $this->regimeService->determineRegime($company);
        // $isTvaApplicable = $this->regimeService->isTvaApplicable($company);

        $regime          = $this->regimeService->determineRegime($company);
        $isTvaApplicable = ($regime === 'normal');
 
        // 2. Calculer HT total avant remises
        $montantHt = $this->calculateTotalHt($items);
        $montantRemiseLignes = $this->calculateTotalRemiseLignes($items);
        $montantRemiseTotal = round($montantRemiseLignes + $remise, 2);
 
        // 3. Calculer TVA total sur la base après remise ligne
        $montantTva = $this->tvaService->calculateTotalTva($items, $isTvaApplicable);
 
        // 4. Appliquer remise globale + remise ligne
        $montantHtRemise = $this->remiseService->calculateHtAfterRemise($montantHt, $montantRemiseTotal);
 
        // 5. Calculer TTC final
        $montantTtc = round($montantHtRemise + $montantTva, 2);
 
        // 6. Sauvegarder les calculs
        $command->update([
            'regime_fiscal' => $regime,
            'montant_ht' => $montantHt,
            'montant_tva' => $montantTva,
            'montant_ht_remise' => $montantHtRemise,
            'remise' => $montantRemiseTotal,
            'montantTotal' => $montantTtc, // Mettre à jour le total
        ]);
 
        // 7. Retourner les détails
        return [
            'regime' => $regime,
            'montant_ht' => $montantHt,
            'montant_remise_lignes' => $montantRemiseLignes,
            'remise' => $montantRemiseTotal,
            'montant_ht_remise' => $montantHtRemise,
            'montant_tva' => $montantTva,
            'montant_ttc' => $montantTtc,
            'is_tva_applicable' => $isTvaApplicable,
        ];
    }
 
    /**
     * Calculer le HT total
     * 
     * @param Collection $items
     * @return float
     */
    private function calculateTotalHt(Collection $items): float
    {
        $total = 0;
 
        foreach ($items as $item) {
            $subtotal = $item['quantity'] * $item['unitPriceAtCommand'];
            $total += $subtotal;
        }
 
        return round($total, 2);
    }

    private function calculateTotalRemiseLignes(Collection $items): float
    {
        $total = 0;
 
        foreach ($items as $item) {
            $total += $item['remise_montant'] ?? 0;
        }
 
        return round($total, 2);
    }
}