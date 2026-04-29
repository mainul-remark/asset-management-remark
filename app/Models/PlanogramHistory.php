<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlanogramHistory extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

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

    protected $casts = [
        'changed_date' => 'datetime',
    ];

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
