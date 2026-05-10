<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_line_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_brand_bill_id')->constrained('store_brand_bills')->cascadeOnDelete();

            // what is billed
            $table->foreignId('asset_id')->nullable()->constrained('assets')->nullOnDelete();
            $table->foreignId('asset_type_id')->nullable()->constrained('asset_types')->nullOnDelete();
            $table->enum('payment_type', ['ground', 'static', 'common']);

            // calculation inputs — all snapshotted at billing time
            $table->decimal('asset_sqft', 10, 4)->default(0);
            $table->decimal('rate_per_sqft', 10, 4)->default(0);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('quantity', 10, 4)->default(1);

            // multi-brand cost sharing snapshot
            $table->unsignedTinyInteger('assigned_brands_count')->default(1);
            $table->decimal('full_calculated_amount', 12, 2)->default(0);

            // result
            $table->decimal('calculated_amount', 12, 2)->default(0);
            $table->decimal('override_amount', 12, 2)->nullable();
            $table->decimal('final_amount', 12, 2)->default(0);
            $table->text('note')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_line_items');
    }
};
