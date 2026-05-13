<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('brand_bill_disputes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bill_period_id')->constrained('bill_periods')->cascadeOnDelete();
            $table->foreignId('brand_id')->constrained('brands')->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users');
            $table->decimal('original_amount', 12, 2);
            $table->decimal('requested_amount', 12, 2);
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'partially_approved', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->decimal('approved_amount', 12, 2)->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('brand_bill_disputes');
    }
};
