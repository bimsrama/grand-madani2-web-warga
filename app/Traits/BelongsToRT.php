<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * RT 03 Strict Mode Trait
 *
 * All models using this trait will be automatically scoped to RT 03.
 * – Admins: scoped to their user->rt_number (should always be '3')
 * – Residents: scoped to session('resident_rt') (always '3')
 * – Public: fallback to '3' (no RT 01/02 anywhere)
 */
trait BelongsToRT
{
    protected static function bootBelongsToRT(): void
    {
        static::addGlobalScope('rt_scope', function (Builder $builder) {
            // Admin auth — scope to their assigned RT
            if (Auth::guard('web')->check()) {
                $rtNumber = Auth::guard('web')->user()->rt_number ?? '3';
                $builder->where('rt_number', $rtNumber);
            }
            // Resident session auth
            elseif (session()->has('resident_id')) {
                $builder->where('rt_number', session('resident_rt', '3'));
            }
            // Public routes — always RT 03
            else {
                $builder->where('rt_number', '3');
            }
        });

        // Auto-assign rt_number = '3' on creation
        static::creating(function ($model) {
            if (empty($model->rt_number)) {
                $model->rt_number = '3';
            }
        });
    }
}
