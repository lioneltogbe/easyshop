<?php
    namespace App\Console\Commands;
 
use App\Models\VenteCommand;
use App\Services\InvoiceGenerationService;
use Illuminate\Console\Command;
 
class GenerateInvoiceCommand extends Command
{
    protected $signature = 'invoice:generate {vente_command_id} {--company_id=}';
    protected $description = 'Générer une facture pour une commande de vente';
 
    public function handle(InvoiceGenerationService $invoiceService)
    {
        $venteCommandId = $this->argument('vente_command_id');
        $companyId = $this->option('company_id');
 
        try {
            $venteCommand = VenteCommand::findOrFail($venteCommandId);
            $invoice = $invoiceService->generateInvoice($venteCommand);
 
            $this->info("✅ Facture générée : {$invoice->invoice_number}");
            $this->info("📄 PDF : {$invoice->pdf_path}");
            $this->info("🔲 QR Code : {$invoice->qr_code_path}");
 
        } catch (\Exception $e) {
            $this->error("❌ Erreur : {$e->getMessage()}");
        }
    }
}
?>