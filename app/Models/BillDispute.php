<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BillDispute extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'store_brand_bill_id',
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
        'original_amount'   => 'decimal:2',
        'requested_amount'  => 'decimal:2',
        'approved_amount'   => 'decimal:2',
        'reviewed_at'       => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('billing')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function storeBrandBill()
    {
        return $this->belongsTo(StoreBrandBill::class);
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
