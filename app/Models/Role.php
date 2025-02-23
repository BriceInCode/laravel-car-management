<?php

namespace App\Models;

use App\Enums\RoleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Convertir le nom du rôle en enum RoleType.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name' => RoleType::class,
    ];

    /**
     * Validation des données du rôle.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data)
    {
        $messages = [
            'name.required' => 'Le nom du rôle est obligatoire.',
            'name.string' => 'Le nom du rôle doit être une chaîne de caractères.',
            'name.max' => 'Le nom du rôle ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Ce nom de rôle est déjà utilisé, veuillez en choisir un autre.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'description.max' => 'La description ne doit pas dépasser 255 caractères.',
        ];

        return Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($data['id'] ?? null),
            ],
            'description' => 'nullable|string|max:255',
        ], $messages); // Application des messages personnalisés
    }

    /**
     * Relation avec les permissions de ce rôle.
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Relation avec les utilisateurs ayant ce rôle.
     *
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Étendre une requête pour n'inclure que les rôles d'un nom donné.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Assigner une permission à ce rôle.
     *
     * @param \App\Models\Permission $permission
     * @return void
     */
    public function assignPermission(Permission $permission)
    {
        if ($this->permissions()->where('permission_id', $permission->id)->exists()) {
            Log::warning("La permission '{$permission->name}' est déjà assignée au rôle '{$this->name}'.");
            return;
        }

        $this->permissions()->attach($permission);
        Log::info("Permission '{$permission->name}' assignée au rôle '{$this->name}'.");
    }

    /**
     * Révoquer une permission de ce rôle.
     *
     * @param \App\Models\Permission $permission
     * @return void
     */
    public function revokePermission(Permission $permission)
    {
        if (!$this->permissions()->where('permission_id', $permission->id)->exists()) {
            Log::warning("La permission '{$permission->name}' n'est pas assignée au rôle '{$this->name}'.");
            return;
        }

        $this->permissions()->detach($permission);
        Log::info("Permission '{$permission->name}' révoquée du rôle '{$this->name}'.");
    }

    /**
     * Méthode de démarrage pour les événements du modèle.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($role) {
            self::validateAndProcessRole($role);
            Log::info("Création d'un nouveau rôle : " . $role->name);
        });

        static::updating(function ($role) {
            self::validateAndProcessRole($role);
            Log::info("Mise à jour du rôle : " . $role->name);
        });

        static::deleting(function ($role) {
            if ($role->users()->exists()) {
                throw new \Exception("Impossible de supprimer un rôle utilisé par des utilisateurs actifs.");
            }

            Log::info("Suppression du rôle : " . $role->name);
        });
    }

    /**
     * Validation et traitement des données du rôle avant création ou mise à jour.
     *
     * @param \App\Models\Role $role
     * @throws \Exception
     */
    protected static function validateAndProcessRole($role)
    {
        $validation = self::validate($role->getAttributes());
        if ($validation->fails()) {
            throw new \Exception("Validation échouée : " . implode(', ', $validation->errors()->all()));
        }

        if (self::where('name', $role->name)->exists()) {
            throw new \Exception("Un rôle avec ce nom existe déjà.");
        }

        if (empty($role->description)) {
            $role->description = "Description par défaut pour le rôle " . $role->name;
        }
    }
}
