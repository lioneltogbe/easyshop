<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\VenteCommand;
use App\Models\Company;
use App\Services\InvoiceGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected InvoiceGenerationService $invoiceService;

    public function __construct(InvoiceGenerationService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    // ──────────────────────────────────────────────
    // GÉNÉRER une facture pour une commande de vente
    // POST /invoices/generate/{venteCommandId}
    // ──────────────────────────────────────────────

    public function generate(Request $request, $venteCommandId)
    {
        try {
            $venteCommand = VenteCommand::with(['client', 'products.product'])
                ->findOrFail($venteCommandId);

            // Vérifier qu'une facture n'existe pas déjà
            if ($venteCommand->invoice) {
                return redirect()
                    ->route('invoices.show', $venteCommand->invoice->id)
                    ->with('error', 'Une facture existe déjà pour cette commande.');
            }

            // Vérifier qu'une entreprise active existe
            $company = Company::where('is_active', true)->first();
            if (!$company) {
                return redirect()->back()
                    ->with('error', 'Aucune entreprise active trouvée. Configurez votre entreprise dans les paramètres.');
            }

           $remise = 0;

            $invoice = $this->invoiceService->generateInvoice($venteCommand, $remise);
           
            return redirect()
                ->route('invoices.show', $invoice->id)
                ->with('success', 'Facture ' . $invoice->invoice_number . ' générée avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Commande de vente introuvable.');
        } catch (\Exception $e) {
            Log::error('Erreur génération facture : ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération : ' . $e->getMessage());
        }
    }

    // ──────────────────────────────────────────────
    // AFFICHER une facture
    // GET /invoices/{invoice}
    // ──────────────────────────────────────────────

    public function show(Invoice $invoice)
    {
        // Charger toutes les relations nécessaires en une seule requête
        $invoice->load([
            'company',
            'client',
            'venteCommand.products.product',
        ]);

        // Recalculer les montants pour s'assurer qu'ils sont à jour
        $calculations = $this->invoiceService->calculateForExistingInvoice($invoice);

        return view('invoices.show', [
            'invoice' => $invoice,
            'company' => $invoice->company,
            'client'  => $invoice->client,
            'items'   => $invoice->venteCommand->products,
            'calculations' => $calculations,
        ]);
    }

    // ──────────────────────────────────────────────
    // TÉLÉCHARGER le PDF
    // GET /invoices/{invoice}/download
    // ──────────────────────────────────────────────

    public function download(Invoice $invoice)
    {
        if (!$invoice->pdf_path || !Storage::disk('public')->exists($invoice->pdf_path)) {
            return redirect()->back()
                ->with('error', 'Le fichier PDF de cette facture est introuvable. Régénérez la facture.');
        }

        return Storage::disk('public')->download(
            $invoice->pdf_path,
            $invoice->invoice_number . '.pdf'
        );
    }

    // ──────────────────────────────────────────────
    // VÉRIFIER l'authenticité via QR code (public)
    // GET /invoices/verify/{invoiceNumber}
    // ──────────────────────────────────────────────

    public function verify($invoiceNumber, $companySlug = null)
    {
        $query = Invoice::with(['company', 'client'])
            ->where('invoice_number', $invoiceNumber);

        if ($companySlug) {
            $query->whereHas('company', fn ($q) => $q->where('slug', $companySlug));
        }

        $invoice = $query->firstOrFail();

        return view('invoices.verify', [
            'invoice' => $invoice,
            'company' => $invoice->company,
            'qrData'  => $invoice->qr_code_data, // déjà casté en array via $casts
        ]);
    }

    // ──────────────────────────────────────────────
    // LISTER les factures
    // GET /invoices
    // ──────────────────────────────────────────────

    public function list()
    {
        $company = Company::where('is_active', true)->first();

        if (!$company) {
            return redirect()->route('parametres.entreprise')
                ->with('error', 'Configurez votre entreprise avant de gérer les factures.');
        }

        $invoices = Invoice::with(['client', 'venteCommand'])
            ->where('company_id', $company->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('invoices.list', compact('invoices', 'company'));
    }
}
