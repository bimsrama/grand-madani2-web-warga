<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToRT;

class Complaint extends Model
{
    use BelongsToRT;
    protected $fillable = [
        'reporter_name', 'reporter_address', 'category',
        'description', 'status', 'admin_response',
    ];

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'waiting'    => 'Menunggu',
            'processing' => 'Diproses',
            'done'       => 'Selesai',
            default      => 'Tidak Diketahui',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'waiting'    => 'yellow',
            'processing' => 'blue',
            'done'       => 'green',
            default      => 'gray',
        };
    }
}

