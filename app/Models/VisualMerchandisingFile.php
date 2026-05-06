<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class VisualMerchandisingFile extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = ['visual_merchandising_id', 'file_path', 'file_type'];

    protected $searchableFields = ['*'];

    protected $table = 'visual_merchandising_files';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('data')
            ->logOnly(['visual_merchandising_id', 'file_path', 'file_type'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
