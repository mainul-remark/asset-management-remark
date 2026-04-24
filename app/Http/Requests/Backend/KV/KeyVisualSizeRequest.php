<?php

namespace App\Http\Requests\Backend\KV;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyVisualSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $routeParam = $this->route('key_visual_size');
        $ignoreId = is_object($routeParam) ? $routeParam->id : $routeParam;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('key_visual_sizes', 'name')->ignore($ignoreId)],
            'height' => ['required', 'numeric', 'gt:0'],
            'width' => ['required', 'numeric', 'gt:0'],
            'unit_name' => ['required', Rule::in(['px', 'in', 'ft', 'cm', 'mm', 'm', 'yd'])],
            'status' => ['required', Rule::in([0, 1, '0', '1'])],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The size name is required.',
            'name.string' => 'The size name must be a valid string.',
            'name.max' => 'The size name may not be greater than 255 characters.',
            'name.unique' => 'This size name already exists.',
            'height.required' => 'Height is required.',
            'height.numeric' => 'Height must be a valid number.',
            'height.gt' => 'Height must be greater than 0.',
            'width.required' => 'Width is required.',
            'width.numeric' => 'Width must be a valid number.',
            'width.gt' => 'Width must be greater than 0.',
            'unit_name.required' => 'Unit is required.',
            'unit_name.in' => 'Please select a valid unit.',
            'status.required' => 'Status is required.',
            'status.in' => 'Status must be active or inactive.',
        ];
    }
}
