<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class KeyVisual extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'asset_type_id',
        'name',
        'unique_code',
        'minimum_res_height',
        'minimum_res_width',
        'kv_type',
        'kv_sample_file',
        'kv_thumb',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'key_visuals';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly([
                'asset_type_id',
                'name',
                'unique_code',
                'minimum_res_height',
                'minimum_res_width',
                'kv_type',
                'kv_sample_file',
                'kv_thumb',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected static function booted(): void
    {
        static::forceDeleted(function (self $keyVisual): void {
            if ($keyVisual->kv_sample_file && file_exists(public_path($keyVisual->kv_sample_file))) {
                File::delete(public_path($keyVisual->kv_sample_file));
            }

            if ($keyVisual->kv_thumb && file_exists(public_path($keyVisual->kv_thumb))) {
                File::delete(public_path($keyVisual->kv_thumb));
            }
        });
    }

    public static function updateOrCreateKeyVisual($request, $keyVisual = null): self
    {
        $data = $request->validated();
        $data['status'] = $request->boolean('status') ? 1 : 0;

        if ($request->hasFile('kv_sample_file')) {
            $data['kv_sample_file'] = CustomHelper::fileUpload(
                $request->file('kv_sample_file'),
                'key-visuals',
                'key-visual-sample',
                null,
                null,
                $keyVisual->kv_sample_file ?? null
            );
        } else {
            unset($data['kv_sample_file']);
        }

        if ($request->hasFile('kv_thumb')) {
            $data['kv_thumb'] = CustomHelper::fileUpload(
                $request->file('kv_thumb'),
                'key-visuals',
                'key-visual-thumb',
                300,
                300,
                $keyVisual->kv_thumb ?? null
            );
        } else {
            unset($data['kv_thumb']);
        }

        return static::updateOrCreate(['id' => $keyVisual?->id], $data);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function brandingAssetType()
    {
        return $this->assetType();
    }

    public function keyVisualSizes()
    {
        return $this->hasMany(KeyVisualSize::class);
    }

    public function keyVisualFiles()
    {
        return $this->hasMany(KeyVisualFiles::class, 'key_visual_id');
    }

    public function brands()
    {
        return $this->belongsToMany(Brand::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function assignKvToAssets()
    {
        return $this->hasMany(AssignKvToAsset::class);
    }

    public function allKeyVisualFiles()
    {
        return $this->hasMany(KeyVisualFiles::class);
    }
}
