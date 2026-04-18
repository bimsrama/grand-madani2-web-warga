@extends('layouts.admin')

@section('title', 'Data Warga RT 03')
@section('page-title', '👥 Data Warga RT 03')
@section('page-subtitle', 'Rekap seluruh data warga aktif RT 03 Grand Madani')

@section('content')

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="font-bold text-gray-900">
            Total Warga Aktif: <span class="text-brand-600">{{ $residents->count() }}</span>
        </h2>
        <a href="{{ route('admin.data-requests.index') }}"
           class="px-4 py-2 text-sm font-semibold text-yellow-700 bg-yellow-100 rounded-xl hover:bg-yellow-200 transition">
            📝 Lihat Permintaan Perubahan Data
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Blok / No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Nama Pemilik</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">WhatsApp</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">PIN</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Keluarga</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($residents as $resident)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $loop->iteration }}</td>
                    <td class="px-4 py-3 font-bold text-gray-900">
                        Blok {{ $resident->block }} / {{ $resident->number }}
                    </td>
                    <td class="px-4 py-3 text-gray-900">{{ $resident->owner_name }}</td>
                    <td class="px-4 py-3 text-gray-600 font-mono text-xs">{{ $resident->masked_wa }}</td>
                    <td class="px-4 py-3 text-center">
                        @if($resident->hasPinSet())
                        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">🔐 Set</span>
                        @else
                        <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">Belum</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center text-xs text-gray-500">
                        {{ $resident->family_members ? count($resident->family_members) . ' orang' : '–' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">
                            Aktif
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
