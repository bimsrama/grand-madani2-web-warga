@extends('layouts.admin')

@section('title', 'Laporan Keuangan')
@section('page-title', '💰 Laporan Keuangan RT 03')
@section('page-subtitle', 'Summary bulanan & transaksi harian')

@section('content')
<div class="space-y-8">

{{-- Year Filter --}}
<div class="flex items-center gap-3">
    @foreach($years->merge([now()->year]) as $y)
    <a href="?year={{ $y }}"
       class="px-4 py-2 rounded-xl text-sm font-semibold transition
              {{ $y == $year ? 'bg-brand-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
        {{ $y }}
    </a>
    @endforeach
</div>

{{-- Section 1: Monthly Summary --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">📊 Rekapitulasi Bulanan {{ $year }}</h2>
        <a href="{{ route('admin.finance.create') }}"
           class="px-4 py-2 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
            + Tambah Laporan
        </a>
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
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Nota</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($reports as $report)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-semibold">{{ $report->month_name }} {{ $year }}</td>
                    <td class="px-4 py-3 text-right text-green-700 font-semibold">{{ $report->formatted_income }}</td>
                    <td class="px-4 py-3 text-right text-red-600">{{ $report->formatted_expense }}</td>
                    <td class="px-4 py-3 text-right font-bold {{ $report->balance >= 0 ? 'text-brand-700' : 'text-red-700' }}">
                        {{ $report->formatted_balance }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($report->receipt_image)
                        <img src="{{ asset('storage/' . $report->receipt_image) }}"
                             alt="Nota"
                             class="h-10 w-10 object-cover rounded-lg inline-block cursor-pointer hover:scale-150 transition-transform shadow-sm"
                             onclick="window.open(this.src, '_blank')">
                        @else
                        <span class="text-gray-300 text-xs">–</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('public.financial.pdf', $report->id) }}"
                           class="text-xs text-brand-600 hover:underline mr-2">PDF</a>
                        <form action="{{ route('admin.finance.destroy', $report->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus laporan ini?')"
                                    class="text-xs text-red-500 hover:underline">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="px-6 py-12 text-center text-gray-400 text-sm">
        Belum ada laporan keuangan untuk tahun {{ $year }}.
        <a href="{{ route('admin.finance.create') }}" class="text-brand-600 hover:underline">Tambahkan sekarang →</a>
    </div>
    @endif
</div>

{{-- Section 2: Daily Transactions with Receipt Preview --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="font-bold text-gray-900 mb-1">📋 Transaksi Harian {{ $year }}</h2>
        <p class="text-xs text-gray-500">Catat pengeluaran/pemasukan harian dengan nota foto.</p>
    </div>

    {{-- Add Transaction Form --}}
    <div class="px-6 py-5 bg-gray-50 border-b border-gray-200"
         x-data="{ previewUrl: '' }">
        <h3 class="text-sm font-bold text-gray-700 mb-4">+ Tambah Transaksi Harian</h3>
        <form action="{{ route('admin.finance.transaction.store') }}" method="POST" enctype="multipart/form-data"
              class="grid sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @csrf
            <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-400">
            <input type="text" name="description" placeholder="Keterangan transaksi" required
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-400">
            <select name="type" required class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-400">
                <option value="pemasukan">💚 Pemasukan</option>
                <option value="pengeluaran">❤️ Pengeluaran</option>
            </select>
            <input type="number" name="amount" placeholder="Nominal (Rp)" min="0" step="1000" required
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-400">
            <input type="text" name="category" placeholder="Kategori (opsional)"
                   class="px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-brand-400">

            {{-- Receipt with live preview --}}
            <div class="flex items-center gap-2">
                <label class="flex-1 flex items-center gap-2 px-3 py-2 border border-dashed border-gray-300
                              rounded-xl cursor-pointer hover:bg-gray-100 transition text-sm text-gray-500">
                    📎 Upload Nota
                    <input type="file" name="receipt_path" accept="image/*" class="hidden"
                           @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                </label>
                {{-- Preview thumbnail --}}
                <template x-if="previewUrl">
                    <img :src="previewUrl" alt="Preview" class="h-10 w-10 object-cover rounded-lg shadow border border-gray-200">
                </template>
            </div>

            <div class="sm:col-span-2 lg:col-span-3">
                <button type="submit"
                        class="px-6 py-2.5 bg-brand-600 text-white text-sm font-semibold rounded-xl hover:bg-brand-700 transition">
                    Simpan Transaksi
                </button>
            </div>
        </form>
    </div>

    {{-- Transaction Table --}}
    @if($dailyTx->count() > 0)
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Tanggal</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Keterangan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Jenis</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Nominal</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Nota</th>
                    <th class="px-4 py-3"></th>
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
                             onclick="window.open(this.src, '_blank')"
                             title="Klik untuk perbesar">
                        @else
                        <span class="text-gray-300 text-xs">–</span>
                        @endif
                    </td>
                    <td class="px-4 py-2 text-right">
                        <form action="{{ route('admin.finance.transaction.destroy', $tx->id) }}" method="POST" class="inline">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus transaksi ini?')"
                                    class="text-xs text-red-400 hover:text-red-600 transition">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="px-6 py-10 text-center text-gray-400 text-sm">Belum ada transaksi harian untuk tahun {{ $year }}.</div>
    @endif
</div>

</div>
@endsection
