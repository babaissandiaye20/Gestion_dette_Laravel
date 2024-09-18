<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCategoryAndMaxDebtAmountToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            // Ajout du champ catégorie avec bronze comme valeur par défaut
            $table->string('category')->default('bronze');

            // Ajout du champ montant de dette maximale (nullable si non silver)
            $table->decimal('max_debt_amount', 10, 2)->nullable();
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('max_debt_amount');
        });
    }
}
