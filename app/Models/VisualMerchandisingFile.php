<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;

class VisualMerchandisingFile extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = ['visual_merchandising_id', 'file_path', 'file_type'];

    protected $searchableFields = ['*'];

    protected $table = 'visual_merchandising_files';

    protected static function booted(): void
    {
        static::deleting(function (self $visualMerchandisingFile): void {
            if (! $visualMerchandisingFile->file_path) {
                return;
            }

            $absolutePath = public_path($visualMerchandisingFile->file_path);

            if (File::exists($absolutePath)) {
                File::delete($absolutePath);
            }
        });
    }

    public function visualMerchandising()
    {
        return $this->belongsTo(VisualMerchandising::class);
    }
}
