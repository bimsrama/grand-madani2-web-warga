@extends('layouts.app')

@section('title', 'Laporan Keuangan – RT 03')

@section('content')

{{-- ════════════════════════════════════════════════════════════
     LOADING ANIMATION OVERLAY
     ════════════════════════════════════════════════════════════ --}}
<div id="loading-overlay"
     class="fixed inset-0 z-[9999] flex flex-col items-center justify-center
            bg-gradient-to-br from-brand-700 to-green-600"
     style="transition: opacity 0.5s ease;">

    {{-- Spinning icon --}}
    <div class="mb-8 relative">
        <div class="w-20 h-20 rounded-full border-4 border-white/30 border-t-white animate-spin"></div>
        <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-3xl">💰</span>
        </div>
    </div>

    {{-- Sequential text --}}
    <div id="loading-text"
         class="text-white text-xl font-bold text-center px-8 min-h-[2em]
                transition-all duration-300"></div>

    <p class="text-green-200 text-sm mt-3 opacity-60">RT 03 Grand Madani</p>

    {{-- Progress bar --}}
    <div class="mt-8 w-48 bg-white/20 rounded-full h-1.5 overflow-hidden">
        <div id="loading-progress"
             class="h-full bg-white rounded-full transition-all duration-1000 ease-in-out"
             style="width: 0%"></div>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════════
     MAIN CONTENT (hidden until animation finishes)
     ════════════════════════════════════════════════════════════ --}}
<div id="finance-content" class="opacity-0 transition-opacity duration-500">

    <div class="bg-gradient-to-br from-brand-700 to-brand-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-extrabold mb-1">💰 Laporan Keuangan RT 03</h1>
            <p class="text-brand-200 text-sm">Grand Madani – Transparansi pengelolaan kas RT</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Year Filter --}}
        <div class="flex items-center gap-3 flex-wrap">
            @foreach($years->merge([now()->year])->unique()->sortDesc() as $y)
            <a href="?year={{ $y }}"
               class="px-4 py-2 rounded-xl text-sm font-semibold transition
                      {{ $y == $year ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
                {{ $y }}
            </a>
            @endforeach
        </div>

        @php
            $totalIncome  = $reports->sum('income');
            $totalExpense = $reports->sum('expense');
            $totalSaldo   = $totalIncome - $totalExpense;
        @endphp

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Total Pemasukan</p>
                <p class="text-xl font-extrabold text-green-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Total Pengeluaran</p>
                <p class="text-xl font-extrabold text-red-600">Rp {{ number_format($totalExpense, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Saldo Akhir</p>
                <p class="text-xl font-extrabold {{ $totalSaldo >= 0 ? 'text-brand-700' : 'text-red-700' }}">
                    Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                </p>
            </div>
        </div>

        {{-- Monthly Reports Table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">📊 Rekap Bulanan {{ $year }}</h2>
            </div>
            @if($reports->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Bulan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Pemasukan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Pengeluaran</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Saldo</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($reports as $report)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 font-semibold text-gray-900">{{ $report->month_name }} {{ $year }}</td>
                            <td class="px-4 py-3 text-right text-green-700 font-semibold">{{ $report->formatted_income }}</td>
                            <td class="px-4 py-3 text-right text-red-600">{{ $report->formatted_expense }}</td>
                            <td class="px-4 py-3 text-right font-bold {{ $report->balance >= 0 ? 'text-brand-700' : 'text-red-700' }}">
                                {{ $report->formatted_balance }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-gray-500">
                                {{ $report->description ?? '–' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($report->receipt_image)
                                <img src="{{ asset('storage/' . $report->receipt_image) }}"
                                     alt="Nota"
                                     class="h-10 w-10 object-cover rounded-lg inline-block cursor-pointer hover:scale-150 transition-transform"
                                     onclick="window.open(this.src, '_blank')">
                                @else
                                <span class="text-gray-300 text-xs">–</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="px-6 py-12 text-center text-gray-400 text-sm">
                Belum ada laporan keuangan untuk tahun {{ $year }}.
            </div>
            @endif
        </div>

        {{-- Daily Transactions --}}
        @if($dailyTx->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">📋 Transaksi Harian {{ $year }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Keterangan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Jenis</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Nominal</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($dailyTx as $tx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600 whitespace-nowrap">
                                {{ $tx->transaction_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $tx->description }}</td>
                            <td class="px-4 py-3">
                                @if($tx->type === 'pemasukan')
                                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-0.5 rounded-full">💚 Pemasukan</span>
                                @else
                                <span class="text-xs font-semibold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">❤️ Pengeluaran</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-semibold {{ $tx->type === 'pemasukan' ? 'text-green-700' : 'text-red-600' }}">
                                {{ $tx->formatted_amount }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($tx->receipt_path)
                                <img src="{{ asset('storage/' . $tx->receipt_path) }}"
                                     alt="Nota"
                                     class="h-10 w-10 object-cover rounded-lg inline-block cursor-pointer hover:scale-150 transition-transform shadow"
                                     onclick="window.open(this.src, '_blank')">
                                @else
                                <span class="text-gray-300 text-xs">–</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const overlay  = document.getElementById('loading-overlay');
    const textEl   = document.getElementById('loading-text');
    const progress = document.getElementById('loading-progress');
    const content  = document.getElementById('finance-content');

    const messages = [
        'Bekerja dengan Integritas...',
        'Melayani penuh Komitmen...',
        'Menjaga Transparansi...',
    ];

    let index = 0;

    // Show first message immediately
    textEl.textContent = messages[0];
    progress.style.width = '33%';

    const interval = setInterval(() => {
        index++;
        if (index < messages.length) {
            // Fade out, swap text, fade in
            textEl.style.opacity = '0';
            setTimeout(() => {
                textEl.textContent = messages[index];
                textEl.style.opacity = '1';
                progress.style.width = ((index + 1) / messages.length * 100) + '%';
            }, 200);
        } else {
            clearInterval(interval);
            // All messages shown — fade out overlay, show content
            setTimeout(() => {
                overlay.style.opacity = '0';
                content.style.opacity = '1';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 500);
            }, 400);
        }
    }, 1000);
})();
</script>
@endpush
