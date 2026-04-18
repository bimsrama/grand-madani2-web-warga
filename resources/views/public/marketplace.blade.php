@extends('layouts.app')
@section('title', 'Pasar Warga')

@section('content')
    <div class="mb-10 text-center">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-bold mb-4 uppercase tracking-widest border border-amber-100">
            🛍️ Pasar Warga Wilayah RT 0{{ $rt ?? '1' }}
        </span>
        <h1 class="text-3xl font-black text-gray-900 leading-tight">Ekonomi Sirkulasi Warga</h1>
        <p class="text-gray-500 text-sm mt-2 max-w-lg mx-auto leading-relaxed">Jual beli dan tawarkan jasa Anda khusus untuk lingkungan Grand Madani 2 demi kemajuan bersama.</p>
    </div>

{{-- Tab Switcher --}}
<div x-data="{ tab: 'preloved' }" class="space-y-6">
    <div class="flex gap-2 bg-white border border-gray-200 rounded-2xl p-1.5 w-fit shadow-sm">
        <button @click="tab = 'preloved'"
                :class="tab === 'preloved' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900'"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            🏷️ Preloved
        </button>
        <button @click="tab = 'jasa'"
                :class="tab === 'jasa' ? 'bg-brand-600 text-white shadow-md' : 'text-gray-500 hover:text-gray-900'"
                class="px-5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200">
            🔧 Direktori Jasa
        </button>
    </div>

    {{-- PRELOVED GRID --}}
    <div x-show="tab === 'preloved'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        @if($preloved->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($preloved as $item)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                {{-- Image --}}
                <div class="aspect-square bg-gray-100 overflow-hidden">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-5xl">🏷️</span>
                        </div>
                    @endif
                </div>
                {{-- Info --}}
                <div class="p-4">
                    <span class="inline-block px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full mb-2">Preloved</span>
                    <h3 class="font-semibold text-gray-900 text-sm leading-tight mb-1">{{ $item->title }}</h3>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $item->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-brand-700 font-bold text-sm">{{ $item->formatted_price }}</span>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $item->contact_wa) }}"
                           target="_blank"
                           class="flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs font-semibold hover:bg-green-600 transition-colors">
                            💬 WA
                        </a>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">👤 {{ $item->contact_name }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
            <div class="text-5xl mb-4">🏷️</div>
            <h3 class="text-lg font-semibold text-gray-700">Belum ada barang preloved</h3>
            <p class="text-sm text-gray-400 mt-1">Tidak ada listing barang preloved saat ini.</p>
        </div>
        @endif
    </div>

    {{-- DIREKTORI JASA GRID --}}
    <div x-show="tab === 'jasa'" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
        @if($jasa->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($jasa as $item)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-shadow p-5 flex gap-4">
                {{-- Icon placeholder --}}
                <div class="w-14 h-14 bg-brand-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->title }}"
                             class="w-14 h-14 rounded-2xl object-cover">
                    @else
                        <span class="text-2xl">🔧</span>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <span class="inline-block px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full mb-1">Jasa</span>
                    <h3 class="font-semibold text-gray-900 text-sm leading-tight">{{ $item->title }}</h3>
                    <p class="text-xs text-gray-500 mt-1 mb-3 line-clamp-2">{{ $item->description }}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-brand-700 font-bold text-xs">{{ $item->formatted_price }}</span>
                        <a href="https://wa.me/{{ preg_replace('/\D/', '', $item->contact_wa) }}"
                           target="_blank"
                           class="flex items-center gap-1 px-3 py-1.5 bg-green-500 text-white rounded-lg text-xs font-semibold hover:bg-green-600 transition-colors">
                            💬 Hubungi
                        </a>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">👤 {{ $item->contact_name }}</p>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20 bg-white rounded-2xl border border-gray-200">
            <div class="text-5xl mb-4">🔧</div>
            <h3 class="text-lg font-semibold text-gray-700">Direktori jasa kosong</h3>
            <p class="text-sm text-gray-400 mt-1">Belum ada penyedia jasa yang terdaftar.</p>
        </div>
        @endif
    </div>
</div>

{{-- Info Banner --}}
<div class="mt-8 bg-brand-50 border border-brand-200 rounded-2xl p-5 flex gap-3">
    <span class="text-2xl">🌿</span>
    <div>
        <p class="font-semibold text-brand-800 text-sm">Dukung Ekonomi Lokal Grand Madani 2</p>
        <p class="text-xs text-brand-600 mt-0.5">Prioritaskan transaksi antar warga untuk memperkuat sirkulasi ekonomi di lingkungan kita. Ingin memasang iklan? Hubungi pengurus RT.</p>
    </div>
</div>
@endsection
