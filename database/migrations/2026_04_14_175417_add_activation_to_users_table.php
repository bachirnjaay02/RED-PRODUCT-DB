<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // je veux les utilisateurs soient actif par defeaut meme si ils n'ont pas encore activer leur compte via le lien d'activation, pour eviter les problemes d'inscription

   public function up() {
    Schema::table('users', function (Blueprint $table) {
        $table->boolean('is_active')->default(true); // ✅ Par défaut, les utilisateurs ne sont pas actifs jusqu'à ce qu'ils activent leur compte via le lien d'activation
        $table->string('activation_token')->nullable();  // ✅ On rend le token nullable pour éviter les problèmes d'inscription
    });
}

public function down() {
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['is_active', 'activation_token']);
    });
}
};
