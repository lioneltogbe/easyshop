<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

/**
 * ============================================================
 * CONTROLLER D'AUTHENTIFICATION
 * ============================================================
 * 
 * Gère tous les processus d'authentification :
 * - Login / Logout
 * - Inscription
 * - Réinitialisation mot de passe
 * - Gestion du profil
 */

class AuthController extends Controller
{
    /**
     * ============================================================
     * LOGIN
     * ============================================================
     */

    /**
     * Affiche le formulaire de connexion
     * 
     * GET /login
     * 
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Traite la connexion
     * POST /login
     */
   

       public function login(Request $request){

        // VALIDATION DES DONNÉES
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'L\'email est requis',
            'email.email' => 'L\'email doit être valide',
            'password.required' => 'Le mot de passe est requis',
            'password.min' => 'Le mot de passe doit contenir au moins 6 caractères',
        ]);

        // VÉRIFIER QUE L'UTILISATEUR EXISTE
        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Aucun utilisateur trouvé avec cet email.',
                ]);
        }

        //  VÉRIFIER LE MOT DE PASSE
        if (!Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'password' => 'Le mot de passe est incorrect.',
                ]);
        }

        // VÉRIFIER QUE L'UTILISATEUR EST ACTIF
        if (!$user->is_active) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Ce compte est désactivé. Contactez l\'administrateur.',
                ]);
        }

        

        // CONNEXION
        Auth::login($user, $request->filled('remember'));
        $request->session()->regenerate();

        return redirect()
            ->intended(route('dashboard'))
            ->with('success', 'Bienvenue ' . $user->name . ' !');
    }

    /**
     * ============================================================
     * LOGOUT
     * ============================================================
     */

    /**
     * Déconnecte l'utilisateur
     * 
     * POST /logout
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        // Déconnexion
        Auth::logout();

        // Invalider la session
        $request->session()->invalidate();

        // Régénérer le token CSRF
        $request->session()->regenerateToken();

        //return redirect(route('login'))
         return redirect()
           ->route('login')
            ->with('success', 'Vous avez été déconnecté avec succès.');
    }

    /**
     * ============================================================
     * INSCRIPTION (OPTIONNEL)
     * ============================================================
     */

    /**
     * Affiche le formulaire d'inscription
     * 
     * GET /register
     * 
     * @return \Illuminate\View\View
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Traite l'inscription
     * 
     * POST /register
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
        ]);

        // Créer l'utilisateur
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assigner le rôle par défaut (Client)
        $user->roles()->attach(
            \App\Models\Role::where('slug', 'client')->first()->id
        );

        // Connecter l'utilisateur
        Auth::login($user);

        return redirect(route('dashboard'))
            ->with('success', 'Inscription réussie ! Bienvenue ' . $user->name);
    }

    /**
     * ============================================================
     * MOT DE PASSE OUBLIÉ (OPTIONNEL)
     * ============================================================
     */

    /**
     * Affiche le formulaire mot de passe oublié
     * 
     * GET /forgot-password
     * 
     * @return \Illuminate\View\View
     */
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie le lien de réinitialisation
     * 
     * POST /forgot-password
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendResetLink(Request $request)
    {
        // Validation
        $request->validate(['email' => 'required|email']);

        // Envoyer le lien
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Affiche le formulaire de réinitialisation
     * 
     * GET /reset-password/{token}
     * 
     * @param Request $request
     * @param string $token
     * @return \Illuminate\View\View
     */
    public function showResetPasswordForm(Request $request, $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Traite la réinitialisation du mot de passe
     * 
     * POST /reset-password
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request)
    {
        // Validation
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Réinitialiser le mot de passe
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect(route('login'))->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * ============================================================
     * GESTION DU PROFIL
     * ============================================================
     */

    /**
     * Affiche le profil de l'utilisateur
     * 
     * GET /profile
     * 
     * @return \Illuminate\View\View
     */
    public function showProfile()
    {
        return view('profile.show', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Affiche le formulaire de modification du profil
     * 
     * GET /profile/edit
     * 
     * @return \Illuminate\View\View
     */
    public function editProfile()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Met à jour le profil de l'utilisateur
     * 
     * PUT /profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateProfile(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Mettre à jour l'utilisateur
        Auth::user()->update($validated);

        return redirect(route('parametres.profil'))
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Met à jour le mot de passe
     * 
     * PUT /profile/password
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePassword(Request $request)
    {
        // Validation
        $validated = $request->validate([
            'current_password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('Le mot de passe actuel est incorrect.');
                    }
                },
            ],
            'password' => 'required|min:8|confirmed',
        ]);

        // Mettre à jour le mot de passe
        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect(route('parametres.securite'))
            ->with('success', 'Mot de passe mis à jour avec succès.');
    }

    /**
     * Supprime le compte de l'utilisateur
     * 
     * DELETE /profile
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(Request $request)
    {
        // Validation du mot de passe
        $request->validate([
            'password' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, Auth::user()->password)) {
                        $fail('Le mot de passe est incorrect.');
                    }
                },
            ],
        ]);

        // Supprimer l'utilisateur
        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/')
            ->with('success', 'Votre compte a été supprimé.');
    }

    /**
     * ============================================================
     * VÉRIFICATION EMAIL (OPTIONNEL)
     * ============================================================
     */

    /**
     * Affiche la page de vérification d'email
     * 
     * GET /email/verify
     * 
     * @return \Illuminate\View\View
     */
    public function verifyEmailNotice()
    {
        return view('auth.verify-email');
    }

    /**
     * Vérifie l'email via le lien
     * 
     * GET /email/verify/{id}/{hash}
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(route('dashboard'));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new \Illuminate\Auth\Events\Verified($request->user()));
        }

        return redirect(route('dashboard'))
            ->with('success', 'Email vérifié avec succès.');
    }

    /**
     * Renvoie l'email de vérification
     * 
     * POST /email/verification-notification
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resendVerificationEmail(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect(route('dashboard'));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()
            ->with('status', 'Email de vérification renvoyé.');
    }
}
