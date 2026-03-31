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
        Schema::create('key_visual_sizes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->decimal('height', 12, 0)->default(0);
            $table->decimal('width', 12, 0)->default(0);
            $table
                ->enum('unit_name', ['px', 'in', 'ft', 'cm', 'mm', 'm', 'yd'])
                ->default('px');
            $table
                ->tinyInteger('status')
                ->default(1)
                ->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_visual_sizes');
    }
};
