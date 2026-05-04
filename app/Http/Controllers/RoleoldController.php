<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
     /**
     * Afficher la liste des rôles
     */
    public function index()
    {
        $roles = Role::with('permissions')->paginate(15);
        return view('roles.index', compact('roles'));
    }

      /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

      /**
     * Créer un nouveau rôle
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'slug' => 'required|string|unique:roles',
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
 
        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ]);
 
        // Assigner les permissions
        $role->permissions()->attach($validated['permissions']);
 
        return redirect()->route('roles.show', $role->id)
                       ->with('success', 'Rôle créé avec succès');
    }
 
    /**
     * Afficher les détails d'un rôle
     */
    public function show(Role $role)
    {
        $role->load('permissions', 'users');
        return view('roles.show', compact('role'));
    }
 
    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::all();
        return view('roles.edit', compact('role', 'permissions'));
    }
 
    /**
     * Mettre à jour un rôle
     */
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'slug' => 'required|string|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);
 
        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'description' => $validated['description'] ?? null,
        ]);
 
        // Mettre à jour les permissions
        $role->permissions()->sync($validated['permissions']);
 
        return redirect()->route('roles.show', $role->id)
                       ->with('success', 'Rôle mis à jour avec succès');
    }
 
    /**
     * Supprimer un rôle
     */
    public function delete(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')->with('success', 'Rôle supprimé avec succès');
    }
}
