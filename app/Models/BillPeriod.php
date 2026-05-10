<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BillPeriod extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'period_type',
        'period_start',
        'period_end',
        'status',
        'generated_at',
        'finalized_at',
        'created_by',
    ];

    protected $casts = [
        'period_start'   => 'date',
        'period_end'     => 'date',
        'generated_at'   => 'datetime',
        'finalized_at'   => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('billing')
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function storeBrandBills()
    {
        return $this->hasMany(StoreBrandBill::class);
    }

    public function commonSpaceLogs()
    {
        return $this->hasMany(CommonSpaceLog::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isGenerated(): bool
    {
        return in_array($this->status, ['generated', 'finalized'], true);
    }

    public function isFinalized(): bool
    {
        return $this->status === 'finalized';
    }
}
