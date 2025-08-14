<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('kanji_name')->default('');
            $table->string('surname')->default('');
            $table->string('kanji_surname')->default('');

            // Añadir restricción única para los cuatro campos
            $table->unique(['name', 'kanji_name', 'surname', 'kanji_surname'], 'unique_person_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
