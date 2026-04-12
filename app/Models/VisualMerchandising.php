<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Mainul\CustomHelperFunctions\Helpers\CustomHelper;
use RuntimeException;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VisualMerchandising extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;
    use LogsActivity;

    protected $fillable = [
        'store_id',
        'asset_id',
        'creator_id',
        'issue_text',
        'issue_fix_status',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'visual_merchandisings';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'store_id',
                'asset_id',
                'creator_id',
                'issue_text',
                'issue_fix_status',
                'status',
            ]);
        // Chain fluent methods for configuration options
    }

    protected static function booted(): void
    {
        static::deleting(function (self $visualMerchandising): void {
            $deleteMethod = $visualMerchandising->isForceDeleting() ? 'forceDelete' : 'delete';

            $visualMerchandising->visualMerchandisingFiles()
                ->withTrashed()
                ->get()
                ->each(function (VisualMerchandisingFile $file) use ($deleteMethod): void {
                    $file->{$deleteMethod}();
                });
        });
    }

    public static function updateOrCreateVisualMerchandising(Request $request, ?self $visualMerchandising = null): self
    {
        $data = $request->validated();
        unset($data['vm_files'], $data['remove_file_ids']);

        $data['status'] = $request->boolean('status') ? 1 : 0;
        $data['creator_id'] = $visualMerchandising?->creator_id ?? CustomHelper::loggedUser()?->id ?? auth()->id();

        if (! $data['creator_id']) {
            throw new RuntimeException('Visual merchandising records require an authenticated creator.');
        }

        if ($visualMerchandising) {
            $visualMerchandising->update($data);
        } else {
            $visualMerchandising = static::create($data);
        }

        $removeFileIds = collect((array) $request->input('remove_file_ids', []))
            ->filter(fn ($value) => is_numeric($value) && (int) $value > 0)
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        if ($removeFileIds->isNotEmpty()) {
            $visualMerchandising->visualMerchandisingFiles()
                ->whereIn('id', $removeFileIds)
                ->get()
                ->each
                ->delete();
        }

        foreach ((array) $request->file('vm_files', []) as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $visualMerchandising->visualMerchandisingFiles()->create(
                static::buildFilePayload($file, (int) $visualMerchandising->getKey())
            );
        }

        return $visualMerchandising->fresh([
            'store:id,title,code',
            'asset:id,name,asset_code,store_id,is_common_asset,asset_type_id',
            'asset.assetType:id,name',
            'creator:id,name,email',
            'visualMerchandisingFiles',
        ]);
    }

    protected static function buildFilePayload(UploadedFile $file, int $visualMerchandisingId): array
    {
        $storedFilePath = CustomHelper::fileUpload(
            $file,
            'visual-merchandising',
            'vm-' . $visualMerchandisingId,
            null,
            null,
            null
        );

        return [
            'visual_merchandising_id' => $visualMerchandisingId,
            'file_path' => $storedFilePath,
            'file_type' => (string) ($file->getClientMimeType() ?? $file->getMimeType() ?? CustomHelper::getFileType($file)),
        ];
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function visualMerchandisingFiles()
    {
        return $this->hasMany(VisualMerchandisingFile::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

}
