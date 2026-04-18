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
        Schema::table('asset_asset_type', function (Blueprint $table) {
            $table
                ->foreign('asset_type_id')
                ->references('id')
                ->on('asset_types')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('asset_id')
                ->references('id')
                ->on('assets')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asset_asset_type', function (Blueprint $table) {
            $table->dropForeign(['asset_type_id']);
            $table->dropForeign(['asset_id']);
        });
    }
};
