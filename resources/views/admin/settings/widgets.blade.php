@extends('layouts.admin')

@section('title', 'Widget Homepage')
@section('page-title', '🔗 Widget Homepage')
@section('page-subtitle', 'Kelola kartu komunitas di halaman utama (WA Group, Karang Taruna, dll)')

@section('content')
<div class="grid lg:grid-cols-3 gap-6">

    {{-- Add Widget Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 sticky top-6">
            <h2 class="text-base font-bold text-gray-900 mb-5">+ Tambah Widget Baru</h2>
            <form action="{{ route('admin.widgets.store') }}" method="POST" enctype="multipart/form-data"
                  class="space-y-4" x-data="{ previewUrl: '' }">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Judul Widget</label>
                    <input type="text" name="title" required placeholder="e.g., Grup WhatsApp RT 03"
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Deskripsi</label>
                    <textarea name="description" rows="2" placeholder="Singkat & jelas..."
                              class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400 resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Link Eksternal</label>
                    <input type="url" name="external_link" placeholder="https://..."
                           class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-2">Thumbnail</label>
                    <label class="block border-2 border-dashed border-gray-200 rounded-xl p-3 cursor-pointer
                                 hover:border-brand-400 hover:bg-brand-50 transition text-center">
                        <template x-if="previewUrl">
                            <img :src="previewUrl" class="h-20 object-cover rounded-lg mx-auto mb-2">
                        </template>
                        <template x-if="!previewUrl">
                            <span class="text-xs text-gray-400">📷 Klik untuk upload gambar</span>
                        </template>
                        <input type="file" name="thumbnail" accept="image/*" class="hidden"
                               @change="previewUrl = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Urutan</label>
                        <input type="number" name="sort_order" value="0" min="0"
                               class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-400">
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                        <select name="is_active" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm">
                            <option value="1">✅ Aktif</option>
                            <option value="0">⏸️ Nonaktif</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                        class="w-full py-2.5 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition text-sm">
                    Simpan Widget
                </button>
            </form>
        </div>
    </div>

    {{-- Widget List --}}
    <div class="lg:col-span-2">
        <div class="grid sm:grid-cols-2 gap-4">
            @forelse($widgets as $widget)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden
                        {{ !$widget->is_active ? 'opacity-60' : '' }}"
                 x-data="{ editing: false }">
                {{-- Thumbnail --}}
                @if($widget->thumbnail_path)
                <div class="h-32 overflow-hidden bg-gray-100">
                    <img src="{{ asset('storage/' . $widget->thumbnail_path) }}"
                         alt="{{ $widget->title }}"
                         class="w-full h-full object-cover">
                </div>
                @else
                <div class="h-24 bg-gradient-to-br from-brand-100 to-brand-200 flex items-center justify-center">
                    <span class="text-4xl">🔗</span>
                </div>
                @endif

                <div class="p-4">
                    <div class="flex items-start justify-between mb-1">
                        <h3 class="font-bold text-gray-900 text-sm">{{ $widget->title }}</h3>
                        @if(!$widget->is_active)
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Nonaktif</span>
                        @endif
                    </div>
                    @if($widget->description)
                    <p class="text-xs text-gray-500 mb-2">{{ $widget->description }}</p>
                    @endif
                    @if($widget->external_link)
                    <a href="{{ $widget->external_link }}" target="_blank"
                       class="text-xs text-brand-600 hover:underline truncate block">{{ $widget->external_link }}</a>
                    @endif

                    <div class="mt-3 flex gap-2">
                        <button @click="editing = !editing"
                                class="flex-1 text-xs py-1.5 border border-gray-200 rounded-lg hover:bg-gray-50 transition text-gray-600">
                            ✏️ Edit
                        </button>
                        <form action="{{ route('admin.widgets.destroy', $widget->id) }}" method="POST" class="flex-1">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Hapus widget ini?')" type="submit"
                                    class="w-full text-xs py-1.5 border border-red-200 rounded-lg hover:bg-red-50 transition text-red-500">
                                🗑️ Hapus
                            </button>
                        </form>
                    </div>

                    {{-- Edit form (toggle) --}}
                    <div x-show="editing" x-cloak class="mt-4 pt-4 border-t border-gray-100"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                        <form action="{{ route('admin.widgets.update', $widget->id) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                            @csrf @method('PUT')
                            <input type="text" name="title" value="{{ $widget->title }}" required
                                   class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-brand-400">
                            <textarea name="description" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs resize-none focus:ring-2 focus:ring-brand-400">{{ $widget->description }}</textarea>
                            <input type="url" name="external_link" value="{{ $widget->external_link }}"
                                   class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs focus:ring-2 focus:ring-brand-400">
                            <select name="is_active" class="w-full border border-gray-200 rounded-xl px-3 py-1.5 text-xs">
                                <option value="1" {{ $widget->is_active ? 'selected' : '' }}>✅ Aktif</option>
                                <option value="0" {{ !$widget->is_active ? 'selected' : '' }}>⏸️ Nonaktif</option>
                            </select>
                            <button type="submit"
                                    class="w-full py-1.5 bg-brand-600 text-white text-xs font-semibold rounded-xl hover:bg-brand-700 transition">
                                Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="sm:col-span-2 bg-white rounded-2xl border border-dashed border-gray-200 px-6 py-12 text-center">
                <p class="text-gray-400 text-sm">Belum ada widget. Tambahkan widget komunitas di form sebelah kiri.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
