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
                ->enum('dimension_unit_name', [
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
