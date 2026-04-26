<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserStoreAssignment extends Model
{
    use HasFactory;
    use Searchable;

    protected $fillable = [
        'store_id',
        'user_id',
        'role_id',
        'status',
        'assigned_by',
        'assigned_at',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'user_store_assignments';

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
