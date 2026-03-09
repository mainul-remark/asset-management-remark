<?php

namespace App\Http\Requests\Backend\KV;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyVisualFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $fileRule = $this->isMethod('post')
            ? ['required', 'file']
            : ['nullable', 'file'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'key_visual_id' => ['required', 'integer', 'exists:key_visuals,id'],
            'key_visual_size_id' => ['required', 'integer', 'exists:key_visual_sizes,id'],
            'kv_file_upload' => array_merge(
                $fileRule,
                ['mimes:jpeg,jpg,png,gif,svg,webp,mp4,mov,avi,mkv,webm', 'max:51200']
            ),
            'kv_size' => ['required', 'integer', 'min:0'],
            'aspect_ratio' => ['nullable', 'numeric', 'min:0'],
            'file_type' => ['nullable', 'string', 'max:255'],
            'file_duration' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The file name is required.',
            'name.string' => 'The file name must be a valid string.',
            'name.max' => 'The file name may not be greater than 255 characters.',
            'key_visual_id.required' => 'Please select a key visual.',
            'key_visual_id.integer' => 'The selected key visual is invalid.',
            'key_visual_id.exists' => 'The selected key visual does not exist.',
            'key_visual_size_id.required' => 'Please select a key visual size.',
            'key_visual_size_id.integer' => 'The selected key visual size is invalid.',
            'key_visual_size_id.exists' => 'The selected key visual size does not exist.',
            'kv_file_upload.required' => 'Upload file is required.',
            'kv_file_upload.file' => 'Please upload a valid file.',
            'kv_file_upload.mimes' => 'Upload file must be an image or video (jpeg, jpg, png, gif, svg, webp, mp4, mov, avi, mkv, webm).',
            'kv_file_upload.max' => 'Upload file size must not exceed 50MB.',
            'kv_size.required' => 'KV size is required.',
            'kv_size.integer' => 'KV size must be an integer value.',
            'kv_size.min' => 'KV size cannot be negative.',
            'aspect_ratio.numeric' => 'Aspect ratio must be a valid number.',
            'aspect_ratio.min' => 'Aspect ratio cannot be negative.',
            'file_type.string' => 'File type must be a valid string.',
            'file_type.max' => 'File type may not be greater than 255 characters.',
            'file_duration.string' => 'File duration must be a valid string.',
            'file_duration.max' => 'File duration may not be greater than 255 characters.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
