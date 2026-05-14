<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class KeyVisualFiles extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'name',
        'key_visual_id',
        'key_visual_size_id',
        'kv_file',
        'kv_size',
        'aspect_ratio',
        'file_type',
        'file_duration',
        'kv_file_code',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'key_visual_files';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly([
                'name',
                'key_visual_id',
                'key_visual_size_id',
                'kv_file',
                'kv_size',
                'aspect_ratio',
                'file_type',
                'file_duration',
                'kv_file_code',
                'status',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    protected $casts = [
        'key_visual_id' => 'integer',
        'key_visual_size_id' => 'integer',
        'kv_size' => 'integer',
        'aspect_ratio' => 'float',
        'status' => 'integer',
    ];

    public static function generateUniqueKvFileCode(int $keyVisualId): string
    {
        $keyVisual = \App\Models\KeyVisual::select('unique_code')->find($keyVisualId);
        $prefix = $keyVisual?->unique_code
            ? strtoupper($keyVisual->unique_code) . '-F'
            : 'KVF-';

        $attempt = 1;
        do {
            $code = $prefix . str_pad($attempt, 3, '0', STR_PAD_LEFT);
            $exists = static::withTrashed()->where('kv_file_code', $code)->exists();
            $attempt++;
        } while ($exists && $attempt <= 9999);

        return $code;
    }

    public function keyVisualSize()
    {
        return $this->belongsTo(KeyVisualSize::class, 'key_visual_size_id');
    }

    public function keyVisual()
    {
        return $this->belongsTo(KeyVisual::class, 'key_visual_id');
    }

    public function assignKvToAssets()
    {
        return $this->hasMany(AssignKvToAsset::class);
    }
}
