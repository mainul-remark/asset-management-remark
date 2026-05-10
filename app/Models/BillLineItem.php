<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BillLineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_brand_bill_id',
        'asset_id',
        'asset_type_id',
        'payment_type',
        'asset_sqft',
        'rate_per_sqft',
        'unit_price',
        'quantity',
        'assigned_brands_count',
        'full_calculated_amount',
        'calculated_amount',
        'override_amount',
        'final_amount',
        'note',
    ];

    protected $casts = [
        'asset_sqft'              => 'decimal:4',
        'rate_per_sqft'           => 'decimal:4',
        'unit_price'              => 'decimal:2',
        'quantity'                => 'decimal:4',
        'assigned_brands_count'   => 'integer',
        'full_calculated_amount'  => 'decimal:2',
        'calculated_amount'       => 'decimal:2',
        'override_amount'         => 'decimal:2',
        'final_amount'            => 'decimal:2',
    ];

    public function storeBrandBill()
    {
        return $this->belongsTo(StoreBrandBill::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function assetType()
    {
        return $this->belongsTo(AssetType::class);
    }
}
