<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bill_line_items', function (Blueprint $table) {
            // set automatically when override_amount is saved — no admin input required
            $table->enum('override_type', ['discount', 'extra'])->nullable()->after('override_amount');
        });
    }

    public function down(): void
    {
        Schema::table('bill_line_items', function (Blueprint $table) {
            $table->dropColumn('override_type');
        });
    }
};
