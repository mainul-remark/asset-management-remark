<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_brand_bills', function (Blueprint $table) {
            // net sum of (override_amount - calculated_amount) across all line items
            // negative = discount applied, positive = extra charge applied
            if (!Schema::hasColumn('store_brand_bills', 'line_item_override_delta'))
            $table->decimal('line_item_override_delta', 12, 2)->default(0)->after('adjustment_amount');
        });
    }

    public function down(): void
    {
        Schema::table('store_brand_bills', function (Blueprint $table) {
            $table->dropColumn('line_item_override_delta');
        });
    }
};
