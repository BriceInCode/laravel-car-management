<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionController extends Controller
{
    /**
     * Affiche la liste des permissions.
     */
    public function index()
    {
        $permissions = Permission::all();  // Récupérer toutes les permissions
        return response()->json($permissions);
    }

    /**
     * Retourne la vue pour créer une nouvelle permission.
     */
    public function create()
    {
        return view('permissions.create');  // Vue pour créer une permission
    }

    /**
     * Crée une nouvelle permission dans la base de données.
     */
    public function store(StorePermissionRequest $request)
    {
        $validatedData = $request->validated();  // Validation des données reçues

        try {
            // Créer une nouvelle permission avec les données validées
            $permission = Permission::create($validatedData);
            Log::info("Permission '{$permission->name}' créée avec succès.");
            return response()->json($permission, 201);  // Retourner la permission créée
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la création de la permission.'], 500);
        }
    }

    /**
     * Affiche une permission spécifique.
     */
    public function show(Permission $permission)
    {
        return response()->json($permission);  // Retourne les détails de la permission
    }

    /**
     * Retourne la vue pour éditer une permission existante.
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission'));  // Vue pour éditer une permission
    }

    /**
     * Met à jour les informations d'une permission spécifique.
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $validatedData = $request->validated();  // Validation des données

        try {
            // Mettre à jour la permission avec les nouvelles données
            $permission->update($validatedData);
            Log::info("Permission '{$permission->name}' mise à jour avec succès.");
            return response()->json($permission);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la mise à jour de la permission.'], 500);
        }
    }

    /**
     * Supprime une permission de la base de données.
     */
    public function destroy(Permission $permission)
    {
        try {
            // Vérifier que la permission n'est pas utilisée par des utilisateurs
            if ($permission->users()->exists()) {
                return response()->json(['error' => 'Impossible de supprimer une permission utilisée par des utilisateurs.'], 400);
            }

            // Supprimer la permission
            $permission->delete();
            Log::info("Permission '{$permission->name}' supprimée avec succès.");
            return response()->json(['message' => 'Permission supprimée avec succès.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la suppression de la permission.'], 500);
        }
    }

    /**
     * Assigne une permission à un utilisateur spécifique.
     */
    public function assignToUser(Request $request, Permission $permission)
    {
        // Vérifier si l'utilisateur existe avec l'ID fourni
        $user = $this->findUserById($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        try {
            // Assigner la permission à l'utilisateur
            $permission->assignToUser($user);
            return response()->json(['message' => "Permission '{$permission->name}' assignée à l'utilisateur {$user->name}."]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Révoque une permission d'un utilisateur spécifique.
     */
    public function revokeFromUser(Request $request, Permission $permission)
    {
        // Vérifier si l'utilisateur existe avec l'ID fourni
        $user = $this->findUserById($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        try {
            // Révoquer la permission de l'utilisateur
            $permission->revokeFromUser($user);
            return response()->json(['message' => "Permission '{$permission->name}' révoquée de l'utilisateur {$user->name}."]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Trouver un utilisateur par son ID.
     *
     * @param int $userId
     * @return User|null
     */
    private function findUserById(int $userId)
    {
        return User::find($userId);  // Recherche d'un utilisateur par ID
    }
}
