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
        Schema::table('assets', function (Blueprint $table) {
            $table
                ->foreign('asset_type_id')
                ->references('id')
                ->on('asset_types')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('store_id')
                ->references('id')
                ->on('stores')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropForeign(['asset_type_id']);
            $table->dropForeign(['store_id']);
        });
    }
};
