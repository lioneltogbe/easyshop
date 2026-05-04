<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VenteCommand;
use App\Models\Client;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PaiementController extends Controller
{
    // Providers supportés
    const PROVIDERS = ['mtn', 'moov', 'celtiis'];

    // ──────────────────────────────────────────────
    // POST /api/paiement/initier
    // Corps :
    // {
    //   "commande_id": 12,
    //   "provider": "mtn",       // mtn | moov | celtiis
    //   "telephone": "0712345678"
    // }
    // ──────────────────────────────────────────────

    public function initier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commande_id' => 'required|integer|exists:vente_commands,id',
            'provider'    => 'required|string|in:mtn,moov,celtiis',
            'telephone'   => 'required|string|min:8|max:15',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        $user     = $request->user();
        $client   = Client::where('email', $user->email)->first();
        $commande = VenteCommand::where('client_id', $client?->id)
                                ->findOrFail($request->commande_id);

        if (!in_array($commande->status, ['EN ATTENTE', 'CONFIRMER'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cette commande ne peut pas être payée (statut : ' . $commande->status . ').',
            ], 409);
        }

        // ── Appel API du provider MoMo ────────────────────────
        try {
            $reference = $this->initierPaiementProvider(
                $request->provider,
                $commande,
                $request->telephone
            );

            return response()->json([
                'success'   => true,
                'message'   => 'Paiement initié. Veuillez confirmer sur votre téléphone.',
                'reference' => $reference,
                'provider'  => $request->provider,
                'montant'   => (float) $commande->montantTotal,
                'telephone' => $request->telephone,
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur paiement {$request->provider} : " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'initiation du paiement : ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────
    // POST /api/paiement/callback/{provider}
    // Appelé par MTN/Moov/Celtiis après paiement
    // Route publique — pas de middleware auth
    // ──────────────────────────────────────────────

    public function callback(Request $request, string $provider)
    {
        Log::info("Callback MoMo [{$provider}]", $request->all());

        if (!in_array($provider, self::PROVIDERS)) {
            return response()->json(['error' => 'Provider inconnu'], 400);
        }

        DB::beginTransaction();

        try {
            // Extraire les données du callback selon le provider
            $data = $this->parseCallback($provider, $request);

            if (!$data) {
                return response()->json(['status' => 'ignored'], 200);
            }

            // Trouver la commande via la référence
            $commande = VenteCommand::where('codeCommand', $data['reference'])->first();

            if (!$commande) {
                Log::warning("Callback MoMo : commande {$data['reference']} introuvable.");
                return response()->json(['status' => 'not_found'], 200);
            }

            if ($data['success']) {
                // Paiement confirmé
                $commande->update([
                    'status'       => 'PAYER',
                    'montantPayer' => $commande->montantTotal,
                ]);

                // Enregistrer la transaction financière
                $solde = FinancialTransaction::calculerNouveauSolde('REVENUE', $commande->montantTotal);

                FinancialTransaction::create([
                    'type'                         => 'REVENUE',
                    'description'                  => "Paiement {$provider} commande {$commande->codeCommand}",
                    'montant'                      => $commande->montantTotal,
                    'paiement_method'              => $provider,
                    'categorie'                    => 'Ventes',
                    'related_model_achat_or_vente' => 'VenteCommand',
                    'related_id_vente'             => $commande->id,
                    'created_by'                   => $commande->created_by ?? 1,
                    'solde_actuel'                 => $solde,
                ]);

                Log::info("Paiement confirmé : {$commande->codeCommand} via {$provider}");
            } else {
                // Paiement échoué — remettre en attente
                Log::warning("Paiement échoué : {$commande->codeCommand} via {$provider}");
            }

            DB::commit();

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur callback {$provider} : " . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    // ──────────────────────────────────────────────
    // Initier le paiement selon le provider
    // (À adapter avec les vraies API sandbox)
    // ──────────────────────────────────────────────

    private function initierPaiementProvider(string $provider, VenteCommand $commande, string $telephone): string
    {
        $callbackUrl = route('api.paiement.callback', $provider);
        $reference   = $commande->codeCommand;
        $montant     = (int) $commande->montantTotal;

        switch ($provider) {
            case 'mtn':
                // MTN MoMo API — Collections
                // Docs : https://momodeveloper.mtn.com/docs
                // $response = Http::withHeaders([
                //     'Authorization'     => 'Bearer ' . $this->getMtnToken(),
                //     'X-Reference-Id'    => $reference,
                //     'X-Target-Environment' => env('MTN_ENV', 'sandbox'),
                //     'Ocp-Apim-Subscription-Key' => env('MTN_SUBSCRIPTION_KEY'),
                // ])->post(env('MTN_API_URL') . '/collection/v1_0/requesttopay', [
                //     'amount'      => $montant,
                //     'currency'    => 'XOF',
                //     'externalId'  => $reference,
                //     'payer'       => ['partyIdType' => 'MSISDN', 'partyId' => $telephone],
                //     'payerMessage'=> 'Paiement commande easyShop',
                //     'payeeNote'   => $reference,
                //     'callbackUrl' => $callbackUrl,
                // ]);
                // return $reference;

                // MODE SIMULATION (sandbox sans credentials)
                return 'MTN-SIM-' . $reference;

            case 'moov':
                // Moov Money API
                return 'MOOV-SIM-' . $reference;

            case 'celtiis':
                // Celtiis API
                return 'CELTIIS-SIM-' . $reference;

            default:
                throw new \Exception("Provider {$provider} non supporté.");
        }
    }

    // ──────────────────────────────────────────────
    // Parser le callback selon le provider
    // ──────────────────────────────────────────────

    private function parseCallback(string $provider, Request $request): ?array
    {
        switch ($provider) {
            case 'mtn':
                // MTN envoie : status, externalId, financialTransactionId
                if ($request->status !== 'SUCCESSFUL') return ['success' => false, 'reference' => $request->externalId];
                return ['success' => true, 'reference' => $request->externalId];

            case 'moov':
                if ($request->transaction_status !== 'SUCCESS') return ['success' => false, 'reference' => $request->reference];
                return ['success' => true, 'reference' => $request->reference];

            case 'celtiis':
                if ($request->result_code !== '0') return ['success' => false, 'reference' => $request->order_id];
                return ['success' => true, 'reference' => $request->order_id];

            default:
                return null;
        }
    }
}
