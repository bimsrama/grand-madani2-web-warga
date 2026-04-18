<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityWidget extends Model
{
    protected $fillable = [
        'rt_number', 'title', 'description', 'thumbnail_path',
        'external_link', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('rt_number', '3')->orderBy('sort_order');
    }
}
