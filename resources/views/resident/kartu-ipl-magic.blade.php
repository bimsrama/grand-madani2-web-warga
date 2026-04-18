{{-- Magic Link view — no layout, standalone page --}}
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kartu IPL Digital – {{ $resident->owner_name }} – RT 03</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gradient-to-br from-brand-50 to-green-100 min-h-screen py-10 px-4" style="--tw-bg-opacity:1;background-color:rgb(240 253 244)">

<div class="max-w-lg mx-auto">

    {{-- Brand Header --}}
    <div class="text-center mb-6">
        <p class="text-sm font-bold text-green-700">🏘️ RT 03 — Grand Madani</p>
        <p class="text-xs text-gray-400 mt-0.5">Kartu IPL Digital</p>
    </div>

    {{-- IPL Card --}}
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden mb-6">
        {{-- Green header --}}
        <div style="background:linear-gradient(135deg,#15803d,#166534)" class="px-6 py-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-green-200 text-xs font-semibold uppercase tracking-widest mb-1">Bukti Pembayaran IPL</p>
                    <h1 class="text-xl font-bold">{{ $resident->owner_name }}</h1>
                    <p class="text-green-200 text-sm mt-0.5">Blok {{ $resident->block }} / No. {{ $resident->number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-4xl">✅</p>
                    <p class="text-green-200 text-xs mt-1">LUNAS</p>
                </div>
            </div>

            <div class="mt-5 bg-white/15 rounded-2xl p-4">
                <p class="text-green-100 text-xs font-semibold uppercase tracking-wide mb-1">Bulan Pembayaran</p>
                <p class="text-2xl font-bold">{{ ucfirst($monthName) }} {{ $year }}</p>
                @if($transaction->paid_at)
                <p class="text-green-200 text-xs mt-1">Diterima: {{ $transaction->paid_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>

        {{-- Breakdown --}}
        <div class="px-6 py-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">📋 Rincian Pembayaran</h3>
            <div class="space-y-2.5">
                @php
                    $items = [
                        '🗑️ Biaya Sampah'    => $transaction->biaya_sampah,
                        '🛡️ Biaya Keamanan' => $transaction->biaya_keamanan,
                        '🏛️ Masuk Kas RT'   => $transaction->kas_rt,
                        '❤️ Dana Sosial'    => $transaction->dana_sosial,
                        '🏘️ Masuk Kas RW'   => $transaction->kas_rw,
                    ];
                @endphp
                @foreach($items as $label => $value)
                <div class="flex items-center justify-between py-1.5 border-b border-dashed border-gray-100 last:border-0">
                    <span class="text-sm text-gray-600">{{ $label }}</span>
                    <span class="text-sm font-semibold text-gray-900">Rp {{ number_format($value, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-xl flex items-center justify-between">
                <span class="text-sm font-bold text-green-800">Total</span>
                <span class="text-lg font-extrabold text-green-700">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- 12-Month Status --}}
        <div class="px-6 pb-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3">📅 Status IPL {{ $year }}</h3>
            @php
                $months = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',
                           7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'];
            @endphp
            <div class="grid grid-cols-6 gap-1.5">
                @for($m = 1; $m <= 12; $m++)
                @php $t = $allTransactions->get($m); $paid = $t && $t->status === 'lunas'; @endphp
                <div class="text-center rounded-xl p-1.5 {{ $paid ? 'bg-green-100' : 'bg-gray-100' }}
                            {{ $m == $month ? 'ring-2 ring-green-500' : '' }}">
                    <p class="text-xs font-bold {{ $paid ? 'text-green-700' : 'text-gray-400' }}">{{ $months[$m] }}</p>
                    <p class="text-base leading-none mt-0.5">{{ $paid ? '✅' : '○' }}</p>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <div class="text-center">
        <p class="text-xs text-gray-500">Dokumen ini diterbitkan otomatis oleh Portal RT 03 Grand Madani.</p>
        <p class="text-xs text-gray-400 mt-1">Link ini berlaku 30 hari. Dilarang memalsukan dokumen ini.</p>
        <div class="mt-3 p-3 bg-white rounded-xl border border-gray-100 text-xs text-gray-400">
            Silakan laporkan ke pengurus jika ada ketidaksesuaian data atau kendala pada sistem.
        </div>
    </div>
</div>

</body>
</html>
