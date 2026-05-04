<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Traits\HasIntelligentPagination;

class UserController extends Controller
{
    use HasIntelligentPagination;

    protected array $searchable = ['name', 'email', 'phone'];
    protected array $sortable   = ['name', 'email', 'created_at', 'is_active'];

    // ──────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────

    public function index(\Illuminate\Http\Request $request)
    {
        $query = User::with('roles');

        $users = $this->applyIntelligentPagination(
            $query, $request,
            $this->searchable,
            $this->sortable,
            15
        );

        return view('users.index', [
            'users'  => $users,
            'params' => $this->getPaginationParams($request),
        ]);
    }

    // ──────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('users.create', compact('roles'));
    }

    // ──────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'phone'     => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        // Spatie : syncRoles accepte des IDs ou des noms
        $user->syncRoles($validated['roles'] ?? []);

        return redirect()
            ->route('users.show', $user)
            ->with('success', "Utilisateur « {$user->name} » créé.");
    }

    // ──────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────

    public function show(User $user)
    {
        $user->load('roles', 'permissions');
        return view('users.show', compact('user'));
    }

    // ──────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────

    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::orderBy('name')->get();
        return view('users.edit', compact('user', 'roles'));
    }

    // ──────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'phone'     => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $user->update([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? $user->is_active,
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()
            ->route('users.show', $user)
            ->with('success', "Utilisateur « {$user->name} » mis à jour.");
    }

    // ──────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────

    public function destroy(User $user)
    {
        // Ne pas supprimer son propre compte
        if ($user->id === auth()->id()) {
            return back()->withErrors('Vous ne pouvez pas supprimer votre propre compte.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', "Utilisateur supprimé.");
    }
}
