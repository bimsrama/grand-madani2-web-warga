<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Portal Warga Grand Madani RT 03 – Transparansi, Komunikasi & Layanan RT">
    <title>@yield('title', 'RT 03 Grand Madani') – Portal Warga</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#f0fdf4', 100: '#dcfce7', 200: '#bbf7d0',
                            300: '#86efac', 400: '#4ade80', 500: '#22c55e',
                            600: '#16a34a', 700: '#15803d', 800: '#166534',
                            900: '#14532d',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">

{{-- ═══════════════════════════ TOP BAR (slim) ═══════════════════════════ --}}
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm border-b border-gray-100 shadow-sm">
    <div class="max-w-4xl mx-auto px-4 h-13 flex items-center justify-between py-2.5">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center shadow-sm">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900 leading-none">RT 03 Grand Madani</p>
                <p class="text-xs text-brand-600 leading-none mt-0.5">Portal Warga Digital</p>
            </div>
        </a>

        {{-- Right: Status warga --}}
        <div class="flex items-center gap-2">
            @if(session('resident_id'))
                <div class="flex items-center gap-1.5 bg-brand-50 border border-brand-200
                            rounded-full px-3 py-1 text-xs font-semibold text-brand-700">
                    <span class="w-1.5 h-1.5 bg-brand-500 rounded-full animate-pulse"></span>
                    {{ session('resident_name') }}
                </div>
                <form action="{{ route('resident.logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="text-xs text-red-400 hover:text-red-600 px-2 py-1 rounded-lg hover:bg-red-50 transition">
                        Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('resident.login') }}"
                   class="text-xs font-semibold text-brand-600 border border-brand-300 hover:bg-brand-50
                          px-3 py-1.5 rounded-full transition">
                    🔐 Login Warga
                </a>
            @endif
        </div>
    </div>
</header>

{{-- ═══════════════════════════ FLASH MESSAGES ═══════════════════════════ --}}
@if(session('success') || session('warning') || session('info') || session('error'))
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
    @foreach(['success' => 'green', 'warning' => 'yellow', 'info' => 'blue', 'error' => 'red'] as $type => $color)
        @if(session($type))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="flex items-center gap-3 px-4 py-3 mb-3 rounded-xl bg-{{ $color }}-50 border border-{{ $color }}-200 text-{{ $color }}-800 text-sm font-medium">
            <span>{{ session($type) }}</span>
            <button @click="show = false" class="ml-auto text-{{ $color }}-500 hover:text-{{ $color }}-700">✕</button>
        </div>
        @endif
    @endforeach
</div>
@endif

{{-- ═══════════════════════════ MAIN CONTENT ═══════════════════════════ --}}
<main>
    @yield('content')
</main>

{{-- ═══════════════════════════ FOOTER ═══════════════════════════ --}}
<footer class="mt-16 bg-gray-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

        <div class="grid md:grid-cols-3 gap-8 mb-8">
            {{-- Brand --}}
            <div>
                <div class="flex items-center gap-2.5 mb-3">
                    <div class="w-8 h-8 bg-brand-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold">Grand Madani – RT 03</p>
                        <p class="text-xs text-gray-400">Portal Warga Digital</p>
                    </div>
                </div>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Platform digital untuk transparansi, komunikasi, dan pelayanan warga RT 03 Grand Madani.
                </p>
            </div>

            {{-- Quick Links --}}
            <div>
                <p class="text-sm font-semibold text-gray-200 mb-3">Navigasi</p>
                <div class="space-y-1.5">
                    <a href="{{ route('home') }}" class="block text-xs text-gray-400 hover:text-white transition-colors">🏠 Beranda</a>
                    <a href="{{ route('public.financial') }}" class="block text-xs text-gray-400 hover:text-white transition-colors">💰 Laporan Keuangan</a>
                    <a href="{{ route('resident.login') }}" class="block text-xs text-gray-400 hover:text-white transition-colors">🔐 Login Warga</a>
                    <a href="{{ route('public.aduan') }}" class="block text-xs text-gray-400 hover:text-white transition-colors">📢 Aduan & Forum</a>
                </div>
            </div>

            {{-- Status --}}
            <div>
                <p class="text-sm font-semibold text-gray-200 mb-3">Status Sistem</p>
                {{-- DB Status Indicator (dynamic via AppServiceProvider) --}}
                <div class="flex items-center gap-2 mb-2">
                    @if($dbConnected ?? false)
                        <span class="text-base leading-none">🟢</span>
                        <span class="text-xs text-green-400 font-medium">Connect to database: Berhasil</span>
                    @else
                        <span class="text-base leading-none">🔴</span>
                        <span class="text-xs text-red-400 font-medium">Connect to database: Gagal</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500">
                    Portal RT 03 &bull; {{ date('Y') }}
                </p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6">
            {{-- Required disclaimer text --}}
            <p class="text-xs text-gray-400 text-center leading-relaxed">
                Silakan laporkan ke pengurus jika ada ketidaksesuaian data atau kendala pada sistem.
            </p>
            <p class="text-xs text-gray-600 text-center mt-2">
                &copy; {{ date('Y') }} Pengurus RT 03 Grand Madani. Dikelola dengan 💚 untuk warga.
            </p>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
