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
        Schema::create('magazines', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('publisher');
            $table->enum('demography', ['shounen', 'shoujo', 'seinen', 'josei']);
            $table->enum('frequency', ['weekly', 'biweekly', 'monthly', 'bimonthly', 'quarterly', 'irregular']);
            $table->date('date')->nullable();

            // Añadir restricción única para los cuatro campos
            $table->unique(['name', 'publisher'], 'unique_magazine_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('magazines');
    }
};
