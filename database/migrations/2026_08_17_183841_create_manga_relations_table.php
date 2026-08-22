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
        Schema::create('manga_relations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('manga_id')->constrained('mangas')->onDelete('cascade');
            $table->foreignId('related_manga_id')->constrained('mangas')->onDelete('cascade');
            $table->enum('type', ['prequel', 'sequel', 'main_story', 'spin_off']);
            $table->timestamps();

            $table->unique(['manga_id', 'related_manga_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manga_relations');
    }
};
