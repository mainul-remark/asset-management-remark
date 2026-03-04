<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('key_visual_key_visual_size', function (
            Blueprint $table
        ) {
            $table
                ->foreign('key_visual_size_id')
                ->references('id')
                ->on('key_visual_sizes')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('key_visual_id')
                ->references('id')
                ->on('key_visuals')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('key_visual_key_visual_size', function (
            Blueprint $table
        ) {
            $table->dropForeign(['key_visual_size_id']);
            $table->dropForeign(['key_visual_id']);
        });
    }
};
