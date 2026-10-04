<?php

namespace App\Http\Requests\Admin;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

class SiteContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasPermission('site_content.manage') ?? false;
    }

    public function rules(): array
    {
        return [
            'impact' => ['required', 'array', 'size:4'],
            'impact.*.value' => ['required', 'integer', 'min:0', 'max:10000000'],
            'contact.whatsapp' => ['nullable', 'string', 'max:40'],
            'contact.email' => ['nullable', 'email', 'max:255'],
            'contact.location' => ['required', 'string', 'max:500'],
            'social.youtube' => ['nullable', 'url:http,https', 'max:2048'],
            'social.instagram' => ['nullable', 'url:http,https', 'max:2048'],
            'social.tiktok' => ['nullable', 'url:http,https', 'max:2048'],
            'social.facebook' => ['nullable', 'url:http,https', 'max:2048'],
            'social_visibility.youtube' => ['nullable', 'boolean'],
            'social_visibility.instagram' => ['nullable', 'boolean'],
            'social_visibility.tiktok' => ['nullable', 'boolean'],
            'social_visibility.facebook' => ['nullable', 'boolean'],
            'profile.name' => ['required', 'string', 'max:255'],
            'profile.home_intro' => ['required', 'string', 'max:1000'],
            'profile.about' => ['required', 'string', 'max:3000'],
            'profile.mission' => ['required', 'string', 'max:2000'],
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }
}
