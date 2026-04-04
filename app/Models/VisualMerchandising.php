<?php

namespace App\Models;

use App\Models\Scopes\Searchable;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VisualMerchandising extends Model
{
    use HasFactory;
    use Searchable;
    use SoftDeletes;

    protected $fillable = [
        'store_id',
        'asset_id',
        'issue_text',
        'issue_fix_status',
        'status',
    ];

    protected $searchableFields = ['*'];

    protected $table = 'visual_merchandisings';

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
            'visualMerchandisingFiles',
        ]);
    }

    protected static function buildFilePayload(UploadedFile $file, int $visualMerchandisingId): array
    {
        $directory = 'backend/assets/uploaded-files/visual-merchandising';
        $absoluteDirectory = public_path($directory);

        if (! File::isDirectory($absoluteDirectory)) {
            File::makeDirectory($absoluteDirectory, 0777, true, true);
        }

        $baseName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeBaseName = Str::slug($baseName) ?: 'vm-file';
        $extension = strtolower((string) $file->getClientOriginalExtension());
        $fileName = 'vm-' . $visualMerchandisingId . '-' . $safeBaseName . '-' . random_int(1000, 99999) . '.' . $extension;

        $file->move($absoluteDirectory, $fileName);

        return [
            'visual_merchandising_id' => $visualMerchandisingId,
            'file_path' => $directory . '/' . $fileName,
            'file_type' => (string) ($file->getClientMimeType() ?? $file->getMimeType() ?? $extension),
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

    public function visualMerchandisingFiles()
    {
        return $this->hasMany(VisualMerchandisingFile::class);
    }
}
