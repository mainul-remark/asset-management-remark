<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\File;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class Asset extends Model
{
    use HasFactory;
    use Searchable, softDeletes;

    protected $fillable = [
        'asset_type_id',
        'name',
        'default_image',
        'store_id',
        'asset_code',
        'has_kv_slot',
        'minimum_fee',
        'asset_price',
        'is_common_asset',
        'planogram_pdf',
        'status',
        'has_self',
        'total_self',
    ];

    protected $searchableFields = ['*'];

    protected static function booted(): void
    {
        static::creating(function (self $asset): void {
            if (blank($asset->asset_code)) {
                $asset->asset_code = self::generateUniqueAssetCode();
            }
        });

        static::forceDeleted(function (self $asset): void {
            if ($asset->default_image && file_exists(public_path($asset->default_image))) {
                File::delete(public_path($asset->default_image));
            }
            if ($asset->planogram_pdf && file_exists(public_path($asset->planogram_pdf))) {
                File::delete(public_path($asset->planogram_pdf));
            }
        });
    }

    public static function updateOrCreateAsset($request, $asset = null): self
    {
        $data = $request->validated();

        $data['asset_type_id']   = is_array($data['asset_type_id'] ?? null) ? ($data['asset_type_id'][0] ?? null) : ($data['asset_type_id'] ?? null);
        $data['has_kv_slot']     = $request->boolean('has_kv_slot') ? 1 : 0;
        $data['is_common_asset'] = $request->boolean('is_common_asset') ? 1 : 0;
        $data['status']          = $request->boolean('status') ? 1 : 0;
        $data['has_self']        = $request->boolean('has_self') ? 1 : 0;

        if ($data['is_common_asset']) {
            $data['store_id'] = null;
        }

        if (!$data['has_self']) {
            $data['total_self'] = null;
        }

        if ($request->hasFile('default_image')) {
            $data['default_image'] = CustomHelper::fileUpload(
                $request->file('default_image'),
                'asset-image',
                'asset-image',
                null,
                null,
                $asset->default_image ?? null
            );
        } else {
            unset($data['default_image']);
        }

        if ($request->hasFile('planogram_pdf')) {
            $data['planogram_pdf'] = CustomHelper::fileUpload(
                $request->file('planogram_pdf'),
                'asset-planogram',
                'asset-planogram',
                null,
                null,
                $asset->planogram_pdf ?? null
            );
        } else {
            unset($data['planogram_pdf']);
        }

        return static::updateOrCreate(['id' => $asset?->id], $data);
    }

    public static function generateUniqueAssetCode(): string
    {
        $baseCode = 50000000;
        $nextId   = ((int) self::max('id')) + 1;
        $nextCode = $baseCode + max(1, $nextId);

        while (self::where('asset_code', (string) $nextCode)->exists()) {
            $nextCode++;
        }

        return (string) $nextCode;
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function assignAssetToStores()
    {
        return $this->hasMany(AssignAssetToStore::class);
    }

    public function assignKvToAssets()
    {
        return $this->hasMany(AssignKvToAsset::class);
    }

    public function visualMerchandisings()
    {
        return $this->hasMany(VisualMerchandising::class);
    }

    public function assignAssetToBrands()
    {
        return $this->hasMany(AssignAssetToBrand::class);
    }

    public function assetTypes()
    {
        return $this->belongsToMany(AssetType::class);
    }
}
