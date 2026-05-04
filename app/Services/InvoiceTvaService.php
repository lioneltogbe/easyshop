<?php
 
namespace App\Services;
 
use App\Models\Product;
 
/**
 * Service pour calculer la TVA
 * 
 * Logique :
 * - TVA = 18% sur produits taxables
 * - TVA = 0% sur produits exonérés
 * - TVA = 0% si régime TPS
 */
class InvoiceTvaService
{
    /**
     * Calculer la TVA pour une ligne d'article
     * 
     * @param Product $product
     * @param int $quantity
     * @param float $unitPrice
     * @param bool $isTvaApplicable (régime fiscal)
     * @return float
     */
    public function calculateLineTva(
        Product $product,
        float $quantity,
        float $unitPriceAtCommand,
        float $remiseMontant,
        bool $isTvaApplicable
    ): float
    {
        // Si TVA pas applicable (régime TPS), retourner 0
        if (!$isTvaApplicable) {
            return 0;
        }
 
        // Si produit exonéré, retourner 0
        if (!$product->is_taxable) {
            return 0;
        }
 
        // Calculer la base imposable après remise ligne
        $subtotal = ($quantity * $unitPriceAtCommand) - $remiseMontant;
        $subtotal = max($subtotal, 0);
        $tvaRate = config('invoice.tva_rate') / 100;
        
        return round($subtotal * $tvaRate, 2);
    }
 
    /**
     * Calculer la TVA totale pour une commande
     * 
     * @param iterable $items Collection ou tableau d'items [['product' => Product, 'quantity' => int, 'unitPriceAtCommand' => float, 'remise_montant' => float], ...]
     * @param bool $isTvaApplicable
     * @return float
     */
    public function calculateTotalTva(iterable $items, bool $isTvaApplicable): float
    {
        $totalTva = 0;
 
        foreach ($items as $item) {
            $lineTva = $this->calculateLineTva(
                $item['product'],
                $item['quantity'],
                $item['unitPriceAtCommand'],
                $item['remise_montant'] ?? 0,
                $isTvaApplicable
            );
            $totalTva += $lineTva;
        }
 
        return round($totalTva, 2);
    }
}