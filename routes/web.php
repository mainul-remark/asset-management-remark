<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CommonPages\AdminViewController;

use App\Http\Controllers\Backend\KV\BrandController;
use App\Http\Controllers\Backend\KV\CategoryController;
use App\Http\Controllers\Backend\Asset\StoreController;
use App\Http\Controllers\Backend\Asset\AssetTypeController;
use App\Http\Controllers\Backend\SiteSettingsController;
use App\Http\Controllers\Backend\Asset\AssetController;
use App\Http\Controllers\Backend\Asset\VisualMerchandisingController;
use App\Http\Controllers\Backend\Asset\VisualMerchandisingFileController;

use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Backend\KV\KeyVisualController;
use App\Http\Controllers\Backend\KV\KeyVisualSizesController;
use App\Http\Controllers\Backend\KV\KeyVisualFilesController;
use App\Http\Controllers\Backend\Asset\AssignKvToAssetController;

use App\Http\Controllers\Backend\Asset\AssignAssetToBrandController;

Route::get('/', function () {
    if (auth()->check())
        return redirect('/dashboard');
    else
        return redirect('/login');
})->name('/');

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
    Route::get('key-visuals/next-unique-code', [KeyVisualController::class, 'nextUniqueCode'])->name('key-visuals.next-unique-code');
    Route::get('assets/next-name', [AssetController::class, 'nextName'])->name('assets.next-name');

    Route::resources([
        'brands'                    => BrandController::class,
        'categories'                => CategoryController::class,
        'stores'                    => StoreController::class,
        'asset-types'               => AssetTypeController::class,
        'site-settings'             => SiteSettingsController::class,
        'assets'                    => AssetController::class,
        'key-visuals'               => KeyVisualController::class,
        'key-visual-sizes'          => KeyVisualSizesController::class,
        'key-visual-files'          => KeyVisualFilesController::class,
        'visual-merchandising'      => VisualMerchandisingController::class,
        'visual-merchandising-files'=> VisualMerchandisingFileController::class,
    ]);

    Route::prefix('vm')->name('vm.')->group(function () {
        Route::get('/vm-issues', [VisualMerchandisingController::class, 'userWiseVmIssues'])->name('vm-issues');
    });

    Route::prefix('store')->group(function () {
        Route::get('/assign-assets', [AssetController::class, 'assignAssets'])->name('assets.assign-assets');
    });

    Route::prefix('asset')->group(function () {
        Route::get('/assign-asset-to-brand', [AssignAssetToBrandController::class, 'index'])->name('assets.assign-asset-to-brand');
        Route::post('/assign-asset-to-brand', [AssignAssetToBrandController::class, 'store'])->name('assets.assign-asset-to-brand.store');
        Route::get('/assign-asset-to-brand/assets', [AssignAssetToBrandController::class, 'assetOptions'])->name('assets.assign-asset-to-brand.assets');
        Route::get('/assign-asset-to-brand/filter', [AssignAssetToBrandController::class, 'filter'])->name('assets.assign-asset-to-brand.filter');
        Route::get('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'show'])->name('assets.assign-asset-to-brand.show');
        Route::get('/assign-asset-to-brand/{assignAssetToBrand}/edit', [AssignAssetToBrandController::class, 'edit'])->name('assets.assign-asset-to-brand.edit');
        Route::put('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'update'])->name('assets.assign-asset-to-brand.update');
        Route::delete('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'destroy'])->name('assets.assign-asset-to-brand.destroy');
    });

    Route::prefix('kv')->group(function () {
        Route::get('/assign-kv-to-asset', [AssignKvToAssetController::class, 'index'])->name('key-visuals.assign-kvs');
        Route::post('/assign-kv-to-asset', [AssignKvToAssetController::class, 'store'])->name('key-visuals.assign-kvs.store');
        Route::get('/assign-kv-to-asset/filter', [AssignKvToAssetController::class, 'filter'])->name('key-visuals.assign-kvs.filter');
        Route::get('/assign-kv-to-asset/stores/{store}/assets', [AssignKvToAssetController::class, 'storeAssets'])->name('key-visuals.assign-kvs.store-assets');
        Route::get('/assign-kv-to-asset/{assignKvToAsset}/edit', [AssignKvToAssetController::class, 'edit'])->name('key-visuals.assign-kvs.edit');
        Route::put('/assign-kv-to-asset/{assignKvToAsset}', [AssignKvToAssetController::class, 'update'])->name('key-visuals.assign-kvs.update');
        Route::delete('/assign-kv-to-asset/{assignKvToAsset}', [AssignKvToAssetController::class, 'destroy'])->name('key-visuals.assign-kvs.destroy');
    });

    Route::get('key-visualsx', [KeyVisualController::class, 'old']);
    Route::post('site-settings/theme', [SiteSettingsController::class, 'saveTheme'])->name('site-settings.theme');

    Route::prefix('admin')->middleware(['resource.maker','auth.acl'])->group(function () {
        Route::resource('/roles',RoleController::class);
        Route::resource('/users',UsersController::class);
    });

    // Cascading dropdowns for stores
    Route::get('get-districts/{division}', [StoreController::class, 'getDistricts'])->name('get.districts');
    Route::get('get-thanas/{district}', [StoreController::class, 'getThanas'])->name('get.thanas');
    Route::get('get-stores-by-district/{district}', [StoreController::class, 'getStoresByDistrict'])->name('get.stores-by-district');

    // Assign assets filter
    Route::get('get-assets-by-type/{assetType}', [AssetController::class, 'getAssetsByType'])->name('get.assets-by-type');
    Route::get('assign-assets/filter', [AssetController::class, 'assignAssetsFilter'])->name('assets.assign-assets.filter');




});

Route::get('/phpinfo', function () {return phpinfo();});
Route::get('/optimize-clear', function () {return \Mainul\CustomHelperFunctions\Helpers\CustomHelper::optimizeClear();});

//Route::get('/store-sync', function (){
//    $locationDbStores = \Illuminate\Support\Facades\DB::connection('location_db')
//        ->table('stores')
//        ->select('*')
//        ->get();
//
//    try {
//        \Illuminate\Support\Facades\DB::transaction(function () use ($locationDbStores) {
//            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
//            $locationDivisions = \Illuminate\Support\Facades\DB::connection('location_db')
//                ->table('divisions')->get();
//            $locationDistricts = \Illuminate\Support\Facades\DB::connection('location_db')
//                ->table('districts')->get();
//            $locationThanas = \Illuminate\Support\Facades\DB::connection('location_db')
//                ->table('thanas')->get();
//            if ($locationDivisions)
//            {
//                \Illuminate\Support\Facades\DB::table('divisions')->truncate();
//                foreach ($locationDivisions as $division) {
//                    \Illuminate\Support\Facades\DB::table('divisions')->insert((array) $division);
//                }
//            }
//
//            if ($locationDistricts)
//            {
//                \Illuminate\Support\Facades\DB::table('districts')->truncate();
//                foreach ($locationDistricts as $division) {
//                    \Illuminate\Support\Facades\DB::table('districts')->insert((array) $division);
//                }
//            }
//
//            if ($locationThanas)
//            {
//                \Illuminate\Support\Facades\DB::table('thanas')->truncate();
//                foreach ($locationThanas as $division) {
//                    \Illuminate\Support\Facades\DB::table('thanas')->insert((array) $division);
//                }
//            }
//
//            foreach ($locationDbStores as $locationDbStore) {
//                $store = \App\Models\Store::updateOrCreate(['title' => $locationDbStore->name], [
//                    'title' => $locationDbStore->name,
//                    'total_area_sqft' => 0,
//                    'address' => $locationDbStore->location,
////            'area' => $locationDbStore->area,
////            'postal_code' => $locationDbStore->postal_code,
//                    'latitude' => $locationDbStore->latitude,
//                    'longitude' => $locationDbStore->longitude,
//                    'monthly_rent' => 0,
//                    'per_sqr_feet_rent' => 0,
////            'store_layout_img' => $locationDbStore->store_layout_img,
////            'store_layout_pdf' => $locationDbStore->store_layout_pdf,
////            'contact_persion' => $locationDbStore->contact_persion,
////            'shop_official_mobile' => $locationDbStore->shop_official_mobile,
////            'shop_official_email' => $locationDbStore->shop_official_email,
//                    'status' => $locationDbStore->is_active,
////            'store_manager_id' => $locationDbStore->store_manager_id,
////            'opened_date' => $locationDbStore->opened_date,
//                    'division_id' => $locationDbStore->division_id,
//                    'store_code' => $locationDbStore->store_code,
//                    'district_id' => $locationDbStore->district_id,
//                    'thana_id' => $locationDbStore->thana_id,
//                    'slug' => str()->slug($locationDbStore->title.'-'.$locationDbStore->code),
//                ]);
//
//                $desiredCode = 'STR' . str_pad((string) $store->id, 3, '0', STR_PAD_LEFT);
//                if ($store->code !== $desiredCode) {
//                    $store->code = $desiredCode;
//                    $store->save();
//                }
//            }
//        });
//        return 'success';
//    } catch (\Throwable $th) {
//        return $th->getMessage();
//    }
//});
Route::get('/has-kv', function (){
    \App\Models\Asset::whereNotIn('id', [1,2])
        ->update(['has_kv_slot' => 1]);

});
