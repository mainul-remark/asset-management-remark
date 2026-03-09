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
        Schema::create('key_visual_files', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->unsignedBigInteger('key_visual_id');
            $table->unsignedBigInteger('key_visual_size_id');
            $table->longText('kv_file');
            $table
                ->mediumInteger('kv_size')
                ->default(0)
                ->nullable();
            $table
                ->float('aspect_ratio')
                ->default(0)
                ->nullable();
            $table->string('file_type')->nullable();
            $table->string('file_duration')->nullable();
            $table
                ->tinyInteger('status')
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
        Schema::dropIfExists('key_visual_files');
    }
};
