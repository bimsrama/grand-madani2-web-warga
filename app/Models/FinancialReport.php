<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

use App\Traits\BelongsToRT;

class FinancialReport extends Model
{
    use BelongsToRT;
    protected $fillable = [
        'month', 'year', 'income', 'expense', 'description', 'receipt_image',
    ];

    protected $casts = [
        'income'  => 'decimal:2',
        'expense' => 'decimal:2',
    ];

    public function getBalanceAttribute(): float
    {
        return (float) $this->income - (float) $this->expense;
    }

    public function getMonthNameAttribute(): string
    {
        return Carbon::create()->month($this->month)->locale('id')->monthName;
    }

    public function getFormattedIncomeAttribute(): string
    {
        return 'Rp ' . number_format($this->income, 0, ',', '.');
    }

    public function getFormattedExpenseAttribute(): string
    {
        return 'Rp ' . number_format($this->expense, 0, ',', '.');
    }

    public function getFormattedBalanceAttribute(): string
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }
}
