<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTableAddTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('photo')->change(); // Modifier la colonne 'photo' en type TEXT
            $table->boolean('type')->default(true); // Ajouter la colonne 'type' avec un booléen par défaut false
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->change(); // Revenir au type original si nécessaire
            $table->dropColumn('type'); // Supprimer la colonne 'type'
        });
    }
}
