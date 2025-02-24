<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    /**
     * Afficher la liste des rôles.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    /**
     * Créer un rôle.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation des données
        $validated = Role::validate($request->all());

        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()
            ], 422);
        }

        try {
            $role = Role::create($request->only('name', 'description'));
            return response()->json($role, 201);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du rôle: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur.'], 500);
        }
    }

    /**
     * Afficher les détails d'un rôle spécifique.
     *
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function show(Role $role)
    {
        return response()->json($role);
    }

    /**
     * Mettre à jour un rôle.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Role $role)
    {
        // Validation des données
        $validated = Role::validate($request->all());

        if ($validated->fails()) {
            return response()->json([
                'errors' => $validated->errors()
            ], 422);
        }

        try {
            $role->update($request->only('name', 'description'));
            return response()->json($role);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du rôle: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur.'], 500);
        }
    }

    /**
     * Supprimer un rôle.
     *
     * @param \App\Models\Role $role
     * @return \Illuminate\Http\Response
     */
    public function destroy(Role $role)
    {
        try {
            $role->delete();
            return response()->json(['message' => 'Rôle supprimé avec succès.']);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du rôle: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur.'], 500);
        }
    }

    /**
     * Assigner une permission à un rôle.
     *
     * @param \App\Models\Role $role
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function assignPermission(Role $role, Permission $permission)
    {
        try {
            $role->assignPermission($permission);
            return response()->json(['message' => "Permission '{$permission->name}' assignée au rôle '{$role->name}' avec succès."]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'assignation de la permission: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur.'], 500);
        }
    }

    /**
     * Révoquer une permission d'un rôle.
     *
     * @param \App\Models\Role $role
     * @param \App\Models\Permission $permission
     * @return \Illuminate\Http\Response
     */
    public function revokePermission(Role $role, Permission $permission)
    {
        try {
            $role->revokePermission($permission);
            return response()->json(['message' => "Permission '{$permission->name}' révoquée du rôle '{$role->name}' avec succès."]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la révocation de la permission: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur interne du serveur.'], 500);
        }
    }
}
