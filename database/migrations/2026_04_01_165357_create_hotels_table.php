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
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Nom de l'hôtel (ex: Terrou-Bi)
            $table->string('address');          // Adresse (ex: Boulevard Martin Luther King)
            $table->string('email');            // Email de l'hôtel
            $table->string('phone');            // Numéro de téléphone
            $table->decimal('price', 10, 2);    // Prix par nuit (ex: 85000.00)
            $table->string('currency')->default('XOF'); // Devise par défaut : FCFA
            $table->string('image')->nullable(); // Photo de l'hôtel (optionnel au début)
            $table->timestamps();               // Crée les colonnes created_at et updated_at
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hotels');
    }
};
