<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Traits\BelongsToRT;

class Camera extends Model
{
    use BelongsToRT;
    protected $fillable = ['name', 'location', 'embed_url', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function residents(): BelongsToMany
    {
        return $this->belongsToMany(Resident::class, 'camera_resident');
    }
}
