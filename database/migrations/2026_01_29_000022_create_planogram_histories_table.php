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
        Schema::create('planogram_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('assigned_by');
            $table->string('file_path');
            $table
                ->tinyInteger('status')
                ->default(1)
                ->nullable();
            $table->unsignedBigInteger('brand_id')->nullable();
            $table->timestamp('changed_date')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planogram_histories');
    }
};
