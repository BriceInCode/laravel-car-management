<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Création de la table 'roles'
        Schema::create('roles', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->string('name')->unique(); // Nom du rôle, unique
            $table->string('description')->nullable(); // Description du rôle, nullable
            $table->timestamps(); // Timestamps pour création et mise à jour
            $table->softDeletes(); // Permet la suppression douce (soft delete)
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Suppression de la table 'roles' lors du rollback des migrations
        Schema::dropIfExists('roles');
    }
};
