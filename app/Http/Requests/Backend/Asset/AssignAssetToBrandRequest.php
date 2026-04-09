<?php

namespace App\Http\Requests\Backend\Asset;

use App\Models\AssignAssetToBrand;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignAssetToBrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignoreId = $this->route('assignAssetToBrand')?->id;
        $isUpdate = $ignoreId !== null || $this->isMethod('PUT') || $this->isMethod('PATCH');

        $rules = [
            'brand_id' => [
                Rule::requiredIf($isUpdate),
                Rule::exists('brands', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')->where('status', 1);
                }),
            ],
            'brand_ids' => [
                Rule::requiredIf(!$isUpdate),
                'array',
                'min:1',
            ],
            'brand_ids.*' => [
                'required',
                'distinct',
                Rule::exists('brands', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')->where('status', 1);
                }),
            ],
            'asset_id' => [
                'required',
                Rule::exists('assets', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')->where('status', 1);
                }),
            ],
            'asset_charge' => ['nullable', 'numeric', 'min:0'],
            'close_date' => ['nullable', 'date', Rule::requiredIf(fn () => (int) $this->input('status', 1) === 0)],
            'status' => ['required', 'in:0,1'],
        ];

        if ($isUpdate) {
            unset($rules['brand_ids'], $rules['brand_ids.*']);
        } else {
            unset($rules['brand_id']);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'brand_id.required' => 'Please select a brand.',
            'brand_id.exists' => 'The selected brand is invalid or inactive.',
            'brand_ids.required' => 'Please select at least one brand.',
            'brand_ids.array' => 'Brands must be submitted as a list.',
            'brand_ids.min' => 'Please select at least one brand.',
            'brand_ids.*.required' => 'Please select a valid brand.',
            'brand_ids.*.distinct' => 'Duplicate brands are not allowed.',
            'brand_ids.*.exists' => 'One or more selected brands are invalid or inactive.',
            'asset_id.required' => 'Please select an asset.',
            'asset_id.exists' => 'The selected asset is invalid or inactive.',
            'asset_charge.numeric' => 'Asset charge must be a valid number.',
            'asset_charge.min' => 'Asset charge cannot be negative.',
            'close_date.required' => 'Close date is required when the assignment is inactive.',
            'close_date.date' => 'Close date must be a valid date.',
            'status.required' => 'Please select an assignment status.',
            'status.in' => 'Assignment status must be Active or Inactive.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (!$this->filled('asset_id')) {
                return;
            }

            $ignoreId = $this->route('assignAssetToBrand')?->id;
            $assetId = (int) $this->input('asset_id');
            $isUpdate = $ignoreId !== null || $this->isMethod('PUT') || $this->isMethod('PATCH');

            if ($isUpdate) {
                if (!$this->filled('brand_id')) {
                    return;
                }

                $duplicateExists = AssignAssetToBrand::query()
                    ->where('asset_id', $assetId)
                    ->where('brand_id', $this->input('brand_id'))
                    ->whereNull('deleted_at')
                    ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
                    ->exists();

                if ($duplicateExists) {
                    $validator->errors()->add('brand_id', 'This asset is already assigned to the selected brand.');
                }

                return;
            }

            $brandIds = collect($this->input('brand_ids', []))
                ->filter(fn ($brandId) => $brandId !== null && $brandId !== '')
                ->map(fn ($brandId) => (int) $brandId)
                ->unique()
                ->values();

            if ($brandIds->isEmpty()) {
                return;
            }

            $duplicateBrandIds = AssignAssetToBrand::query()
                ->where('asset_id', $assetId)
                ->whereIn('brand_id', $brandIds)
                ->whereNull('deleted_at')
                ->pluck('brand_id');

            if ($duplicateBrandIds->isNotEmpty()) {
                $validator->errors()->add('brand_ids', 'One or more selected brands are already assigned to this asset.');
            }
        });
    }
}
