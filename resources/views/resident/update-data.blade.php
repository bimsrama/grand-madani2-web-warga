@extends('layouts.app')

@section('title', 'Perubahan Data Warga – RT 03')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 py-10">

    <div class="mb-6">
        <a href="{{ route('resident.portal') }}" class="text-sm text-brand-600 hover:underline">← Kembali ke Portal</a>
    </div>

    <div class="bg-white rounded-2xl shadow-md border border-gray-100 overflow-hidden">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-brand-600 to-brand-700 px-6 py-5">
            <h1 class="text-xl font-bold text-white">✏️ Perubahan Data Warga</h1>
            <p class="text-brand-100 text-sm mt-1">
                Isi data yang ingin diubah. Pengurus RT akan meninjau dan mengonfirmasi.
            </p>
        </div>

        <form action="{{ route('resident.update-data.post') }}" method="POST" class="p-6 space-y-5">
            @csrf

            {{-- Current Info (readonly display) --}}
            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Data Saat Ini</p>
                <div class="grid sm:grid-cols-2 gap-3 text-sm">
                    <div><span class="text-gray-500">Nama:</span> <strong>{{ $resident->owner_name }}</strong></div>
                    <div><span class="text-gray-500">Blok:</span> <strong>Blok {{ $resident->block }} / No. {{ $resident->number }}</strong></div>
                    <div><span class="text-gray-500">WA:</span> <strong>{{ $resident->masked_wa }}</strong></div>
                </div>
            </div>

            {{-- Requested Name --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nama Baru <span class="font-normal text-gray-400">(kosongkan jika tidak ada perubahan)</span>
                </label>
                <input type="text" name="requested_name"
                       value="{{ old('requested_name') }}"
                       placeholder="{{ $resident->owner_name }}"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                              focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                @error('requested_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Requested WA --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Nomor WhatsApp Baru <span class="font-normal text-gray-400">(kosongkan jika tidak ada perubahan)</span>
                </label>
                <input type="tel" name="requested_wa"
                       value="{{ old('requested_wa') }}"
                       placeholder="Contoh: 628123456789"
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                              focus:ring-2 focus:ring-brand-500 focus:border-transparent transition">
                <p class="text-xs text-gray-400 mt-1">Format: 628xxxxxxxxxx (tanpa +/-/spasi)</p>
                @error('requested_wa') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Family Members --}}
            <div x-data="familyForm()">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-sm font-semibold text-gray-700">Anggota Keluarga</label>
                    <button type="button" @click="add()"
                            class="text-xs text-brand-600 hover:text-brand-800 font-semibold flex items-center gap-1">
                        + Tambah Anggota
                    </button>
                </div>

                @if($resident->family_members && count($resident->family_members) > 0)
                <p class="text-xs text-gray-400 mb-2">Data terakhir: {{ collect($resident->family_members)->pluck('name')->join(', ') }}</p>
                @endif

                <div class="space-y-2">
                    <template x-for="(member, index) in members" :key="index">
                        <div class="flex gap-2 items-center">
                            <input type="text" :name="`family_name[${index}]`"
                                   x-model="member.name" placeholder="Nama anggota"
                                   class="flex-1 px-3 py-2 border border-gray-200 rounded-lg text-sm
                                          focus:ring-2 focus:ring-brand-400 focus:border-transparent">
                            <input type="text" :name="`family_relation[${index}]`"
                                   x-model="member.relation" placeholder="Hubungan"
                                   class="w-32 px-3 py-2 border border-gray-200 rounded-lg text-sm
                                          focus:ring-2 focus:ring-brand-400 focus:border-transparent">
                            <button type="button" @click="remove(index)"
                                    class="text-red-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-red-50 transition">
                                ✕
                            </button>
                        </div>
                    </template>
                    <template x-if="members.length === 0">
                        <p class="text-xs text-gray-400 italic py-2">Belum ada anggota keluarga. Klik "Tambah Anggota" untuk menambah.</p>
                    </template>
                </div>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    Catatan / Keterangan Tambahan
                </label>
                <textarea name="notes" rows="3"
                          placeholder="Opsional – tuliskan alasan perubahan atau keterangan lainnya"
                          class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                                 focus:ring-2 focus:ring-brand-500 focus:border-transparent transition resize-none">{{ old('notes') }}</textarea>
            </div>

            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                <p class="text-xs text-yellow-800">
                    ⚠️ Permintaan ini akan dikirim ke pengurus RT untuk ditinjau. Perubahan data baru akan aktif
                    setelah disetujui. Notifikasi WhatsApp akan dikirim ke pengurus RT dan sekretaris.
                </p>
            </div>

            <button type="submit"
                    class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl
                           hover:bg-brand-700 transition-all shadow-md">
                📤 Kirim Permintaan Perubahan Data
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function familyForm() {
    return {
        members: @json($resident->family_members ?? []),
        add() { this.members.push({ name: '', relation: '' }); },
        remove(index) { this.members.splice(index, 1); }
    }
}
</script>
@endpush
