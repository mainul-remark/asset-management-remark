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
        Schema::create('assets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('asset_type_id');
            $table->string('name');
            $table->string('default_image')->nullable();
            $table->unsignedBigInteger('store_id')->nullable();
            $table->string('asset_code')->unique();
            $table->tinyInteger('has_kv_slot')->nullable();
            $table
                ->decimal('minimum_fee', 10, 2)
                ->default(0)
                ->nullable();
            $table
                ->decimal('asset_price', 10, 2)
                ->default(0)
                ->nullable();
            $table
                ->tinyInteger('is_common_asset')
                ->default(0)
                ->nullable();
            $table->string('planogram_pdf')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('has_self')->nullable();
            $table->tinyInteger('total_self')->nullable();

            $table->index('asset_code');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
