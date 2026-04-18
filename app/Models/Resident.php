<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Hash;

use App\Traits\BelongsToRT;

class Resident extends Model
{
    use BelongsToRT;

    protected $fillable = [
        'block', 'number', 'owner_name', 'wa_number', 'pin', 'family_members', 'is_active',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'family_members' => 'array',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function iplTransactions(): HasMany
    {
        return $this->hasMany(IplTransaction::class);
    }

    public function cameras(): BelongsToMany
    {
        return $this->belongsToMany(Camera::class, 'camera_resident');
    }

    public function dataUpdateRequests(): HasMany
    {
        return $this->hasMany(ResidentDataUpdate::class);
    }

    // ── Accessors ─────────────────────────────────────────────────────────

    /** "Blok A / No. 12 (Budi Santoso)" */
    public function getDisplayNameAttribute(): string
    {
        return "Blok {$this->block} / No. {$this->number} ({$this->owner_name})";
    }

    /** Short label for dropdown */
    public function getDropdownLabelAttribute(): string
    {
        return "Blok {$this->block} / No. {$this->number}";
    }

    /**
     * Masked WhatsApp number for display: +62 858 1023 xxxx
     * Keeps first 9 digits then masks the rest.
     */
    public function getMaskedWaAttribute(): string
    {
        $wa = $this->wa_number;

        // Normalize — remove spaces, dashes
        $clean = preg_replace('/[\s\-]/', '', $wa);

        // Convert 08xx → +628xx
        if (str_starts_with($clean, '0')) {
            $clean = '+62' . substr($clean, 1);
        }

        // Mask: show first 7 chars after +62, mask the last 4
        $visible = substr($clean, 0, strlen($clean) - 4);
        $masked  = str_repeat('x', 4);

        // Format for display: +62 812 3456 xxxx
        $formatted = $visible . $masked;
        // Insert spaces: +62 XXX XXXX xxxx
        if (strlen($formatted) >= 13) {
            $formatted = substr($formatted, 0, 3) . ' '
                       . substr($formatted, 3, 3) . ' '
                       . substr($formatted, 6, 4) . ' '
                       . substr($formatted, 10);
        }

        return $formatted;
    }

    /** The "first-time password" = last 4 digits of the WA number */
    public function getWaPasswordAttribute(): string
    {
        return substr(preg_replace('/[\s\-+]/', '', $this->wa_number), -4);
    }

    // ── Auth Helpers ───────────────────────────────────────────────────────

    /** Check last-4-digit WA for first-time login */
    public function verifyWaLastDigits(string $input): bool
    {
        return $this->wa_password === trim($input);
    }

    /** Check 6-digit PIN (bcrypt) */
    public function verifyPin(string $input): bool
    {
        return $this->pin !== null && Hash::check($input, $this->pin);
    }

    /** Set and hash the 6-digit PIN */
    public function setNewPin(string $plainPin): void
    {
        $this->update(['pin' => Hash::make($plainPin)]);
    }

    /** Has this resident ever set a PIN? */
    public function hasPinSet(): bool
    {
        return $this->pin !== null;
    }
}
