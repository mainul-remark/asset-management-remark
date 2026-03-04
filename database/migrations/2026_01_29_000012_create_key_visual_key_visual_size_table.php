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
        Schema::create('key_visual_key_visual_size', function (
            Blueprint $table
        ) {
            $table->unsignedBigInteger('key_visual_size_id');
            $table->unsignedBigInteger('key_visual_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_visual_key_visual_size');
    }
};
