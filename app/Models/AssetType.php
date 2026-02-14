<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
        'dimension_unit_name',
        'default_price',
        'status',
        'is_digital',
        'total_self',
        'has_kv_space',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'asset_types';
}
