<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('assign_asset_to_brands')) {
            return;
        }

        if (!Schema::hasColumn('assign_asset_to_brands', 'is_asset_assigned_currently')) {
            Schema::table('assign_asset_to_brands', function (Blueprint $table) {
                $table
                    ->tinyInteger('is_asset_assigned_currently')
                    ->default(1)
                    ->nullable()
                    ->after('status');
            });
        }

        DB::table('assign_asset_to_brands')->update([
            'is_asset_assigned_currently' => DB::raw('CASE WHEN `status` = 1 THEN 1 ELSE 0 END'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('assign_asset_to_brands')) {
            return;
        }

        if (Schema::hasColumn('assign_asset_to_brands', 'is_asset_assigned_currently')) {
            Schema::table('assign_asset_to_brands', function (Blueprint $table) {
                $table->dropColumn('is_asset_assigned_currently');
            });
        }
    }
};
