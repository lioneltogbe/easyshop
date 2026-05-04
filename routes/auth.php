<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;

/**
 * ============================================================
 * ROUTES D'AUTHENTIFICATION
 * ============================================================
 * 
 * Ce fichier contient toutes les routes publiques liées à
 * l'authentification (login, logout, etc.)
 * 
 * Toutes ces routes sont PUBLIQUES (pas de middleware 'auth')
 */

Route::middleware('guest')->group(function () {
    /**
     * PAGE DE LOGIN
     * 
     * GET /login
     * Affiche le formulaire de connexion
     * 
     * Middleware 'guest' : Redirige vers dashboard si déjà connecté
     */
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    /**
     * TRAITEMENT DU LOGIN
     * 
     * POST /login
     * Traite la soumission du formulaire de connexion
     * 
     * Valide :
     * - email (requis, email valide, existe en BDD)
     * - password (requis, min 6 caractères)
     * - remember (optionnel, booléen)
     * 
     * Retour :
     * - Succès : Redirige vers dashboard
     * - Erreur : Redirige vers login avec erreurs
     */
    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.post');

    /**
     * PAGE D'INSCRIPTION (optionnel)
     * 
     * GET /register
     * Affiche le formulaire d'inscription
     */
    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    /**
     * TRAITEMENT DE L'INSCRIPTION (optionnel)
     * 
     * POST /register
     * Traite la soumission du formulaire d'inscription
     * 
     * Valide :
     * - name (requis, string)
     * - email (requis, email valide, unique)
     * - password (requis, min 8, confirmed)
     * - password_confirmation (requis)
     * 
     * Retour :
     * - Succès : Crée l'utilisateur et le connecte
     * - Erreur : Redirige vers register avec erreurs
     */
    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.post');

    /**
     * PAGE MOT DE PASSE OUBLIÉ (optionnel)
     * 
     * GET /forgot-password
     * Affiche le formulaire pour réinitialiser le mot de passe
     */
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    /**
     * ENVOI EMAIL RÉINITIALISATION (optionnel)
     * 
     * POST /forgot-password
     * Envoie un email de réinitialisation
     * 
     * Valide :
     * - email (requis, email valide, existe en BDD)
     * 
     * Retour :
     * - Succès : Message de confirmation
     * - Erreur : Message d'erreur
     */
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])
        ->name('password.email');

    /**
     * PAGE RÉINITIALISATION MOT DE PASSE (optionnel)
     * 
     * GET /reset-password/{token}
     * Affiche le formulaire de réinitialisation
     * 
     * Paramètres :
     * - token : Token de réinitialisation
     * - email : Email de l'utilisateur (query string)
     */
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])
        ->name('password.reset');

    /**
     * TRAITEMENT RÉINITIALISATION (optionnel)
     * 
     * POST /reset-password
     * Traite la soumission du formulaire de réinitialisation
     * 
     * Valide :
     * - email (requis, email valide, existe en BDD)
     * - token (requis, valide)
     * - password (requis, min 8, confirmed)
     * - password_confirmation (requis)
     * 
     * Retour :
     * - Succès : Redirige vers login avec message
     * - Erreur : Redirige vers reset avec erreurs
     */
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])
        ->name('password.update');
});

/**
 * ROUTES PROTÉGÉES (Utilisateur doit être connecté)
 * 
 * Middleware 'auth' : Redirige vers login si pas connecté
 */
Route::middleware('auth')->group(function () {
    /**
     * LOGOUT
     * 
     * POST /logout
     * Déconnecte l'utilisateur
     * 
     * Actions :
     * - Invalide la session
     * - Supprime le cookie de session
     * - Redirige vers login
     * 
     * Retour :
     * - Redirige vers login avec message de succès
     */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    /**
     * PAGE MON PROFIL
     * 
     * GET /profile
     * Affiche le profil de l'utilisateur connecté
     */
    Route::get('/profile', [AuthController::class, 'showProfile'])
        ->name('profile.show');

    /**
     * PAGE MODIFICATION PROFIL
     * 
     * GET /profile/edit
     * Affiche le formulaire de modification du profil
     */
    Route::get('/profile/edit', [AuthController::class, 'editProfile'])
        ->name('profile.edit');

    /**
     * TRAITEMENT MODIFICATION PROFIL
     * 
     * PUT /profile
     * Traite la modification du profil
     * 
     * Valide :
     * - name (requis, string)
     * - email (requis, email valide, unique sauf pour l'utilisateur)
     * - phone (optionnel)
     * - address (optionnel)
     * 
     * Retour :
     * - Succès : Redirige vers profil avec message
     * - Erreur : Redirige vers edit avec erreurs
     */
    Route::put('/profile', [AuthController::class, 'updateProfile'])
        ->name('profile.update');

    /**
     * MODIFICATION MOT DE PASSE
     * 
     * PUT /profile/password
     * Modifie le mot de passe de l'utilisateur
     * 
     * Valide :
     * - current_password (requis, doit correspondre au mot de passe actuel)
     * - password (requis, min 8, confirmed)
     * - password_confirmation (requis)
     * 
     * Retour :
     * - Succès : Message de succès
     * - Erreur : Message d'erreur
     */
    Route::put('/profile/password', [AuthController::class, 'updatePassword'])
        ->name('profile.password.update');

    /**
     * SUPPRESSION DE COMPTE (optionnel)
     * 
     * DELETE /profile
     * Supprime le compte de l'utilisateur
     * 
     * Actions :
     * - Supprime l'utilisateur de la BDD
     * - Invalide la session
     * - Redirige vers accueil
     * 
     * Retour :
     * - Redirige vers accueil avec message
     */
    Route::delete('/profile', [AuthController::class, 'deleteAccount'])
        ->name('profile.delete');
});

/**
 * ROUTES SPÉCIALES
 */

/**
 * VÉRIFICATION EMAIL (optionnel)
 * 
 * GET /email/verify
 * Affiche la page de vérification d'email
 */
Route::get('/email/verify', [AuthController::class, 'verifyEmailNotice'])
    ->middleware('auth')
    ->name('verification.notice');

/**
 * VÉRIFICATION EMAIL - LIEN
 * 
 * GET /email/verify/{id}/{hash}
 * Vérifie l'email via le lien reçu
 */
Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

/**
 * RENVOI EMAIL DE VÉRIFICATION
 * 
 * POST /email/verification-notification
 * Renvoie l'email de vérification
 */
Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
