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
        Schema::table('visual_merchandising_files', function (
            Blueprint $table
        ) {
            $table
                ->foreign('visual_merchandising_id')
                ->references('id')
                ->on('visual_merchandisings')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visual_merchandising_files', function (
            Blueprint $table
        ) {
            $table->dropForeign(['visual_merchandising_id']);
        });
    }
};
