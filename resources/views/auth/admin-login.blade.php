<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login – Grand Madani 2</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>tailwind.config = { theme: { extend: { colors: { brand: { 600:'#16a34a', 700:'#15803d' } }, fontFamily: { sans: ['Inter','system-ui','sans-serif'] } } } }</script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-brand-900 font-sans flex items-center justify-center p-4">

<div class="w-full max-w-sm">
    {{-- Card --}}
    <div class="bg-white/10 backdrop-blur-md rounded-3xl border border-white/20 shadow-2xl p-8">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-brand-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
            </div>
            <h1 class="text-xl font-bold text-white">Admin Login</h1>
            <p class="text-sm text-white/60 mt-1">Grand Madani 2 – Portal RT</p>
        </div>

        @if($errors->any())
        <div class="mb-4 bg-red-500/20 border border-red-500/40 rounded-xl px-4 py-3 text-sm text-red-200">
            {{ $errors->first() }}
        </div>
        @endif

        <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-white/70 mb-1.5">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" autofocus required
                       placeholder="admin@grandmadani2.com"
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/30 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            <div>
                <label class="block text-xs font-semibold text-white/70 mb-1.5">Password</label>
                <input type="password" name="password" required
                       placeholder="••••••••"
                       class="w-full px-4 py-3 rounded-xl bg-white/10 border border-white/20 text-white placeholder-white/30 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition">
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="remember" id="remember" class="rounded border-white/30">
                <label for="remember" class="text-xs text-white/60">Ingat saya</label>
            </div>
            <button type="submit"
                    class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl hover:bg-brand-700 transition-colors text-sm shadow-lg mt-2">
                🔐 Masuk sebagai Admin
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('public.financial') }}" class="text-xs text-white/40 hover:text-white/60 transition-colors">
                ← Kembali ke Portal Warga
            </a>
        </div>
    </div>
</div>
</body>
</html>
