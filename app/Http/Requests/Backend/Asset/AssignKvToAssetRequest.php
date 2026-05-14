<?php

namespace App\Http\Requests\Backend\Asset;

use App\Models\Asset;
use App\Models\KeyVisual;
use App\Models\KeyVisualFiles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignKvToAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asset_id' => [
                'required',
                Rule::exists('assets', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('status', 1)
                        ->where('has_kv_slot', 1);
                }),
            ],
            'key_visual_id' => [
                'required',
                Rule::exists('key_visuals', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('status', 1);
                }),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $asset = Asset::query()->find($this->input('asset_id'));
                    $keyVisual = KeyVisual::query()->find($value);

                    if ($asset && $keyVisual && (int) $asset->asset_type_id !== (int) $keyVisual->asset_type_id) {
                        $fail('The selected key visual must match the selected asset category.');
                    }
                },
            ],
            'key_visual_files_id' => [
                'required',
                Rule::exists('key_visual_files', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')
                        ->where('status', 1);
                }),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $keyVisualId = $this->input('key_visual_id');

                    if (!$keyVisualId) {
                        return;
                    }

                    $belongsToSelectedKeyVisual = KeyVisualFiles::query()
                        ->where('id', $value)
                        ->where('key_visual_id', $keyVisualId)
                        ->whereNull('deleted_at')
                        ->exists();

                    if (!$belongsToSelectedKeyVisual) {
                        $fail('The selected key visual file does not belong to the chosen key visual.');
                    }
                },
            ],
            'has_perfect_size_kv' => ['nullable', 'in:0,1'],
            'assigned_date' => ['nullable', 'date'],
            'installed_by' => ['nullable', Rule::exists('users', 'id')],
            'instalation_proof' => ['nullable', 'string', 'max:5000'],
            'instalation_status' => ['nullable', Rule::in(['pending', 'planned', 'installed', 'verified'])],
            'instalation_date' => ['nullable', 'date'],
            'slot_number' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_id.required' => 'Please select an asset.',
            'asset_id.exists' => 'The selected asset is invalid or unavailable for KV assignment.',
            'key_visual_id.required' => 'Please select a key visual.',
            'key_visual_id.exists' => 'The selected key visual is invalid.',
            'key_visual_files_id.required' => 'Please select a key visual file.',
            'key_visual_files_id.exists' => 'The selected key visual file is invalid.',
            'has_perfect_size_kv.in' => 'Perfect-size selection must be yes or no.',
            'assigned_date.date' => 'Assigned date must be a valid date.',
            'installed_by.exists' => 'The selected installer is invalid.',
            'instalation_proof.string' => 'Installation proof must be valid text.',
            'instalation_proof.max' => 'Installation proof cannot exceed 5000 characters.',
            'instalation_status.in' => 'The installation status is invalid.',
            'instalation_date.date' => 'Installation date must be a valid date.',
        ];
    }
}
