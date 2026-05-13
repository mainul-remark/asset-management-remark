<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BrandBillDispute extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'bill_period_id',
        'brand_id',
        'requested_by',
        'original_amount',
        'requested_amount',
        'reason',
        'status',
        'admin_response',
        'approved_amount',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'original_amount'  => 'decimal:2',
        'requested_amount' => 'decimal:2',
        'approved_amount'  => 'decimal:2',
        'reviewed_at'      => 'datetime',
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

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
