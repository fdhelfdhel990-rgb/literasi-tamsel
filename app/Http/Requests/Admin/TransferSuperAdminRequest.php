<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransferSuperAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSuperAdmin() ?? false;
    }

    public function rules(): array
    {
        return [
            'target_user_id' => ['required', 'integer', Rule::exists('users', 'id')->where('is_active', true)],
            'confirmation' => ['required', Rule::in(['TRANSFER SUPER ADMIN'])],
            'current_password' => ['required', 'current_password:web'],
        ];
    }
}