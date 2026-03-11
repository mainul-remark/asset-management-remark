<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;

class AssetType extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'default_image',
        'height',
        'width',
        'depth',
        'dimention_unit_name',
        'default_price',
        'status',
        'is_digital',
        'total_self',
        'has_kv_space',
        'has_default_dimension',
        'need_asset_image',
        'need_asset_planogram',
        'has_asset_self',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'asset_types';

    public static function updateOrCreateAssetType($request, ?self $assetType = null): self
    {
        $data = [
            'name'                  => $request->name,
            'height'                => $request->height,
            'width'                 => $request->width,
            'depth'                 => $request->depth,
            'dimention_unit_name'   => $request->dimension_unit_name,
            'default_price'         => $request->default_price,
            'total_self'            => $request->total_self ?? 0,
            'status'                => $request->status ?? 0,
            'is_digital'            => $request->is_digital ?? 0,
            'has_kv_space'          => $request->has_kv_space ?? 0,
            'has_default_dimension' => $request->has_default_dimension ?? 0,
            'need_asset_image'      => $request->need_asset_image ?? 0,
            'need_asset_planogram'  => $request->need_asset_planogram ?? 0,
            'has_asset_self'        => $request->has_asset_self ?? 0,
        ];

        if ($request->hasFile('default_image')) {
            $data['default_image'] = CustomHelper::fileUpload(
                $request->file('default_image'),
                'asset-types',
                'at',
                null,
                null,
                $assetType?->default_image
            );
        }

        if ($assetType) {
            $assetType->update($data);
            return $assetType;
        }

        return self::create($data);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function keyVisuals()
    {
        return $this->hasMany(KeyVisual::class);
    }
}
