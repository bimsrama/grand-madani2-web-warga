<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToRT;

class Announcement extends Model
{
    use BelongsToRT;
    protected $fillable = ['title', 'body', 'is_published', 'published_at'];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];
}
