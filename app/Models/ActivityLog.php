<?php

namespace App\Models;

use App\Enums\PermissionType;  // Import de l'enum PermissionType
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * Attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'user_id',       // ID de l'utilisateur qui a effectué l'action
        'action',        // Action effectuée (ex. 'create_car', 'send_message', etc.)
        'description',   // Description détaillée de l'action
    ];

    /**
     * Validation des données d'un log d'activité.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data)
    {
        return Validator::make($data, [
            'user_id' => 'required|exists:users,id',  // Vérifie que l'utilisateur existe
            'action' => ['required', 'string', \Illuminate\Validation\Rule::enum(PermissionType::class)],  // Assure que l'action est une valeur valide de PermissionType
            'description' => 'nullable|string|max:500', // Description facultative mais limitée à 500 caractères
        ]);
    }

    /**
     * Relation avec l'utilisateur qui a effectué l'action.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class); // Relation avec l'utilisateur
    }

    /**
     * Crée un log d'activité pour un utilisateur en utilisant une permission spécifique.
     *
     * @param \App\Models\User $user
     * @param PermissionType $permissionType
     * @param string $description
     * @return \App\Models\ActivityLog
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function createLog(User $user, PermissionType $permissionType, string $description)
    {
        // Données à valider
        $data = [
            'user_id' => $user->id,
            'action' => $permissionType->value, // Utilisation de la valeur de l'énumération PermissionType
            'description' => $description,
        ];

        // Validation des données
        $validation = self::validate($data);

        if ($validation->fails()) {
            throw new ValidationException($validation); // Lancer une exception de validation si elle échoue
        }

        // Log de la création de l'activité
        Log::info("Log d'activité créé pour l'utilisateur ID {$user->id} : {$permissionType->value}");

        // Créer et retourner le log d'activité
        return self::create($data);
    }

    /**
     * Méthode de démarrage pour les événements du modèle.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Event 'creating' pour valider et vérifier les données avant la création du log
        static::creating(function ($log) {
            // Validation des données
            $validation = self::validate($log->getAttributes());
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            // Log de la création du log d'activité
            Log::info("Création du log d'activité pour l'utilisateur ID {$log->user_id} : {$log->action}");
        });
    }
}
