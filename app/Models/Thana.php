<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Thana extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['district_id', 'name', 'boundary_polygon'];

    protected $hidden = ['boundary_polygon'];

    protected $searchableFields = ['*'];

    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
