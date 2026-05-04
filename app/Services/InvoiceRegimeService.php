<?php
 
namespace App\Services;
 
use App\Models\Company;
use  \App\Models\VenteCommand;
/**
 * Service pour déterminer le régime fiscal
 * 
 * Logique :
 * - TPS : CA < 50M FCFA
 * - Normal : CA ≥ 50M FCFA
 */
class InvoiceRegimeService
{
    /**
     * Déterminer le régime fiscal d'une entreprise
     * 
     * @param Company $company
     * @return string 'tps' ou 'normal'
     */
    public function determineRegime(Company $company): string
    {
        $threshold = config('invoice.tps_threshold');
        
        // Récupérer le CA cumulé de l'année
        $caCumule = $this->calculateYearlyCA($company);
        
        // Déterminer le régime
        return $caCumule >= $threshold ? 'normal' : 'tps';
    }
 
    /**
     * Calculer le CA cumulé de l'année
     * 
     * @param Company $company
     * @return float
     */
    private function calculateYearlyCA(Company $company): float
    {
        // Utiliser le CA cumulé stocké dans la table companies
        return (float) $company->ca_cumule_annee;
    }
 
    /**
     * Vérifier si TVA est applicable
     * 
     * @param Company $company
     * @return bool
     */
    public function isTvaApplicable(Company $company): bool
    {
        $regime = $this->determineRegime($company);
        return $regime === 'normal';
    }
}