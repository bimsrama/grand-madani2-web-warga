@extends('layouts.admin')
@section('title', 'Generator Surat RT')
@section('page-title', 'Generator Surat RT')
@section('page-subtitle', 'Buat surat resmi dengan kop surat dan unduh sebagai PDF')

@section('content')
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

    {{-- Letter Form --}}
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <h2 class="font-bold text-gray-900 mb-5 flex items-center gap-2">
            <span class="text-xl">📝</span> Buat Surat Baru
        </h2>

        <form action="{{ route('admin.secretary.generate') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Jenis / Kategori Surat *</label>
                <select name="category" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm bg-white focus:ring-2 focus:ring-brand-500 outline-none">
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Perihal / Judul Surat *</label>
                <input type="text" name="subject" required
                       placeholder="Contoh: Undangan Kerja Bakti Minggu 20 April 2025"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Tanggal Surat *</label>
                <input type="date" name="date" required value="{{ now()->format('Y-m-d') }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5">
                    Isi Surat *
                    <span class="font-normal text-gray-400 ml-1">(Gunakan enter untuk paragraf baru)</span>
                </label>
                <textarea name="content" rows="10" required
                          placeholder="Dengan hormat,&#10;&#10;Bersama surat ini kami ingin memberitahukan kepada seluruh warga Grand Madani 2 bahwa...&#10;&#10;Demikian pemberitahuan ini kami sampaikan. Atas perhatian dan kerjasamanya kami ucapkan terima kasih."
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-brand-500 outline-none resize-none font-mono leading-relaxed"></textarea>
            </div>
            <button type="submit"
                    class="w-full py-3.5 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow-sm flex items-center justify-center gap-2">
                <span>📥</span> Generate & Download PDF
            </button>
        </form>
    </div>

    {{-- Preview / Tips --}}
    <div class="space-y-5">
        {{-- Template Tips --}}
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2"><span>💡</span> Template Tersimpan</h3>
            @forelse($templates as $tpl)
            <div class="border border-gray-100 rounded-xl p-4 mb-3 last:mb-0">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="inline-block px-2 py-0.5 bg-brand-50 text-brand-700 text-xs font-semibold rounded-full mb-1">{{ $tpl->category }}</span>
                        <p class="text-sm font-semibold text-gray-800">{{ $tpl->name }}</p>
                        @if($tpl->default_content)
                        <p class="text-xs text-gray-400 mt-1 line-clamp-2">{{ $tpl->default_content }}</p>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <p class="text-sm text-gray-400 italic">Belum ada template tersimpan. Template akan muncul di sini setelah ditambahkan.</p>
            @endforelse
        </div>

        {{-- PDF Preview Info --}}
        <div class="bg-brand-50 border border-brand-200 rounded-2xl p-6">
            <h3 class="font-bold text-brand-800 mb-3 flex items-center gap-2"><span>📄</span> Format Surat Resmi</h3>
            <ul class="space-y-2 text-sm text-brand-700">
                <li class="flex items-start gap-2"><span>✅</span> Kop Surat Resmi RT Grand Madani 2</li>
                <li class="flex items-start gap-2"><span>✅</span> Nomor surat otomatis (format: XXX/RT-GM2/Bulan-Romawi/Tahun)</li>
                <li class="flex items-start gap-2"><span>✅</span> Tanggal dan tempat penandatanganan</li>
                <li class="flex items-start gap-2"><span>✅</span> Area tanda tangan Ketua RT</li>
                <li class="flex items-start gap-2"><span>✅</span> Format kertas A4, siap cetak</li>
            </ul>
        </div>
    </div>
</div>
@endsection
