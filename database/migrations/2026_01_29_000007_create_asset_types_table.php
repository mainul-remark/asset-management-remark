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
        Schema::create('asset_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('default_image')->nullable();
            $table
                ->decimal('height')
                ->default(0)
                ->nullable();
            $table
                ->decimal('width')
                ->default(0)
                ->nullable();
            $table
                ->decimal('depth')
                ->default(0)
                ->nullable();
            $table
                ->enum('dimention_unit_name', [
                    'px',
                    'in',
                    'ft',
                    'cm',
                    'mm',
                    'm',
                    'yd',
                ])
                ->default('px')
                ->nullable();
            $table
                ->decimal('default_price', 10, 2)
                ->default(0)
                ->nullable();
            $table
                ->tinyInteger('status')
                ->default(1)
                ->nullable();
            $table
                ->tinyInteger('is_digital')
                ->default(0)
                ->nullable();
            $table
                ->tinyInteger('total_self')
                ->default(0)
                ->nullable();
            $table
                ->tinyInteger('has_kv_space')
                ->default(1)
                ->nullable();
            $table
                ->tinyInteger('has_default_dimension')
                ->default(0)
                ->nullable();
            $table->tinyInteger('need_asset_image')->nullable();
            $table
                ->tinyInteger('need_asset_planogram')
                ->default(0)
                ->nullable();
            $table->tinyInteger('has_asset_self')->nullable();
            $table
                ->tinyInteger('total_kv_slot')
                ->default(1)
                ->nullable();

            $table
                ->string('code', 191)
                ->nullable()
                ->unique();
            $table
                ->tinyInteger('is_double_side')
                ->default(0)
                ->nullable();

            $table
                ->tinyInteger('is_ground_type_assets')
                ->default(0)
                ->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_types');
    }
};
