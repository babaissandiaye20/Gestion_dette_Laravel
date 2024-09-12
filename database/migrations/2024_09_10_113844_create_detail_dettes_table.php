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
        Schema::create('detail_dettes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('dette_id');
            $table->integer('qte_vente');
            $table->decimal('prix_vente', 8, 2);
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('article_id')->references('id')->on('articles');
            $table->foreign('dette_id')->references('id')->on('dettes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_dettes');
    }
};
