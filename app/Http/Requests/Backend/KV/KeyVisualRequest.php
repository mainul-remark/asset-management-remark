<?php

namespace App\Http\Requests\Backend\KV;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyVisualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $ignoreId = is_object($this->route('key_visual'))
            ? $this->route('key_visual')->id
            : $this->route('key_visual');
        $kvType = $this->input('kv_type', 'image');

        $sampleFileRules = ['nullable', 'file'];
        if ($kvType === 'video') {
            $sampleFileRules[] = 'mimes:mp4,mov,avi,mkv,webm';
            $sampleFileRules[] = 'max:30720'; // 30 MB
        } else {
            $sampleFileRules[] = 'image';
            $sampleFileRules[] = 'mimes:jpg,jpeg,png,webp';
            $sampleFileRules[] = 'max:5120'; // 5 MB
            $sampleFileRules[] = 'dimensions:width=1920,height=1080';
        }

        return [
            'brand_ids'           => ['nullable', 'array'],
            'brand_ids.*'         => ['integer', 'exists:brands,id'],
            'category_ids'        => ['nullable', 'array'],
            'category_ids.*'      => ['integer', 'exists:categories,id'],
            'asset_type_id'       => ['required', 'exists:asset_types,id'],
            'name'                => ['required', 'string', 'max:255'],
            'unique_code'         => [
                'required',
                'string',
                'max:255',
                Rule::unique('key_visuals', 'unique_code')
                    ->ignore($ignoreId)
                    ->whereNull('deleted_at'),
            ],
            'minimum_res_height'  => ['nullable', 'integer', 'min:0'],
            'minimum_res_width'   => ['nullable', 'integer', 'min:0'],
            'kv_type'             => ['required', 'in:image,video'],
            'kv_sample_file'      => $sampleFileRules,
            'kv_thumb'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'status'              => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_type_id.required' => 'Please select an asset type.',
            'asset_type_id.exists'   => 'The selected asset type is invalid.',

            'name.required'          => 'Key visual name is required.',
            'name.max'               => 'Key visual name cannot exceed 255 characters.',

            'unique_code.required'   => 'Unique code is required.',
            'unique_code.max'        => 'Unique code cannot exceed 255 characters.',
            'unique_code.unique'     => 'This unique code is already in use.',

            'minimum_res_height.integer' => 'Minimum height must be a whole number.',
            'minimum_res_height.min'     => 'Minimum height cannot be negative.',
            'minimum_res_width.integer'  => 'Minimum width must be a whole number.',
            'minimum_res_width.min'      => 'Minimum width cannot be negative.',

            'kv_type.required'       => 'Please select a KV type.',
            'kv_type.in'             => 'KV type must be either image or video.',

            'kv_sample_file.file'    => 'Sample file must be a valid file.',
            'kv_sample_file.image'   => 'For image KV type, sample file must be an image.',
            'kv_sample_file.mimes'   => 'Sample file type is invalid for the selected KV type.',
            'kv_sample_file.max'     => 'Image max size is 5 MB and video max size is 30 MB.',
            'kv_sample_file.dimensions' => 'Image sample must be exactly 1920 x 1080 pixels.',

            'kv_thumb.image'         => 'Thumbnail must be a valid image.',
            'kv_thumb.mimes'         => 'Thumbnail image must be jpg, jpeg, png, or webp.',
            'kv_thumb.max'           => 'Thumbnail size must not exceed 3 MB.',
        ];
    }
}
