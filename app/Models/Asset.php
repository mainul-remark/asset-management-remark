<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Asset extends Model
{
    use HasFactory;
    use Searchable;

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
    }

    public static function generateUniqueAssetCode(): string
    {
        $baseCode = 50000000;
        $nextId = ((int) self::max('id')) + 1;
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
}
