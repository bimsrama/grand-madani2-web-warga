<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardMember extends Model
{
    protected $fillable = [
        'rt_number', 'name', 'role', 'photo_path', 'sort_order',
    ];

    public function scopeOrdered($query)
    {
        return $query->where('rt_number', '3')->orderBy('sort_order');
    }
}
