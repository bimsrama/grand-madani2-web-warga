<?php

namespace App\Http\Controllers;

use App\Models\IplTransaction;
use App\Models\Resident;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResidentIplController extends Controller
{
    /**
     * Protected: resident dashboard kartu IPL (requires resident.auth middleware)
     */
    public function index(): \Illuminate\View\View
    {
        $resident = Resident::findOrFail(session('resident_id'));
        $year = now()->year;

        $transactions = IplTransaction::where('resident_id', $resident->id)
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        return view('resident.kartu-ipl', compact('resident', 'transactions', 'year'));
    }

    /**
     * Public Magic Link: viewable without login via signed URL
     * Route: GET /kartu-ipl/magic?resident=X&month=Y&year=Z&signature=...
     */
    public function magicView(Request $request)
    {
        if (! $request->hasValidSignature()) {
            abort(403, 'Link IPL tidak valid atau sudah kadaluarsa.');
        }

        $resident = Resident::withoutGlobalScopes()->findOrFail($request->resident);
        $month    = (int) $request->month;
        $year     = (int) $request->year;

        $transaction = IplTransaction::withoutGlobalScopes()
            ->where('resident_id', $resident->id)
            ->where('month', $month)
            ->where('year', $year)
            ->firstOrFail();

        // All year transactions for the summary grid
        $allTransactions = IplTransaction::withoutGlobalScopes()
            ->where('resident_id', $resident->id)
            ->where('year', $year)
            ->get()
            ->keyBy('month');

        $monthName = Carbon::create()->month($month)->locale('id')->monthName;

        return view('resident.kartu-ipl-magic', compact(
            'resident', 'transaction', 'allTransactions', 'year', 'month', 'monthName'
        ));
    }
}
