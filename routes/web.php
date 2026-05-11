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
use App\Http\Controllers\Backend\Asset\VmIssueFixController;
use App\Http\Controllers\Admin\UserStoreAssignmentController;
use App\Http\Controllers\Backend\KV\KvInstallationController;

use App\Http\Controllers\Backend\Asset\ImportExport\AssetImportController;
use App\Http\Controllers\Backend\Billing\BillingController;
use App\Http\Controllers\Backend\Billing\BillDisputeController;

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
    Route::get('/dashboard', [AdminViewController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/dashboard-gpt', [AdminViewController::class, 'dashboardGpt'])->name('admin.dashboard-gpt');
    Route::get('/admin/activity-log', [AdminViewController::class, 'activityLog'])->name('admin.activity-logs');

    Route::get('stores/json-list', [StoreController::class, 'jsonList'])->name('stores.json-list');
    Route::post('stores/export', [StoreController::class, 'export'])->name('stores.export');
    Route::get('stores/layout-list', [StoreController::class, 'layoutStores'])->name('stores.layout-list');
    Route::post('stores/{store}/layouts', [StoreController::class, 'uploadLayout'])->name('stores.upload-layout');
    Route::get('key-visuals/next-unique-code', [KeyVisualController::class, 'nextUniqueCode'])->name('key-visuals.next-unique-code');
    Route::get('assets/next-name', [AssetController::class, 'nextName'])->name('assets.next-name');
    Route::get('asset-types/next-code', [AssetTypeController::class, 'nextCode'])->name('asset-types.next-code');
    Route::get('user-store-assignments/users/search', [UserStoreAssignmentController::class, 'searchUsers'])->name('user-store-assignments.users.search');
    Route::get('user-store-assignments/datatable', [UserStoreAssignmentController::class, 'datatable'])->name('user-store-assignments.datatable');
    Route::get('user-store-assignments/users/{user}/current', [UserStoreAssignmentController::class, 'currentByUser'])->name('user-store-assignments.current-by-user');

    Route::prefix('admin')->middleware(['resource.maker','auth.acl'])->group(function () {
        Route::post('/users/import', [UsersController::class, 'import'])->name('users.import');
        Route::resource('/roles',RoleController::class);
        Route::resource('/users',UsersController::class);
    });



    Route::get('key-visuals/{keyVisual}/files', [KeyVisualFilesController::class, 'getByKeyVisual'])->name('key-visuals.files');

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
        'user-store-assignments'    => UserStoreAssignmentController::class,
    ]);

    Route::prefix('vm')->name('vm.')->group(function () {
        Route::get('/vm-issues', [VisualMerchandisingController::class, 'userWiseVmIssues'])->name('vm-issues');
        Route::get('/vm-issues/datatable', [VisualMerchandisingController::class, 'vmIssuesDatatable'])->name('vm-issues.datatable');
        Route::post('/vm-issues/export', [VisualMerchandisingController::class, 'exportVmIssues'])->name('vm-issues.export');
        Route::get('/vm-issues/export/status/{key}', [VisualMerchandisingController::class, 'exportVmIssuesStatus'])->name('vm-issues.export.status');
        Route::get('/vm-issues/export/download/{key}', [VisualMerchandisingController::class, 'exportVmIssuesDownload'])->name('vm-issues.export.download');
        Route::post('/change-vm-issue-status/{visualMerchandising}/{issueStatus}', [VisualMerchandisingController::class, 'changeVmIssueStatus'])->name('change-vm-issue-status');


        Route::get('/fix-issues', [VmIssueFixController::class, 'index'])->name('fix-issues');
        Route::get('/fix-issues/datatable', [VmIssueFixController::class, 'datatable'])->name('fix-issues.datatable');
        Route::get('/fix-issues/{visualMerchandising}', [VmIssueFixController::class, 'show'])->name('fix-issues.show');
        Route::post('/fix-issues/{visualMerchandising}/assign-user', [VmIssueFixController::class, 'assignUser'])->name('assign-user');
        Route::post('/fix-issues/{visualMerchandising}/upload-proof', [VmIssueFixController::class, 'uploadProof'])->name('upload-proof');
        Route::post('/fix-issues/{visualMerchandising}/change-fix-status', [VmIssueFixController::class, 'changeFixStatus'])->name('change-fix-status');
    });

    Route::prefix('store')->group(function () {
        Route::get('/assigned-assets', [AssetController::class, 'assignAssets'])->name('assets.assigned-assets');
        Route::get('/assign-assets/datatable', [AssetController::class, 'assignAssetsDatatable'])->name('assets.assign-assets.datatable');
    });

    Route::prefix('asset')->group(function () {
        Route::get('/assign-asset-to-brand', [AssignAssetToBrandController::class, 'index'])->name('assets.assign-asset-to-brand');
        Route::post('/assign-asset-to-brand', [AssignAssetToBrandController::class, 'store'])->name('assets.assign-asset-to-brand.store');
        Route::get('/assign-asset-to-brand/assets', [AssignAssetToBrandController::class, 'assetOptions'])->name('assets.assign-asset-to-brand.assets');
        Route::get('/assign-asset-to-brand/filter', [AssignAssetToBrandController::class, 'filter'])->name('assets.assign-asset-to-brand.filter');
        Route::get('/assign-asset-to-brand/by-asset/list', [AssignAssetToBrandController::class, 'assignmentsByAsset'])->name('assets.assign-asset-to-brand.by-asset');
        Route::get('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'show'])->name('assets.assign-asset-to-brand.show');
        Route::get('/assign-asset-to-brand/{assignAssetToBrand}/edit', [AssignAssetToBrandController::class, 'edit'])->name('assets.assign-asset-to-brand.edit');
        Route::put('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'update'])->name('assets.assign-asset-to-brand.update');
        Route::delete('/assign-asset-to-brand/{assignAssetToBrand}', [AssignAssetToBrandController::class, 'destroy'])->name('assets.assign-asset-to-brand.destroy');

        Route::post('/import-assets', [AssetImportController::class, 'import'])->name('assets.import-assets');
        Route::post('/check-asset-type-code', [AssetTypeController::class, 'checkTypeCode'])->name('assets.check-asset-type-code');

        Route::get('/planogram-histories', [AssetController::class, 'planogramHistories'])->name('assets.planogram-histories');
    });

    Route::prefix('kv')->group(function () {
        Route::get('/assign-kv-to-asset', [AssignKvToAssetController::class, 'index'])->name('key-visuals.assign-kvs');
        Route::post('/assign-kv-to-asset', [AssignKvToAssetController::class, 'store'])->name('key-visuals.assign-kvs.store');
        Route::get('/assign-kv-to-asset/filter', [AssignKvToAssetController::class, 'filter'])->name('key-visuals.assign-kvs.filter');
        Route::get('/assign-kv-to-asset/stores/{store}/assets', [AssignKvToAssetController::class, 'storeAssets'])->name('key-visuals.assign-kvs.store-assets');
        Route::get('/assign-kv-to-asset/{assignKvToAsset}/edit', [AssignKvToAssetController::class, 'edit'])->name('key-visuals.assign-kvs.edit');
        Route::put('/assign-kv-to-asset/{assignKvToAsset}', [AssignKvToAssetController::class, 'update'])->name('key-visuals.assign-kvs.update');
        Route::delete('/assign-kv-to-asset/{assignKvToAsset}', [AssignKvToAssetController::class, 'destroy'])->name('key-visuals.assign-kvs.destroy');

        Route::name('key-visuals.')->group(function () {
            Route::get('/kv-installation', [KvInstallationController::class, 'kvInstallation'])->name('kv-installation');
            Route::get('/kv-installation/datatable', [KvInstallationController::class, 'kvInstallationDatatable'])->name('kv-installation.datatable');
            Route::get('/kv-installation/{id}/detail', [KvInstallationController::class, 'kvInstallationDetail'])->name('kv-installation.detail');
            Route::post('/update-asset-assigned-kv-data', [KvInstallationController::class, 'updateAssignedKvStatusData'])->name('update-asset-assigned-kv-data');
        });
    });

    // ── Billing ──────────────────────────────────────────────────────────────
    Route::prefix('billing')->name('billing.')->group(function () {
        // Periods
        Route::get('/periods',                           [BillingController::class, 'index'])->name('periods.index');
        Route::get('/periods/create',                    [BillingController::class, 'create'])->name('periods.create');
        Route::post('/periods',                          [BillingController::class, 'store'])->name('periods.store');
        Route::get('/periods/{period}',                  [BillingController::class, 'show'])->name('periods.show');
        Route::get('/periods/{period}/status',                        [BillingController::class, 'periodStatus'])->name('periods.status');
        Route::get('/periods/{period}/brand-invoice/{brand}',        [BillingController::class, 'brandInvoiceView'])->name('periods.brand-invoice');
        Route::post('/periods/{period}/generate',                    [BillingController::class, 'generate'])->name('periods.generate');
        Route::post('/periods/{period}/finalize',        [BillingController::class, 'finalizePeriod'])->name('periods.finalize');

        // Bills
        Route::get('/bills/{bill}',                      [BillingController::class, 'showBill'])->name('bills.show');
        Route::post('/bills/{bill}/issue',               [BillingController::class, 'issueBill'])->name('bills.issue');
        Route::post('/bills/{bill}/adjust',              [BillingController::class, 'adjustBill'])->name('bills.adjust');
        Route::post('/bills/{bill}/finalize',            [BillingController::class, 'finalizeBill'])->name('bills.finalize');
        Route::post('/bills/{bill}/paid',                [BillingController::class, 'markPaid'])->name('bills.paid');
        Route::get('/bills/{bill}/invoice',              [BillingController::class, 'invoiceView'])->name('bills.invoice');
        Route::post('/line-items/{lineItem}/override',   [BillingController::class, 'overrideLineItem'])->name('line-items.override');

        // Disputes
        Route::get('/disputes',                          [BillDisputeController::class, 'index'])->name('disputes.index');
        Route::post('/disputes/{bill}',                  [BillDisputeController::class, 'store'])->name('disputes.store');
        Route::get('/disputes/{dispute}',                [BillDisputeController::class, 'show'])->name('disputes.show');
        Route::post('/disputes/{dispute}/approve',       [BillDisputeController::class, 'approve'])->name('disputes.approve');
        Route::post('/disputes/{dispute}/partial',       [BillDisputeController::class, 'partialApprove'])->name('disputes.partial');
        Route::post('/disputes/{dispute}/reject',        [BillDisputeController::class, 'reject'])->name('disputes.reject');
    });
    // ── End Billing ───────────────────────────────────────────────────────────

    Route::get('key-visualsx', [KeyVisualController::class, 'old']);
    Route::post('site-settings/theme', [SiteSettingsController::class, 'saveTheme'])->name('site-settings.theme');

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

Route::get('/has-kv', function (){
//    \App\Models\Asset::whereNotIn('id', [1,2])
//        ->update(['has_kv_slot' => 1]);
//    return \Illuminate\Support\Facades\Hash::make('@');
    return response()->json([
        'success'   => true,
        'message'   => 'Response message goes here.',
        'data'      => [
            ['data-index-2' => 'data-index-value-2' ],
            ['data-index-2' => 'data-index-value-2' ],
            ['data-index-3' => 'data-index-value-3' ],
        ],
    ]);
});
