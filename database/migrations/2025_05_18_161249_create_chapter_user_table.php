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
        Schema::create('chapter_user', function (Blueprint $table) {
            $table->smallInteger('chapter_order');
            $table->unsignedBigInteger('manga_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->primary(['manga_id', 'chapter_order', 'user_id']);
            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE chapter_user
            ADD CONSTRAINT fk_chapter_user_chapter
            FOREIGN KEY (manga_id, chapter_order)
            REFERENCES chapters(manga_id, `order`)
            ON DELETE CASCADE
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_user');
    }
};
