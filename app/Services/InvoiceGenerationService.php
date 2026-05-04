<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Company;
use App\Models\VenteCommand;
use App\Models\VenteCommandProduct;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;
use Carbon\Carbon;


/**
 * ============================================================================
 * SERVICE DE GÉNÉRATION DE FACTURE
 * ============================================================================
 * 
 * Ce service gère l'ensemble du processus de génération de facture :
 * 1. Récupération des données de la commande
 * 2. Calcul des montants (HT, TVA, remise, TTC)
 * 3. Génération du numéro de facture unique
 * 4. Création du code QR
 * 5. Génération du PDF
 * 6. Stockage du fichier
 * 7. Mise à jour de la base de données
 * 
 * Dépendances :
 * - InvoiceCalculationService : Calcul des montants
 * - dompdf : Génération PDF
 * - simple-qrcode : Génération QR
 * 
 * ============================================================================
 */
class InvoiceGenerationService
{
    /**
     * Service de calcul des montants
     * 
     * @var InvoiceCalculationService
     */
    protected InvoiceCalculationService $calculationService;

    /**
     * Constructeur
     * 
     * Injection du service de calcul via le conteneur Laravel
     * 
     * @param InvoiceCalculationService $calculationService
     */
    public function __construct(InvoiceCalculationService $calculationService)
    {
        $this->calculationService = $calculationService;
    }

    /**
     * ========================================================================
     * MÉTHODE PRINCIPALE : GÉNÉRER UNE FACTURE COMPLÈTE
     * ========================================================================
     * 
     * Flux complet :
     * 1. Validation de la commande
     * 2. Récupération des données
     * 3. Calcul des montants
     * 4. Génération du numéro de facture
     * 5. Création du code QR
     * 6. Génération du PDF
     * 7. Stockage du fichier
     * 8. Mise à jour de la base de données
     * 
     * @param VenteCommand $command La commande à facturer
     * @param float $remise Montant de remise en FCFA (optionnel)
     * @return Invoice La facture générée
     * @throws Exception Si une erreur survient
     */
    public function generateInvoice(VenteCommand $command, float $remise = 0): Invoice
    {
        try {
            // ====================================================================
            // ÉTAPE 1 : VALIDATION
            // ====================================================================

            // Vérifier que la commande a des articles
            if ($command->products()->count() === 0) {
                throw new Exception('La commande n\'a pas d\'articles');
            }

            // Vérifier que la remise est positive
            if ($remise < 0) {
                throw new Exception('La remise ne peut pas être négative');
            }

            // ====================================================================
            // ÉTAPE 2 : RÉCUPÉRATION DES DONNÉES
            // ====================================================================
            
            // Récupérer l'entreprise (configuration générale)
            $company = Company::where('is_active', true)->first();
            if (!$company) {
                throw new Exception('Aucune entreprise configurée');
            }

            // Récupérer les articles de la commande avec les produits
            $items = $command->products()
                ->with('product')
                ->get();

            // Récupérer le client
            $client = $command->client;
            if (!$client) {
                throw new Exception('Le client n\'existe pas');
            }

            // ====================================================================
            // ÉTAPE 3 : CALCUL DES MONTANTS
            // ====================================================================
            
            // Utiliser le service de calcul pour obtenir tous les montants
            // Cela inclut :
            // - Montant HT (avant remise)
            // - Montant TVA (18% si applicable)
            // - Montant HT après remise
            // - Montant TTC final
            // - Régime fiscal utilisé
            $calculations = $this->calculationService->calculateInvoice(
                $command,
                $items,
                $remise,
                $company
            );

            // ====================================================================
            // ÉTAPE 4 : GÉNÉRATION DU NUMÉRO DE FACTURE
            // ====================================================================
            
            // Générer un numéro de facture unique
            // Format : FAC-YYYYMMDD-XXXXXX (ex: FAC-20260331-000001)
            $numeroFacture = $this->generateInvoiceNumber($command);

            // ====================================================================
            // ÉTAPE 5 : CRÉATION DU CODE QR
            // ====================================================================
            
            // Créer un code QR contenant les informations de la facture
            // Utile pour la vérification et l'audit
            $qrCode = $this->generateQrCode(
                $numeroFacture,
                $calculations,
                $client,
                $company,
                $command
            );

                 // Lire le SVG et l'encoder en base64 pour l'intégrer dans le PDF
        $qrCodeContent = Storage::disk('public')->get($qrCode['path']);

        // Nettoyer la déclaration XML (cause 1)
        $qrCodeContent = preg_replace('/<\?xml[^?]*\?>/', '', $qrCodeContent);
        $qrCodeContent = trim($qrCodeContent);


         $invoice = Invoice::create([
                'company_id'       => $company->id,
                'vente_command_id' => $command->id,
                'client_id'        => $command->client_id,
                'invoice_number'   => $numeroFacture,
                'invoice_date'     => now(),
                'due_date'         => now()->addDays(30),
                'subtotal'         => $calculations['montant_ht'],
                'tax_amount'       =>  $calculations['montant_tva'],
                'discount_amount'  => $calculations['remise'],
                'total_amount'     => $calculations['montant_ttc'],
                'qr_code_path'     => $qrCode['path'],
                'pdf_path'         => null,
                'qr_code_data'     => $qrCode['data'], // Stocker les données du QR pour vérification
                'status'           => 'GENERATED',
            ]);
            // ====================================================================
            // ÉTAPE 6 : GÉNÉRATION DU PDF
            // ====================================================================
            
              // Préparer les données pour le template
            $data = [
                'command' => $command,
                'company' => $company,
                'client' => $client,
                'items' => $items,
                'calculations' => $calculations,
                'numeroFacture' => $numeroFacture,
                'qr_code_svg'  => base64_encode($qrCodeContent),
                'dateFacture' => now()->format('d/m/Y'),
                'heureFacture' => now()->format('H:i:s'),
                'invoice' => $invoice,
                
            ];


            // Charger le template Blade et générer le PDF
            $pdf = Pdf::loadView('invoices.template', $data)
                ->setPaper('a4')
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10);

            // ====================================================================
            // ÉTAPE 7 : STOCKAGE DU FICHIER
            // ====================================================================
            
             $filePath = "invoices/pdfs/{$numeroFacture}.pdf";
             Storage::disk('public')->put($filePath, $pdf->output());
             
            // Vérifier que le fichier a bien été créé
            if (!Storage::disk('public')->exists($filePath)) {
                throw new Exception('Erreur lors de la sauvegarde du PDF');
            }

                    // UPDATE APRÈS PDF
                // =========================
                $invoice->update([
                    'pdf_path' => $filePath
                ]);

            // ====================================================================
            // ÉTAPE 8 : MISE À JOUR DE LA BASE DE DONNÉES
            // ====================================================================
            
            // Mettre à jour la commande avec les informations de facturation
            $command->update([
                'lien_facture' => $filePath,
                'regime_fiscal' => $calculations['regime'],
                'montant_ht' => $calculations['montant_ht'],
                'montant_tva' => $calculations['montant_tva'],
                'montant_ht_remise' => $calculations['montant_ht_remise'],
                'remise' => $calculations['remise'],
                'montantTotal' => $calculations['montant_ttc'],
            ]);

            // ====================================================================
            // RETOUR DU RÉSULTAT
            // ====================================================================
            
            return $invoice;

        } catch (Exception $e) {
            // Propager l'erreur pour que le contrôleur et la commande puissent la gérer
            throw $e;
        }
    }

    /**
     * ========================================================================
     * GÉNÉRER UN NUMÉRO DE FACTURE UNIQUE
     * ========================================================================
     * 
     * Format : FAC-YYYYMMDD-XXXXXX
     * Exemple : FAC-20260331-000001
     * 
     * Logique :
     * 1. Récupérer la date actuelle
     * 2. Compter le nombre de factures du jour
     * 3. Générer un numéro séquentiel
     * 
     * @param VenteCommand $command
     * @return string Numéro de facture unique
     */
    protected function generateInvoiceNumber(VenteCommand $command): string
    {
        // Récupérer la date actuelle au format YYYYMMDD
        $dateFormat = now()->format('Ymd');

        // Générer le numéro séquentiel (ex: 000001)
         $sequenceNumber = str_pad($command->id, 6, '0', STR_PAD_LEFT);

        // Construire le numéro de facture complet
        $numeroFacture = "FAC-{$dateFormat}-{$sequenceNumber}";
        // ex: FAC-20260403-000042

        return $numeroFacture;
    }

    /**
     * ========================================================================
     * GÉNÉRER UN CODE QR
     * ========================================================================
     * 
     * Le code QR contient les informations essentielles de la facture :
     * - Numéro de facture
     * - Montant TTC
     * - Régime fiscal
     * - Date
     * - Entreprise
     * 
     * Format JSON pour faciliter la lecture
     * 
     * @param string $numeroFacture
     * @param array $calculations
     * @param object $client
     * @param Company $company
     * @return array ['path' => string, 'data' => array]
     */
    protected function generateQrCode(
        string $numeroFacture,
        array $calculations,
        $client,
        Company $company,
        VenteCommand $command
    ): array
    {
        // Préparer les données du code QR en JSON
        $qrData = [
            'numero_facture' => $numeroFacture,
            'montant_ttc' => $calculations['montant_ttc'],
            'montant_tva' => $calculations['montant_tva'],
            'regime' => $calculations['regime'],
            'date' => now()->format('Y-m-d H:i:s'),
            'entreprise' => $company->name,
            'client' => $client->name ?? 'Client',
            'vente_command_id' => $command->id,
            'verification_url' => route('invoices.verify', $numeroFacture),
        ];

        // Convertir en JSON
        $qrDataJson = json_encode($qrData, JSON_UNESCAPED_UNICODE);

        // Générer le code QR en format SVG
        $qrCode = QrCode::format('svg')
            ->size(200)
            ->errorCorrection('H')
            ->generate($qrDataJson);

        $path = "invoices/qr-codes/{$numeroFacture}.svg";
        Storage::disk('public')->put($path, $qrCode);

        // Retourner le code QR en HTML
        return  [
            'path' => $path,    // "invoices/qr-codes/FAC-20260403-000042.svg"
            'data' => $qrData,  // ['numero_facture' => ..., 'montant_ttc' => ..., ...]
        ];
    }

    /**
     * ========================================================================
     * TÉLÉCHARGER UNE FACTURE
     * ========================================================================
     * 
     * Permet de télécharger une facture PDF existante
     * 
     * @param string $numeroFacture
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws Exception
     */
    public function downloadInvoice(string $numeroFacture)
    {

        $filePath = "invoices/pdfs/{$numeroFacture}.pdf";

        if (!Storage::disk('public')->exists($filePath)) {
            throw new Exception('La facture n\'existe pas');
        }

        // Télécharger le fichier
       return Storage::disk('public')->download(
            $filePath,
            $numeroFacture . '.pdf'
        );
    }

    /**
     * ========================================================================
     * GÉNÉRER UNE FACTURE AVEC ARTICLES PERSONNALISÉS
     * ========================================================================
     * 
     * Variante permettant de générer une facture avec des articles spécifiques
     * Utile pour les corrections ou les factures d'avoir
     * 
     * @param VenteCommand $command
     * @param array $customItems Articles personnalisés
     * @param float $remise
     * @return array
     */
    public function generateInvoiceWithCustomItems(
        VenteCommand $command,
        array $customItems,
        float $remise = 0
    ): array
    {
        try {
            // Récupérer l'entreprise
            $company = Company::where('is_active', true)->first();
            if (!$company) {
                throw new Exception('Aucune entreprise configurée');
            }

            // Récupérer le client
            $client = $command->client;
            if (!$client) {
                throw new Exception('Le client n\'existe pas');
            }

            // Convertir les articles personnalisés en collection
            $items = collect($customItems);

            // Calculer les montants
            $calculations = $this->calculationService->calculateInvoice(
                $command,
                $items,
                $remise,
                $company
            );

            // Générer le numéro de facture
            $numeroFacture = $this->generateInvoiceNumber($command);

            // Générer le code QR
            $qrCode = $this->generateQrCode(
                $numeroFacture,
                $calculations,
                $client,
                $company,
                $command
            );
                      // Lire le SVG et l'encoder en base64 pour l'intégrer dans le PDF
        $qrCodeContent = Storage::disk('public')->get($qrCode['path']);

        // Nettoyer la déclaration XML (cause 1)
        $qrCodeContent = preg_replace('/<\?xml[^?]*\?>/', '', $qrCodeContent);
        $qrCodeContent = trim($qrCodeContent);
            // Préparer les données
           $data = [
                'command' => $command,
                'company' => $company,
                'client' => $client,
                'items' => $items,
                'calculations' => $calculations,
                'numeroFacture' => $numeroFacture,
                'qr_code_svg'  => base64_encode($qrCodeContent),
                'dateFacture' => now()->format('d/m/Y'),
                'heureFacture' => now()->format('H:i:s'),
            ];

            // Générer le PDF
            $pdf = Pdf::loadView('invoices.template', $data)
                ->setPaper('a4')
                ->setOption('margin-top', 10)
                ->setOption('margin-bottom', 10)
                ->setOption('margin-left', 10)
                ->setOption('margin-right', 10);

            $filePath = "invoices/pdfs/{$numeroFacture}.pdf";
             Storage::disk('public')->put($filePath, $pdf->output());
             
            // Vérifier que le fichier a bien été créé
           if (!Storage::disk('public')->exists($filePath)) {
                throw new Exception('Erreur lors de la sauvegarde du PDF');
            }

            // Mettre à jour la commande
            $command->update([
                'lien_facture' => $filePath,
                'regime_fiscal' => $calculations['regime'],
                'montant_ht' => $calculations['montant_ht'],
                'montant_tva' => $calculations['montant_tva'],
                'montant_ht_remise' => $calculations['montant_ht_remise'],
                'remise' => $remise,
                'montantTotal' => $calculations['montant_ttc'],
            ]);

            

            return [
                'success' => true,
                'message' => 'Facture générée avec succès',
                'numero_facture' => $numeroFacture,
                'chemin_pdf' => $filePath,
                'calculations' => $calculations,
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ];
        }
    }

    /**
     * ========================================================================
     * RECALCULER LES MONTANTS POUR UNE FACTURE EXISTANTE
     * ========================================================================
     * 
     * Recalcule les montants d'une facture existante en tenant compte
     * des changements de régime fiscal
     * 
     * @param Invoice $invoice
     * @return array
     */
    public function calculateForExistingInvoice(Invoice $invoice): array
    {
        $command = $invoice->venteCommand;
        $company = $invoice->company;
        $items = $command->products()->with('product')->get();
        $remise = $invoice->discount_amount ?? 0;

        return $this->calculationService->calculateInvoice(
            $command,
            $items,
            $remise,
            $company
        );
    }

}
