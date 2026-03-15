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
        Schema::create('assign_kv_to_assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('key_visual_id');
            $table->unsignedBigInteger('key_visual_files_id');
            $table->tinyInteger('has_perfect_size_kv');
            $table->string('assigned_date')->nullable();
            $table->unsignedBigInteger('assigned_by');
            $table->unsignedBigInteger('installed_by')->nullable();
            $table->text('instalation_proof')->nullable();
            $table
                ->enum('instalation_status', [
                    'pending',
                    'planned',
                    'installed',
                    'verified',
                ])
                ->default('pending')
                ->nullable();
            $table->string('instalation_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assign_kv_to_assets');
    }
};
