<?php
 
namespace App\Services;
 
/**
 * Service pour gérer la remise
 * 
 * Logique :
 * - Remise = montant FCFA fixe (pas de %)
 * - Remise appliquée sur HT (avant TVA)
 * - Remise ≤ montant HT
 */
class InvoiceRemiseService
{
    /**
     * Valider la remise
     * 
     * @param float $remise
     * @param float $montantHt
     * @return bool
     * @throws \Exception
     */
    public function validateRemise(float $remise, float $montantHt): bool
    {
        // Remise ne peut pas être négative
        if ($remise < 0) {
            throw new \Exception('La remise ne peut pas être négative');
        }
 
        // Remise ne peut pas dépasser le montant HT
        if ($remise > $montantHt) {
            throw new \Exception('La remise ne peut pas dépasser le montant HT');
        }
 
        return true;
    }
 
    /**
     * Calculer le montant HT après remise
     * 
     * @param float $montantHt
     * @param float $remise
     * @return float
     */
    public function calculateHtAfterRemise(float $montantHt, float $remise): float
    {
        $this->validateRemise($remise, $montantHt);
        return round($montantHt - $remise, 2);
    }
}