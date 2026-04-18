@extends('layouts.admin')
@section('title', 'Kelola Akses – ' . $camera->name)
@section('page-title', 'Akses CCTV: ' . $camera->name)
@section('page-subtitle', 'Centang warga yang diizinkan melihat kamera ini')

@section('content')

<div class="mb-5 flex items-center gap-3">
    <a href="{{ route('admin.cctv.index') }}"
       class="flex items-center gap-1 text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
        ← Kembali ke Daftar Kamera
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

    {{-- Camera Header --}}
    <div class="bg-gray-900 px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-2 h-2 rounded-full {{ $camera->is_active ? 'bg-green-400 animate-pulse' : 'bg-gray-500' }}"></div>
            <div>
                <p class="font-bold text-white">{{ $camera->name }}</p>
                <p class="text-xs text-gray-400">📍 {{ $camera->location }}</p>
            </div>
        </div>
        <span class="text-xs font-semibold px-2 py-1 rounded-full {{ $camera->is_active ? 'bg-green-800 text-green-300' : 'bg-gray-700 text-gray-400' }}">
            {{ $camera->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>

    {{-- Access Control Form --}}
    <form action="{{ route('admin.cctv.updateAccess', $camera->id) }}" method="POST">
        @csrf

        {{-- Bulk action buttons --}}
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-4 flex-wrap">
            <div class="flex items-center gap-2">
                <button type="button" onclick="checkAll(true)"
                        class="px-3 py-1.5 text-xs font-semibold text-brand-700 bg-brand-50 rounded-lg hover:bg-brand-100 transition-colors">
                    ☑ Centang Semua
                </button>
                <button type="button" onclick="checkAll(false)"
                        class="px-3 py-1.5 text-xs font-semibold text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    ☐ Hapus Semua
                </button>
                <span class="text-xs text-gray-400">
                    <span id="checked-count">{{ count($grantedIds) }}</span> / {{ $residents->count() }} warga diberi akses
                </span>
            </div>
            <button type="submit"
                    class="px-5 py-2 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow">
                💾 Simpan Perubahan
            </button>
        </div>

        {{-- Resident Checkboxes --}}
        <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($residents->groupBy('block') as $block => $blockResidents)
            <div class="col-span-full">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2 mt-2">Blok {{ $block }}</p>
            </div>
            @foreach($blockResidents as $resident)
            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 hover:border-brand-300 hover:bg-brand-50 cursor-pointer transition-all group has-[:checked]:border-brand-400 has-[:checked]:bg-brand-50">
                <input type="checkbox" name="resident_ids[]" value="{{ $resident->id }}"
                       {{ in_array($resident->id, $grantedIds) ? 'checked' : '' }}
                       onchange="updateCount()"
                       class="w-4 h-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500">
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">{{ $resident->owner_name }}</p>
                    <p class="text-xs text-gray-400">Blok {{ $resident->block }} / No. {{ $resident->number }}</p>
                </div>
            </label>
            @endforeach
            @endforeach
        </div>

        {{-- Bottom Save --}}
        <div class="px-6 py-4 border-t border-gray-100">
            <button type="submit"
                    class="w-full py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow">
                💾 Simpan Perubahan Akses CCTV
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function checkAll(state) {
    document.querySelectorAll('input[name="resident_ids[]"]').forEach(cb => cb.checked = state);
    updateCount();
}
function updateCount() {
    const count = document.querySelectorAll('input[name="resident_ids[]"]:checked').length;
    document.getElementById('checked-count').textContent = count;
}
</script>
@endpush
@endsection
