<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('common_space_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_period_id')->constrained('bill_periods')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores');

            // calculation snapshot
            $table->decimal('total_store_sqft', 12, 2)->default(0);
            $table->decimal('dedicated_ground_sqft', 12, 2)->default(0);
            $table->decimal('common_ground_asset_sqft', 12, 2)->default(0);
            $table->decimal('remaining_sqft', 12, 2)->default(0);
            $table->decimal('common_static_fees_total', 12, 2)->default(0);
            $table->unsignedSmallInteger('brand_count')->default(0);
            $table->decimal('rate_per_sqft', 10, 4)->default(0);
            $table->decimal('common_charge_per_brand', 12, 2)->default(0);
            $table->timestamp('calculated_at')->nullable();

            $table->timestamps();

            $table->unique(['bill_period_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('common_space_logs');
    }
};
