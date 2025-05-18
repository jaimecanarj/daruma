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
        Schema::create('mangas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cover');
            $table->text('sinopsis')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('reading_direction', ['ltr', 'rtl']);
            $table->smallInteger('volumes')->nullable();
            $table->smallInteger('tankoubon')->nullable();
            $table->smallInteger('chapters')->nullable();
            $table->boolean('finished');
            $table->mediumInteger('mal')->nullable();
            $table->mediumInteger('listado_manga')->nullable();
            $table->enum('language', ['es', 'en', 'jp']);
            $table->foreignId('magazine_id')->nullable()->constrained('magazines')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mangas');
    }
};
