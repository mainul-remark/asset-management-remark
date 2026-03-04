<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeyVisualSize extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'name',
        'height',
        'width',
        'unit_name',
        'kv_file',
        'kv_size',
        'aspect_ratio',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'key_visual_sizes';

    public function keyVisuals()
    {
        return $this->belongsToMany(KeyVisual::class);
    }
}
