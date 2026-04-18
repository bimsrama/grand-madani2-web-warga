@extends('layouts.app')

@section('title', 'Buat PIN Baru – RT 03')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-emerald-50 flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        {{-- Progress Indicator --}}
        <div class="flex items-center justify-center gap-2 mb-8">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">✓</div>
                <span class="text-xs font-medium text-brand-600">Verifikasi WA</span>
            </div>
            <div class="w-8 h-0.5 bg-brand-300"></div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-brand-600 text-white flex items-center justify-center text-xs font-bold">2</div>
                <span class="text-xs font-bold text-brand-700">Buat PIN</span>
            </div>
            <div class="w-8 h-0.5 bg-gray-200"></div>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-xs font-bold">3</div>
                <span class="text-xs text-gray-400">Masuk Portal</span>
            </div>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8" x-data="pinForm()">
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center w-14 h-14 bg-brand-100 rounded-2xl mb-3">
                    <span class="text-3xl">🔐</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900">Buat PIN 6 Digit</h1>
                <p class="text-sm text-gray-500 mt-1">PIN ini akan digunakan untuk login berikutnya.</p>
            </div>

            @if(session('info'))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('resident.set-pin.post') }}" method="POST">
                @csrf

                {{-- PIN Input --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">PIN Baru (6 digit angka)</label>
                    <div class="flex justify-center gap-3 mb-2">
                        @for($i = 0; $i < 6; $i++)
                        <input type="password"
                               class="pin-box w-10 h-12 border-2 border-gray-200 rounded-xl text-center text-xl font-mono font-bold
                                      focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition"
                               maxlength="1" inputmode="numeric" pattern="[0-9]"
                               data-index="{{ $i }}" autocomplete="off">
                        @endfor
                    </div>
                    <input type="hidden" name="pin" x-model="pin">
                    @error('pin') <p class="text-red-500 text-xs text-center mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- PIN Confirmation --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi PIN</label>
                    <div class="flex justify-center gap-3 mb-2">
                        @for($i = 0; $i < 6; $i++)
                        <input type="password"
                               class="pin-confirm-box w-10 h-12 border-2 border-gray-200 rounded-xl text-center text-xl font-mono font-bold
                                      focus:border-brand-500 focus:ring-2 focus:ring-brand-200 transition"
                               maxlength="1" inputmode="numeric" pattern="[0-9]"
                               data-index="{{ $i }}" autocomplete="off">
                        @endfor
                    </div>
                    <input type="hidden" name="pin_confirmation" x-model="pinConfirm">
                    @error('pin_confirmation') <p class="text-red-500 text-xs text-center mt-1">{{ $message }}</p> @enderror

                    {{-- Match indicator --}}
                    <p x-show="pin.length === 6 && pinConfirm.length === 6" x-cloak
                       :class="pin === pinConfirm ? 'text-green-600' : 'text-red-500'"
                       class="text-xs text-center mt-2 font-medium"
                       x-text="pin === pinConfirm ? '✅ PIN cocok!' : '❌ PIN tidak cocok'"></p>
                </div>

                <button type="submit"
                        :disabled="pin.length < 6 || pinConfirm.length < 6 || pin !== pinConfirm"
                        class="w-full py-3 bg-brand-600 text-white font-semibold rounded-xl
                               hover:bg-brand-700 transition-all duration-200 shadow-md
                               disabled:opacity-40 disabled:cursor-not-allowed">
                    Simpan PIN & Masuk Portal →
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            ⚠️ Jangan bagikan PIN Anda kepada siapapun.
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
function pinForm() {
    return {
        pin: '',
        pinConfirm: '',

        init() {
            this.$nextTick(() => {
                this.initPinBoxes('.pin-box', () => {
                    this.pin = Array.from(document.querySelectorAll('.pin-box')).map(el => el.value).join('');
                });
                this.initPinBoxes('.pin-confirm-box', () => {
                    this.pinConfirm = Array.from(document.querySelectorAll('.pin-confirm-box')).map(el => el.value).join('');
                });
            });
        },

        initPinBoxes(selector, onUpdate) {
            const boxes = document.querySelectorAll(selector);
            boxes.forEach((box, idx) => {
                box.addEventListener('input', (e) => {
                    const val = e.target.value.replace(/\D/g, '');
                    e.target.value = val.slice(-1);
                    if (val && idx < boxes.length - 1) boxes[idx + 1].focus();
                    onUpdate();
                });
                box.addEventListener('keydown', (e) => {
                    if (e.key === 'Backspace' && !e.target.value && idx > 0) {
                        boxes[idx - 1].focus();
                        boxes[idx - 1].value = '';
                        onUpdate();
                    }
                });
            });
        }
    }
}
</script>
@endpush
