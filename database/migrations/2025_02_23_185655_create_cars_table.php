<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cars', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique(); // Numéro de série unique
            $table->string('brand'); // Marque
            $table->string('model'); // Modèle
            $table->year('year'); // Année
            $table->enum('drive_type', ['2WD', '4WD', 'AWD'])->nullable(); // Type de traction
            $table->string('color')->nullable(); // Couleur
            $table->string('image')->nullable(); // Image de la voiture
            $table->decimal('price', 10, 2); // Prix
            $table->integer('mileage'); // Kilométrage
            $table->enum('fuel_type', ['Petrol', 'Diesel', 'Electric', 'Hybrid'])->nullable(); // Type de carburant
            $table->enum('transmission', ['Manual', 'Automatic'])->nullable(); // Type de transmission
            $table->enum('engine', ['V6', 'V8', 'Electric', 'Inline 4', 'Hybrid'])->nullable(); // Type de moteur
            $table->integer('seats'); // Nombre de sièges
            $table->integer('doors'); // Nombre de portes
            $table->enum('status', ['Available', 'Sold'])->default('Available'); // Statut de la voiture
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null'); // Créé par (relation avec l'utilisateur)
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null'); // Mis à jour par (relation avec l'utilisateur)
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null'); // Supprimé par (relation avec l'utilisateur)
            $table->timestamps();
            $table->softDeletes(); // Support pour les suppressions logiques
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
