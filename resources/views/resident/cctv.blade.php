@extends('layouts.app')
@section('title', 'Pantauan Keamanan (CCTV)')

@section('content')
<div class="mb-6 flex items-center justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">📹 Pantauan Keamanan</h1>
        <p class="text-sm text-gray-500 mt-0.5">
            Hanya kamera yang diizinkan admin yang ditampilkan untuk Anda
        </p>
    </div>
    <div class="flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-3 py-2">
        <div class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></div>
        <span class="text-xs font-semibold text-green-700">Live</span>
    </div>
</div>

@if($cameras->isNotEmpty())
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($cameras as $camera)
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Camera Label Bar --}}
        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100 bg-gray-900">
            <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
            <div>
                <p class="text-sm font-bold text-white">{{ $camera->name }}</p>
                <p class="text-xs text-gray-400">📍 {{ $camera->location }}</p>
            </div>
        </div>

        {{-- CCTV Embed --}}
        <div class="relative aspect-video bg-gray-900">
            <iframe src="{{ $camera->embed_url }}"
                    title="{{ $camera->name }}"
                    class="w-full h-full border-0"
                    allowfullscreen
                    loading="lazy">
            </iframe>
            {{-- Overlay if stream fails to load --}}
            <div class="absolute inset-0 flex items-center justify-center text-white/30 pointer-events-none">
                <div class="text-center">
                    <p class="text-5xl mb-2">📷</p>
                    <p class="text-xs">Memuat stream...</p>
                </div>
            </div>
        </div>

        <div class="px-4 py-3 text-xs text-gray-400 flex items-center justify-between">
            <span>Diizinkan oleh Admin RT</span>
            <span>{{ now()->format('H:i') }} WIB</span>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center py-20 bg-white rounded-2xl border border-dashed border-gray-200">
    <div class="text-6xl mb-4">🔒</div>
    <h3 class="text-lg font-bold text-gray-700">Akses CCTV Terbatas</h3>
    <p class="text-sm text-gray-400 mt-2 max-w-sm mx-auto">
        Anda belum memiliki akses ke kamera CCTV manapun. Hubungi pengurus RT untuk mengajukan akses.
    </p>
    <div class="mt-6 inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 text-white rounded-xl text-sm font-semibold">
        {{ $resident->owner_name }} – Blok {{ $resident->block }} / No. {{ $resident->number }}
    </div>
</div>
@endif

{{-- Security Notice --}}
<div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3 text-xs text-amber-700">
    <span class="text-lg">⚠️</span>
    <p>Akses CCTV ini bersifat pribadi dan terbatas per rumah. Dilarang merekam atau menyebarkan siaran ini. Akses dapat dicabut sewaktu-waktu oleh pengurus RT.</p>
</div>
@endsection
