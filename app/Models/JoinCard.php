<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JoinCard extends Model
{
    protected $fillable = ['key', 'title', 'description', 'form_url', 'is_open', 'closed_description', 'button_label', 'position'];

    protected function casts(): array
    {
        return ['is_open' => 'boolean', 'position' => 'integer'];
    }

    public function toPublicArray(): array
    {
        return [
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'url' => $this->form_url,
            'open' => $this->is_open,
            'closed_description' => $this->closed_description,
            'button' => $this->button_label,
        ];
    }
}