<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialReport;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminFinanceController extends Controller
{
    // ── Monthly Summary ───────────────────────────────────────────────────

    public function index(Request $request)
    {
        $year    = (int) $request->get('year', now()->year);
        $reports = FinancialReport::where('year', $year)->orderBy('month')->get();
        $years   = FinancialReport::selectRaw('DISTINCT year')->orderByDesc('year')->pluck('year');

        // Daily transactions for this year
        $dailyTx = FinancialTransaction::forRt3()
            ->whereYear('transaction_date', $year)
            ->orderByDesc('transaction_date')
            ->get();

        return view('admin.finance.index', compact('reports', 'year', 'years', 'dailyTx'));
    }

    public function create()
    {
        return view('admin.finance.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'month'         => ['required', 'integer', 'between:1,12'],
            'year'          => ['required', 'integer', 'min:2020'],
            'income'        => ['required', 'numeric', 'min:0'],
            'expense'       => ['required', 'numeric', 'min:0'],
            'description'   => ['nullable', 'string'],
            'receipt_image' => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('receipt_image')) {
            $data['receipt_image'] = $request->file('receipt_image')
                ->store('receipts', 'public');
        }

        FinancialReport::updateOrCreate(
            ['month' => $data['month'], 'year' => $data['year']],
            $data
        );

        return redirect()->route('admin.finance.index')
            ->with('success', 'Laporan keuangan berhasil disimpan!');
    }

    public function destroy(FinancialReport $report)
    {
        $report->delete();
        return back()->with('success', 'Laporan berhasil dihapus.');
    }

    // ── Daily Transactions (Pengeluaran / Pemasukan Harian) ───────────────

    public function storeDailyTransaction(Request $request)
    {
        $data = $request->validate([
            'transaction_date' => ['required', 'date'],
            'description'      => ['required', 'string', 'max:255'],
            'type'             => ['required', 'in:pemasukan,pengeluaran'],
            'amount'           => ['required', 'numeric', 'min:0'],
            'category'         => ['nullable', 'string', 'max:50'],
            'receipt_path'     => ['nullable', 'image', 'max:5120'],
        ]);

        if ($request->hasFile('receipt_path')) {
            $data['receipt_path'] = $request->file('receipt_path')
                ->store('receipts/daily', 'public');
        }

        $data['rt_number'] = '3';
        FinancialTransaction::create($data);

        return back()->with('success', 'Transaksi berhasil dicatat!');
    }

    public function destroyDailyTransaction(FinancialTransaction $transaction)
    {
        if ($transaction->receipt_path) {
            Storage::disk('public')->delete($transaction->receipt_path);
        }
        $transaction->delete();
        return back()->with('success', 'Transaksi dihapus.');
    }
}
