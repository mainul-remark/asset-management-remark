<?php

namespace App\Http\Controllers\Backend\CommonPages;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssignAssetToBrand;
use App\Models\AssignKvToAsset;
use App\Models\Brand;
use App\Models\PlanogramHistory;
use App\Models\Store;
use App\Models\User;
use App\Models\UserStoreAssignment;
use App\Models\VisualMerchandising;
use Illuminate\Support\Facades\DB;

class AdminViewController extends Controller
{
    public function dashboard()
    {
        $kpis = [
            'stores' => [
                'label' => 'Active Stores',
                'value' => Store::query()->where('status', 1)->count(),
                'meta'  => 'Retail footprint currently active',
            ],
            'assets' => [
                'label' => 'Active Assets',
                'value' => Asset::query()->where('status', 1)->count(),
                'meta'  => 'Published and trackable assets',
            ],
            'brands' => [
                'label' => 'Active Brands',
                'value' => Brand::query()->where('status', 1)->count(),
                'meta'  => 'Brands currently configured',
            ],
            'assignments' => [
                'label' => 'Current Brand Assignments',
                'value' => AssignAssetToBrand::query()->where('is_asset_assigned_currently', 1)->count(),
                'meta'  => 'Assets currently assigned to brands',
            ],
            'kv_pending' => [
                'label' => 'KV Pending / Planned',
                'value' => AssignKvToAsset::query()
                    ->whereIn('instalation_status', ['pending', 'planned'])
                    ->count(),
                'meta'  => 'KV installation backlog',
            ],
            'vm_open' => [
                'label' => 'Open VM Issues',
                'value' => VisualMerchandising::query()
                    ->whereNotIn('issue_fix_status', ['solved'])
                    ->count(),
                'meta'  => 'Issues still requiring action',
            ],
        ];

        $assetMix = DB::table('assets as assets')
            ->join('asset_types as asset_types', 'asset_types.id', '=', 'assets.asset_type_id')
            ->select('asset_types.name', DB::raw('COUNT(*) as total'))
            ->groupBy('asset_types.id', 'asset_types.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $topStores = Store::query()
            ->withCount('assets')
            ->orderByDesc('assets_count')
            ->limit(8)
            ->get(['title']);

        $userSectorMix = User::query()
            ->select('usages_sector', DB::raw('COUNT(*) as total'))
            ->groupBy('usages_sector')
            ->get();

        $brandCoverage = DB::table('assign_asset_to_brands as assignments')
            ->join('brands', 'brands.id', '=', 'assignments.brand_id')
            ->select('brands.name', DB::raw('COUNT(*) as total'))
            ->groupBy('brands.id', 'brands.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $monthlyAssets = DB::table('assets')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $monthlyUsers = DB::table('users')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $monthlyVmIssues = DB::table('visual_merchandisings')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as period, COUNT(*) as total")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        $planogramActivity = DB::table('planogram_histories')
            ->selectRaw("DATE(created_at) as activity_date, COUNT(*) as total")
            ->groupBy('activity_date')
            ->orderBy('activity_date')
            ->limit(14)
            ->get();

        $operationalHealth = [
            'assets_with_planogram' => Asset::query()->whereNotNull('planogram_pdf')->count(),
            'assets_with_kv_slot'   => Asset::query()->where('has_kv_slot', 1)->count(),
            'store_assigned_users'  => UserStoreAssignment::query()->distinct('user_id')->count('user_id'),
            'vm_solved'             => VisualMerchandising::query()->where('issue_fix_status', 'solved')->count(),
        ];

        $recentUsers = User::query()
            ->latest()
            ->limit(5)
            ->get(['name', 'email', 'employee_id', 'usages_sector', 'created_at']);

        $recentPlanograms = PlanogramHistory::query()
            ->with([
                'store:id,title',
                'asset:id,name',
                'brand:id,name',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view('backend.common-pages.dashboard.dashboard', [
            'dashboardData' => [
                'kpis'              => $kpis,
                'assetMix'          => $assetMix,
                'topStores'         => $topStores,
                'userSectorMix'     => $userSectorMix,
                'brandCoverage'     => $brandCoverage,
                'monthlyAssets'     => $monthlyAssets,
                'monthlyUsers'      => $monthlyUsers,
                'monthlyVmIssues'   => $monthlyVmIssues,
                'planogramActivity' => $planogramActivity,
                'operationalHealth' => $operationalHealth,
                'recentUsers'       => $recentUsers,
                'recentPlanograms'  => $recentPlanograms,
            ],
        ]);
    }
}
