<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KeyVisualSize extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['name', 'height', 'width', 'unit_name', 'status'];

    protected $searchableFields = ['*'];

    protected $table = 'key_visual_sizes';

    protected $casts = [
        'height' => 'decimal:0',
        'width' => 'decimal:0',
        'status' => 'integer',
    ];

    public function keyVisualFiles()
    {
        return $this->hasMany(KeyVisualFiles::class, 'key_visual_size_id');
    }

    public function allKeyVisualFiles()
    {
        return $this->keyVisualFiles();
    }
}
