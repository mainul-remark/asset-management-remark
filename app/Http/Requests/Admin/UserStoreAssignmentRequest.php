<?php

namespace App\Http\Requests\Admin;

use App\Models\UserStoreAssignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', Rule::exists('users', 'id')],
            'store_ids' => ['required', 'array', 'min:1'],
            'store_ids.*' => [
                'required',
                'distinct',
                Rule::exists('stores', 'id')->where(function ($query) {
                    $query->whereNull('deleted_at')->where('status', 1);
                }),
            ],
            'role_id' => ['nullable', Rule::exists('roles', 'role_id')],
            'status' => ['required', 'in:0,1'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'Please select a user.',
            'user_id.exists' => 'The selected user is invalid.',
            'store_ids.required' => 'Please select at least one store.',
            'store_ids.array' => 'Stores must be submitted as a list.',
            'store_ids.min' => 'Please select at least one store.',
            'store_ids.*.required' => 'Please select a valid store.',
            'store_ids.*.distinct' => 'Duplicate stores are not allowed.',
            'store_ids.*.exists' => 'One or more selected stores are invalid or inactive.',
            'role_id.exists' => 'The selected role is invalid.',
            'status.required' => 'Please select an assignment status.',
            'status.in' => 'Assignment status must be Active or Inactive.',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            /** @var UserStoreAssignment|null $assignment */
            $assignment = $this->route('user_store_assignment') ?? $this->route('userStoreAssignment');

            if ($assignment && (int) $this->input('user_id') !== (int) $assignment->user_id) {
                $validator->errors()->add('user_id', 'Changing the assigned user during edit is not allowed.');
                return;
            }

            if ($assignment || !$this->filled('user_id')) {
                return;
            }

            $alreadyAssigned = UserStoreAssignment::query()
                ->where('user_id', $this->input('user_id'))
                ->exists();

            if ($alreadyAssigned) {
                $validator->errors()->add('user_id', 'This user already has store assignments. Please edit the existing assignment instead.');
            }
        });
    }
}
