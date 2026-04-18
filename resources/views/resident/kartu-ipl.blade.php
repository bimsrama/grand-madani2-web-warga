@extends('layouts.app')

@section('title', 'Kartu IPL – RT 03')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('resident.portal') }}" class="text-sm text-brand-600 hover:underline">← Kembali ke Portal</a>
        <span class="text-sm text-gray-500">Tahun {{ $year }}</span>
    </div>

    {{-- Header Card --}}
    <div class="bg-gradient-to-br from-brand-600 to-brand-800 rounded-2xl p-6 text-white mb-6 shadow-lg">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-brand-200 text-xs font-semibold uppercase tracking-wider mb-1">Kartu IPL Digital</p>
                <h1 class="text-2xl font-bold">{{ $resident->owner_name }}</h1>
                <p class="text-brand-200 text-sm mt-1">Blok {{ $resident->block }} / No. {{ $resident->number }} — RT 03</p>
            </div>
            <div class="text-right">
                <div class="text-4xl">🪪</div>
                <p class="text-brand-200 text-xs mt-1">Grand Madani</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="mt-5 grid grid-cols-3 gap-3">
            @php
                $lunas = $transactions->filter(fn($t) => $t->status === 'lunas')->count();
                $belum = 12 - $lunas;
                $totalPaid = $transactions->filter(fn($t) => $t->status === 'lunas')->sum('amount');
            @endphp
            <div class="bg-white/15 rounded-xl p-3 text-center">
                <p class="text-xl font-bold">{{ $lunas }}</p>
                <p class="text-brand-200 text-xs">Bulan Lunas</p>
            </div>
            <div class="bg-white/15 rounded-xl p-3 text-center">
                <p class="text-xl font-bold">{{ $belum }}</p>
                <p class="text-brand-200 text-xs">Belum Bayar</p>
            </div>
            <div class="bg-white/15 rounded-xl p-3 text-center">
                <p class="text-base font-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                <p class="text-brand-200 text-xs">Total Dibayar</p>
            </div>
        </div>
    </div>

    {{-- 12-Month Grid --}}
    @php
        $monthNames = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember',
        ];
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 mb-6">
        @for($m = 1; $m <= 12; $m++)
        @php
            $tx = $transactions->get($m);
            $isLunas = $tx && $tx->status === 'lunas';
            $isFuture = $m > now()->month && $year == now()->year;
        @endphp
        <div class="bg-white rounded-xl border {{ $isLunas ? 'border-brand-200 bg-brand-50' : ($isFuture ? 'border-gray-100 bg-gray-50' : 'border-red-100 bg-red-50') }}
                    p-4 text-center transition-all hover:shadow-md">
            <p class="text-xs font-semibold text-gray-500 mb-1">{{ $monthNames[$m] }}</p>
            @if($isLunas)
                <div class="text-2xl mb-1">✅</div>
                <p class="text-xs font-bold text-brand-700">LUNAS</p>
                <p class="text-xs text-gray-400 mt-1">{{ number_format($tx->amount, 0, ',', '.') }}</p>
                @if($tx->paid_at)
                <p class="text-xs text-gray-300 mt-0.5">{{ $tx->paid_at->format('d/m') }}</p>
                @endif
            @elseif($isFuture)
                <div class="text-2xl mb-1">⏳</div>
                <p class="text-xs font-medium text-gray-400">Mendatang</p>
            @else
                <div class="text-2xl mb-1">❌</div>
                <p class="text-xs font-bold text-red-600">BELUM</p>
            @endif
        </div>
        @endfor
    </div>

    {{-- Detail Breakdown (if any paid months) --}}
    @if($transactions->filter(fn($t) => $t->status === 'lunas')->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-base font-bold text-gray-900">📊 Rincian Pembayaran</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Bulan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Sampah</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Keamanan</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Kas RT</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Sosial</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Kas RW</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transactions->filter(fn($t) => $t->status === 'lunas') as $tx)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $monthNames[$tx->month] }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($tx->biaya_sampah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($tx->biaya_keamanan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($tx->kas_rt, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($tx->dana_sosial, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-600">{{ number_format($tx->kas_rw, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold text-brand-700">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
