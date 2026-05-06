<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $timestamp = now();

        $brands = [
            ['name' => 'Nior', 'code' => 'NRR', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-nior.webp'],
            ['name' => 'SIODIL', 'code' => 'SDL', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-siodil.webp'],
            ['name' => 'SIODIL DAILY', 'code' => 'SID', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-siodil-daily.svg'],
            ['name' => 'Dermo-U', 'code' => 'DRM', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-dermo-u.webp'],
            ['name' => 'Blaze O\' Skin', 'code' => 'BOS', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-blaze-o-skin.webp'],
            ['name' => 'Max Beu', 'code' => 'MAB', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-max-beu.webp'],
            ['name' => 'Lily', 'code' => 'LLY', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-lily.webp'],
            ['name' => 'Cavotin', 'code' => 'CVT', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-cavotin.webp'],
            ['name' => 'HERLAN', 'code' => 'HRL', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-herlan.webp'],
            ['name' => 'Skin Mynt', 'code' => 'SKM', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-skin-mynt.webp'],
            ['name' => 'Body N Beard', 'code' => 'BNB', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-body-n-beard.png'],
            ['name' => 'Glorin', 'code' => 'GLR', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-glorin.png'],
            ['name' => 'D32', 'code' => 'D32', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-d32.webp'],
            ['name' => 'LILY Essentials', 'code' => 'LIE', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-lily-essentials.png'],
            ['name' => 'Little One', 'code' => 'LIO', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-little-one.webp'],
            ['name' => 'Noirita', 'code' => 'NRT', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-noirita.png'],
            ['name' => 'Ribana', 'code' => 'RBN', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-ribana.svg'],
            ['name' => 'Streax', 'code' => 'STR', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-streax.jpg'],
            ['name' => 'Flormar', 'code' => 'FLR', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-flormar.png'],
            ['name' => 'Acnol', 'code' => 'ANL', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-acnol.webp'],
            ['name' => 'Tylox', 'code' => 'TYL', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-tylox.webp'],
            ['name' => 'Orix', 'code' => 'OXX', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-orix.webp'],
            ['name' => 'Sunbit', 'code' => 'SNB', 'logo' => 'backend/assets/uploaded-files/brands/brand-logo-herlan-sunbit.webp'],
        ];

        $payload = array_map(static function (array $brand) use ($timestamp): array {
            return [
                'name' => $brand['name'],
                'code' => $brand['code'],
                'description' => null,
                'status' => 1,
                'logo' => $brand['logo'],
                'is_common' => 0,
                'created_by' => 1,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];
        }, $brands);

        Schema::disableForeignKeyConstraints();
        DB::table('brands')->truncate();
        Schema::enableForeignKeyConstraints();

        DB::table('brands')->insert($payload);
    }
}
