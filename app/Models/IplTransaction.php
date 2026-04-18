<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Traits\BelongsToRT;

class IplTransaction extends Model
{
    use BelongsToRT;

    protected $fillable = [
        'resident_id', 'month', 'year', 'amount',
        'biaya_sampah', 'biaya_keamanan', 'kas_rt', 'dana_sosial', 'kas_rw',
        'status', 'paid_at', 'invoice_path', 'magic_link_token',
    ];

    protected $casts = [
        'paid_at'         => 'datetime',
        'amount'          => 'decimal:2',
        'biaya_sampah'    => 'decimal:2',
        'biaya_keamanan'  => 'decimal:2',
        'kas_rt'          => 'decimal:2',
        'dana_sosial'     => 'decimal:2',
        'kas_rw'          => 'decimal:2',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    public function getMonthNameAttribute(): string
    {
        return Carbon::create()->month($this->month)->locale('id')->monthName;
    }

    public function getIsLunasAttribute(): bool
    {
        return $this->status === 'lunas';
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /** Sum of all breakdown items */
    public function getTotalBreakdownAttribute(): float
    {
        return (float) $this->biaya_sampah
             + (float) $this->biaya_keamanan
             + (float) $this->kas_rt
             + (float) $this->dana_sosial
             + (float) $this->kas_rw;
    }

    /** Format a single breakdown item */
    public function formatRp(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
