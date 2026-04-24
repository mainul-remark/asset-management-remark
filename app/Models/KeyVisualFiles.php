<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeyVisualFiles extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'key_visual_id',
        'key_visual_size_id',
        'kv_file',
        'kv_size',
        'aspect_ratio',
        'file_type',
        'file_duration',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'key_visual_files';

    protected $casts = [
        'key_visual_id' => 'integer',
        'key_visual_size_id' => 'integer',
        'kv_size' => 'integer',
        'aspect_ratio' => 'float',
        'status' => 'integer',
    ];

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
