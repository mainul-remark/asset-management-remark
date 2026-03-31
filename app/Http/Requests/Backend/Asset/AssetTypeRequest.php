<?php

namespace App\Http\Requests\Backend\Asset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignoreId = $this->route('asset_type')?->id;

        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('asset_types', 'name')
                    ->ignore($ignoreId)
                    ->whereNull('deleted_at'),
            ],
            'default_image'       => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'height'              => ['required_if:has_default_dimension,1', 'nullable', 'numeric', 'min:0'],
            'width'               => ['required_if:has_default_dimension,1', 'nullable', 'numeric', 'min:0'],
            'depth'               => ['nullable', 'numeric', 'min:0'],
            'dimension_unit_name' => ['required_if:has_default_dimension,1', 'nullable', 'in:px,in,ft,cm,mm,m,yd'],
            'default_price'       => ['nullable', 'numeric', 'min:0'],
            'status'                => ['nullable', 'in:0,1'],
            'is_digital'            => ['nullable', 'in:0,1'],
            'total_self'            => ['nullable', 'integer', 'min:0'],
            'has_kv_space'          => ['nullable', 'in:0,1'],
            'has_default_dimension' => ['nullable', 'in:0,1'],
            'need_asset_image'      => ['nullable', 'in:0,1'],
            'need_asset_planogram'  => ['nullable', 'in:0,1'],
            'has_asset_self'        => ['nullable', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Asset type name is required.',
            'name.string'   => 'Asset type name must be valid text.',
            'name.max'      => 'Asset type name cannot exceed 255 characters.',
            'name.unique'   => 'An asset type with this name already exists.',

            'default_image.image' => 'The uploaded file must be a valid image.',
            'default_image.mimes' => 'Accepted image formats: jpg, jpeg, png, webp.',
            'default_image.max'   => 'Image file size must not exceed 2 MB.',

            'height.required_if' => 'Height is required when Default Dimension is enabled.',
            'height.numeric'     => 'Height must be a numeric value.',
            'height.min'         => 'Height cannot be negative.',
            'width.required_if'  => 'Width is required when Default Dimension is enabled.',
            'width.numeric'      => 'Width must be a numeric value.',
            'width.min'          => 'Width cannot be negative.',
            'depth.numeric'  => 'Depth must be a numeric value.',
            'depth.min'      => 'Depth cannot be negative.',

            'dimension_unit_name.required_if' => 'Unit is required when Default Dimension is enabled.',
            'dimension_unit_name.in'          => 'Dimension unit must be one of: px, in, ft, cm, mm, m, yd.',

            'default_price.numeric' => 'Default price must be a numeric value.',
            'default_price.min'     => 'Default price cannot be negative.',

            'status.in'          => 'Status must be Active or Inactive.',
            'is_digital.in'      => 'Digital flag must be Yes or No.',
            'total_self.integer' => 'Total shelf must be a whole number.',
            'total_self.min'     => 'Total shelf cannot be negative.',
            'has_kv_space.in'    => 'KV space flag must be Yes or No.',
        ];
    }
}
