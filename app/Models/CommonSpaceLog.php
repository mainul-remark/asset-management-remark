<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CommonSpaceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'bill_period_id',
        'store_id',
        'total_store_sqft',
        'dedicated_ground_sqft',
        'common_ground_asset_sqft',
        'remaining_sqft',
        'common_static_fees_total',
        'brand_count',
        'rate_per_sqft',
        'common_charge_per_brand',
        'calculated_at',
    ];

    protected $casts = [
        'total_store_sqft'         => 'decimal:2',
        'dedicated_ground_sqft'    => 'decimal:2',
        'common_ground_asset_sqft' => 'decimal:2',
        'remaining_sqft'           => 'decimal:2',
        'common_static_fees_total' => 'decimal:2',
        'brand_count'              => 'integer',
        'rate_per_sqft'            => 'decimal:4',
        'common_charge_per_brand'  => 'decimal:2',
        'calculated_at'            => 'datetime',
    ];

    public function billPeriod()
    {
        return $this->belongsTo(BillPeriod::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
