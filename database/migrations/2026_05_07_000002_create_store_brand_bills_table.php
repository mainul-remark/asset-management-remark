<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_brand_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_period_id')->constrained('bill_periods')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores');
            $table->foreignId('brand_id')->constrained('brands');

            // computed totals
            $table->decimal('ground_amount', 12, 2)->default(0);
            $table->decimal('static_amount', 12, 2)->default(0);
            $table->decimal('common_amount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);

            // admin override
            $table->decimal('adjustment_amount', 12, 2)->default(0);
            $table->decimal('final_amount', 12, 2)->default(0);

            // workflow
            $table->enum('bill_status', ['draft', 'issued', 'disputed', 'adjusted', 'finalized', 'paid'])->default('draft');
            $table->text('dispute_reason')->nullable();
            $table->text('admin_note')->nullable();

            $table->timestamp('issued_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('finalized_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->unique(['bill_period_id', 'store_id', 'brand_id'], 'unique_period_store_brand');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('store_brand_bills');
    }
};
