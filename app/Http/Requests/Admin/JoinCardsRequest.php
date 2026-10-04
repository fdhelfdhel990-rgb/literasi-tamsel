<?php

namespace App\Http\Requests\Admin;

use App\Models\JoinCard;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JoinCardsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('join_cards.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'cards' => ['required', 'array', 'size:3'],
            'cards.*.key' => ['required', Rule::in(['volunteer', 'volunteer_event', 'collaboration'])],
            'cards.*.title' => ['required', 'string', 'max:255'],
            'cards.*.description' => ['required', 'string', 'max:2000'],
            'cards.*.form_url' => ['nullable', 'url:http,https', 'max:2048'],
            'cards.*.is_open' => ['nullable', 'boolean'],
            'cards.*.closed_description' => ['required', 'string', 'max:1000'],
            'cards.*.button_label' => ['required', 'string', 'max:100'],
        ];
    }
}