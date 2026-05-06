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
        Schema::create('visual_merchandisings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('asset_id');
            $table->unsignedBigInteger('creator_id');
            $table->longText('issue_text');
            $table
                ->enum('issue_fix_status', [
                    'pending',
                    'planned',
                    'assigned',
                    'processing',
                    'solved',
                ])
                ->default('pending')
                ->nullable();
            $table
                ->tinyInteger('status')
                ->default(1)
                ->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->text('fix_proof')->nullable();
            $table->text('fix_note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visual_merchandisings');
    }
};
