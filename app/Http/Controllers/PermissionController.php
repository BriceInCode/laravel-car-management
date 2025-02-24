<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PermissionController extends Controller
{
    /**
     * Affiche la liste des permissions.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $permissions = Permission::all(); // Récupérer toutes les permissions
        return response()->json($permissions);
    }

    /**
     * Retourne la vue pour créer une nouvelle permission.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('permissions.create'); // Vue pour créer une permission
    }

    /**
     * Crée une nouvelle permission dans la base de données.
     *
     * @param StorePermissionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StorePermissionRequest $request)
    {
        $validatedData = $request->validated(); // Validation des données reçues

        try {
            $permission = Permission::create($validatedData); // Créer la permission
            Log::info("Permission '{$permission->name}' créée avec succès.");
            return response()->json($permission, 201); // Retourner la permission créée
        } catch (\Exception $e) {
            Log::error("Erreur lors de la création de la permission: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la création de la permission.'], 500);
        }
    }

    /**
     * Affiche une permission spécifique.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Permission $permission)
    {
        return response()->json($permission); // Retourner les détails de la permission
    }

    /**
     * Retourne la vue pour éditer une permission existante.
     *
     * @param Permission $permission
     * @return \Illuminate\View\View
     */
    public function edit(Permission $permission)
    {
        return view('permissions.edit', compact('permission')); // Vue pour éditer une permission
    }

    /**
     * Met à jour les informations d'une permission spécifique.
     *
     * @param UpdatePermissionRequest $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdatePermissionRequest $request, Permission $permission)
    {
        $validatedData = $request->validated(); // Validation des données

        try {
            $permission->update($validatedData); // Mettre à jour la permission
            Log::info("Permission '{$permission->name}' mise à jour avec succès.");
            return response()->json($permission);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la mise à jour de la permission: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la mise à jour de la permission.'], 500);
        }
    }

    /**
     * Supprime une permission de la base de données.
     *
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Permission $permission)
    {
        try {
            // Vérifier que la permission n'est pas utilisée par des utilisateurs
            if ($permission->users()->exists()) {
                return response()->json(['error' => 'Impossible de supprimer une permission utilisée par des utilisateurs.'], 400);
            }

            $permission->delete(); // Supprimer la permission
            Log::info("Permission '{$permission->name}' supprimée avec succès.");
            return response()->json(['message' => 'Permission supprimée avec succès.']);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la suppression de la permission: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la suppression de la permission.'], 500);
        }
    }

    /**
     * Assigne une permission à un utilisateur spécifique.
     *
     * @param Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function assignToUser(Request $request, Permission $permission)
    {
        $user = $this->findUserById($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        try {
            $permission->users()->attach($user->id); // Assigner la permission
            return response()->json(['message' => "Permission '{$permission->name}' assignée à l'utilisateur {$user->name}."]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'assignation de la permission: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de l\'assignation de la permission.'], 400);
        }
    }

    /**
     * Révoque une permission d'un utilisateur spécifique.
     *
     * @param Request $request
     * @param Permission $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function revokeFromUser(Request $request, Permission $permission)
    {
        $user = $this->findUserById($request->user_id);

        if (!$user) {
            return response()->json(['error' => 'Utilisateur non trouvé.'], 404);
        }

        try {
            $permission->users()->detach($user->id); // Révoquer la permission
            return response()->json(['message' => "Permission '{$permission->name}' révoquée de l'utilisateur {$user->name}."]);
        } catch (\Exception $e) {
            Log::error("Erreur lors de la révocation de la permission: " . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la révocation de la permission.'], 400);
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
        return User::find($userId); // Recherche d'un utilisateur par ID
    }
}
