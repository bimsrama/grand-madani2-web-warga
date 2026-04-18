@extends('layouts.admin')

@section('title', 'IPL Warga')
@section('page-title', '📅 IPL Warga RT 03')
@section('page-subtitle', 'Kelola status pembayaran IPL per rumah')

@section('content')

{{-- Year Filter --}}
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        @foreach(range(now()->year - 1, now()->year + 1) as $y)
        <a href="?year={{ $y }}"
           class="px-4 py-2 rounded-xl text-sm font-semibold transition
                  {{ $y == $year ? 'bg-brand-600 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' }}">
            {{ $y }}
        </a>
        @endforeach
    </div>
    <div class="text-sm text-gray-500">
        Total Warga: <strong>{{ $residents->count() }}</strong>
    </div>
</div>

{{-- IPL Grid --}}
@php
    $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-xs">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3 text-left font-semibold text-gray-700 whitespace-nowrap sticky left-0 bg-gray-50 z-10">Rumah</th>
                    @foreach($monthNames as $mn)
                    <th class="px-2 py-3 text-center font-semibold text-gray-600 min-w-[48px]">{{ $mn }}</th>
                    @endforeach
                    <th class="px-4 py-3 text-right font-semibold text-gray-600">Lunas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($residents as $resident)
                @php $resTx = $rawTx->get($resident->id, collect()); @endphp
                <tr class="hover:bg-gray-50 group" x-data="{}">
                    <td class="px-4 py-3 sticky left-0 bg-white group-hover:bg-gray-50 z-10 whitespace-nowrap">
                        <p class="font-semibold text-gray-900">Blok {{ $resident->block }}/{{ $resident->number }}</p>
                        <p class="text-gray-400 text-xs truncate max-w-[120px]">{{ $resident->owner_name }}</p>
                    </td>
                    @for($m = 1; $m <= 12; $m++)
                    @php
                        $tx = $resTx->get($m);
                        $isLunas = $tx && $tx->status === 'lunas';
                    @endphp
                    <td class="px-1 py-2 text-center" x-data="{ open: false }">
                        {{-- Status badge / trigger --}}
                        <button @click="open = true"
                                class="w-10 h-8 rounded-lg text-base transition hover:scale-110 flex items-center justify-center mx-auto
                                       {{ $isLunas ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-50 text-red-400 hover:bg-red-100' }}">
                            {{ $isLunas ? '✅' : '○' }}
                        </button>

                        {{-- Modal: Payment input --}}
                        <div x-show="open" x-cloak @click.self="open = false"
                             class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                            <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6"
                                 @click.stop
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100">

                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="font-bold text-gray-900 text-sm">
                                        IPL {{ ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'][$m-1] }} {{ $year }}<br>
                                        <span class="text-gray-500 font-normal">{{ $resident->display_name }}</span>
                                    </h3>
                                    <button @click="open = false" class="text-gray-400 hover:text-gray-700 text-xl leading-none">✕</button>
                                </div>

                                <form action="{{ route('admin.ipl.update', [$resident->id, $m, $year]) }}" method="POST"
                                      x-data="breakdownForm()">
                                    @csrf
                                    @method('POST')

                                    {{-- Status --}}
                                    <div class="mb-4">
                                        <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                                        <select name="status" x-model="status"
                                                class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500">
                                            <option value="lunas" {{ $isLunas ? 'selected' : '' }}>✅ Lunas</option>
                                            <option value="belum" {{ !$isLunas ? 'selected' : '' }}>❌ Belum Bayar</option>
                                        </select>
                                    </div>

                                    {{-- Breakdown (shown only when lunas) --}}
                                    <div x-show="status === 'lunas'" class="space-y-2.5 mb-4">
                                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Rincian Pembayaran</p>

                                        @php
                                            $breakdowns = [
                                                ['name' => 'biaya_sampah',   'label' => '🗑️ Biaya Sampah'],
                                                ['name' => 'biaya_keamanan', 'label' => '🛡️ Biaya Keamanan'],
                                                ['name' => 'kas_rt',         'label' => '🏛️ Kas RT'],
                                                ['name' => 'dana_sosial',     'label' => '❤️ Dana Sosial'],
                                                ['name' => 'kas_rw',         'label' => '🏘️ Kas RW'],
                                            ];
                                        @endphp
                                        @foreach($breakdowns as $bd)
                                        <div class="flex items-center gap-2">
                                            <label class="text-xs text-gray-600 w-32 flex-shrink-0">{{ $bd['label'] }}</label>
                                            <div class="flex-1 relative">
                                                <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs">Rp</span>
                                                <input type="number" name="{{ $bd['name'] }}"
                                                       x-model="breakdown.{{ $bd['name'] }}"
                                                       @input="calcTotal"
                                                       value="{{ $tx?->{$bd['name']} ?? 0 }}"
                                                       min="0" step="1000"
                                                       class="w-full pl-7 pr-2 py-1.5 border border-gray-200 rounded-lg text-xs text-right
                                                              focus:ring-1 focus:ring-brand-400">
                                            </div>
                                        </div>
                                        @endforeach

                                        {{-- Total --}}
                                        <div class="flex items-center justify-between bg-brand-50 border border-brand-200 rounded-xl px-3 py-2">
                                            <span class="text-xs font-bold text-brand-800">Total</span>
                                            <span class="text-sm font-extrabold text-brand-700">
                                                Rp <span x-text="total.toLocaleString('id-ID')">0</span>
                                            </span>
                                        </div>
                                    </div>

                                    <button type="submit"
                                            class="w-full py-2.5 bg-brand-600 text-white font-semibold rounded-xl
                                                   hover:bg-brand-700 transition text-sm shadow-sm">
                                        Simpan & Kirim Notif WA
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>
                    @endfor
                    @php $lunasCount = $resTx->filter(fn($t) => $t->status === 'lunas')->count(); @endphp
                    <td class="px-4 py-3 text-right">
                        <span class="text-xs font-bold {{ $lunasCount == 12 ? 'text-green-600' : ($lunasCount > 6 ? 'text-yellow-600' : 'text-red-500') }}">
                            {{ $lunasCount }}/12
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
function breakdownForm() {
    return {
        status: 'lunas',
        total: 0,
        breakdown: {
            biaya_sampah: 0,
            biaya_keamanan: 0,
            kas_rt: 0,
            dana_sosial: 0,
            kas_rw: 0,
        },
        calcTotal() {
            this.total = Object.values(this.breakdown).reduce((sum, v) => sum + (parseFloat(v) || 0), 0);
        }
    }
}
</script>
@endpush
