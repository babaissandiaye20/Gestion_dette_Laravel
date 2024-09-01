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
            $table->foreignId('dette_id')->constrained('dettes')->onDelete('cascade');
            $table->string('article');
            $table->integer('quantite');
            $table->decimal('prix', 10, 2);
            $table->timestamps();
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
