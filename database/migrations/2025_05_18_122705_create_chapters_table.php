<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->string('name');
            $table->smallInteger('order');
            $table->foreignId('manga_id')->constrained('mangas')->onDelete('cascade');
            $table->smallInteger('volume_order');

            // Clave primaria
            $table->primary(['manga_id', 'order']);
        });

        DB::statement('
            ALTER TABLE chapters
            ADD CONSTRAINT fk_chapters_volume
            FOREIGN KEY (manga_id, volume_order)
            REFERENCES volumes(manga_id, `order`)
            ON DELETE CASCADE
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
