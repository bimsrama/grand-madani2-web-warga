<?php

namespace App\Http\Controllers;

use App\Models\FinancialReport;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;

class PublicFinancialController extends Controller
{
    /**
     * Laporan Keuangan publik — hardcoded to RT 03.
     * No ?rt= query param. The BelongsToRT trait & scope handle this.
     */
    public function index(Request $request)
    {
        $year = (int) $request->get('year', now()->year);

        $reports = FinancialReport::where('year', $year)
            ->orderBy('month', 'asc')
            ->get();

        $years = FinancialReport::selectRaw('DISTINCT year')
            ->orderByDesc('year')
            ->pluck('year');

        // Daily transactions for the selected year
        $dailyTx = FinancialTransaction::forRt3()
            ->whereYear('transaction_date', $year)
            ->orderByDesc('transaction_date')
            ->limit(50)
            ->get();

        return view('public.laporan-keuangan', compact('reports', 'year', 'years', 'dailyTx'));
    }

    public function downloadPdf(int $id)
    {
        $report = FinancialReport::withoutGlobalScopes()
            ->where('rt_number', '3')
            ->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.financial-report', compact('report'))
            ->setPaper('A4', 'portrait');

        $filename = "Laporan-Keuangan-RT03-{$report->month_name}-{$report->year}.pdf";
        return $pdf->download($filename);
    }
}
