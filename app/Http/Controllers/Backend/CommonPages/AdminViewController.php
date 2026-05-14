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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Spatie\Activitylog\Models\Activity;

class AdminViewController extends Controller
{
    public function dashboard()
    {
        if (!auth()->user()->isAdmin()) {
            return view('backend.common-pages.dashboard.dashboard-common');
        }
        $kpis = [
            'stores' => [
                'label' => 'Active Stores',
                'value' => Store::query()->where('status', 1)->count(),
                'total' => Store::query()->count(),
                'meta'  => 'Retail footprint currently active',
                'icon'  => 'store',
                'color' => 'blue',
            ],
            'assets' => [
                'label' => 'Active Assets',
                'value' => Asset::query()->where('status', 1)->count(),
                'total' => Asset::query()->count(),
                'meta'  => 'Published and trackable assets',
                'icon'  => 'layers',
                'color' => 'indigo',
            ],
            'brands' => [
                'label' => 'Active Brands',
                'value' => Brand::query()->where('status', 1)->count(),
                'total' => Brand::query()->count(),
                'meta'  => 'Brands currently configured',
                'icon'  => 'tag',
                'color' => 'purple',
            ],
            'assignments' => [
                'label' => 'Brand Assignments',
                'value' => AssignAssetToBrand::query()->where('is_asset_assigned_currently', 1)->count(),
                'total' => AssignAssetToBrand::query()->count(),
                'meta'  => 'Assets currently assigned to brands',
                'icon'  => 'link',
                'color' => 'teal',
            ],
            'kv_pending' => [
                'label' => 'KV Pending / Planned',
                'value' => AssignKvToAsset::query()->whereIn('instalation_status', ['pending', 'planned'])->count(),
                'total' => AssignKvToAsset::query()->count(),
                'meta'  => 'KV installation backlog',
                'icon'  => 'clock',
                'color' => 'amber',
            ],
            'vm_open' => [
                'label' => 'Open VM Issues',
                'value' => VisualMerchandising::query()->whereNotIn('issue_fix_status', ['solved'])->count(),
                'total' => VisualMerchandising::query()->count(),
                'meta'  => 'Issues still requiring action',
                'icon'  => 'alert',
                'color' => 'red',
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
            ->with(['store:id,title', 'asset:id,name', 'brand:id,name'])
            ->latest()
            ->limit(5)
            ->get();

        return view('backend.common-pages.dashboard.dashboard', [
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
        ]);
    }

    public function dashboardGpt()
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

        return view('backend.common-pages.dashboard.dashboard-gpt', [
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

    public function activityLog(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'event' => ['nullable', 'string', 'max:50'],
            'log_name' => ['nullable', 'string', 'max:255'],
            'subject_type' => ['nullable', 'string', 'max:255'],
            'causer_id' => ['nullable', 'integer', 'exists:users,id'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $activityLogsQuery = $this->applyActivityLogFilters(
            Activity::query()->with(['causer', 'subject'])->latest(),
            $filters
        );

        $activityLogs = $activityLogsQuery
            ->paginate(15)
            ->withQueryString();

        $summaryQuery = $this->applyActivityLogFilters(Activity::query(), $filters);

        $summary = [
            'total' => (clone $summaryQuery)->count(),
            'today' => (clone $summaryQuery)->whereDate('created_at', today())->count(),
            'auth' => (clone $summaryQuery)->where('log_name', 'auth')->count(),
            'data' => (clone $summaryQuery)->where('log_name', 'data')->count(),
            'workflow' => (clone $summaryQuery)->where('log_name', 'workflow')->count(),
        ];

        $eventOptions = Activity::query()
            ->whereNotNull('event')
            ->select('event')
            ->distinct()
            ->orderBy('event')
            ->pluck('event');

        $logNameOptions = Activity::query()
            ->whereNotNull('log_name')
            ->select('log_name')
            ->distinct()
            ->orderBy('log_name')
            ->pluck('log_name');

        $subjectTypeOptions = Activity::query()
            ->whereNotNull('subject_type')
            ->select('subject_type')
            ->distinct()
            ->orderBy('subject_type')
            ->pluck('subject_type')
            ->map(fn (string $subjectType) => [
                'value' => $subjectType,
                'label' => class_basename($subjectType),
            ]);

        $causerOptions = User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return view('backend.common-pages.activity-error-log', [
            'activityLogs' => $activityLogs,
            'summary' => $summary,
            'filters' => $filters,
            'eventOptions' => $eventOptions,
            'logNameOptions' => $logNameOptions,
            'subjectTypeOptions' => $subjectTypeOptions,
            'causerOptions' => $causerOptions,
        ]);
    }

    private function applyActivityLogFilters(Builder $query, array $filters): Builder
    {
        $search = trim((string) ($filters['search'] ?? ''));

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('description', 'like', "%{$search}%")
                    ->orWhere('event', 'like', "%{$search}%")
                    ->orWhere('log_name', 'like', "%{$search}%")
                    ->orWhere('subject_type', 'like', "%{$search}%")
                    ->orWhere('causer_type', 'like', "%{$search}%")
                    ->orWhere('properties', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['event'])) {
            $query->where('event', $filters['event']);
        }

        if (! empty($filters['log_name'])) {
            $query->where('log_name', $filters['log_name']);
        }

        if (! empty($filters['subject_type'])) {
            $query->where('subject_type', $filters['subject_type']);
        }

        if (! empty($filters['causer_id'])) {
            $query->where('causer_type', User::class)
                ->where('causer_id', (int) $filters['causer_id']);
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}
