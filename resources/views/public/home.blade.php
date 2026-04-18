@extends('layouts.app')

@section('title', 'Beranda – RT 03 Grand Madani')

@section('content')

{{-- ════════════════ HERO HEADER ════════════════ --}}
<section class="relative bg-gradient-to-br from-brand-700 via-brand-600 to-emerald-500 text-white overflow-hidden">
    <div class="absolute inset-0 overflow-hidden pointer-events-none select-none">
        <div class="absolute -top-20 -right-20 w-80 h-80 rounded-full bg-white/5 blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-72 h-72 rounded-full bg-black/10 blur-3xl"></div>
    </div>
    <div class="relative max-w-4xl mx-auto px-4 py-10 text-center">
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/30
                    rounded-full px-4 py-1.5 text-xs font-semibold mb-4">
            🏘️ Grand Madani — RT 03
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight mb-2">
            Portal Layanan Warga
        </h1>
        <p class="text-green-100 text-sm mb-6 max-w-md mx-auto">
            Semua layanan RT 03 dalam satu genggaman. Pilih layanan yang Anda butuhkan.
        </p>

        {{-- Saldo pill --}}
        <div class="inline-flex items-center gap-2 bg-white/20 backdrop-blur-sm border border-white/20
                    rounded-xl px-5 py-2.5">
            <span class="text-lg">💰</span>
            <div class="text-left">
                <p class="text-green-200 text-xs leading-none">Saldo Kas RT {{ $year }}</p>
                <p class="font-extrabold text-base leading-tight">
                    Rp {{ number_format($saldo, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </div>
</section>

{{-- ════════════════ MENU GRID ════════════════ --}}
<section class="max-w-4xl mx-auto px-4 py-8" x-data="{ loginModal: false, activeMenu: '' }">

    {{-- Login modal — muncul saat klik menu yang butuh login warga --}}
    <div x-show="loginModal" x-cloak
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-4"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="loginModal = false"></div>

        {{-- Modal card --}}
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 z-10"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="text-center mb-5">
                <div class="w-14 h-14 bg-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <span class="text-3xl">🔐</span>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Login Warga Diperlukan</h3>
                <p class="text-sm text-gray-500 mt-1">
                    Akses <strong x-text="activeMenu"></strong> memerlukan login terlebih dahulu.
                </p>
            </div>

            <a href="{{ route('resident.login') }}"
               class="block w-full py-3 bg-brand-600 text-white text-center font-bold rounded-xl
                      hover:bg-brand-700 transition mb-3">
                Masuk Sekarang →
            </a>
            <button @click="loginModal = false"
                    class="block w-full py-2.5 text-sm text-gray-500 hover:text-gray-700 text-center transition">
                Batal
            </button>
        </div>
    </div>

    {{-- Section: Layanan Utama --}}
    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Layanan Utama</p>

    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-8">

        {{-- 1. Internet Gratis — butuh login --}}
        @if(session('resident_id'))
        <a href="{{ route('resident.portal') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 cursor-pointer text-center">
            <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center
                        group-hover:bg-sky-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📶</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Internet<br>Gratis</p>
        </a>
        @else
        <button @click="activeMenu = 'Internet Gratis'; loginModal = true"
                class="menu-card group flex flex-col items-center justify-center gap-2
                       bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                       hover:shadow-lg hover:-translate-y-1 transition-all duration-200 cursor-pointer text-center
                       relative">
            <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center
                        group-hover:bg-sky-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📶</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Internet<br>Gratis</p>
            <span class="absolute top-1.5 right-1.5 text-xs">🔒</span>
        </button>
        @endif

        {{-- 2. Laporan Keuangan — publik --}}
        <a href="{{ route('public.financial') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center
                        group-hover:bg-emerald-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">💰</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Laporan<br>Keuangan</p>
        </a>

        {{-- 3. Pasar Warga — publik --}}
        <a href="{{ route('public.marketplace') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center
                        group-hover:bg-orange-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">🛒</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Pasar<br>Warga</p>
        </a>

        {{-- 4. Aduan & Forum — butuh login --}}
        @if(session('resident_id'))
        <a href="{{ route('public.aduan') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center
                        group-hover:bg-yellow-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📢</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Aduan &<br>Forum</p>
        </a>
        @else
        <button @click="activeMenu = 'Aduan & Forum'; loginModal = true"
                class="menu-card group flex flex-col items-center justify-center gap-2
                       bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                       hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center relative">
            <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center
                        group-hover:bg-yellow-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📢</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Aduan &<br>Forum</p>
            <span class="absolute top-1.5 right-1.5 text-xs">🔒</span>
        </button>
        @endif

        {{-- 5. CCTV — butuh login --}}
        @if(session('resident_id'))
        <a href="{{ route('resident.cctv') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center
                        group-hover:bg-red-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📹</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Pantau<br>CCTV</p>
        </a>
        @else
        <button @click="activeMenu = 'Pantau CCTV'; loginModal = true"
                class="menu-card group flex flex-col items-center justify-center gap-2
                       bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                       hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center relative">
            <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center
                        group-hover:bg-red-500 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📹</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Pantau<br>CCTV</p>
            <span class="absolute top-1.5 right-1.5 text-xs">🔒</span>
        </button>
        @endif

        {{-- 6. Kartu IPL — butuh login --}}
        @if(session('resident_id'))
        <a href="{{ route('resident.kartu-ipl') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-brand-100 flex items-center justify-center
                        group-hover:bg-brand-600 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">🪪</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Kartu<br>IPL</p>
        </a>
        @else
        <button @click="activeMenu = 'Kartu IPL'; loginModal = true"
                class="menu-card group flex flex-col items-center justify-center gap-2
                       bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                       hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center relative">
            <div class="w-12 h-12 rounded-xl bg-brand-100 flex items-center justify-center
                        group-hover:bg-brand-600 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">🪪</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Kartu<br>IPL</p>
            <span class="absolute top-1.5 right-1.5 text-xs">🔒</span>
        </button>
        @endif

        {{-- 7. Login Admin --}}
        <a href="{{ route('admin.login') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center
                        group-hover:bg-gray-700 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">⚙️</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">Login<br>Admin</p>
        </a>

        {{-- 8. Administrasi & Pendataan --}}
        @if(session('resident_id'))
        <a href="{{ route('resident.portal') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-brand-600 rounded-2xl border border-brand-700 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center
                        group-hover:bg-white/30 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📋</span>
            </div>
            <p class="text-xs font-semibold text-white leading-tight">Administrasi<br>& Pendataan</p>
        </a>
        @else
        <a href="{{ route('resident.login') }}"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-brand-600 rounded-2xl border border-brand-700 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center
                        group-hover:bg-white/30 transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">📋</span>
            </div>
            <p class="text-xs font-semibold text-white leading-tight">Administrasi<br>& Pendataan</p>
        </a>
        @endif

    </div>

    {{-- ════════ SECTION: KOMUNITAS (dari DB — Widgets) ════════ --}}
    @if($widgets->count() > 0 || true)
    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Komunitas RT 03</p>
    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3 mb-8">

        {{-- Submenu komunitas dari CommunityWidget DB --}}
        @php
            // Icon mapping berdasarkan kata kunci judul
            $iconMap = [
                'badminton'  => ['icon' => '🏸', 'bg' => 'bg-blue-100',   'hover' => 'group-hover:bg-blue-500'],
                'futsal'     => ['icon' => '⚽', 'bg' => 'bg-green-100',  'hover' => 'group-hover:bg-green-500'],
                'voli'       => ['icon' => '🏐', 'bg' => 'bg-yellow-100', 'hover' => 'group-hover:bg-yellow-500'],
                'basket'     => ['icon' => '🏀', 'bg' => 'bg-orange-100', 'hover' => 'group-hover:bg-orange-500'],
                'senam'      => ['icon' => '🤸', 'bg' => 'bg-pink-100',   'hover' => 'group-hover:bg-pink-500'],
                'whatsapp'   => ['icon' => '💬', 'bg' => 'bg-green-100',  'hover' => 'group-hover:bg-green-500'],
                'wa'         => ['icon' => '💬', 'bg' => 'bg-green-100',  'hover' => 'group-hover:bg-green-500'],
                'grup'       => ['icon' => '👥', 'bg' => 'bg-blue-100',   'hover' => 'group-hover:bg-blue-500'],
                'karang'     => ['icon' => '🌟', 'bg' => 'bg-purple-100', 'hover' => 'group-hover:bg-purple-500'],
                'taruna'     => ['icon' => '🌟', 'bg' => 'bg-purple-100', 'hover' => 'group-hover:bg-purple-500'],
                'arisan'     => ['icon' => '🎉', 'bg' => 'bg-rose-100',   'hover' => 'group-hover:bg-rose-500'],
                'pengajian'  => ['icon' => '📿', 'bg' => 'bg-teal-100',   'hover' => 'group-hover:bg-teal-500'],
                'default'    => ['icon' => '🔗', 'bg' => 'bg-gray-100',   'hover' => 'group-hover:bg-gray-500'],
            ];
            function getWidgetIcon($title, $iconMap) {
                $lower = strtolower($title);
                foreach ($iconMap as $key => $val) {
                    if ($key !== 'default' && str_contains($lower, $key)) return $val;
                }
                return $iconMap['default'];
            }
        @endphp

        @forelse($widgets as $widget)
        @php $wi = getWidgetIcon($widget->title, $iconMap); @endphp
        <a href="{{ $widget->external_link ?: '#' }}"
           target="{{ $widget->external_link ? '_blank' : '_self' }}"
           rel="noopener"
           class="menu-card group flex flex-col items-center justify-center gap-2
                  bg-white rounded-2xl border border-gray-100 shadow-sm p-4
                  hover:shadow-lg hover:-translate-y-1 transition-all duration-200 text-center">
            <div class="w-12 h-12 rounded-xl {{ $wi['bg'] }} flex items-center justify-center
                        {{ $wi['hover'] }} transition-colors">
                <span class="text-2xl group-hover:scale-110 transition-transform">{{ $wi['icon'] }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-700 leading-tight">{{ Str::limit($widget->title, 14) }}</p>
        </a>
        @empty
        {{-- Placeholder jika belum ada widget —tambah via Admin → Widget Homepage --}}
        @php
            $placeholders = [
                ['icon'=>'🏸','label'=>'Badminton','bg'=>'bg-blue-100'],
                ['icon'=>'⚽','label'=>'Futsal','bg'=>'bg-green-100'],
                ['icon'=>'💬','label'=>'Grup WA','bg'=>'bg-emerald-100'],
                ['icon'=>'🌟','label'=>'Karang Taruna','bg'=>'bg-purple-100'],
            ];
        @endphp
        @foreach($placeholders as $p)
        <div class="menu-card flex flex-col items-center justify-center gap-2
                    bg-white rounded-2xl border border-dashed border-gray-200 p-4 text-center opacity-50">
            <div class="w-12 h-12 rounded-xl {{ $p['bg'] }} flex items-center justify-center">
                <span class="text-2xl">{{ $p['icon'] }}</span>
            </div>
            <p class="text-xs font-semibold text-gray-400 leading-tight">{{ $p['label'] }}</p>
        </div>
        @endforeach
        @endforelse
    </div>
    @endif

    {{-- Keterangan kunci --}}
    <div class="flex items-center gap-1.5 justify-center text-xs text-gray-400 mb-8">
        <span>🔒</span>
        <span>Menu dengan ikon kunci memerlukan <strong>Login Warga</strong> terlebih dahulu.</span>
    </div>

</section>

{{-- ════════════════ STRUKTUR PENGURUS ════════════════ --}}
@if($members->count() > 0)
<section class="bg-gray-50 border-t border-gray-100 py-10">
    <div class="max-w-4xl mx-auto px-4">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-5 text-center">
            Pengurus RT 03
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            @foreach($members as $member)
            <div class="text-center bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-4 w-36
                        hover:shadow-md hover:-translate-y-0.5 transition-all">
                @if($member->photo_path)
                <img src="{{ asset('storage/' . $member->photo_path) }}"
                     alt="{{ $member->name }}"
                     class="w-14 h-14 rounded-full object-cover border-4 border-brand-100 mx-auto mb-2">
                @else
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-brand-400 to-brand-600
                            flex items-center justify-center mx-auto mb-2">
                    <span class="text-white text-xl font-bold">{{ strtoupper(substr($member->name, 0, 1)) }}</span>
                </div>
                @endif
                <p class="font-bold text-gray-900 text-xs leading-tight">{{ $member->name }}</p>
                <span class="inline-block mt-1.5 text-xs font-semibold text-brand-700 bg-brand-100 px-2 py-0.5 rounded-full">
                    {{ $member->role }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection

@push('styles')
<style>
.menu-card {
    min-height: 92px;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
}
.menu-card:active {
    transform: scale(0.96);
}
</style>
@endpush
