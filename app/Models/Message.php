<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Message extends Model
{
    use HasFactory;

    /**
     * Attributs qui peuvent être assignés en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'sender_id',    // ID de l'utilisateur qui envoie le message
        'receiver_id',  // ID de l'utilisateur qui reçoit le message
        'content',      // Contenu du message
    ];

    /**
     * Règles de validation pour la création/mise à jour d'un message.
     *
     * @var array
     */
    public static $rules = [
        'sender_id' => 'required|exists:users,id',   // Vérifie que l'expéditeur existe dans la table des utilisateurs
        'receiver_id' => 'required|exists:users,id', // Vérifie que le destinataire existe dans la table des utilisateurs
        'content' => 'required|string|max:500',      // Le contenu doit être une chaîne de caractères avec une longueur maximale de 500
    ];

    /**
     * Validation des données du message.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function validate(array $data)
    {
        // Messages personnalisés pour chaque règle de validation
        $messages = [
            'sender_id.required' => 'L\'expéditeur est requis.',
            'sender_id.exists' => 'L\'expéditeur doit exister dans la base de données.',
            'receiver_id.required' => 'Le destinataire est requis.',
            'receiver_id.exists' => 'Le destinataire doit exister dans la base de données.',
            'content.required' => 'Le contenu du message est obligatoire.',
            'content.string' => 'Le contenu du message doit être une chaîne de caractères.',
            'content.max' => 'Le contenu du message ne peut pas dépasser 500 caractères.',
        ];

        return Validator::make($data, self::$rules, $messages); // Application des messages personnalisés
    }

    /**
     * Relation avec l'utilisateur expéditeur.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id'); // Relation avec l'utilisateur expéditeur
    }

    /**
     * Relation avec l'utilisateur destinataire.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id'); // Relation avec l'utilisateur destinataire
    }

    /**
     * Envoie un message entre un expéditeur et un destinataire.
     *
     * @param \App\Models\User $sender
     * @param \App\Models\User $receiver
     * @param string $content
     * @return \App\Models\Message
     * @throws \Illuminate\Validation\ValidationException
     */
    public static function sendMessage(User $sender, User $receiver, string $content)
    {
        // Données à valider
        $data = [
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'content' => $content,
        ];

        // Validation des données
        $validation = self::validate($data);

        if ($validation->fails()) {
            throw new ValidationException($validation); // Lancer une exception de validation si elle échoue
        }

        // Log de l'envoi du message
        Log::info("Envoi du message de l'utilisateur ID {$sender->id} à l'utilisateur ID {$receiver->id}");

        // Créer et retourner le message si la validation passe
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

        // Event 'creating' pour valider et vérifier les données avant la création du message
        static::creating(function ($message) {
            // Validation des données
            $validation = self::validate($message->getAttributes());
            if ($validation->fails()) {
                throw new ValidationException($validation);
            }

            // Log de la création du message
            Log::info("Création du message entre l'utilisateur ID {$message->sender_id} et l'utilisateur ID {$message->receiver_id}");
        });

        // Event 'deleting' pour valider la suppression du message
        static::deleting(function ($message) {
            // Log de la suppression du message
            Log::info("Suppression du message entre l'utilisateur ID {$message->sender_id} et l'utilisateur ID {$message->receiver_id}");
        });
    }
}
