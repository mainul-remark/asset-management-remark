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
        Schema::create('key_visuals', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_type_id');
            $table->string('name');
            $table->string('unique_code')->unique();
            $table->integer('minimum_res_height')->nullable();
            $table->integer('minimum_res_width')->nullable();
            $table
                ->enum('kv_type', ['image', 'video'])
                ->default('image')
                ->nullable();
            $table->longText('kv_sample_file')->nullable();
            $table->string('kv_thumb')->nullable();
            $table->tinyInteger('status')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_visuals');
    }
};
