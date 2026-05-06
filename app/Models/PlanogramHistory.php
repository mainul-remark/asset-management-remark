<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use App\Models\Asset;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PlanogramHistory extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'store_id',
        'asset_id',
        'assigned_by',
        'file_path',
        'status',
        'brand_id',
        'changed_date',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'planogram_histories';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly([
                'store_id',
                'asset_id',
                'assigned_by',
                'file_path',
                'status',
                'brand_id',
                'changed_date',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'changed_date' => 'datetime',
    ];

    public static function createPlanogramHistory($request)
    {
        return static::create([
            'store_id'     => $request->store_id,
            'asset_id'     => $request->asset_id,
            'assigned_by'  => CustomHelper::loggedUser()->id,
            'file_path'    => $request->file_path,
            'status'       => 1,
            'brand_id'     => $request->brand_id,
            'changed_date' => now(),
        ]);
    }

    public static function recordForAsset(Asset $asset, string $filePath, ?int $brandId = null): void
    {
        if (! $asset->store_id || ! $filePath) {
            return;
        }

        static::where('asset_id', $asset->id)->update(['status' => 0]);

        static::create([
            'store_id'     => $asset->store_id,
            'asset_id'     => $asset->id,
            'assigned_by'  => CustomHelper::loggedUser()->id,
            'file_path'    => $filePath,
            'status'       => 1,
            'brand_id'     => $brandId,
            'changed_date' => now(),
        ]);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
