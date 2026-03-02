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
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();

            // Section 1 : Profil
            $table->string('situation')->nullable();
            $table->string('quartier')->nullable();

            // Section 2 : Petit Déjeuner
            $table->string('petit_dej_habitude')->nullable();
            $table->string('petit_dej_interet')->nullable();
            $table->jsonb('petit_dej_menu')->nullable()->default('[]');    // tableau de choix
            $table->string('petit_dej_heure')->nullable();
            $table->string('petit_dej_budget')->nullable();

            // Section 3 : Déjeuner
            $table->jsonb('dejeuner_lieu')->nullable()->default('[]');
            $table->string('dejeuner_interet')->nullable();
            $table->jsonb('dejeuner_menu')->nullable()->default('[]');
            $table->jsonb('contraintes')->nullable()->default('[]');
            $table->string('dejeuner_heure')->nullable();
            $table->string('dejeuner_budget')->nullable();

            // Section 4 : Mode de service
            $table->jsonb('mode_service')->nullable()->default('[]');
            $table->string('abonnement')->nullable();

            // Section 5 : Contact
            $table->string('nom')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('contact_ok')->nullable();

            // Metadata
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps(); // created_at, updated_at
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_responses');
    }
};
