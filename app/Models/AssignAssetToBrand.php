<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use RuntimeException;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssignAssetToBrand extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected static function booted(): void
    {
        static::saving(function (self $assignment): void {
            $assignment->is_asset_assigned_currently = static::resolveCurrentAssignmentFlag(
                (int) $assignment->status
            );
        });
    }

    protected $fillable = [
        'asset_id',
        'brand_id',
        'assigned_by_user_id',
        'asset_charge',
        'close_date',
        'status',
        'is_asset_assigned_currently',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'assign_asset_to_brands';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly([
                'asset_id',
                'brand_id',
                'assigned_by_user_id',
                'asset_charge',
                'close_date',
                'status',
                'is_asset_assigned_currently',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'asset_id' => 'integer',
        'brand_id' => 'integer',
        'assigned_by_user_id' => 'integer',
        'asset_charge' => 'decimal:2',
        'status' => 'integer',
        'is_asset_assigned_currently' => 'integer',
        'close_date' => 'date:Y-m-d',
    ];

    public static function detailRelations(): array
    {
        return [
            'asset:id,name,asset_code,asset_type_id,store_id,is_common_asset',
            'asset.assetType:id,name',
            'asset.store:id,title,code,division_id,district_id',
            'asset.store.division:id,name',
            'asset.store.district:id,name',
            'brand:id,name,code,status',
            'assignedBy:id,name',
        ];
    }

    public static function filteredQuery(array $filters = []): Builder
    {
        return static::applyFilters(
            static::query()->with(static::detailRelations()),
            $filters
        );
    }

    public static function filteredAssetQuery(array $filters = []): Builder
    {
        return static::applyFilters(
            static::query()
                ->select('asset_id')
                ->selectRaw('MAX(id) as latest_assignment_id')
                ->groupBy('asset_id'),
            $filters
        );
    }

    private static function applyFilters(Builder $query, array $filters = []): Builder
    {
        return $query
            ->when(!empty($filters['division_id']), function (Builder $query) use ($filters) {
                $query->whereHas('asset.store', fn (Builder $q) => $q->where('division_id', $filters['division_id']));
            })
            ->when(!empty($filters['district_id']), function (Builder $query) use ($filters) {
                $query->whereHas('asset.store', fn (Builder $q) => $q->where('district_id', $filters['district_id']));
            })
            ->when(!empty($filters['store_id']), function (Builder $query) use ($filters) {
                $query->whereHas('asset', fn (Builder $q) => $q->where('store_id', $filters['store_id']));
            })
            ->when(!empty($filters['asset_type_id']), function (Builder $query) use ($filters) {
                $query->whereHas('asset', fn (Builder $q) => $q->where('asset_type_id', $filters['asset_type_id']));
            })
            ->when(!empty($filters['asset_id']), fn (Builder $query) => $query->where('asset_id', $filters['asset_id']))
            ->when(!empty($filters['brand_id']), fn (Builder $query) => $query->where('brand_id', $filters['brand_id']))
            ->when(array_key_exists('status', $filters) && $filters['status'] !== '' && $filters['status'] !== null, fn (Builder $query) => $query->where('status', (int) $filters['status']))
            ->when(array_key_exists('is_asset_assigned_currently', $filters) && $filters['is_asset_assigned_currently'] !== '' && $filters['is_asset_assigned_currently'] !== null, fn (Builder $query) => $query->where('is_asset_assigned_currently', (int) $filters['is_asset_assigned_currently']));
    }

    public static function updateOrCreateAssignment($request, ?self $assignment = null): self
    {
        $data = $request->validated();
        $assignedByUserId = static::resolveAssignedByUserId($assignment);
        $payload = static::buildPayload(
            $request,
            $data,
            (int) $data['brand_id'],
            $assignedByUserId,
            $assignment
        );

        if ($assignment) {
            $assignment->update($payload);

            return $assignment->fresh(static::detailRelations());
        }

        return static::create($payload)->load(static::detailRelations());
    }

    public static function createAssignments($request): Collection
    {
        $data = $request->validated();
        $assignedByUserId = static::resolveAssignedByUserId();
        $brandIds = collect($data['brand_ids'] ?? [])
            ->map(fn ($brandId) => (int) $brandId)
            ->unique()
            ->values();

        $existingBrandIds = static::query()
            ->where('asset_id', (int) $data['asset_id'])
            ->whereNull('deleted_at')
            ->pluck('brand_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $newBrandIds = $brandIds->reject(fn (int $brandId) => in_array($brandId, $existingBrandIds, true));

        return $newBrandIds->map(function (int $brandId) use ($request, $data, $assignedByUserId) {
            $payload = static::buildPayload($request, $data, $brandId, $assignedByUserId);

            return static::create($payload)->load(static::detailRelations());
        });
    }

    private static function resolveAssignedByUserId(?self $assignment = null): int
    {
        $assignedByUserId = $assignment?->assigned_by_user_id
            ?? CustomHelper::loggedUser()?->id
            ?? auth()->id()
            ?? User::query()->value('id');

        if (!$assignedByUserId) {
            throw new RuntimeException('AssignAssetToBrand requires an existing user.');
        }

        return (int) $assignedByUserId;
    }

    private static function buildPayload($request, array $data, int $brandId, int $assignedByUserId, ?self $assignment = null): array
    {
        return [
            'asset_id' => (int) $data['asset_id'],
            'brand_id' => $brandId,
            'assigned_by_user_id' => $assignedByUserId,
            'asset_charge' => $request->filled('asset_charge') && array_key_exists('asset_charge', $data)
                ? number_format((float) $data['asset_charge'], 2, '.', '')
                : ($assignment?->asset_charge ?? '0.00'),
            'close_date' => $request->filled('close_date') && array_key_exists('close_date', $data)
                ? Carbon::parse($data['close_date'])->toDateString()
                : $assignment?->close_date,
            'status' => (int) $data['status'],
        ];
    }

    private static function resolveCurrentAssignmentFlag(int $status): int
    {
        return $status === 1 ? 1 : 0;
    }

    public function markAsNotCurrentlyAssigned(): void
    {
        $this->forceFill([
            'is_asset_assigned_currently' => 0,
        ])->saveQuietly();
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
