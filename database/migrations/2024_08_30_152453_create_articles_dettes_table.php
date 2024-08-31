<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles_dettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('dette_id')->constrained()->onDelete('cascade');
            $table->integer('qteVente');
            $table->float('prixVente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles_dettes');
    }
};

