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
        Schema::create('site_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('favicon')->nullable();
            $table->string('menu_logo')->nullable();
            $table->string('logo')->nullable();
            $table->longText('meta_header')->nullable();
            $table->string('site_color')->nullable();
            $table->longText('meta_footer')->nullable();
            $table->text('site_info')->nullable();
            $table->longText('header_custom_code')->nullable();
            $table->longText('footer_custom_code')->nullable();
            $table->string('office_mobile')->nullable();
            $table->string('office_email')->nullable();
            $table->string('office_address')->nullable();
            $table->text('banner')->nullable();



//            valex theme settings
            $table->string('theme_style')->default('light')->comment('light or dark');
            $table->string('direction')->default('ltr')->comment('ltr or rtl');
            $table->string('navigation_style')->default('horizontal')->comment('horizontal or vertical');
            $table->string('navigation_menu_styles')->default('menu-hover')->comment('menu-hover / menu-click / icon-click / icon-hover');
            $table->string('page_styles')->default('regular')->comment('regular / classic / modern');
            $table->string('layout_width')->default('fullwidth')->comment('fullwidth / boxed');
            $table->string('menu_positions')->default('fixed')->comment('fixed / scrollable');
            $table->string('header_positions')->default('fixed')->comment('fixed / scrollable');
            $table->string('page_loader')->default('disable')->comment('disable / enable');
            $table->string('menu_colors')->default('light')->comment('light / dark / color / gradient / transparent');
            $table->string('menu_color_code')->nullable();
            $table->string('header_colors')->default('light')->comment('light / dark / color / gradient / transparent');
            $table->string('header_color_code')->nullable();
            $table->string('theme_primary')->default('light')->comment('light / dark / color / gradient / transparent');
            $table->string('theme_primary_code')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
