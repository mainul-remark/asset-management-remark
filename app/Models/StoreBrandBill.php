<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class StoreBrandBill extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'bill_period_id',
        'store_id',
        'brand_id',
        'ground_amount',
        'static_amount',
        'common_amount',
        'subtotal',
        'adjustment_amount',
        'final_amount',
        'bill_status',
        'dispute_reason',
        'admin_note',
        'issued_at',
        'finalized_at',
        'finalized_by',
    ];

    protected $casts = [
        'ground_amount'     => 'decimal:2',
        'static_amount'     => 'decimal:2',
        'common_amount'     => 'decimal:2',
        'subtotal'          => 'decimal:2',
        'adjustment_amount' => 'decimal:2',
        'final_amount'      => 'decimal:2',
        'issued_at'         => 'datetime',
        'finalized_at'      => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('billing')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function billPeriod()
    {
        return $this->belongsTo(BillPeriod::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function lineItems()
    {
        return $this->hasMany(BillLineItem::class);
    }

    public function disputes()
    {
        return $this->hasMany(BillDispute::class);
    }

    public function finalizedBy()
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function hasPendingDispute(): bool
    {
        return $this->disputes()->where('status', 'pending')->exists();
    }

    public function recalculateTotals(): void
    {
        $this->ground_amount     = $this->lineItems()->where('payment_type', 'ground')->sum('final_amount');
        $this->static_amount     = $this->lineItems()->where('payment_type', 'static')->sum('final_amount');
        $this->common_amount     = $this->lineItems()->where('payment_type', 'common')->sum('final_amount');
        $this->subtotal          = $this->ground_amount + $this->static_amount + $this->common_amount;
        $this->final_amount      = $this->subtotal + $this->adjustment_amount;
        $this->save();
    }
}
