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
        Schema::create('stores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->string('code')->unique()->nullable();
            $table->decimal('total_area_sqft', 12, 2)->default(0);
            $table->text('address')->nullable();
            $table->string('area')->nullable();
            $table->string('postal_code')->nullable();
            $table
                ->decimal('latitude', 10, 8)
                ->default(0)
                ->nullable();
            $table
                ->decimal('longitude', 10, 8)
                ->default(0)
                ->nullable();
            $table
                ->float('monthly_rent', 10, 2)
                ->default(0)
                ->nullable();
            $table
                ->float('per_sqr_feet_rent', 6, 2)
                ->default(0)
                ->nullable();
            $table->text('store_layout_img')->nullable();
            $table->text('store_layout_pdf')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('shop_official_mobile')->nullable();
            $table->string('shop_official_email')->nullable();
            $table
                ->tinyInteger('status')
                ->default(1)
                ->nullable();
            $table->unsignedBigInteger('store_manager_id')->nullable();
            $table->string('opened_date')->nullable();
            $table->unsignedBigInteger('division_id')->nullable();
            $table->string('store_code')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('thana_id')->nullable();
            $table->string('slug')->nullable();
            $table->string('store_type')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
