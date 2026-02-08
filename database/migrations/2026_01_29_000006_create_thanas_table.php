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
        if (!Schema::hasTable('thanas')) {
            Schema::create('thanas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('district_id');
                $table->string('name');
                $table->geometry('boundary_polygon');

                $table->index('boundary_polygon');

                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('thanas');
    }
};
