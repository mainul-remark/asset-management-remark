<?php

namespace App\Http\Requests\Backend\KV;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BrandRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $brand = $this->route('brand');
        $nameRule = Rule::unique('brands', 'name')->withoutTrashed();
        $codeRule = Rule::unique('brands', 'code')->withoutTrashed();

        if ($brand) {
            $nameRule = $nameRule->ignore($brand);
            $codeRule = $codeRule->ignore($brand);
        }

        return [
            'name'        => ['required', 'string', 'max:255', $nameRule],
            'code'        => ['required', 'string', 'min:2', 'max:3', 'alpha', $codeRule],
            'description' => 'nullable|string|max:1000',
            'status'      => 'required|in:0,1',
            'is_common'   => 'nullable|in:0,1',
            'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            // Name validation messages
            'name.required'    => 'The brand name is required.',
            'name.string'      => 'The brand name must be a valid text.',
            'name.max'         => 'The brand name cannot exceed 255 characters.',
            'name.unique'      => 'This brand name is already taken.',

            // Code validation messages
            'code.required'    => 'The brand code is required.',
            'code.string'      => 'The brand code must be a valid text.',
            'code.min'         => 'The brand code must be at least 2 characters.',
            'code.max'         => 'The brand code cannot exceed 3 characters.',
            'code.alpha'       => 'The brand code must contain only letters.',
            'code.unique'      => 'This brand code is already in use.',

            // Description validation messages
            'description.string' => 'The description must be a valid text.',
            'description.max'    => 'The description cannot exceed 1000 characters.',

            // Status validation messages
            'status.required'  => 'The brand status is required.',
            'status.in'        => 'The status must be either active or inactive.',

            // Logo validation messages
            'logo.image'       => 'The logo must be an image file.',
            'logo.mimes'       => 'The logo must be a file of type: jpeg, png, jpg, gif, svg, or webp.',
            'logo.max'         => 'The logo size cannot exceed 2MB.',
        ];
    }
}
