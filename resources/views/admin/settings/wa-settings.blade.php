@extends('layouts.admin')

@section('title', 'Pengaturan WhatsApp Bot')
@section('page-title', '📱 WhatsApp Bot Settings')
@section('page-subtitle', 'Konfigurasi Fonnte API untuk notifikasi WA otomatis')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Fonnte Config Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
            <h2 class="text-white font-bold text-base">🤖 Konfigurasi Fonnte API</h2>
            <p class="text-green-100 text-xs mt-1">Daftar & dapatkan token di <a href="https://fonnte.com" target="_blank" class="underline">fonnte.com</a></p>
        </div>

        <form action="{{ route('admin.wa-settings.save') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">🔑 API Token Fonnte</label>
                <input type="text" name="fonnte_token" value="{{ $fonnteToken }}"
                       placeholder="Masukkan token Fonnte Anda..."
                       class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono
                              focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                <p class="text-xs text-gray-400 mt-1">
                    Token ini digunakan untuk mengirim pesan WA ke warga dan pengurus.
                </p>
            </div>

            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">📞 Nomor WA Ketua RT</label>
                    <input type="text" name="rt_wa_number" value="{{ $rtWaNumber }}"
                           placeholder="628xxxxxxxxxx"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm
                                  focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">📞 Nomor WA Sekretaris</label>
                    <input type="text" name="secretary_wa" value="{{ $secretaryWa }}"
                           placeholder="628xxxxxxxxxx"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm
                                  focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                </div>
            </div>

            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-800 space-y-1">
                <p class="font-semibold">ℹ️ Cara mendapatkan Fonnte Token:</p>
                <ol class="list-decimal list-inside space-y-0.5 text-blue-700">
                    <li>Daftar di <a href="https://fonnte.com" target="_blank" class="underline font-semibold">fonnte.com</a></li>
                    <li>Buat device baru dan hubungkan nomor WhatsApp Anda</li>
                    <li>Scan QR Code dengan WhatsApp Anda</li>
                    <li>Salin API Token dari dashboard Fonnte</li>
                    <li>Paste di field "API Token" di atas</li>
                </ol>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition shadow-md">
                💾 Simpan Pengaturan WA Bot
            </button>
        </form>
    </div>

    {{-- QR Code Placeholder --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-900 mb-3">📲 Status Koneksi WhatsApp</h3>
        <div class="text-center p-8 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
            <div class="text-5xl mb-3">📱</div>
            @if($fonnteToken)
            <p class="text-sm font-semibold text-green-700">✅ API Token dikonfigurasi</p>
            <p class="text-xs text-gray-500 mt-1">
                Pesan WA akan dikirim menggunakan Fonnte API.<br>
                Pastikan device Fonnte Anda dalam keadaan terhubung di
                <a href="https://fonnte.com" target="_blank" class="text-brand-600 hover:underline">fonnte.com</a>.
            </p>
            @else
            <p class="text-sm font-semibold text-yellow-700">⚠️ API Token belum dikonfigurasi</p>
            <p class="text-xs text-gray-500 mt-1">
                Isi token Fonnte di atas untuk mengaktifkan notifikasi WA otomatis.
            </p>
            @endif
        </div>
    </div>

    {{-- Message Format Preview --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-900 mb-3">📋 Preview Format Pesan IPL</h3>
        <div class="bg-[#e5ddd5] rounded-xl p-4 font-mono text-xs text-gray-800 whitespace-pre-line">Selamat pembayaran Anda telah diterima!
Rincian Pembayaran IPL Bulan [Bulan]:
- Sampah: Rp [X]
- Keamanan: Rp [X]
- Masuk Kas RT: Rp [X]
- Masuk Dana Sosial: Rp [X]
- Masuk Kas RW: Rp [X]
----------------------
Total: Rp [Total]

Akses Kartu IPL Digital Anda melalui link berikut:
[Signed_Magic_Link]</div>
        <p class="text-xs text-gray-400 mt-2">Format ini dikirim otomatis saat admin menandai pembayaran IPL sebagai Lunas.</p>
    </div>
</div>
@endsection
