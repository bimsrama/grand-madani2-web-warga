@extends('layouts.admin')

@section('title', 'Struktur Pengurus')
@section('page-title', '👤 Struktur Pengurus RT 03')
@section('page-subtitle', 'Kelola daftar pengurus yang tampil di halaman utama')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">

    {{-- Add Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-6">
            <h2 class="text-base font-bold text-gray-900 mb-5">+ Tambah Pengurus</h2>
            <form action="{{ route('admin.board.store') }}" method="POST" enctype="multipart/form-data"
                  class="space-y-4" x-data="{ previewUrl: '' }">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" required placeholder="e.g., Budi Santoso"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Jabatan / Peran</label>
                    <input type="text" name="role" required placeholder="e.g., Ketua RT, Sekretaris"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Foto</label>
                    <label class="block border-2 border-dashed border-gray-200 rounded-xl p-4 cursor-pointer
                                 hover:border-brand-400 hover:bg-brand-50 transition text-center">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" class="h-20 w-20 object-cover rounded-full mx-auto mb-2">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="text-center">
                                <span class="text-3xl">👤</span>
                                <p class="text-xs text-gray-400 mt-1">Upload foto</p>
                            </div>
                        </template>
                        <input type="file" name="photo" accept="image/*" class="hidden"
                               @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan Tampil</label>
                    <input type="number" name="sort_order" value="{{ $members->count() }}" min="0"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition text-sm">
                    Simpan Pengurus
                </button>
            </form>
        </div>
    </div>

    {{-- Board Member List --}}
    <div class="lg:col-span-2">
        <div class="grid sm:grid-cols-2 gap-4">
            @forelse($members as $member)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-5 text-center"
                 x-data="{ editing: false }">
                {{-- Photo --}}
                @if($member->photo_path)
                <img src="{{ asset('storage/' . $member->photo_path) }}"
                     alt="{{ $member->name }}"
                     class="w-20 h-20 rounded-full object-cover mx-auto mb-3 border-4 border-brand-100">
                @else
                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-brand-400 to-brand-600
                            flex items-center justify-center mx-auto mb-3">
                    <span class="text-white text-2xl font-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                </div>
                @endif

                <h3 class="font-bold text-gray-900">{{ $member->name }}</h3>
                <span class="inline-block text-xs font-semibold text-brand-700 bg-brand-100 px-2.5 py-1 rounded-full mt-1">
                    {{ $member->role }}
                </span>

                <div class="flex gap-2 mt-4">
                    <button @click="editing = !editing"
                            class="flex-1 text-xs py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-600">
                        ✏️ Edit
                    </button>
                    <form action="{{ route('admin.board.destroy', $member->id) }}" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Hapus anggota pengurus ini?')" type="submit"
                                class="w-full text-xs py-1.5 border border-red-200 rounded-lg hover:bg-red-50 transition text-red-500">
                            🗑️ Hapus
                        </button>
                    </form>
                </div>

                {{-- Edit form --}}
                <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-gray-100 text-left">
                    <form action="{{ route('admin.board.update', $member->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                        @csrf @method('PUT')
                        <input type="text" name="name" value="{{ $member->name }}" required
                               class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-brand-400">
                        <input type="text" name="role" value="{{ $member->role }}" required
                               class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-brand-400">
                        <label class="block border border-dashed border-gray-200 rounded-xl p-2 cursor-pointer hover:border-brand-400 text-center">
                            <span class="text-xs text-gray-400">📷 Ganti foto (opsional)</span>
                            <input type="file" name="photo" accept="image/*" class="hidden">
                        </label>
                        <input type="number" name="sort_order" value="{{ $member->sort_order }}"
                               class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs">
                        <button type="submit"
                                class="w-full py-1.5 bg-brand-600 text-white text-xs font-semibold rounded-xl hover:bg-brand-700 transition">
                            Simpan
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="sm:col-span-2 bg-white rounded-2xl border border-dashed border-gray-200 px-6 py-12 text-center">
                <span class="text-4xl">👤</span>
                <p class="text-gray-400 text-sm mt-2">Belum ada pengurus. Tambahkan di form sebelah kiri.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
