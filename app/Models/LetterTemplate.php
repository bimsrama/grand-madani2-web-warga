<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToRT;

class LetterTemplate extends Model
{
    use BelongsToRT;
    protected $fillable = ['category', 'name', 'default_content'];
}
