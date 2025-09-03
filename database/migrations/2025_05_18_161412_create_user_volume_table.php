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
        Schema::create('user_volume', function (Blueprint $table) {
            $table->unsignedBigInteger('manga_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->smallInteger('volume_order');
            $table->boolean('read')->default(false);
            $table->primary(['manga_id', 'user_id', 'volume_order']);
            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE user_volume
            ADD CONSTRAINT fk_user_volume_volume
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
        Schema::dropIfExists('user_volume');
    }
};
