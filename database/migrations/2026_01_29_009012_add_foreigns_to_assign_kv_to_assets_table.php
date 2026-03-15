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
        Schema::table('assign_kv_to_assets', function (Blueprint $table) {
            $table
                ->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('key_visual_id')
                ->references('id')
                ->on('key_visuals')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('key_visual_files_id')
                ->references('id')
                ->on('key_visual_files')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('assigned_by')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('installed_by')
                ->references('id')
                ->on('users')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assign_kv_to_assets', function (Blueprint $table) {
            $table->dropForeign(['asset_id']);
            $table->dropForeign(['key_visual_id']);
            $table->dropForeign(['key_visual_files_id']);
            $table->dropForeign(['assigned_by']);
            $table->dropForeign(['installed_by']);
        });
    }
};
