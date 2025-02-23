<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\DriveType;
use App\Enums\EngineType;
use App\Enums\TransmissionType;
use App\Enums\FuelType;
use App\Enums\StatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class Car extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Champs remplissables pour le modèle Car.
     *
     * @var array<string>
     */
    protected $fillable = [
        'serial_number',
        'brand',
        'model',
        'year',
        'drive_type',
        'color',
        'image',
        'price',
        'mileage',
        'fuel_type',
        'transmission',
        'engine',
        'seats',
        'doors',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * Convertir les champs enum.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'drive_type' => DriveType::class,
        'transmission' => TransmissionType::class,
        'fuel_type' => FuelType::class,
        'engine' => EngineType::class,
        'status' => StatusType::class,
    ];

    /**
     * Relations avec l'utilisateur ayant créé, mis à jour ou supprimé l'entrée.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Valider les données pour la création ou la mise à jour d'une voiture.
     *
     * @param array $data
     * @return array
     */
    public static function validateCarData(array $data): array
    {
        // Validation
        $validator = Validator::make($data, [
            'serial_number' => 'required|unique:cars,serial_number,' . ($data['id'] ?? 'NULL'),
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|min:1886|max:' . (date('Y') + 1),
            'price' => 'required|numeric|min:0',
            'mileage' => 'required|integer|min:0',
            'status' => ['required', 'string', \Illuminate\Validation\Rule::enum(StatusType::class)],
        ], [
            'serial_number.required' => 'Le numéro de série est requis.',
            'serial_number.unique' => 'Le numéro de série doit être unique.',
            'brand.required' => 'La marque est requise.',
            'brand.string' => 'La marque doit être une chaîne de caractères.',
            'brand.max' => 'La marque ne peut pas dépasser 255 caractères.',
            'model.required' => 'Le modèle est requis.',
            'model.string' => 'Le modèle doit être une chaîne de caractères.',
            'model.max' => 'Le modèle ne peut pas dépasser 255 caractères.',
            'year.required' => 'L\'année est requise.',
            'year.integer' => 'L\'année doit être un entier.',
            'year.min' => 'L\'année doit être supérieure ou égale à 1886.',
            'year.max' => 'L\'année ne peut pas dépasser l\'année en cours.',
            'price.required' => 'Le prix est requis.',
            'price.numeric' => 'Le prix doit être un nombre.',
            'price.min' => 'Le prix doit être supérieur ou égal à 0.',
            'mileage.required' => 'Le kilométrage est requis.',
            'mileage.integer' => 'Le kilométrage doit être un nombre entier.',
            'mileage.min' => 'Le kilométrage doit être supérieur ou égal à 0.',
            'status.required' => 'Le statut est requis.',
            'status.string' => 'Le statut doit être une chaîne de caractères.',
            'status.in' => 'Le statut doit être valide.',
        ]);

        // Retourner les erreurs si la validation échoue
        if ($validator->fails()) {
            return $validator->errors()->toArray();
        }

        return []; // Retourner un tableau vide si aucune erreur n'est trouvée
    }

    /**
     * Méthode de démarrage pour les événements du modèle.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        // Création
        static::creating(function ($car) {
            $errors = self::validateCarData($car->toArray());
            if (!empty($errors)) {
                // Si des erreurs sont présentes, on peut les journaliser ou les gérer autrement
                Log::error("Erreur de validation lors de la création de la voiture : " . collect($errors)->flatten()->implode(", "));
                throw new \Exception("Erreur de validation lors de la création de la voiture.");
            }
            self::logCarAction($car, 'Création');
        });

        // Mise à jour
        static::updating(function ($car) {
            $errors = self::validateCarData($car->toArray());
            if (!empty($errors)) {
                Log::error("Erreur de validation lors de la mise à jour de la voiture : " . collect($errors)->flatten()->implode(", "));
                throw new \Exception("Erreur de validation lors de la mise à jour de la voiture.");
            }
            self::logCarAction($car, 'Mise à jour');
        });

        // Suppression
        static::deleting(function ($car) {
            self::logCarAction($car, 'Suppression');
        });
    }

    /**
     * Log la création, mise à jour ou suppression de la voiture.
     *
     * @param \App\Models\Car $car
     * @param string $action
     * @return void
     */
    protected static function logCarAction($car, $action)
    {
        $logMessage = "{$action} de la voiture : {$car->brand} {$car->model} (Numéro de série: {$car->serial_number})";
        Log::info($logMessage);
    }

    /**
     * Scope pour filtrer les voitures disponibles.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', StatusType::Available);
    }

    /**
     * Scope pour filtrer les voitures vendues.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSold($query)
    {
        return $query->where('status', StatusType::Sold);
    }
}
