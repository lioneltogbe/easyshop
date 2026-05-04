<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthClientController extends Controller
{
    // ──────────────────────────────────────────────
    // POST /api/auth/login
    // Corps : { "email": "...", "password": "...", "device": "web" }
    // ──────────────────────────────────────────────

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email'    => 'required|email',
            'password' => 'required|string',
            'device'   => 'nullable|string|max:80',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Chercher l'utilisateur (opérateurs internes ET clients)
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email ou mot de passe incorrect.',
            ], 401);
        }

        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Compte désactivé. Contactez l\'administrateur.',
            ], 403);
        }

        // Révoquer les anciens tokens du même device
        $user->tokens()->where('name', $request->device ?? 'api-client')->delete();

        // Créer le token Sanctum
        $token = $user->createToken($request->device ?? 'api-client')->plainTextToken;

        // Chercher le profil client lié (si existe)
        $clientProfile = Client::where('email', $user->email)->first();

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => $user->getRoleNames()->first(),
                'client_profile' => $clientProfile ? [
                    'id'               => $clientProfile->id,
                    'credit_disponible'=> $clientProfile->getAvailableCredit(),
                    'phone'            => $clientProfile->phone,
                    'address'          => $clientProfile->address,
                    'city'             => $clientProfile->city,
                ] : null,
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    // POST /api/auth/register
    // Crée un User avec rôle "commercial" + Client lié
    // ──────────────────────────────────────────────

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone'    => 'nullable|string|max:30',
            'address'  => 'nullable|string|max:255',
            'city'     => 'nullable|string|max:80',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors(),
            ], 422);
        }

        // Créer l'utilisateur
        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'is_active' => true,
        ]);

        // Assigner le rôle client (Spatie)
        $user->assignRole('commercial');

        // Créer le profil client lié
        $client = Client::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'phone'         => $request->phone,
            'address'       => $request->address,
            'city'          => $request->city,
            'creditLimit'   => 0,
            'current_credit'=> 0,
        ]);

        $token = $user->createToken('api-client')->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'role'           => 'commercial',
                'client_profile' => [
                    'id'                => $client->id,
                    'credit_disponible' => 0,
                ],
            ],
        ], 201);
    }

    // ──────────────────────────────────────────────
    // GET /api/auth/me
    // ──────────────────────────────────────────────

    public function me(Request $request)
    {
        $user          = $request->user();
        $clientProfile = Client::where('email', $user->email)->first();

        return response()->json([
            'success' => true,
            'user'    => [
                'id'             => $user->id,
                'name'           => $user->name,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'role'           => $user->getRoleNames()->first(),
                'client_profile' => $clientProfile ? [
                    'id'                => $clientProfile->id,
                    'phone'             => $clientProfile->phone,
                    'address'           => $clientProfile->address,
                    'city'              => $clientProfile->city,
                    'country'           => $clientProfile->country,
                    'credit_limite'     => $clientProfile->creditLimit,
                    'credit_disponible' => $clientProfile->getAvailableCredit(),
                ] : null,
            ],
        ]);
    }

    // ──────────────────────────────────────────────
    // POST /api/auth/logout
    // ──────────────────────────────────────────────

    public function logout(Request $request)
    {
        // Révoquer uniquement le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Déconnecté avec succès.',
        ]);
    }
}
