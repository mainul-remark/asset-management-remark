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
            $table->decimal('height')->default(0);
            $table->decimal('width');
            $table
                ->enum('unit_name', ['px', 'in', 'ft', 'cm', 'mm', 'm', 'yd'])
                ->default('px');
            $table->longText('kv_file')->nullable();
            $table->mediumInteger('kv_size')->default(0);
            $table
                ->float('aspect_ratio')
                ->default(0)
                ->nullable();
            $table->tinyInteger('status');

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
