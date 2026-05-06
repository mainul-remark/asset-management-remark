<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssignKvToAsset extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'asset_id',
        'key_visual_id',
        'key_visual_files_id',
        'has_perfect_size_kv',
        'assigned_date',
        'assigned_by',
        'installed_by',
        'instalation_proof',
        'instalation_status',
        'instalation_date',
        'slot_number',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'assign_kv_to_assets';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly([
                'asset_id',
                'key_visual_id',
                'key_visual_files_id',
                'has_perfect_size_kv',
                'assigned_date',
                'assigned_by',
                'installed_by',
                'instalation_proof',
                'instalation_status',
                'instalation_date',
                'slot_number',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'asset_id' => 'integer',
        'key_visual_id' => 'integer',
        'key_visual_files_id' => 'integer',
        'has_perfect_size_kv' => 'integer',
        'assigned_by' => 'integer',
        'installed_by' => 'integer',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function keyVisual()
    {
        return $this->belongsTo(KeyVisual::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function installedBy()
    {
        return $this->belongsTo(User::class, 'installed_by');
    }

    public function keyVisualFile()
    {
        return $this->belongsTo(KeyVisualFiles::class, 'key_visual_files_id');
    }

    public function keyVisualFiles()
    {
        return $this->keyVisualFile();
    }
}
