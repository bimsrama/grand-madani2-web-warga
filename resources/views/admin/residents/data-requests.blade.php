@extends('layouts.admin')

@section('title', 'Permintaan Perubahan Data')
@section('page-title', '📝 Permintaan Perubahan Data Warga')
@section('page-subtitle', 'Tinjau dan setujui/tolak perubahan data yang diajukan warga')

@section('content')

@php
    $pending  = $requests->where('status', 'pending');
    $resolved = $requests->whereIn('status', ['approved', 'rejected']);
@endphp

{{-- Pending Requests --}}
<div class="mb-8">
    <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
        <span class="text-yellow-500">⏳</span>
        Menunggu Persetujuan
        @if($pending->count() > 0)
        <span class="ml-1 px-2 py-0.5 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">{{ $pending->count() }}</span>
        @endif
    </h2>

    @forelse($pending as $req)
    <div class="bg-white rounded-2xl shadow-sm border border-yellow-200 overflow-hidden mb-4">
        <div class="px-5 py-4 border-b border-gray-100 flex items-start justify-between">
            <div>
                <p class="font-bold text-gray-900">{{ $req->resident->display_name }}</p>
                <p class="text-xs text-gray-400 mt-0.5">Diajukan {{ $req->created_at->diffForHumans() }}</p>
            </div>
            <span class="text-xs bg-yellow-100 text-yellow-700 font-semibold px-2 py-1 rounded-full">Menunggu</span>
        </div>
        <div class="px-5 py-4">
            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                {{-- Name change --}}
                @if($req->requested_name && $req->requested_name !== $req->resident->owner_name)
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-gray-500 mb-1">Nama</p>
                    <p class="text-xs text-red-500 line-through">{{ $req->resident->owner_name }}</p>
                    <p class="text-sm font-bold text-green-700">{{ $req->requested_name }}</p>
                </div>
                @endif

                {{-- WA change --}}
                @if($req->requested_wa && $req->requested_wa !== $req->resident->wa_number)
                <div class="bg-gray-50 rounded-xl p-3">
                    <p class="text-xs font-semibold text-gray-500 mb-1">WhatsApp</p>
                    <p class="text-xs text-red-500 line-through">{{ $req->resident->wa_number }}</p>
                    <p class="text-sm font-bold text-green-700">{{ $req->requested_wa }}</p>
                </div>
                @endif

                {{-- Family members --}}
                @if($req->requested_family_members)
                <div class="bg-gray-50 rounded-xl p-3 sm:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 mb-2">Anggota Keluarga Baru</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($req->requested_family_members as $m)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">
                            {{ $m['name'] }}{{ !empty($m['relation']) ? " ({$m['relation']})" : '' }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            @if($req->notes)
            <div class="mb-4 p-3 bg-blue-50 rounded-xl text-xs text-blue-800">
                <span class="font-semibold">Catatan:</span> {{ $req->notes }}
            </div>
            @endif

            <div class="flex gap-2">
                <form action="{{ route('admin.data-requests.approve', $req->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Setujui dan terapkan perubahan ini?')"
                            class="w-full py-2 bg-green-600 text-white text-sm font-semibold rounded-xl hover:bg-green-700 transition">
                        ✅ Setujui & Terapkan
                    </button>
                </form>
                <form action="{{ route('admin.data-requests.reject', $req->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit"
                            onclick="return confirm('Tolak permintaan ini?')"
                            class="w-full py-2 bg-red-50 text-red-600 border border-red-200 text-sm font-semibold rounded-xl hover:bg-red-100 transition">
                        ❌ Tolak
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl border border-dashed border-gray-200 px-6 py-8 text-center">
        <p class="text-green-600 font-semibold">✅ Tidak ada permintaan yang menunggu persetujuan.</p>
    </div>
    @endforelse
</div>

{{-- Resolved --}}
@if($resolved->count() > 0)
<div>
    <h2 class="text-base font-bold text-gray-900 mb-4">📁 Riwayat Perubahan Data</h2>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Warga</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Perubahan</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Ditinjau</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($resolved as $req)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <p class="font-semibold text-gray-900 text-xs">{{ $req->resident->display_name }}</p>
                            <p class="text-xs text-gray-400">{{ $req->created_at->format('d M Y') }}</p>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            @if($req->requested_name) Nama: {{ $req->requested_name }} @endif
                            @if($req->requested_wa) | WA: {{ $req->requested_wa }} @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($req->status === 'approved')
                            <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">✅ Disetujui</span>
                            @else
                            <span class="text-xs bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">❌ Ditolak</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500">
                            {{ $req->reviewed_by }} · {{ $req->reviewed_at?->format('d M Y') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

@endsection
