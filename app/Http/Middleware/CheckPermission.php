<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * ============================================================
 * MIDDLEWARE DE PERMISSION
 * ============================================================
 * 
 * Vérifie que l'utilisateur a la permission requise
 * 
 * Utilisation :
 * Route::middleware('permission:create-user')->group(...);
 */

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $permission
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        // Vérifier que l'utilisateur est connecté
        if (!$request->user()) {
            return redirect(route('login'));
        }

        // Vérifier que l'utilisateur a la permission requise
        if (!$request->user()->hasPermission($permission)) {
            abort(403, "Vous n'avez pas la permission d'accéder à cette ressource.");
        }
        return $next($request);
    }
}

