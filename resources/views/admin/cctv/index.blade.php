@extends('layouts.admin')
@section('title', 'Kelola CCTV')
@section('page-title', 'Kelola Akses CCTV')
@section('page-subtitle', 'Tambah kamera dan atur akses per warga')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    {{-- Add Camera Form --}}
    <div class="xl:col-span-1">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h2 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
                <span>📷</span> Tambah Kamera Baru
            </h2>
            <form action="{{ route('admin.cctv.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Nama Kamera *</label>
                    <input type="text" name="name" placeholder="CCTV Gerbang Utama"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Lokasi *</label>
                    <input type="text" name="location" placeholder="Pintu Masuk Blok A"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5">Embed URL / Stream URL *</label>
                    <input type="text" name="embed_url" placeholder="https://stream.example.com/cam1"
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none" required>
                    <p class="text-xs text-gray-400 mt-1">URL HLS, embed iframe, atau IP cam HTTP endpoint</p>
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors text-sm">
                    ➕ Tambah Kamera
                </button>
            </form>
        </div>
    </div>

    {{-- Camera List --}}
    <div class="xl:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-900">Daftar Kamera ({{ $cameras->count() }})</h2>
            </div>
            @forelse($cameras as $camera)
            <div class="px-6 py-4 border-b border-gray-100 last:border-0 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl {{ $camera->is_active ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center flex-shrink-0">
                        <span class="text-lg">📹</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-semibold text-gray-900 text-sm">{{ $camera->name }}</p>
                            @if($camera->is_active)
                                <span class="px-1.5 py-0.5 text-xs bg-green-100 text-green-700 rounded-full font-semibold">Aktif</span>
                            @else
                                <span class="px-1.5 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-full font-semibold">Nonaktif</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400">📍 {{ $camera->location }} · {{ $camera->residents_count }} warga memiliki akses</p>
                        <p class="text-xs text-gray-300 font-mono truncate max-w-xs mt-0.5">{{ Str::limit($camera->embed_url, 50) }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('admin.cctv.manage', $camera->id) }}"
                       class="px-3 py-1.5 bg-brand-600 text-white text-xs font-semibold rounded-lg hover:bg-brand-700 transition-colors">
                        Kelola Akses
                    </a>
                    <form action="{{ route('admin.cctv.toggle', $camera->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1.5 {{ $camera->is_active ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }} text-xs font-semibold rounded-lg hover:opacity-80 transition-opacity">
                            {{ $camera->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.cctv.destroy', $camera->id) }}" method="POST"
                          onsubmit="return confirm('Hapus kamera {{ $camera->name }}? Akses semua warga akan dihapus.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-500 text-xs font-semibold rounded-lg hover:bg-red-100 transition-colors">Hapus</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center text-gray-400">
                <p class="text-4xl mb-3">📹</p>
                <p class="text-sm">Belum ada kamera. Tambahkan kamera di form sebelah kiri.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
