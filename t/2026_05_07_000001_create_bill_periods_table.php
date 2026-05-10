<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bill_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('period_type', ['monthly', 'quarterly', 'custom'])->default('monthly');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['open', 'generating', 'generated', 'finalized'])->default('open');
            $table->timestamp('generated_at')->nullable();
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bill_periods');
    }
};
