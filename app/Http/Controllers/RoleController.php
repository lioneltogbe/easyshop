<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    // ──────────────────────────────────────────────
    // INDEX
    // ──────────────────────────────────────────────

    public function index()
    {
        $roles = Role::with('permissions')->orderBy('name')->paginate(15);
        return view('roles.index', compact('roles'));
    }

    // ──────────────────────────────────────────────
    // CREATE
    // ──────────────────────────────────────────────

    public function create()
    {
        $permissions = Permission::orderBy('name')->get();
        return view('roles.create', compact('permissions'));
    }

    // ──────────────────────────────────────────────
    // STORE
    // ──────────────────────────────────────────────

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'name.unique' => 'Un rôle avec ce nom existe déjà.',
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (! empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()
            ->route('roles.show', $role)
            ->with('success', "Rôle « {$role->name} » créé avec succès.");
    }

    // ──────────────────────────────────────────────
    // SHOW
    // ──────────────────────────────────────────────

    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }

    // ──────────────────────────────────────────────
    // EDIT
    // ──────────────────────────────────────────────

    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('name')->get();
        return view('roles.edit', compact('role', 'permissions'));
    }

    // ──────────────────────────────────────────────
    // UPDATE
    // ──────────────────────────────────────────────

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()
            ->route('roles.show', $role)
            ->with('success', "Rôle « {$role->name} » mis à jour.");
    }

    // ──────────────────────────────────────────────
    // DESTROY
    // ──────────────────────────────────────────────

    public function destroy(Role $role)
    {
        // Protéger le rôle admin
        if ($role->name === 'admin') {
            return back()->withErrors('Le rôle admin ne peut pas être supprimé.');
        }

        $role->delete();

        return redirect()
            ->route('roles.index')
            ->with('success', "Rôle supprimé.");
    }
}
