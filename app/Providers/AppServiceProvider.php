<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * – Shares $dbConnected to ALL views for the footer DB status indicator.
     * – Locks SQLite varchar column length to avoid key too long errors.
     */
    public function boot(): void
    {
        // SQLite / older MySQL fix
        Schema::defaultStringLength(191);

        // ── DB Status — shared to all Blade views ───────────────────────
        $dbConnected = false;
        try {
            DB::connection()->getPdo();
            $dbConnected = true;
        } catch (\Throwable) {
            $dbConnected = false;
        }

        View::share('dbConnected', $dbConnected);

        // ── RT 03 Strict Mode — lock all URL defaults ───────────────────
        // (Signed routes will always embed rt_number=3 by default)
        \Illuminate\Support\Facades\URL::defaults(['rt_number' => '3']);
    }
}
