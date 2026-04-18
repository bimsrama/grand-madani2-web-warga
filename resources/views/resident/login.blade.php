@extends('layouts.app')

@section('title', 'Login Warga – RT 03')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-green-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-600 rounded-2xl shadow-lg mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Portal Warga RT 03</h1>
            <p class="text-gray-500 text-sm mt-1">Grand Madani – Masuk ke akun warga Anda</p>
            <div class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 bg-brand-100 rounded-full">
                <span class="text-xs font-semibold text-brand-700">🏘️ RT 03 Exclusive</span>
            </div>
        </div>

        {{-- Login Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8" x-data="loginForm()">

            <form id="login-form" action="{{ route('resident.login.post') }}" method="POST">
                @csrf

                {{-- Step 1: Select House --}}
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🏠 Blok / Nomor Rumah
                    </label>
                    <select name="number" id="resident-select"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm
                                   focus:ring-2 focus:ring-brand-500 focus:border-transparent transition
                                   bg-white text-gray-900"
                            @change="onResidentChange"
                            x-model="selectedNumber">
                        <option value="">-- Pilih Blok / Nomor Rumah --</option>
                        @foreach($residents->groupBy('block') as $block => $group)
                            <optgroup label="Blok {{ $block }}">
                                @foreach($group as $resident)
                                    <option value="{{ $resident->number }}"
                                            data-block="{{ $resident->block }}"
                                            {{ old('number') == $resident->number ? 'selected' : '' }}>
                                        Blok {{ $resident->block }} / No. {{ $resident->number }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <input type="hidden" name="block" x-model="selectedBlock">
                    @error('block') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Masked WA Display (appears after selecting house) --}}
                <div x-show="maskedWa" x-cloak
                     class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <p class="text-xs font-semibold text-green-700 mb-1">📱 Nomor WhatsApp Terdaftar:</p>
                    <p class="text-sm font-mono font-bold text-gray-900" x-text="maskedWa"></p>
                </div>

                {{-- Loading indicator --}}
                <div x-show="loading" x-cloak class="mb-4 flex items-center gap-2 text-sm text-gray-500">
                    <svg class="animate-spin w-4 h-4 text-brand-600" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Mengambil data...
                </div>

                {{-- Step 2: Password field (dynamic label) --}}
                <div x-show="selectedNumber" x-cloak class="mb-6"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        🔐 <span x-text="hasPin
                            ? 'Masukkan PIN 6 Digit Anda'
                            : 'Masukkan 4 Digit Terakhir No. WhatsApp'"></span>
                    </label>
                    <input type="password"
                           name="password"
                           id="password-input"
                           :maxlength="hasPin ? 6 : 4"
                           :placeholder="hasPin ? 'PIN 6 digit' : 'Contoh: 4567'"
                           class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm text-center
                                  tracking-widest text-2xl font-mono
                                  focus:ring-2 focus:ring-brand-500 focus:border-transparent transition"
                           inputmode="numeric" pattern="[0-9]*"
                           autocomplete="off" required>

                    {{-- Hint text --}}
                    <p class="text-xs text-gray-500 mt-2 text-center" x-show="!hasPin">
                        💡 Jika belum pernah login, masukkan 4 digit terakhir nomor WA Anda.
                    </p>
                    <p class="text-xs text-gray-500 mt-2 text-center" x-show="hasPin" x-cloak>
                        🔐 Masukkan PIN 6 digit yang Anda buat sebelumnya.
                    </p>

                    @error('password')
                        <p class="text-red-500 text-xs mt-2 text-center">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit --}}
                <button type="submit"
                        x-show="selectedNumber" x-cloak
                        class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl
                               hover:bg-brand-700 active:scale-95 transition-all duration-200 shadow-md
                               disabled:opacity-50"
                        :disabled="loading">
                    Masuk ke Portal Warga →
                </button>
            </form>

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-xl">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
            @endif

        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            Masalah login? Hubungi pengurus RT 03.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function loginForm() {
    return {
        selectedNumber: '{{ old("number", "") }}',
        selectedBlock: '{{ old("block", "") }}',
        maskedWa: '',
        hasPin: false,
        loading: false,

        onResidentChange(event) {
            const option = event.target.selectedOptions[0];
            this.selectedBlock = option?.dataset?.block || '';
            this.maskedWa = '';

            if (!this.selectedNumber || !this.selectedBlock) return;

            this.loading = true;

            fetch(`/warga/ajax/wa-mask?block=${encodeURIComponent(this.selectedBlock)}&number=${encodeURIComponent(this.selectedNumber)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.masked_wa) {
                        this.maskedWa = data.masked_wa;
                        this.hasPin   = data.has_pin;
                    } else {
                        this.maskedWa = '';
                    }
                })
                .catch(() => { this.maskedWa = ''; })
                .finally(() => { this.loading = false; });
        }
    }
}
</script>
@endpush
