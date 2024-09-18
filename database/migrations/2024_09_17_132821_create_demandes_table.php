<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDemandesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('demandes', function (Blueprint $table) {
            $table->id(); // Clé primaire
            $table->foreignId('client_id')->constrained('users'); // Clé étrangère vers la table users
            $table->double('montant'); // Le montant de la demande
            $table->json('articles'); // Articles sous forme de JSON
            $table->enum('statut', ['en attente', 'confirmée', 'rejetée', 'annulée'])->default('en attente'); // Statut
            $table->timestamps(); // Colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('demandes');
    }
}

