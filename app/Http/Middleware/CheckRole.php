<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ============================================================
 * MIDDLEWARE DE RÔLE
 * ============================================================
 * 
 * Vérifie que l'utilisateur a l'un des rôles requis
 * 
 * Utilisation :
 * Route::middleware('role:admin,manager')->group(...);
 */

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  ...$roles
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Vérifier que l'utilisateur est connecté
        if (!$request->user()) {
            return redirect(route('login'));
        }

        // Vérifier que l'utilisateur a l'un des rôles requis
        foreach ($roles as $role) {
            if ($request->user()->hasRole($role)) {
                return $next($request);
            }
        }

        // Accès refusé
        abort(403, 'Vous n\'avez pas accès à cette ressource.');
    }
}

