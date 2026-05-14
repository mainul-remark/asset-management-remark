<?php

namespace App\Http\Requests\Backend\Asset;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AssetImportRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                // Accept both xlsx and xls
                'mimes:xlsx,xls,csv',
                // 10 MB max — tune to your environment
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please upload an Excel file.',
            'file.mimes'    => 'The file must be a valid Excel file (.xlsx, .xls) or CSV.',
            'file.max'      => 'The file size must not exceed 10 MB.',
        ];
    }
}
