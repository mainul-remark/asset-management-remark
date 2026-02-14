<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CommonPages\AdminViewController;

use App\Http\Controllers\Backend\KV\BrandController;
use App\Http\Controllers\Backend\KV\CategoryController;
use App\Http\Controllers\Backend\Asset\StoreController;
use App\Http\Controllers\Backend\Asset\AssetTypeController;
use App\Http\Controllers\Backend\SiteSettingsController;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsersController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'resource.maker',
    'auth.acl',
])->group(function () {
    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name('dashboard');

    Route::get('stores/layout-list', [StoreController::class, 'layoutStores'])->name('stores.layout-list');
    Route::post('stores/{store}/layouts', [StoreController::class, 'uploadLayout'])->name('stores.upload-layout');

    Route::resources([
        'brands' => BrandController::class,
        'categories' => CategoryController::class,
        'stores' => StoreController::class,
        'asset-types' => AssetTypeController::class,
        'site-settings' => SiteSettingsController::class,
    ]);

    Route::prefix('admin')->middleware('resource.maker','auth.acl')->group(function () {
        Route::resource('/roles',RoleController::class);
        Route::resource('/users',UsersController::class);
    });

    // Cascading dropdowns for stores
    Route::get('get-districts/{division}', [StoreController::class, 'getDistricts'])->name('get.districts');
    Route::get('get-thanas/{district}', [StoreController::class, 'getThanas'])->name('get.thanas');

});

Route::get('/store-sync', function (){
    $locationDbStores = \Illuminate\Support\Facades\DB::connection('location_db')
        ->table('stores')
        ->select('*')
        ->get();

    try {
        \Illuminate\Support\Facades\DB::transaction(function () use ($locationDbStores) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            $locationDivisions = \Illuminate\Support\Facades\DB::connection('location_db')
                ->table('divisions')->get();
            $locationDistricts = \Illuminate\Support\Facades\DB::connection('location_db')
                ->table('districts')->get();
            $locationThanas = \Illuminate\Support\Facades\DB::connection('location_db')
                ->table('thanas')->get();
            if ($locationDivisions)
            {
                \Illuminate\Support\Facades\DB::table('divisions')->truncate();
                foreach ($locationDivisions as $division) {
                    \Illuminate\Support\Facades\DB::table('divisions')->insert((array) $division);
                }
            }

            if ($locationDistricts)
            {
                \Illuminate\Support\Facades\DB::table('districts')->truncate();
                foreach ($locationDistricts as $division) {
                    \Illuminate\Support\Facades\DB::table('districts')->insert((array) $division);
                }
            }

            if ($locationThanas)
            {
                \Illuminate\Support\Facades\DB::table('thanas')->truncate();
                foreach ($locationThanas as $division) {
                    \Illuminate\Support\Facades\DB::table('thanas')->insert((array) $division);
                }
            }

            foreach ($locationDbStores as $locationDbStore) {
                $store = \App\Models\Store::updateOrCreate(['title' => $locationDbStore->name], [
                    'title' => $locationDbStore->name,
                    'total_area_sqft' => 0,
                    'address' => $locationDbStore->location,
//            'area' => $locationDbStore->area,
//            'postal_code' => $locationDbStore->postal_code,
                    'latitude' => $locationDbStore->latitude,
                    'longitude' => $locationDbStore->longitude,
                    'monthly_rent' => 0,
                    'per_sqr_feet_rent' => 0,
//            'store_layout_img' => $locationDbStore->store_layout_img,
//            'store_layout_pdf' => $locationDbStore->store_layout_pdf,
//            'contact_persion' => $locationDbStore->contact_persion,
//            'shop_official_mobile' => $locationDbStore->shop_official_mobile,
//            'shop_official_email' => $locationDbStore->shop_official_email,
                    'status' => $locationDbStore->is_active,
//            'store_manager_id' => $locationDbStore->store_manager_id,
//            'opened_date' => $locationDbStore->opened_date,
                    'division_id' => $locationDbStore->division_id,
                    'store_code' => $locationDbStore->store_code,
                    'district_id' => $locationDbStore->district_id,
                    'thana_id' => $locationDbStore->thana_id,
                ]);

                $desiredCode = 'STR' . str_pad((string) $store->id, 3, '0', STR_PAD_LEFT);
                if ($store->code !== $desiredCode) {
                    $store->code = $desiredCode;
                    $store->save();
                }
            }
        });
        return 'success';
    } catch (\Throwable $th) {
        return $th->getMessage();
    }
});
