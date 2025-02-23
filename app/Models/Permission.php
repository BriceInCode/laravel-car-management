<?php

namespace App\Models;

use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Permission extends Model
{
    use HasFactory;

    /**
     * Attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',        // Nom de la permission (ex. 'create_car', 'edit_user')
        'description', // Description de la permission
    ];

    /**
     * Convertir le nom de la permission en enum PermissionType.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => PermissionType::class, // Convertir le nom de la permission en enum PermissionType
    ];

    /**
     * Validation des données de la permission.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data)
    {
        $messages = [
            'name.required' => 'Le nom de la permission est obligatoire.',
            'name.string' => 'Le nom de la permission doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la permission ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Ce nom de permission est déjà utilisé, veuillez en choisir un autre.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 255 caractères.',
        ];

        return Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions')->ignore($data['id'] ?? null), // Ignorer lors de la mise à jour
            ],
            'description' => 'nullable|string|max:255',
        ], $messages); // Application des messages personnalisés
    }

    /**
     * Relation avec les utilisateurs ayant cette permission.
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'permission_user'); // Relation plusieurs-à-plusieurs avec les utilisateurs
    }

    /**
     * Assigner une permission à un utilisateur.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function assignToUser(User $user)
    {
        if ($this->users()->where('user_id', $user->id)->exists()) {
            Log::warning("L'utilisateur ID {$user->id} possède déjà la permission '{$this->name}'.");
            return;
        }

        $this->users()->attach($user); // Attacher la permission à un utilisateur
        Log::info("Permission '{$this->name}' assignée à l'utilisateur ID {$user->id} identifié par {$user->name}.");
    }

    /**
     * Révoquer une permission d'un utilisateur.
     *
     * @param \App\Models\User $user
     * @return void
     */
    public function revokeFromUser(User $user)
    {
        if (!$this->users()->where('user_id', $user->id)->exists()) {
            Log::warning("L'utilisateur ID {$user->id} ne possède pas la permission '{$this->name}'.");
            return;
        }

        $this->users()->detach($user); // Détacher la permission de l'utilisateur
        Log::info("Permission '{$this->name}' révoquée de l'utilisateur ID {$user->id} identifié par {$user->name}.");
    }

    /**
     * Méthode de démarrage pour les événements du modèle.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($permission) {
            // Vérifier que la permission n'existe pas déjà
            $validation = self::validate($permission->getAttributes());
            if ($validation->fails()) {
                throw new \Exception("Validation échouée pour la permission : " . implode(', ', $validation->errors()->all()));
            }

            // Ajouter des valeurs par défaut si nécessaire
            if (empty($permission->description)) {
                $permission->description = "Description par défaut pour la permission " . $permission->name;
            }

            // Log de la création de la permission
            Log::info("Création d'une nouvelle permission : " . $permission->name);
        });

        static::updating(function ($permission) {
            // Vérifier que le nom de la permission n'est pas modifié en un nom déjà existant
            if ($permission->isDirty('name') && self::where('name', $permission->name)->exists()) {
                throw new \Exception("Une permission avec ce nom existe déjà.");
            }

            // Log de la mise à jour de la permission
            Log::info("Mise à jour de la permission : " . $permission->name);
        });

        static::deleting(function ($permission) {
            // Vérifier que la permission n'est pas utilisée par des utilisateurs actifs
            if ($permission->users()->exists()) {
                throw new \Exception("Impossible de supprimer une permission utilisée par des utilisateurs actifs.");
            }

            // Log de la suppression de la permission
            Log::info("Suppression de la permission : " . $permission->name);
        });
    }
}
