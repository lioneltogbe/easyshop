<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VenteCommand;
use App\Models\VenteCommandProduct;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminValidationController extends Controller
{
    // ──────────────────────────────────────────────
    // GET /api/admin/commandes
    // Toutes les commandes EN ATTENTE (côté admin)
    // ──────────────────────────────────────────────

    public function index(Request $request)
    {
        $status = $request->get('status', 'EN ATTENTE');

        $commandes = VenteCommand::with(['client', 'products.product', 'creator'])
            ->where('status', $status)
            // Commandes clients = code commence par CLI-
            ->where('codeCommand', 'like', 'CLI-%')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn ($c) => [
                'id'         => $c->id,
                'code'       => $c->codeCommand,
                'status'     => $c->status,
                'client'     => [
                    'id'   => $c->client?->id,
                    'name' => $c->client?->name,
                    'phone'=> $c->client?->phone,
                ],
                'montant'    => (float) $c->montantTotal,
                'articles'   => $c->products->count(),
                'created_at' => $c->created_at?->format('d/m/Y H:i'),
                'detail'     => $c->products->map(fn ($p) => [
                    'nom'      => $p->product?->name,
                    'quantite' => (float) $p->quantity,
                    'prix'     => (float) $p->unitPriceAtCommand,
                    'stock_ok' => $p->product ? $p->product->getCurrentStock() >= $p->quantity : false,
                ]),
            ]);

        return response()->json([
            'success' => true,
            'total'   => $commandes->count(),
            'data'    => $commandes,
        ]);
    }

    // ──────────────────────────────────────────────
    // POST /api/admin/commandes/{id}/valider
    // Valide la commande → CONFIRMER
    // Le stock est débité à la livraison (markAsDelivered)
    // ──────────────────────────────────────────────

    public function valider(Request $request, $id)
    {
        $commande = VenteCommand::with('products.product')->findOrFail($id);

        if ($commande->status !== 'EN ATTENTE') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les commandes EN ATTENTE peuvent être validées.',
            ], 409);
        }

        DB::beginTransaction();

        try {
            // Vérifier le stock une dernière fois avant validation
            foreach ($commande->products as $item) {
                if (!$item->product) continue;
                $stock = $item->product->getCurrentStock();
                if ($stock < $item->quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Stock insuffisant pour « {$item->product->name} » au moment de la validation. Disponible : {$stock}.",
                    ], 409);
                }
            }

            $commande->update([
                'status'     => 'CONFIRMER',
                'updated_by' => $request->user()->id ?? null,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Commande {$commande->codeCommand} validée. Le client sera notifié.",
                'data'    => [
                    'id'     => $commande->id,
                    'code'   => $commande->codeCommand,
                    'status' => 'CONFIRMER',
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }
    }

    // ──────────────────────────────────────────────
    // POST /api/admin/commandes/{id}/refuser
    // Corps optionnel : { "motif": "Stock insuffisant" }
    // ──────────────────────────────────────────────

    public function refuser(Request $request, $id)
    {
        $commande = VenteCommand::findOrFail($id);

        if ($commande->status !== 'EN ATTENTE') {
            return response()->json([
                'success' => false,
                'message' => 'Seules les commandes EN ATTENTE peuvent être refusées.',
            ], 409);
        }

        $commande->update(['status' => 'ANNULER']);

        return response()->json([
            'success' => true,
            'message' => "Commande {$commande->codeCommand} refusée.",
            'motif'   => $request->motif ?? null,
        ]);
    }
}
