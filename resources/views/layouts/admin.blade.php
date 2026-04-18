<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') – RT 03 Grand Madani</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:'#f0fdf4',100:'#dcfce7',200:'#bbf7d0',
                            400:'#4ade80',500:'#22c55e',600:'#16a34a',
                            700:'#15803d',800:'#166534',900:'#14532d',
                        }
                    },
                    fontFamily: { sans: ['Inter','system-ui','sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak]{display:none!important}
        /* Compensate for bottom nav on mobile */
        @media (max-width: 767px) {
            .main-content { padding-bottom: 72px !important; }
        }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-100 font-sans antialiased"
      x-data="{ sidebarOpen: false, desktopSidebarOpen: true }">

{{-- ══════════════════════════════════════════════════
     MOBILE: Sidebar Overlay Drawer (slide in from left)
     ══════════════════════════════════════════════════ --}}
<div class="md:hidden">
    {{-- Backdrop --}}
    <div x-show="sidebarOpen" x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 bg-black/60 z-40 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    </div>

    {{-- Drawer --}}
    <div x-show="sidebarOpen" x-cloak
         class="fixed top-0 left-0 h-full w-72 bg-gray-900 z-50 flex flex-col shadow-2xl"
         x-transition:enter="transition ease-out duration-250"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full">

        {{-- Drawer Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-bold text-white">Grand Madani</p>
                    <p class="text-xs text-brand-400">Admin RT 03</p>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="text-gray-400 hover:text-white p-1">✕</button>
        </div>

        {{-- Drawer Nav --}}
        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
            @php
            $navItems = [
                ['route'=>'admin.ipl.index',          'icon'=>'📅','label'=>'IPL Warga'],
                ['route'=>'admin.finance.index',       'icon'=>'💰','label'=>'Keuangan RT'],
                ['route'=>'admin.residents.index',     'icon'=>'👥','label'=>'Data Warga'],
                ['route'=>'admin.data-requests.index', 'icon'=>'📝','label'=>'Perubahan Data'],
                ['route'=>'admin.secretary.index',     'icon'=>'📄','label'=>'Surat RT'],
                ['route'=>'admin.cctv.index',          'icon'=>'📹','label'=>'Kelola CCTV'],
                ['route'=>'admin.widgets.index',       'icon'=>'🔗','label'=>'Widget Homepage'],
                ['route'=>'admin.board.index',         'icon'=>'👤','label'=>'Struktur Pengurus'],
                ['route'=>'admin.wa-settings',         'icon'=>'📱','label'=>'WA Bot Settings'],
            ];
            @endphp
            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}" @click="sidebarOpen = false"
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs($item['route']) ? 'bg-brand-600 text-white' : 'text-gray-300 hover:text-white hover:bg-gray-800' }}">
                <span class="text-lg flex-shrink-0">{{ $item['icon'] }}</span>
                <span>{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Drawer Footer --}}
        <div class="px-3 py-4 border-t border-gray-800">
            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-gray-400 hover:text-white hover:bg-gray-800 transition mb-2">
                <span>🌐</span><span>Lihat Portal Publik</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm text-red-400 hover:bg-red-900/30 transition">
                    <span>🚪</span><span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     DESKTOP LAYOUT: Sidebar + Main
     ══════════════════════════════════════════════════ --}}
<div class="flex h-screen overflow-hidden">

    {{-- Desktop Sidebar (hidden on mobile) --}}
    <aside class="hidden md:flex flex-col flex-shrink-0 bg-gray-900 transition-all duration-300 ease-in-out overflow-hidden"
           :class="desktopSidebarOpen ? 'w-64' : 'w-16'">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-4 py-5 border-b border-gray-800 flex-shrink-0">
            <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
            </div>
            <div x-show="desktopSidebarOpen" class="min-w-0 overflow-hidden">
                <p class="text-xs font-bold text-white truncate">Grand Madani</p>
                <p class="text-xs text-brand-400 truncate">Admin Panel – RT 03</p>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="flex-1 px-2 py-4 space-y-1 overflow-y-auto">
            @foreach($navItems as $item)
            <a href="{{ route($item['route']) }}"
               class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                      {{ request()->routeIs($item['route']) ? 'bg-brand-600 text-white' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}"
               :title="!desktopSidebarOpen ? '{{ $item['label'] }}' : ''">
                <span class="text-base flex-shrink-0">{{ $item['icon'] }}</span>
                <span x-show="desktopSidebarOpen" class="truncate">{{ $item['label'] }}</span>
            </a>
            @endforeach
        </nav>

        {{-- Collapse + Logout --}}
        <div class="px-2 py-4 border-t border-gray-800 space-y-1.5 flex-shrink-0">
            <button @click="desktopSidebarOpen = !desktopSidebarOpen"
                    class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-gray-400 hover:bg-gray-800 hover:text-white transition text-sm">
                <span class="text-base flex-shrink-0" x-text="desktopSidebarOpen ? '◀' : '▶'"></span>
                <span x-show="desktopSidebarOpen" class="text-xs">Tutup</span>
            </button>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2 rounded-xl text-red-400 hover:bg-red-900/30 transition text-sm">
                    <span class="text-base flex-shrink-0">🚪</span>
                    <span x-show="desktopSidebarOpen" class="text-xs">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- ══ MAIN AREA ══ --}}
    <div class="flex-1 flex flex-col overflow-hidden min-w-0">

        {{-- ── TOP HEADER ── --}}
        <header class="bg-white border-b border-gray-200 px-4 sm:px-6 py-3 flex items-center gap-3 flex-shrink-0">

            {{-- Mobile: hamburger --}}
            <button class="md:hidden p-2 -ml-1 rounded-xl hover:bg-gray-100 transition-colors flex-shrink-0"
                    @click="sidebarOpen = true">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            {{-- Page Title --}}
            <div class="flex-1 min-w-0">
                <h1 class="text-base sm:text-lg font-bold text-gray-900 truncate">
                    @yield('page-title', 'Dashboard')
                </h1>
                <p class="text-xs text-gray-400 hidden sm:block">
                    @yield('page-subtitle', 'Admin Panel RT 03 Grand Madani')
                </p>
            </div>

            {{-- Right actions --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <a href="{{ route('home') }}" target="_blank"
                   class="hidden sm:flex items-center gap-1 text-xs text-brand-600 hover:underline">
                    🌐 Portal
                </a>
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-2.5 py-1.5">
                    <div class="w-6 h-6 bg-brand-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold">A</span>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-xs font-semibold text-gray-900 leading-none">{{ auth()->user()?->name ?? 'Admin' }}</p>
                        <p class="text-xs text-gray-400 leading-none mt-0.5">RT 03</p>
                    </div>
                </div>
            </div>
        </header>

        {{-- Flash Messages --}}
        @if(session('success') || session('warning') || session('info') || session('error'))
        <div class="px-4 sm:px-6 pt-3">
            @foreach(['success'=>'green','warning'=>'yellow','info'=>'blue','error'=>'red'] as $type=>$color)
                @if(session($type))
                <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,6000)"
                     class="flex items-start gap-3 px-4 py-3 mb-2 rounded-xl
                            bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 text-sm font-medium">
                    <span class="flex-1">{{ session($type) }}</span>
                    <button @click="show=false" class="text-{{ $color }}-400 hover:text-{{ $color }}-700 flex-shrink-0 leading-none">✕</button>
                </div>
                @endif
            @endforeach
        </div>
        @endif

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 main-content">
            @yield('content')
        </main>

        {{-- ── DESKTOP FOOTER ── --}}
        <footer class="hidden md:block bg-white border-t border-gray-200 px-6 py-3 flex-shrink-0">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-1.5">
                <div class="flex items-center gap-2">
                    @if($dbConnected ?? false)
                        <span class="text-sm">🟢</span>
                        <span class="text-xs text-green-600 font-medium">Connect to database: Berhasil</span>
                    @else
                        <span class="text-sm">🔴</span>
                        <span class="text-xs text-red-600 font-medium">Connect to database: Gagal</span>
                    @endif
                </div>
                <p class="text-xs text-gray-400 text-center">
                    Silakan laporkan ke pengurus jika ada ketidaksesuaian data atau kendala pada sistem.
                </p>
                <p class="text-xs text-gray-400">Admin RT 03 &bull; {{ date('Y') }}</p>
            </div>
        </footer>
    </div>
</div>

{{-- ══════════════════════════════════════════════════
     MOBILE: Bottom Navigation Bar
     ══════════════════════════════════════════════════ --}}
<nav class="md:hidden fixed bottom-0 inset-x-0 bg-white border-t border-gray-200 z-30 safe-area-pb">
    <div class="flex items-center justify-around px-1 py-1.5">

        {{-- IPL --}}
        <a href="{{ route('admin.ipl.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition
                  {{ request()->routeIs('admin.ipl.index') ? 'text-brand-600' : 'text-gray-400' }}">
            <span class="text-xl">📅</span>
            <span class="text-xs font-medium">IPL</span>
        </a>

        {{-- Keuangan --}}
        <a href="{{ route('admin.finance.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition
                  {{ request()->routeIs('admin.finance.index') ? 'text-brand-600' : 'text-gray-400' }}">
            <span class="text-xl">💰</span>
            <span class="text-xs font-medium">Keuangan</span>
        </a>

        {{-- Data Warga --}}
        <a href="{{ route('admin.residents.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition
                  {{ request()->routeIs('admin.residents.*') || request()->routeIs('admin.data-requests.*') ? 'text-brand-600' : 'text-gray-400' }}">
            <span class="text-xl">👥</span>
            <span class="text-xs font-medium">Warga</span>
        </a>

        {{-- CCTV --}}
        <a href="{{ route('admin.cctv.index') }}"
           class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition
                  {{ request()->routeIs('admin.cctv.*') ? 'text-brand-600' : 'text-gray-400' }}">
            <span class="text-xl">📹</span>
            <span class="text-xs font-medium">CCTV</span>
        </a>

        {{-- Menu Lainnya (open drawer) --}}
        <button @click="sidebarOpen = true"
                class="flex flex-col items-center gap-0.5 px-3 py-1.5 rounded-xl transition text-gray-400">
            <span class="text-xl">☰</span>
            <span class="text-xs font-medium">Menu</span>
        </button>
    </div>
</nav>

{{-- Mobile DB status strip (above bottom nav) --}}
<div class="md:hidden fixed bottom-16 inset-x-0 z-20 pointer-events-none">
    <div class="flex items-center justify-center gap-1.5 py-1 bg-white/80 backdrop-blur-sm border-t border-gray-100 text-xs">
        @if($dbConnected ?? false)
            <span>🟢</span>
            <span class="text-green-600 font-medium">DB: Berhasil</span>
        @else
            <span>🔴</span>
            <span class="text-red-500 font-medium">DB: Gagal</span>
        @endif
    </div>
</div>

@stack('scripts')
</body>
</html>
