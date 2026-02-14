<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class District extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['division_id', 'name', 'boundary_polygon'];

    protected $hidden = ['boundary_polygon'];

    protected $searchableFields = ['*'];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function thanas()
    {
        return $this->hasMany(Thana::class);
    }
}
