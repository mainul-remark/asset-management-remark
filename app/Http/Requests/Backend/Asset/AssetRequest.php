<?php

namespace App\Http\Requests\Backend\Asset;

use App\Models\AssetType;
use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate  = $this->isMethod('PUT') || $this->isMethod('PATCH');
        $assetType = AssetType::find($this->asset_type_id);

        $needImage    = $assetType?->need_asset_image    == 1;
        $needPlanogram = $assetType?->need_asset_planogram == 1;

        return [
            'asset_type_id'  => ['required', 'exists:asset_types,id'],
            'name'           => ['required', 'string', 'max:255'],
            'default_image'  => [!$isUpdate && $needImage ? 'required' : 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'store_id'       => ['nullable', 'exists:stores,id'],
            'has_kv_slot'    => ['nullable', 'boolean'],
            'minimum_fee'    => ['nullable', 'numeric', 'min:0'],
            'asset_price'    => ['nullable', 'numeric', 'min:0'],
            'is_common_asset'=> ['nullable', 'boolean'],
            'planogram_pdf'  => [!$isUpdate && $needPlanogram ? 'required' : 'nullable', 'mimes:pdf', 'max:10240'],
            'status'         => ['nullable', 'boolean'],
            'has_self'       => ['nullable', 'boolean'],
            'total_self'     => ['nullable', 'integer', 'min:0', 'max:127'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_type_id.required' => 'Please select an asset type.',
            'asset_type_id.exists'   => 'The selected asset type is invalid.',

            'name.required' => 'Asset name is required.',
            'name.string'   => 'Asset name must be valid text.',
            'name.max'      => 'Asset name cannot exceed 255 characters.',

            'default_image.required' => 'A default image is required for this asset category.',
            'default_image.image'    => 'The uploaded file must be a valid image.',
            'default_image.mimes'    => 'Accepted formats: JPG, JPEG, PNG, WEBP.',
            'default_image.max'      => 'Image must not exceed 2 MB.',

            'store_id.exists'        => 'The selected store is invalid.',

            'minimum_fee.numeric'    => 'Minimum charge must be a numeric value.',
            'minimum_fee.min'        => 'Minimum charge cannot be negative.',

            'asset_price.numeric'    => 'Asset price must be a numeric value.',
            'asset_price.min'        => 'Asset price cannot be negative.',

            'planogram_pdf.required' => 'A planogram PDF is required for this asset category.',
            'planogram_pdf.mimes'    => 'Planogram must be a PDF file.',
            'planogram_pdf.max'      => 'Planogram PDF must not exceed 10 MB.',

            'total_self.integer'     => 'Total shelf must be a whole number.',
            'total_self.min'         => 'Total shelf cannot be negative.',
            'total_self.max'         => 'Total shelf cannot exceed 127.',
        ];
    }
}
