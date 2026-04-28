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
        'total_kv_slot',
        'code',
        'is_double_side',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'asset_types';

    public static function generateUniqueCodeFromName(string $name, ?int $ignoreId = null): string
    {
        $baseCode = static::buildCodeSeed($name);
        $candidate = $baseCode;
        $suffix = 2;

        while (static::withTrashed()
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->where('code', $candidate)
            ->exists()) {
            $candidate = $baseCode . $suffix;
            $suffix++;
        }

        return $candidate;
    }

    protected static function buildCodeSeed(string $name): string
    {
        preg_match_all('/[A-Za-z0-9]+/', strtoupper($name), $matches);
        $words = $matches[0] ?? [];

        if (count($words) > 1) {
            $seed = implode('', array_map(fn ($word) => substr($word, 0, 1), $words));

            return $seed !== '' ? $seed : 'AST';
        }

        if (! empty($words[0])) {
            return substr($words[0], 0, 3);
        }

        return 'AST';
    }

    public static function updateOrCreateAssetType($request, ?self $assetType = null): self
    {
        $resolvedCode = strtoupper(trim((string) ($request->code ?? '')));

        if ($resolvedCode === '') {
            $resolvedCode = static::generateUniqueCodeFromName((string) $request->name, $assetType?->id);
        }

        $data = [
            'name'                  => $request->name,
            'height'                => $request->height,
            'width'                 => $request->width,
            'depth'                 => $request->depth,
            'dimention_unit_name'   => $request->dimension_unit_name,
            'default_price'         => $request->default_price,
            'total_self'            => ($request->has_asset_self ?? 0) ? ($request->total_self ?? 0) : null,
            'status'                => $request->status ?? 0,
            'is_digital'            => $request->is_digital ?? 0,
            'has_kv_space'          => $request->has_kv_space ?? 0,
            'has_default_dimension' => $request->has_default_dimension ?? 0,
            'need_asset_image'      => $request->need_asset_image ?? 0,
            'need_asset_planogram'  => $request->need_asset_planogram ?? 0,
            'has_asset_self'        => $request->has_asset_self ?? 0,
            'total_kv_slot'         => $request->total_kv_slot ?? 0,
            'code'                  => $resolvedCode,
            'is_double_side'        => $request->is_double_side ?? 0,
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

    public function assignedAssets()
    {
        return $this->belongsToMany(Asset::class);
    }
}
