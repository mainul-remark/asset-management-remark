<?php

namespace App\Http\Requests\Backend\Asset;

use App\Models\Asset;
use App\Models\VisualMerchandising;
use App\Models\VisualMerchandisingFile;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class VisualMerchandisingRequest extends FormRequest
{
    public const ISSUE_FIX_STATUSES = [
        'pending',
        'reviewed',
        'assigned',
        'processing',
        'solved',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'store_id' => [
                'required',
                'integer',
                Rule::exists('stores', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'asset_id' => [
                'required',
                'integer',
                Rule::exists('assets', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
            'issue_text' => ['required', 'string'],
            'issue_fix_status' => ['required', Rule::in(self::ISSUE_FIX_STATUSES)],
            'status' => ['nullable', 'boolean'],
            'vm_files' => ['nullable', 'array'],
            'vm_files.*' => [
                'file',
                'mimes:jpeg,jpg,png,gif,svg,webp,mp4,mov,avi,mkv,webm',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (! $value instanceof UploadedFile) {
                        return;
                    }

                    $mimeType = (string) ($value->getClientMimeType() ?? $value->getMimeType() ?? '');
                    $extension = strtolower((string) $value->getClientOriginalExtension());
                    $isImage = str_starts_with($mimeType, 'image/')
                        || in_array($extension, ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'], true);
                    $isVideo = str_starts_with($mimeType, 'video/')
                        || in_array($extension, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true);
                    $maxKilobytes = $isImage
                        ? 5 * 1024
                        : ($isVideo ? 10 * 1024 : null);

                    if ($maxKilobytes !== null && (int) ceil(((int) $value->getSize()) / 1024) > $maxKilobytes) {
                        $fail(
                            $isImage
                                ? 'Image files must not exceed 5 MB.'
                                : 'Video files must not exceed 10 MB.'
                        );
                    }
                },
            ],
            'remove_file_ids' => ['nullable', 'array'],
            'remove_file_ids.*' => [
                'integer',
                Rule::exists('visual_merchandising_files', 'id')->where(fn ($query) => $query->whereNull('deleted_at')),
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $storeId = (int) $this->input('store_id');
                $assetId = (int) $this->input('asset_id');

                if ($storeId > 0 && $assetId > 0) {
                    $asset = Asset::query()
                        ->with([
                            'assignAssetToStores' => fn ($query) => $query
                                ->whereNull('deleted_at')
                                ->where('store_id', $storeId),
                        ])
                        ->find($assetId);

                    if ($asset) {
                        $isValidForStore = (int) $asset->store_id === $storeId
                            || (int) $asset->is_common_asset === 1
                            || $asset->assignAssetToStores->isNotEmpty();

                        if (! $isValidForStore) {
                            $validator->errors()->add(
                                'asset_id',
                                'The selected asset is not available for the chosen store.'
                            );
                        }
                    }
                }

                $currentVisualMerchandising = $this->route('visual_merchandising');
                $currentVisualMerchandisingId = $currentVisualMerchandising instanceof VisualMerchandising
                    ? (int) $currentVisualMerchandising->getKey()
                    : (is_numeric($currentVisualMerchandising) ? (int) $currentVisualMerchandising : 0);

                if ($currentVisualMerchandisingId <= 0) {
                    return;
                }

                $removeFileIds = collect((array) $this->input('remove_file_ids', []))
                    ->filter(fn ($value) => is_numeric($value) && (int) $value > 0)
                    ->map(fn ($value) => (int) $value)
                    ->unique()
                    ->values()
                    ->all();

                if ($removeFileIds === []) {
                    return;
                }

                $invalidRemovalExists = VisualMerchandisingFile::query()
                    ->whereIn('id', $removeFileIds)
                    ->where('visual_merchandising_id', '!=', $currentVisualMerchandisingId)
                    ->exists();

                if ($invalidRemovalExists) {
                    $validator->errors()->add(
                        'remove_file_ids',
                        'One or more selected files could not be removed.'
                    );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'store_id.required' => 'Please select a store.',
            'store_id.exists' => 'The selected store is invalid.',
            'asset_id.required' => 'Please select an asset.',
            'asset_id.exists' => 'The selected asset is invalid.',
            'issue_text.required' => 'Issue details are required.',
            'issue_text.string' => 'Issue details must be valid text.',
            'issue_fix_status.required' => 'Please select an issue fix status.',
            'issue_fix_status.in' => 'The selected issue fix status is invalid.',
            'vm_files.array' => 'The uploaded files payload is invalid.',
            'vm_files.*.file' => 'Each upload must be a valid file.',
            'vm_files.*.mimes' => 'Only image or video files are allowed (jpg, png, webp, gif, svg, mp4, mov, avi, mkv, webm).',
            'remove_file_ids.array' => 'The selected files for removal are invalid.',
            'remove_file_ids.*.exists' => 'One or more selected files for removal are invalid.',
        ];
    }
}
