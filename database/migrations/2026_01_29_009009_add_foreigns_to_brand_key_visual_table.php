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
        Schema::table('brand_key_visual', function (Blueprint $table) {
            $table
                ->foreign('brand_id')
                ->references('id')
                ->on('brands')
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
        Schema::table('brand_key_visual', function (Blueprint $table) {
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['key_visual_id']);
        });
    }
};
