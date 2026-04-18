@extends('layouts.app')

@section('title', 'Portal Warga – RT 03')

@section('content')
<div class="bg-gradient-to-b from-brand-50 to-white min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        {{-- Welcome Header --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-brand-100 rounded-full mb-3">
                <span class="text-xs font-semibold text-brand-700">🏘️ RT 03 Grand Madani</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">
                Selamat Datang, {{ session('resident_name') }}! 👋
            </h1>
            <p class="text-gray-500 mt-2">
                Blok {{ session('resident_block') }} / No. {{ session('resident_number') }}
            </p>
        </div>

        {{-- Pending Update Alert --}}
        @if($pendingUpdate)
        <div class="mb-6 p-4 bg-yellow-50 border border-yellow-300 rounded-2xl flex items-start gap-3">
            <span class="text-xl">⏳</span>
            <div>
                <p class="text-sm font-semibold text-yellow-800">Permintaan Perubahan Data Sedang Ditinjau</p>
                <p class="text-xs text-yellow-700 mt-1">
                    Permintaan Anda yang dikirim pada {{ $pendingUpdate->created_at->diffForHumans() }}
                    sedang dalam proses peninjauan oleh pengurus RT.
                </p>
            </div>
        </div>
        @endif

        {{-- Main Cards --}}
        <div class="grid md:grid-cols-2 gap-6 mb-8">

            {{-- Card 1: Kartu IPL --}}
            <a href="{{ route('resident.kartu-ipl') }}"
               class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100
                      p-6 flex flex-col transition-all duration-300 hover:-translate-y-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center group-hover:bg-brand-600 transition-colors">
                        <span class="text-2xl group-hover:scale-110 transition-transform">🪪</span>
                    </div>
                    <span class="text-2xl text-gray-200 group-hover:text-brand-300 transition-colors">→</span>
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-2">Kartu IPL Warga</h2>
                <p class="text-sm text-gray-500 flex-1">
                    Lihat riwayat pembayaran Iuran Pengelolaan Lingkungan (IPL) 12 bulan terakhir.
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    <span class="text-xs font-semibold text-brand-600">Lihat Kartu IPL →</span>
                </div>
            </a>

            {{-- Card 2: Perubahan Data --}}
            <a href="{{ route('resident.update-data') }}"
               class="group bg-white rounded-2xl shadow-md hover:shadow-xl border border-gray-100
                      p-6 flex flex-col transition-all duration-300 hover:-translate-y-1
                      {{ $pendingUpdate ? 'opacity-70 pointer-events-none' : '' }}">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                        <span class="text-2xl group-hover:scale-110 transition-transform">✏️</span>
                    </div>
                    @if($pendingUpdate)
                        <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full font-semibold">Menunggu</span>
                    @else
                        <span class="text-2xl text-gray-200 group-hover:text-blue-300 transition-colors">→</span>
                    @endif
                </div>
                <h2 class="text-lg font-bold text-gray-900 mb-2">Perubahan Data Warga</h2>
                <p class="text-sm text-gray-500 flex-1">
                    Ajukan perubahan nama, nomor WhatsApp, atau data anggota keluarga Anda.
                </p>
                <div class="mt-4 pt-4 border-t border-gray-100">
                    @if($pendingUpdate)
                        <span class="text-xs font-semibold text-yellow-600">⏳ Menunggu persetujuan pengurus</span>
                    @else
                        <span class="text-xs font-semibold text-blue-600">Ajukan Perubahan Data →</span>
                    @endif
                </div>
            </a>
        </div>

        {{-- Info Warga --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-bold text-gray-900 mb-4">📋 Informasi Akun Saya</h3>
            <div class="grid sm:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">Nama Pemilik</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $resident->owner_name }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Alamat</p>
                    <p class="text-sm font-semibold text-gray-900">Blok {{ $resident->block }} / No. {{ $resident->number }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Nomor WhatsApp</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $resident->masked_wa }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">Status</p>
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                        🟢 Aktif – RT 03
                    </span>
                </div>
            </div>

            @if($resident->family_members && count($resident->family_members) > 0)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-xs text-gray-500 mb-2">Anggota Keluarga</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($resident->family_members as $member)
                    <span class="text-xs bg-gray-100 text-gray-700 px-2.5 py-1 rounded-full">
                        {{ $member['name'] }}
                        @if(!empty($member['relation']))
                            <span class="text-gray-400">({{ $member['relation'] }})</span>
                        @endif
                    </span>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
