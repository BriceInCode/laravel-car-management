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
        // Création de la table 'users'
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID de l'utilisateur
            $table->string('name'); // Nom de l'utilisateur
            $table->string('email')->unique(); // Email unique
            $table->timestamp('email_verified_at')->nullable(); // Date de vérification de l'email
            $table->string('password'); // Mot de passe de l'utilisateur
            $table->string('phone', 15)->nullable(); // Numéro de téléphone, optionnel
            $table->string('address', 255)->nullable(); // Adresse, optionnel
            $table->string('profile_image')->nullable(); // Image de profil, optionnel
            $table->enum('status', ['active', 'inactive'])->default('inactive'); // Statut de l'utilisateur
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade'); // Ajout de la colonne role_id et la contrainte de clé étrangère
            $table->rememberToken(); // Token pour "remember me"
            $table->timestamps(); // Timestamps pour les dates de création et de mise à jour
        });

        // Création de la table 'password_reset_tokens'
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); // Email comme clé primaire
            $table->string('token'); // Token de réinitialisation
            $table->timestamp('created_at')->nullable(); // Date de création du token
        });

        // Création de la table 'sessions'
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // ID de la session
            $table->foreignId('user_id')->nullable()->index(); // Clé étrangère vers l'utilisateur
            $table->string('ip_address', 45)->nullable(); // Adresse IP de l'utilisateur
            $table->text('user_agent')->nullable(); // User Agent
            $table->longText('payload'); // Données de session
            $table->integer('last_activity')->index(); // Dernière activité
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
