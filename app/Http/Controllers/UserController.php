<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Afficher la liste des utilisateurs.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Afficher un utilisateur spécifique.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        return response()->json($user);
    }

    /**
     * Créer un nouvel utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Validation des données
        $validation = User::validate($data);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        // Création de l'utilisateur
        $user = User::create($data);

        Log::info("Nouvel utilisateur créé : " . $user->name);

        return response()->json(['message' => 'Utilisateur créé avec succès.', 'user' => $user], 201);
    }

    /**
     * Mettre à jour un utilisateur.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $data = $request->all();

        // Validation des données
        $validation = User::validate($data, $id);
        if ($validation->fails()) {
            return response()->json(['errors' => $validation->errors()], 400);
        }

        // Mise à jour de l'utilisateur
        $user->update($data);

        Log::info("Utilisateur mis à jour : " . $user->name);

        return response()->json(['message' => 'Utilisateur mis à jour avec succès.', 'user' => $user]);
    }

    /**
     * Supprimer un utilisateur.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        // Supprimer l'utilisateur
        $user->delete();

        Log::info("Utilisateur supprimé : " . $user->name);

        return response()->json(['message' => 'Utilisateur supprimé avec succès.']);
    }

    /**
     * Activer un utilisateur.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function activate($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->activate();

        return response()->json(['message' => 'Utilisateur activé avec succès.']);
    }

    /**
     * Désactiver un utilisateur.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deactivate($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Utilisateur non trouvé.'], 404);
        }

        $user->deactivate();

        return response()->json(['message' => 'Utilisateur désactivé avec succès.']);
    }

    /**
     * Assigner un rôle à un utilisateur.
     *
     * @param  int  $id
     * @param  int  $roleId
     * @return \Illuminate\Http\Response
     */
    public function assignRole($id, $roleId)
    {
        $user = User::find($id);
        $role = Role::find($roleId);

        if (!$user || !$role) {
            return response()->json(['message' => 'Utilisateur ou rôle non trouvé.'], 404);
        }

        $user->role()->associate($role);
        $user->save();

        Log::info("Rôle assigné à l'utilisateur : " . $user->name);

        return response()->json(['message' => 'Rôle assigné à l\'utilisateur avec succès.']);
    }

    /**
     * Assigner une permission à un utilisateur.
     *
     * @param  int  $id
     * @param  int  $permissionId
     * @return \Illuminate\Http\Response
     */
    public function assignPermission($id, $permissionId)
    {
        $user = User::find($id);
        $permission = Permission::find($permissionId);

        if (!$user || !$permission) {
            return response()->json(['message' => 'Utilisateur ou permission non trouvé.'], 404);
        }

        $user->permissions()->attach($permission);

        Log::info("Permission assignée à l'utilisateur : " . $user->name);

        return response()->json(['message' => 'Permission assignée à l\'utilisateur avec succès.']);
    }

    /**
     * Révoquer une permission d'un utilisateur.
     *
     * @param  int  $id
     * @param  int  $permissionId
     * @return \Illuminate\Http\Response
     */
    public function revokePermission($id, $permissionId)
    {
        $user = User::find($id);
        $permission = Permission::find($permissionId);

        if (!$user || !$permission) {
            return response()->json(['message' => 'Utilisateur ou permission non trouvé.'], 404);
        }

        $user->permissions()->detach($permission);

        Log::info("Permission révoquée pour l'utilisateur : " . $user->name);

        return response()->json(['message' => 'Permission révoquée de l\'utilisateur avec succès.']);
    }
}
