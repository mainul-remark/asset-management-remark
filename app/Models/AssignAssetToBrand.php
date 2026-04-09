<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssignAssetToBrand extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'asset_id',
        'brand_id',
        'assigned_by_user_id',
        'asset_charge',
        'close_date',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'assign_asset_to_brands';

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
