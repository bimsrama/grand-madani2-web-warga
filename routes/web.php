<?php

use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicComplaintController;
use App\Http\Controllers\PublicFinancialController;
use App\Http\Controllers\PublicMarketplaceController;
use App\Http\Controllers\ResidentAuthController;
use App\Http\Controllers\ResidentCctvController;
use App\Http\Controllers\ResidentIplController;
use App\Http\Controllers\Admin\AdminCctvController;
use App\Http\Controllers\Admin\AdminFinanceController;
use App\Http\Controllers\Admin\AdminIplController;
use App\Http\Controllers\Admin\AdminSecretaryController;
use App\Http\Controllers\Admin\AdminSettingsController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════════════════════
// PUBLIC ROUTES — No login required (ALL RT 03 only)
// ══════════════════════════════════════════════════════════════════════════════

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');

// Laporan Keuangan RT 03 (public, with loading animation)
Route::get('/laporan-keuangan', [PublicFinancialController::class, 'index'])->name('public.financial');
Route::get('/laporan-keuangan/pdf/{id}', [PublicFinancialController::class, 'downloadPdf'])->name('public.financial.pdf');

// Pasar Warga (Preloved + Direktori Jasa)
Route::get('/pasar-warga', [PublicMarketplaceController::class, 'index'])->name('public.marketplace');

// Aduan Lingkungan & Forum
Route::get('/aduan-forum', [PublicComplaintController::class, 'index'])->name('public.aduan');
Route::post('/aduan-forum', [PublicComplaintController::class, 'store'])->name('public.aduan.store');

// ══════════════════════════════════════════════════════════════════════════════
// RESIDENT AUTH — Login with PIN system (/login-warga)
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/login-warga', [ResidentAuthController::class, 'showLogin'])->name('resident.login');
Route::post('/login-warga', [ResidentAuthController::class, 'login'])->name('resident.login.post');
Route::post('/warga/logout', [ResidentAuthController::class, 'logout'])->name('resident.logout');

// AJAX: get masked WA number for dropdown selection
Route::get('/warga/ajax/wa-mask', [ResidentAuthController::class, 'getWaMask'])->name('resident.ajax.wa-mask');

// PIN Setup (first-time residents)
Route::get('/warga/set-pin', [ResidentAuthController::class, 'showSetPin'])->name('resident.set-pin');
Route::post('/warga/set-pin', [ResidentAuthController::class, 'storePin'])->name('resident.set-pin.post');

// Magic Link — public, signed URL, no login required
Route::get('/kartu-ipl/magic', [ResidentIplController::class, 'magicView'])->name('resident.ipl.magic');

// ══════════════════════════════════════════════════════════════════════════════
// RESIDENT PROTECTED ROUTES — requires resident.auth middleware
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware('resident.auth')
    ->prefix('warga')
    ->name('resident.')
    ->group(function () {

        // Main resident portal / dashboard
        Route::get('/portal', [ResidentAuthController::class, 'showPortal'])->name('portal');

        // Perubahan Data Warga
        Route::get('/ubah-data', [ResidentAuthController::class, 'showUpdateData'])->name('update-data');
        Route::post('/ubah-data', [ResidentAuthController::class, 'submitUpdateData'])->name('update-data.post');

        // Kartu IPL Warga — 12-month payment status grid
        Route::get('/kartu-ipl', [ResidentIplController::class, 'index'])->name('kartu-ipl');

        // Pantauan Keamanan — resident sees ONLY cameras granted by admin
        Route::get('/cctv', [ResidentCctvController::class, 'index'])->name('cctv');
    });

// ══════════════════════════════════════════════════════════════════════════════
// ADMIN AUTH — standard Laravel Email/Password via users table
// ══════════════════════════════════════════════════════════════════════════════

Route::get('/admin/login', [AdminLoginController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

// ══════════════════════════════════════════════════════════════════════════════
// ADMIN PROTECTED ROUTES — requires auth (Laravel default guard)
// ══════════════════════════════════════════════════════════════════════════════

Route::middleware('auth')
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', fn () => redirect()->route('admin.ipl.index'))->name('dashboard');

        // ── Financial Reports ─────────────────────────────────────────────
        Route::get('/keuangan', [AdminFinanceController::class, 'index'])->name('finance.index');
        Route::get('/keuangan/tambah', [AdminFinanceController::class, 'create'])->name('finance.create');
        Route::post('/keuangan', [AdminFinanceController::class, 'store'])->name('finance.store');
        Route::delete('/keuangan/{report}', [AdminFinanceController::class, 'destroy'])->name('finance.destroy');

        // Daily Cash Transactions
        Route::post('/keuangan/transaksi', [AdminFinanceController::class, 'storeDailyTransaction'])->name('finance.transaction.store');
        Route::delete('/keuangan/transaksi/{transaction}', [AdminFinanceController::class, 'destroyDailyTransaction'])->name('finance.transaction.destroy');

        // ── IPL Payments ──────────────────────────────────────────────────
        Route::get('/ipl', [AdminIplController::class, 'index'])->name('ipl.index');
        Route::post('/ipl/{resident}/{month}/{year}', [AdminIplController::class, 'update'])->name('ipl.update');

        // ── Secretary / Surat RT ──────────────────────────────────────────
        Route::get('/surat', [AdminSecretaryController::class, 'index'])->name('secretary.index');
        Route::post('/surat/generate', [AdminSecretaryController::class, 'generate'])->name('secretary.generate');

        // ── CCTV Access Control ───────────────────────────────────────────
        Route::get('/cctv', [AdminCctvController::class, 'index'])->name('cctv.index');
        Route::post('/cctv', [AdminCctvController::class, 'store'])->name('cctv.store');
        Route::get('/cctv/{camera}/manage', [AdminCctvController::class, 'manage'])->name('cctv.manage');
        Route::post('/cctv/{camera}/access', [AdminCctvController::class, 'updateAccess'])->name('cctv.updateAccess');
        Route::post('/cctv/{camera}/toggle', [AdminCctvController::class, 'toggleActive'])->name('cctv.toggle');
        Route::delete('/cctv/{camera}', [AdminCctvController::class, 'destroy'])->name('cctv.destroy');

        // ── Community Widgets ─────────────────────────────────────────────
        Route::get('/widgets', [AdminSettingsController::class, 'widgetIndex'])->name('widgets.index');
        Route::post('/widgets', [AdminSettingsController::class, 'widgetStore'])->name('widgets.store');
        Route::put('/widgets/{widget}', [AdminSettingsController::class, 'widgetUpdate'])->name('widgets.update');
        Route::delete('/widgets/{widget}', [AdminSettingsController::class, 'widgetDestroy'])->name('widgets.destroy');

        // ── Struktur Pengurus ─────────────────────────────────────────────
        Route::get('/pengurus', [AdminSettingsController::class, 'boardIndex'])->name('board.index');
        Route::post('/pengurus', [AdminSettingsController::class, 'boardStore'])->name('board.store');
        Route::put('/pengurus/{member}', [AdminSettingsController::class, 'boardUpdate'])->name('board.update');
        Route::delete('/pengurus/{member}', [AdminSettingsController::class, 'boardDestroy'])->name('board.destroy');

        // ── WhatsApp Bot Settings ─────────────────────────────────────────
        Route::get('/wa-settings', [AdminSettingsController::class, 'waSettings'])->name('wa-settings');
        Route::post('/wa-settings', [AdminSettingsController::class, 'saveWaSettings'])->name('wa-settings.save');

        // ── Data Warga Management ─────────────────────────────────────────
        Route::get('/warga', [AdminSettingsController::class, 'residentIndex'])->name('residents.index');
        Route::get('/data-requests', [AdminSettingsController::class, 'dataRequests'])->name('data-requests.index');
        Route::post('/data-requests/{update}/approve', [AdminSettingsController::class, 'approveRequest'])->name('data-requests.approve');
        Route::post('/data-requests/{update}/reject', [AdminSettingsController::class, 'rejectRequest'])->name('data-requests.reject');
    });
