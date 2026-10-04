<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaPartner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MediaPartnerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $partner = $this->route('partner');

        return $this->user()?->can($partner ? 'update' : 'create', $partner ?: MediaPartner::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url:http,https', 'max:2048'],
            'position' => ['required', 'integer', 'min:0', 'max:10000'],
            'is_active' => ['required', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }
}