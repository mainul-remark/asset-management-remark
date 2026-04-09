<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'code',
        'description',
        'status',
        'is_common',
        'created_by',
    ];

    protected $searchableFields = ['*'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'category_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function categoriesRecursive()
    {
        return $this->children()->with('categoriesRecursive');
    }

    public static function updateOrCreateCategory($request, $category = null)
    {
        $data = [
            'category_id' => $request->category_id ?: null,
            'name'        => $request->name,
            'code'        => strtoupper($request->code),
            'description' => $request->description,
            'status'      => $request->boolean('status') ? 1 : 0,
            'is_common'   => $request->boolean('is_common') ? 1 : 0,
        ];

        if (! $category) {
            $data['created_by'] = auth()->id();
        }

        return static::updateOrCreate(['id' => $category?->id], $data);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function keyVisuals()
    {
        return $this->belongsToMany(KeyVisual::class);
    }
}
