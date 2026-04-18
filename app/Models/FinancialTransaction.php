<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialTransaction extends Model
{
    protected $fillable = [
        'rt_number', 'transaction_date', 'description', 'type', 'amount', 'category', 'receipt_path',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount'           => 'decimal:2',
    ];

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function scopeForRt3($query)
    {
        return $query->where('rt_number', '3');
    }
}
