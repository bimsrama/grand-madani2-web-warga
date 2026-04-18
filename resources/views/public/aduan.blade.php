@extends('layouts.app')
@section('title', 'Aduan & Forum')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- ═══ LEFT: Announcements + Complaint Form ═══ --}}
    <div class="lg:col-span-2 space-y-8">

        {{-- Announcements --}}
        <section>
            <div class="mb-10 text-center">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-4 uppercase tracking-widest border border-blue-100">
                    📢 Aduan & Info Wilayah RT 0{{ $rt ?? '1' }}
                </span>
                <h1 class="text-3xl font-black text-gray-900 leading-tight">Suara & Informasi Warga</h1>
                <p class="text-gray-500 text-sm mt-2 max-w-lg mx-auto leading-relaxed">Pantau pengumuman terbaru dan sampaikan aspirasi atau keluhan Anda untuk lingkungan yang lebih baik.</p>
            </div>

            @forelse($announcements as $ann)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4">
                <div class="flex items-start justify-between gap-3">
                    <h3 class="font-semibold text-gray-900 text-sm">{{ $ann->title }}</h3>
                    <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0">
                        {{ $ann->published_at?->diffForHumans() ?? $ann->created_at->diffForHumans() }}
                    </span>
                </div>
                <p class="mt-2 text-sm text-gray-600 leading-relaxed">{{ $ann->body }}</p>
            </div>
            @empty
            <div class="bg-white rounded-2xl border border-dashed border-gray-200 p-8 text-center">
                <span class="text-3xl">📭</span>
                <p class="text-sm text-gray-400 mt-2">Belum ada pengumuman</p>
            </div>
            @endforelse
        </section>

        {{-- Complaint Form --}}
        <section>
            <h2 class="text-xl font-bold text-gray-900 mb-1">📝 Kirim Aduan</h2>
            <p class="text-sm text-gray-500 mb-5">Laporkan masalah lingkungan kepada pengurus RT</p>

            @if($errors->any())
            <div class="mb-4 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('public.aduan.store') }}" method="POST"
                  class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Lengkap *</label>
                        <input type="text" name="reporter_name" value="{{ old('reporter_name') }}"
                               placeholder="Budi Santoso"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5">Alamat / Nomor Rumah *</label>
                        <input type="text" name="reporter_address" value="{{ old('reporter_address') }}"
                               placeholder="Blok A / No. 12"
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Kategori *</label>
                    <select name="category"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition bg-white">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="Kebersihan" {{ old('category') === 'Kebersihan' ? 'selected' : '' }}>🧹 Kebersihan</option>
                        <option value="Keamanan"   {{ old('category') === 'Keamanan'   ? 'selected' : '' }}>🔒 Keamanan</option>
                        <option value="Fasilitas"  {{ old('category') === 'Fasilitas'  ? 'selected' : '' }}>🏗️ Fasilitas Umum</option>
                        <option value="Jalan"      {{ old('category') === 'Jalan'      ? 'selected' : '' }}>🛣️ Jalan / Drainase</option>
                        <option value="Lainnya"    {{ old('category') === 'Lainnya'    ? 'selected' : '' }}>💬 Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Deskripsi Aduan *</label>
                    <textarea name="description" rows="4"
                              placeholder="Jelaskan masalah yang ingin Anda laporkan secara detail..."
                              class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition resize-none">{{ old('description') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow-sm">
                    📨 Kirim Aduan
                </button>
            </form>
        </section>
    </div>

    {{-- ═══ RIGHT: Complaint Tracker ═══ --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-1">📋 Daftar Aduan Masuk</h2>
        <p class="text-sm text-gray-500 mb-5">Status penanganan aduan warga</p>

        <div class="space-y-3">
            @forelse($complaints as $complaint)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4">
                <div class="flex items-start justify-between gap-2 mb-2">
                    <span class="text-xs font-medium text-gray-700 leading-tight">{{ $complaint->reporter_name }} – {{ $complaint->reporter_address }}</span>
                    @php
                        $colorMap = ['waiting' => 'yellow', 'processing' => 'blue', 'done' => 'green'];
                        $color = $colorMap[$complaint->status] ?? 'gray';
                    @endphp
                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-{{ $color }}-100 text-{{ $color }}-700 whitespace-nowrap flex-shrink-0">
                        {{ $complaint->status_label }}
                    </span>
                </div>
                <p class="text-xs font-semibold text-brand-600 mb-1">{{ $complaint->category }}</p>
                <p class="text-xs text-gray-500 line-clamp-2">{{ $complaint->description }}</p>
                @if($complaint->admin_response)
                <div class="mt-2 bg-brand-50 rounded-lg px-3 py-2 text-xs text-brand-700">
                    💬 {{ $complaint->admin_response }}
                </div>
                @endif
                <p class="text-xs text-gray-300 mt-2">{{ $complaint->created_at->diffForHumans() }}</p>
            </div>
            @empty
            <div class="text-center py-10 bg-white rounded-2xl border border-dashed border-gray-200">
                <span class="text-3xl">✅</span>
                <p class="text-sm text-gray-400 mt-2">Belum ada aduan masuk</p>
            </div>
            @endforelse
        </div>
        {{ $complaints->links() }}
    </div>
</div>
@endsection
