<?php

namespace App\Http\Requests\Backend\Asset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        $store = $this->route('store');
        $ignoreId = $store instanceof \App\Models\Store ? $store->id : $store;
        return [
            'title'               => ['required', 'string', 'max:255', Rule::unique('stores', 'title')->ignore($ignoreId)],
            'code'                => ['required', 'string', 'min:2', Rule::unique('stores', 'code')->ignore($ignoreId)],
            'store_code'          => 'nullable|string|max:50',
            'total_area_sqft'     => 'nullable|numeric|min:0',
            'address'             => 'nullable|string|max:1000',
            'area'                => 'nullable|string|max:255',
            'division_id'         => 'nullable|exists:divisions,id',
            'district_id'         => 'nullable|exists:districts,id',
            'thana_id'            => 'nullable|exists:thanas,id',
            'postal_code'         => 'nullable|string|max:20',
            'latitude'            => 'nullable|numeric|between:-90,90',
            'longitude'           => 'nullable|numeric|between:-180,180',
            'monthly_rent'        => 'nullable|numeric|min:0',
            'per_sqr_feet_rent'   => 'nullable|numeric|min:0',
            'store_layout_pdf'    => 'nullable|mimes:pdf|max:10240',
            'contact_person'      => 'required|string|max:255',
            'shop_official_mobile'=> ['required', 'string', 'size:11', 'regex:/^01[3-9]\d{8}$/'],
            'shop_official_email' => 'required|email|max:255',
            'status'              => 'required|in:0,1',
            'store_type'          => 'required',
//            'store_manager_id'    => 'nullable|exists:users,id',
            'opened_date'         => 'nullable|date',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'    => 'The store title is required.',
            'title.string'      => 'The store title must be a valid text.',
            'title.max'         => 'The store title must not exceed 255 characters.',
            'title.unique'      => 'This store title is already taken.',
            'code.required'     => 'The store code is required.',
            'code.string'       => 'The store code must be a valid text.',
            'code.min'          => 'The store code must be at least 2 characters.',
            'code.alpha'        => 'The store code must contain only letters.',
            'code.unique'       => 'This store code is already in use.',
            'total_area_sqft.numeric' => 'Store size must be a valid number.',
            'total_area_sqft.min'     => 'Store size cannot be negative.',
            'address.max'       => 'The address must not exceed 1000 characters.',
            'latitude.numeric'  => 'Latitude must be a valid number.',
            'latitude.between'  => 'Latitude must be between -90 and 90.',
            'longitude.numeric' => 'Longitude must be a valid number.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'monthly_rent.numeric'      => 'Monthly rent must be a valid number.',
            'monthly_rent.min'          => 'Monthly rent cannot be negative.',
            'per_sqr_feet_rent.numeric' => 'Rent per sq ft must be a valid number.',
            'per_sqr_feet_rent.min'     => 'Rent per sq ft cannot be negative.',
            'store_layout_pdf.mimes'    => 'The layout file must be a PDF.',
            'store_layout_pdf.max'      => 'The layout PDF must not exceed 10MB.',
            'contact_person.required'  => 'The contact person name is required.',
            'contact_person.max'       => 'The contact person name must not exceed 255 characters.',
            'shop_official_mobile.required' => 'The phone number is required.',
            'shop_official_mobile.size'     => 'The phone number must be exactly 11 digits.',
            'shop_official_mobile.regex'    => 'Please enter a valid Bangladeshi mobile number (e.g. 01XXXXXXXXX).',
            'shop_official_email.required'  => 'The email address is required.',
            'shop_official_email.email'     => 'Please enter a valid email address.',
            'shop_official_email.max'       => 'The email address must not exceed 255 characters.',
            'status.required'   => 'The status is required.',
            'status.in'         => 'The status must be either Active or Inactive.',
            'opened_date.date'  => 'Please enter a valid date.',
            'division_id.exists'  => 'The selected division is invalid.',
            'district_id.exists'  => 'The selected district is invalid.',
            'thana_id.exists'     => 'The selected thana is invalid.',
        ];
    }
}
