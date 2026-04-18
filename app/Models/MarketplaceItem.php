<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToRT;

class MarketplaceItem extends Model
{
    use BelongsToRT;
    protected $fillable = [
        'category', 'title', 'description', 'price',
        'contact_name', 'contact_wa', 'image', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'decimal:2',
    ];

    public function getFormattedPriceAttribute(): string
    {
        return $this->price
            ? 'Rp ' . number_format($this->price, 0, ',', '.')
            : 'Harga Nego';
    }

    public function getIsPrelovedAttribute(): bool
    {
        return $this->category === 'preloved';
    }
}
