<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResidentDataUpdate extends Model
{
    protected $fillable = [
        'resident_id', 'rt_number', 'requested_name', 'requested_wa',
        'requested_family_members', 'notes', 'status', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'requested_family_members' => 'array',
        'reviewed_at'              => 'datetime',
    ];

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
