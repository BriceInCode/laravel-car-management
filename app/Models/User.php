<?php

namespace App\Models;

use App\Enums\RoleType;
use App\Enums\StatusType;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * Attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',        // Nom de l'utilisateur
        'email',       // E-mail de l'utilisateur
        'password',    // Mot de passe de l'utilisateur
        'role',        // Rôle de l'utilisateur (admin, utilisateur, etc.)
        'phone',       // Numéro de téléphone de l'utilisateur
        'address',     // Adresse de l'utilisateur
        'profile_image', // Image de profil de l'utilisateur
        'status',      // Statut de l'utilisateur (actif, inactif)
    ];

    /**
     * Attributs qui doivent être cachés lors de la sérialisation.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',        // Le mot de passe doit être caché
        'remember_token',  // Le token de "remember me" doit être caché
    ];

    /**
     * Attributs qui doivent être convertis (casting).
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',  // Convertir `email_verified_at` en objet DateTime
        'password' => 'hashed',             // Le mot de passe doit être stocké de manière sécurisée (haché)
        'role' => RoleType::class,          // Convertir le rôle selon l'énumération RoleType
        'status' => 'string',               // Convertir le statut en chaîne de caractères
    ];

    /**
     * Règles de validation pour la création et la mise à jour d'un utilisateur.
     *
     * @param array $data
     * @param int|null $userId
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data, int $userId = null)
    {
        $rules = [
            'name' => 'required|string|max:255', // Le nom est requis et doit être une chaîne de caractères
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId), // L'email doit être unique, sauf pour l'utilisateur en cours
            ],
            'password' => $userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed', // Le mot de passe est requis pour un nouvel utilisateur, facultatif pour une mise à jour
            'role' => ['required', 'string', \Illuminate\Validation\Rule::enum(RoleType::class)], // Le rôle doit être valide parmi les valeurs de l'énumération RoleType
            'status' => ['required', 'string', \Illuminate\Validation\Rule::enum(StatusType::class)], // Le statut doit être "actif" ou "inactif"
            'phone' => 'nullable|string|max:15', // Le numéro de téléphone est facultatif, mais limité à 15 caractères
            'address' => 'nullable|string|max:255', // L'adresse est facultative, mais limitée à 255 caractères
            'profile_image' => 'nullable|image|max:4096', // L'image de profil doit être une image et ne doit pas dépasser 4MB
        ];

        $messages = [
            'name.required' => 'Le nom est obligatoire.',
            'name.string' => 'Le nom doit être une chaîne de caractères.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'email.required' => 'L\'adresse e-mail est obligatoire.',
            'email.email' => 'Veuillez entrer une adresse e-mail valide.',
            'email.unique' => 'Cette adresse e-mail est déjà utilisée.',
            'password.required' => 'Le mot de passe est obligatoire pour un nouvel utilisateur.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
            'role.required' => 'Le rôle de l\'utilisateur est requis.',
            'role.in' => 'Le rôle doit être valide.',
            'status.required' => 'Le statut est obligatoire.',
            'status.in' => 'Le statut doit être "actif" ou "inactif".',
            'phone.max' => 'Le numéro de téléphone ne doit pas dépasser 15 caractères.',
            'address.max' => 'L\'adresse ne doit pas dépasser 255 caractères.',
            'profile_image.image' => 'Le fichier doit être une image.',
            'profile_image.max' => 'L\'image ne doit pas dépasser 4MB.',
        ];

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Définir la relation avec les voitures créées par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carsCreated(): HasMany
    {
        return $this->hasMany(Car::class, 'created_by');
    }

    /**
     * Définir la relation avec les voitures mises à jour par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carsUpdated(): HasMany
    {
        return $this->hasMany(Car::class, 'updated_by');
    }

    /**
     * Définir la relation avec les voitures supprimées par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carsDeleted(): HasMany
    {
        return $this->hasMany(Car::class, 'deleted_by');
    }

    /**
     * Relation avec le rôle de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relation avec les permissions de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }

    /**
     * Relation avec les journaux d'activité de l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Relation avec les messages envoyés par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relation avec les messages reçus par l'utilisateur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Vérifier si l'utilisateur est un administrateur.
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role->name === RoleType::ADMIN;
    }

    /**
     * Vérifier si l'utilisateur est actif.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Associer un utilisateur à un token de réinitialisation de mot de passe.
     *
     * @param string $token
     * @return void
     */
    public function setPasswordResetToken(string $token): void
    {
        $this->password_reset_token = $token;
        $this->save();

        // Log de l'attribution du token de réinitialisation
        Log::info("Le token de réinitialisation de mot de passe a été attribué à l'utilisateur {$this->id}.");
    }

    /**
     * Hacher le mot de passe avant de l'enregistrer.
     *
     * @param string $password
     * @return void
     */
    public function setPasswordAttribute(string $password): void
    {
        if (!empty($password)) {
            $this->attributes['password'] = Hash::make($password);

            // Log de la mise à jour du mot de passe
            Log::info("Le mot de passe a été mis à jour pour l'utilisateur {$this->id}.");
        }
    }

    /**
     * Vérifier si l'utilisateur a une permission spécifique.
     *
     * @param string $permissionName
     * @return bool
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains('name', $permissionName);
    }

    /**
     * Mettre à jour le statut de l'utilisateur en actif.
     *
     * @return void
     */
    public function activate(): void
    {
        $this->update(['status' => 'active']);

        // Log de l'activation de l'utilisateur
        Log::info("L'utilisateur {$this->id} a été activé.");
    }

    /**
     * Mettre à jour le statut de l'utilisateur en inactif.
     *
     * @return void
     */
    public function deactivate(): void
    {
        $this->update(['status' => 'inactive']);

        // Log de la désactivation de l'utilisateur
        Log::info("L'utilisateur {$this->id} a été désactivé.");
    }
}
