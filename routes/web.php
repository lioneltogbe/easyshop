<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\StockMovementController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\AchatCommandController;
use App\Http\Controllers\VenteCommandController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfilController;
// ============================================================
// RACINE
// ============================================================

Route::get('/', fn ()=>view('acceuil') )->name('acceuil');

Route::get('/acceuil', fn () => auth()->check()
    ? redirect()->route('dashboard')
    : redirect()->route('login')
)->name('home');

// ============================================================
// AUTHENTIFICATION (invités seulement)
// ============================================================

Route::middleware('guest')->group(function () {
    Route::get('/login',  [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ============================================================
// ZONE PROTÉGÉE — utilisateur connecté
// ============================================================

Route::middleware('auth')->group(function () {

    // ── Dashboard ─────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ============================================================
    // UTILISATEURS — admin ou manager
    // ============================================================

    Route::prefix('users')->group(function () {

        Route::get('/',        [UserController::class, 'index'])->name('users.index');
        Route::get('/create',  [UserController::class, 'create'])->name('users.create');
        Route::post('/',       [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}',  [UserController::class, 'show'])->name('users.show');
        Route::get('/{user}/edit',  [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}',       [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // ============================================================
    // RÔLES — admin seulement
    // ============================================================

    Route::prefix('roles')->group(function () {

        Route::get('/',        [RoleController::class, 'index'])->name('roles.index');
        Route::get('/create',      [RoleController::class, 'create'])->name('roles.create');
        Route::post('/',           [RoleController::class, 'store'])->name('roles.store');
        Route::get('/{role}',  [RoleController::class, 'show'])->name('roles.show');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/{role}',      [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/{role}',   [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    // ============================================================
    // PRODUITS
    // ============================================================

    Route::prefix('produits')->group(function () {
        Route::get('/',              [ProductController::class, 'index'])->middleware('permission:view-product')->name('produits.index');
        Route::get('/create',        [ProductController::class, 'create'])->middleware('permission:create-product')->name('produits.create');
        Route::post('/',             [ProductController::class, 'store'])->middleware('permission:create-product')->name('produits.store');
        Route::get('/{product}',     [ProductController::class, 'show'])->middleware('permission:view-product')->name('produits.show');
        Route::post('/{product}/lots', [ProductController::class, 'storeBatch'])->middleware('permission:edit-product')->name('produits.batches.store');
        Route::delete('/{product}/lots/{batch}', [ProductController::class, 'destroyBatch'])->middleware('permission:edit-product')->name('produits.batches.destroy');
        Route::get('/produits/{product}/edit', function ($id) { $product = \App\Models\Product::findOrFail($id); $categories=\App\Models\Categorie::all(); return view('produits.edit', ['product'=>$product, 'categories'=>$categories]); })->middleware('permission:edit-product')->name('produits.edit');
        Route::put('/{product}',     [ProductController::class, 'update'])->middleware('permission:edit-product')->name('produits.update');
        Route::delete('/{product}',  [ProductController::class, 'destroy'])->middleware('permission:delete-product')->name('produits.destroy');
    });

    // ============================================================
    // STOCKS
    // ============================================================

    Route::prefix('stocks')->middleware('permission:view-stock')->group(function () {
        Route::get('/',             fn () => view('stocks.index'))->name('stocks.index');
        Route::get('/mouvements',   [StockMovementController::class, 'index'])->name('stocks.mouvements');
        Route::get('/produit/{id}', [StockMovementController::class, 'getProductHistory'])->name('stocks.produit');

        Route::get('/entree',  fn () => view('stocks.entree'))->name('stocks.entree');
        Route::post('/entree', [StockMovementController::class, 'recordInbound'])->name('stocks.entree.store');

        Route::get('/sortie',  fn () => view('stocks.sortie'))->name('stocks.sortie');
        Route::post('/sortie', [StockMovementController::class, 'recordOutbound'])->name('stocks.sortie.store');

        Route::middleware('permission:adjust-stock')->group(function () {
            Route::get('/ajustement',  [StockMovementController::class, 'showAdjustment'])->name('stocks.ajustement');
            Route::post('/ajustement', [StockMovementController::class, 'recordAdjustment'])->name('stocks.ajustement.store');
        });

        Route::get('/alertes', [AlertController::class, 'stockAlertes'])->name('stocks.alertes');
    });

    // ============================================================
    // ALERTES
    // ============================================================

    Route::prefix('alertes')->group(function () {
        Route::get('/', [AlertController::class, 'index'])->name('alertes.index');
        Route::post('/toutes-lues',  [AlertController::class, 'markAllAsRead'])->name('alertes.markAllAsRead');
        Route::post('/{id}/lue',     [AlertController::class, 'markAsRead'])->name('alertes.markAsRead');
        Route::delete('/{id}',       [AlertController::class, 'destroy'])->name('alertes.delete');
    });

    // ============================================================
    // VENTES
    // ============================================================

    Route::prefix('ventes')->group(function () {
        Route::get('/',        [VenteCommandController::class, 'index'])->name('ventes.index');
        Route::get('/create',    [VenteCommandController::class, 'create'])->name('ventes.create');
        Route::post('/create',   [VenteCommandController::class, 'store'])->name('ventes.store');
        Route::get('/{id}',    [VenteCommandController::class, 'show'])->middleware('permission:create-vente')->name('ventes.show');
        Route::post('/{id}/confirmer', [VenteCommandController::class, 'confirm'])->middleware('permission:edit-vente')->name('ventes.confirmer');
        Route::post('/{id}/livrer',    [VenteCommandController::class, 'markAsDelivered'])->middleware('permission:edit-vente')->name('ventes.livrer');
        Route::post('/{id}/payer',     [VenteCommandController::class, 'markAsPaid'])->middleware('permission:edit-vente')->name('ventes.payer');
        Route::delete('/{id}', [VenteCommandController::class, 'delete'])->middleware('permission:delete-vente')->name('ventes.delete');
    });

    // ============================================================
    // ACHATS
    // ============================================================

    Route::prefix('achats')->group(function () {
        Route::get('/',          [AchatCommandController::class, 'index'])->middleware('permission:view-achat')->name('achats.index');
        Route::get('/create',  [AchatCommandController::class, 'create'])->middleware('permission:create-achat')->name('achats.create');
        Route::post('/create', [AchatCommandController::class, 'store'])->middleware('permission:create-achat')->name('achats.store');
        Route::get('/{id}',      [AchatCommandController::class, 'show'])->middleware('permission:view-achat')->name('achats.show');
        Route::post('/{id}/recu',    [AchatCommandController::class, 'commandRecu'])->middleware('permission:edit-achat')->name('achats.commandrecu');
        Route::post('/{id}/payer',   [AchatCommandController::class, 'commandPayer'])->middleware('permission:edit-achat')->name('achats.commandpayer');
        Route::post('/{id}/attente', [AchatCommandController::class, 'commandAttente'])->middleware('permission:edit-achat')->name('achats.commandattente');
        Route::delete('/{id}', [AchatCommandController::class, 'destroy'])->middleware('permission:delete-achat')->name('achats.destroy');
    });

    // ============================================================
    // CLIENTS
    // ============================================================

    Route::prefix('clients')->group(function () {
        Route::get('/',           [ClientController::class, 'index'])->middleware('permission:view-client')->name('clients.index');
        Route::get('/create',     [ClientController::class, 'create'])->middleware('permission:create-client')->name('clients.create');
        Route::post('/',          [ClientController::class, 'store'])->middleware('permission:create-client')->name('clients.store');
        Route::get('/{client}',   [ClientController::class, 'show'])->middleware('permission:view-client')->name('clients.show');
        // Route::get('/{client}/edit', [ClientController::class, 'edit'])->middleware('permission:edit-client')->name('clients.edit');
        Route::get('/client/{id}/edit', function ($id) { $client = \App\Models\Client::findOrFail($id); return view('clients.edit', compact('client')); })->name('clients.edit');
        Route::put('/{client}',   [ClientController::class, 'update'])->middleware('permission:edit-client')->name('clients.update');
        Route::delete('/{client}',[ClientController::class, 'destroy'])->middleware('permission:delete-client')->name('clients.destroy');
    });

    // ============================================================
    // FOURNISSEURS
    // ============================================================

    Route::get('/fournisseurs', [FournisseurController::class, 'index'])->middleware('permission:view-fournisseur')->name('fournisseurs.index');
    Route::get('/fournisseurs/create', function () { return view('fournisseurs.create'); })->middleware('permission:create-fournisseur')->name('fournisseurs.create');
    Route::post('/fournisseurs', [FournisseurController::class, 'store'])->middleware('permission:create-fournisseur')->name('fournisseurs.store');
    Route::get('/fournisseurs/{id}', [FournisseurController::class, 'show'])->middleware('permission:view-fournisseur')->name('fournisseurs.show');
    Route::get('/fournisseurs/{id}/edit', function ($id) { $fournisseur = \App\Models\Fournisseur::findOrFail($id); return view('fournisseurs.edit', compact('fournisseur')); })->middleware('permission:edit-fournisseur')->name('fournisseurs.edit');
    Route::put('/fournisseurs/{id}', [FournisseurController::class, 'update'])->middleware('permission:edit-fournisseur')->name('fournisseurs.update');
    Route::delete('/fournisseurs/{id}', [FournisseurController::class, 'delete'])->middleware('permission:delete-fournisseur')->name('fournisseurs.delete');

    // ============================================================================
    // ROUTES FINANCES
    // ============================================================================
    Route::group(['middleware'=>['role:admin|manager|comptable']], function () {
    Route::get('/finances',[FinancialTransactionController::class, 'index'])->name('finances.index');
    Route::get('/finances/transactions', [FinancialTransactionController::class, 'getTransactions'])->name('finances.transactions');
    Route::get('/finances/transactions/create', function () { return view('finances.transactions.create'); })->name('finances.transactions.create');
    Route::post('/finances/transactions', [FinancialTransactionController::class, 'store'])->name('finances.transactions.store');
    Route::get('/finances/metriques', [FinancialTransactionController::class, 'getMetrics'])->name('finances.metriques');
    Route::get('/finances/periode', function () { return view('finances.periode'); })->name('finances.periode');
    Route::post('/finances/periode', [FinancialTransactionController::class, 'getByPeriod'])->name('finances.periode.show');
    Route::get('/finances/revenus', [FinancialTransactionController::class, 'getRevenues'])->name('finances.revenus');
    Route::get('/finances/depenses', [FinancialTransactionController::class, 'getExpenses'])->name('finances.depenses');
    });

    // ============================================================
    // CATÉGORIES
    // ============================================================

    Route::prefix('categories')->name('categories.')->group(function () {
    Route::get('/',           [CategorieController::class, 'index'])->middleware('permission:view-product')->name('index');
    Route::get('/create',     [CategorieController::class, 'create']) ->middleware('permission:create-product')->name('create');
    Route::post('/',          [CategorieController::class, 'store'])  ->middleware('permission:create-product')->name('store');
    Route::get('/{id}',       [CategorieController::class, 'show'])   ->name('show');
    Route::get('/{id}/edit',  [CategorieController::class, 'edit'])   ->middleware('permission:edit-product')->name('edit');
    Route::put('/{id}',       [CategorieController::class, 'update']) ->middleware('permission:edit-product')->name('update');
    Route::delete('/{id}',    [CategorieController::class, 'destroy'])->middleware('permission:delete-product')->name('destroy');

    });

    Route::prefix('invoices')->middleware('role:admin|manager|comptable|vendeur')->group(function () {
        // Lister les factures
        Route::get('/', [InvoiceController::class, 'list'])->name('invoices.list');

        // Générer une facture
        Route::post('/generate/{venteCommandId}', [InvoiceController::class, 'generate'])->name('invoices.generate');
    
        // Vérifier l'authenticité (public)
        Route::get('/verify/{invoiceNumber}', [InvoiceController::class, 'verify'])->name('invoices.verify');

          // Afficher une facture
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
    

         // Télécharger le PDF
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    });

    // ============================================================
    // paramètres
    // ============================================================

    Route::get('/parametres', function () { return view('parametres.index'); })->name('parametres.index');
    Route::get('/parametres/profil', function () { return view('parametres.profil'); })->name('parametres.profil');
    Route::get('/parametres/securite', function () { return view('parametres.securite'); })->name('parametres.securite');
    Route::get('/parametres/notifications', function () { return view('parametres.notifications'); })->name('parametres.notifications');
    Route::get('/parametres/entreprise', function () { return view('parametres.entreprise'); })->name('parametres.entreprise');

    Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])->name('profile.password.update');
});
