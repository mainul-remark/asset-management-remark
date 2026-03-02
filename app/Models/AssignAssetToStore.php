<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;

class AssignAssetToStore extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'store_id',
        'assign_date',
        'asset_charge',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'assign_asset_to_stores';

    public static function assignAssetsToStoreLog($asset)
    {
        return static::create([
            'asset_id' => $asset->id,
            'store_id' => $asset->store_id,
            'assign_date' => Carbon::now(),
            'asset_charge' => 0,
        ]);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
