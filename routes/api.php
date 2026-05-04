<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthClientController;
use App\Http\Controllers\Api\CatalogueController;
use App\Http\Controllers\Api\CommandeClientController;
use App\Http\Controllers\Api\PaiementController;
use App\Http\Controllers\Api\AdminValidationController;

// ============================================================
// API ROUTES — easyShop Client
// Préfixe : /api  (défini dans bootstrap/app.php)
// ============================================================

// ── PUBLIQUES — aucune auth requise ──────────────────────────

// Vérification que l'API est vivante
Route::get('/ping', fn () => response()->json(['status' => 'ok', 'version' => '1.0']));

// Auth client
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthClientController::class, 'login']);
    Route::post('/register', [AuthClientController::class, 'register']);
});

// Catalogue public (lecture seule, sans achatPrice)
Route::prefix('catalogue')->group(function () {
    Route::get('/produits',      [CatalogueController::class, 'index']);
    Route::get('/produits/{id}', [CatalogueController::class, 'show']);
    Route::get('/categories',    [CatalogueController::class, 'categories']);
});

// Webhook paiement MoMo (doit rester public — appelé par MTN/Moov/Celtiis)
Route::post('/paiement/callback/{provider}', [PaiementController::class, 'callback'])
    ->name('api.paiement.callback');

// ── PROTÉGÉES — token Sanctum requis ─────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Profil & déconnexion
    Route::get('/auth/me',     [AuthClientController::class, 'me']);
    Route::post('/auth/logout', [AuthClientController::class, 'logout']);

    // Commandes client
    Route::prefix('commandes')->group(function () {
        Route::get('/',          [CommandeClientController::class, 'index']);   // mes commandes
        Route::post('/',         [CommandeClientController::class, 'store']);   // créer commande
        Route::get('/{id}',      [CommandeClientController::class, 'show']);    // détail commande
        Route::delete('/{id}',   [CommandeClientController::class, 'cancel']); // annuler (EN ATTENTE seulement)
    });

    // Initier un paiement MoMo
    Route::post('/paiement/initier', [PaiementController::class, 'initier']);

    // ── ADMIN / MANAGER seulement ─────────────────────────────
    Route::middleware('role:admin|manager')->prefix('admin')->group(function () {
        Route::get('/commandes',           [AdminValidationController::class, 'index']);       // toutes commandes EN ATTENTE
        Route::post('/commandes/{id}/valider', [AdminValidationController::class, 'valider']); // valider → CONFIRMER + stock
        Route::post('/commandes/{id}/refuser', [AdminValidationController::class, 'refuser']); // refuser → ANNULER
    });
});
