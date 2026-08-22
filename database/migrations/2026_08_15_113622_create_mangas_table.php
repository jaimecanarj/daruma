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
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('nas_folder_name')->unique();
            $table->text('synopsis')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('reading_direction', ['ltr', 'rtl'])->default('rtl');
            $table->smallInteger('volumes_edition')->nullable();
            $table->smallInteger('volumes_tankoubon')->nullable();
            $table->smallInteger('chapters')->nullable();
            $table->boolean('finished')->default(false);
            $table->enum('language', ['es', 'en', 'ja']);
            $table->string('magazine')->nullable();
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
