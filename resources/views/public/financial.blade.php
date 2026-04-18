@extends('layouts.app')
@section('title', 'Laporan Keuangan RT')

@section('content')
{{-- Page Header --}}
    <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 text-brand-700 text-xs font-bold mb-4 uppercase tracking-widest border border-brand-100">
            📊 Laporan Keuangan Wilayah RT 0{{ $rt ?? '1' }}
        </span>
        <h1 class="text-3xl font-black text-gray-900 leading-tight">Transparansi Keuangan Warga</h1>
        <p class="text-gray-500 text-sm mt-2 max-w-lg mx-auto leading-relaxed">Berikut adalah rekap pemasukan dan pengeluaran RT untuk transparansi dan dikelola bersama demi kenyamanan warga.</p>
    </div>

{{-- Year Selector --}}
<div class="mb-6 flex items-center gap-3 flex-wrap">
    @foreach($years as $y)
    <a href="{{ route('public.financial', ['year' => $y]) }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold transition-all
              {{ $y == $year ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:border-brand-400' }}">
        {{ $y }}
    </a>
    @endforeach
    @if($years->isEmpty())
    <span class="text-sm text-gray-400 italic">Belum ada data keuangan.</span>
    @endif
</div>

@if($reports->isNotEmpty())
{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    @php
        $totalIncome  = $reports->sum('income');
        $totalExpense = $reports->sum('expense');
        $totalBalance = $totalIncome - $totalExpense;
    @endphp
    <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pemasukan</p>
        <p class="mt-1 text-2xl font-bold text-green-600">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Tahun {{ $year }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Total Pengeluaran</p>
        <p class="mt-1 text-2xl font-bold text-red-500">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Tahun {{ $year }}</p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Saldo Kas</p>
        <p class="mt-1 text-2xl font-bold {{ $totalBalance >= 0 ? 'text-brand-600' : 'text-red-600' }}">
            Rp {{ number_format(abs($totalBalance), 0, ',', '.') }}
            <span class="text-base">{{ $totalBalance >= 0 ? '▲' : '▼' }}</span>
        </p>
        <p class="text-xs text-gray-400 mt-1">Tahun {{ $year }}</p>
    </div>
</div>

{{-- Reports Table with modal trigger --}}
<div x-data="{ modal: false, activeReport: {} }" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-semibold text-gray-900">Detail Per Bulan – {{ $year }}</h2>
        <span class="text-xs text-gray-400">Klik baris untuk melihat bukti pembayaran</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-6 py-3 text-left">Bulan</th>
                    <th class="px-6 py-3 text-right">Pemasukan</th>
                    <th class="px-6 py-3 text-right">Pengeluaran</th>
                    <th class="px-6 py-3 text-right">Saldo</th>
                    <th class="px-6 py-3 text-center">Bukti</th>
                    <th class="px-6 py-3 text-center">PDF</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($reports as $report)
                <tr class="hover:bg-brand-50 cursor-pointer transition-colors"
                    @click="modal = true; activeReport = {
                        month: '{{ $report->month_name }}',
                        year: {{ $report->year }},
                        income: 'Rp {{ number_format($report->income, 0, ',', '.') }}',
                        expense: 'Rp {{ number_format($report->expense, 0, ',', '.') }}',
                        balance: 'Rp {{ number_format($report->balance, 0, ',', '.') }}',
                        description: '{{ addslashes($report->description ?? '-') }}',
                        image: '{{ $report->receipt_image ? Storage::url($report->receipt_image) : '' }}'
                    }">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $report->month_name }}</td>
                    <td class="px-6 py-4 text-right text-green-600 font-semibold">{{ $report->formatted_income }}</td>
                    <td class="px-6 py-4 text-right text-red-500">{{ $report->formatted_expense }}</td>
                    <td class="px-6 py-4 text-right font-bold {{ $report->balance >= 0 ? 'text-brand-600' : 'text-red-600' }}">
                        {{ $report->formatted_balance }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($report->receipt_image)
                            <span class="inline-block w-2 h-2 rounded-full bg-green-400"></span>
                        @else
                            <span class="text-xs text-gray-300">–</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center" @click.stop>
                        <a href="{{ route('public.financial.pdf', $report->id) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-brand-600 text-white rounded-lg text-xs font-semibold hover:bg-brand-700 transition-colors">
                            ⬇ PDF
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Receipt Modal --}}
    <div x-show="modal" x-cloak @keydown.escape.window="modal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div @click.away="modal = false"
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900" x-text="`Detail – ${activeReport.month} ${activeReport.year}`"></h3>
                <button @click="modal = false" class="text-gray-400 hover:text-gray-700 text-xl">✕</button>
            </div>
            <div class="p-6 space-y-4">
                <div class="grid grid-cols-3 gap-3 text-sm">
                    <div class="bg-green-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500">Pemasukan</p>
                        <p class="font-bold text-green-600" x-text="activeReport.income"></p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500">Pengeluaran</p>
                        <p class="font-bold text-red-500" x-text="activeReport.expense"></p>
                    </div>
                    <div class="bg-brand-50 rounded-xl p-3">
                        <p class="text-xs text-gray-500">Saldo</p>
                        <p class="font-bold text-brand-600" x-text="activeReport.balance"></p>
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Keterangan</p>
                    <p class="text-sm text-gray-700" x-text="activeReport.description || '–'"></p>
                </div>
                <div x-show="activeReport.image">
                    <p class="text-xs text-gray-500 mb-2">Bukti / Kwitansi</p>
                    <img :src="activeReport.image" alt="Bukti Pembayaran"
                         class="w-full rounded-xl border border-gray-200 object-contain max-h-64">
                </div>
                <div x-show="!activeReport.image"
                     class="py-6 text-center text-gray-400 text-sm bg-gray-50 rounded-xl">
                    📄 Tidak ada gambar kwitansi terlampir
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
    <div class="text-5xl mb-4">📊</div>
    <h3 class="text-lg font-semibold text-gray-700">Belum ada laporan</h3>
    <p class="text-sm text-gray-400 mt-1">Laporan keuangan tahun {{ $year }} belum tersedia.</p>
</div>
@endif
@endsection
