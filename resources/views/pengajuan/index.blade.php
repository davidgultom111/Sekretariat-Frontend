@extends('layouts.app')

@section('title', 'Pengajuan Surat — Sekretariat GPdI')
@section('page-title', 'Pengajuan Surat')

@section('content')

<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Pengajuan Surat</h1>
        <p class="text-sm text-gray-500 mt-1">Surat yang diajukan oleh jemaat melalui aplikasi</p>
    </div>
</div>

{{-- Filter Status --}}
<div class="flex flex-wrap gap-2 mb-6">
    @foreach(['' => 'Semua', 'Dalam Proses' => 'Dalam Proses', 'Disetujui' => 'Disetujui', 'Ditolak' => 'Ditolak'] as $val => $label)
        <a href="{{ route('pengajuan.index', $val ? ['status' => $val] : []) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium border transition-colors
                  {{ $statusFilter === ($val ?: null) || ($val === '' && !$statusFilter)
                     ? 'bg-blue-600 text-white border-blue-600'
                     : 'bg-white text-gray-600 border-gray-200 hover:border-blue-400 hover:text-blue-600' }}">
            {{ $label }}
        </a>
    @endforeach
</div>

{{-- Tabel --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    @if(count($pengajuans) === 0)
        <div class="py-16 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm font-medium">Tidak ada pengajuan{{ $statusFilter ? ' dengan status ' . $statusFilter : '' }}</p>
        </div>
    @else
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jemaat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe Surat</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                    <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($pengajuans as $item)
                    @php
                        $statusClass = match($item['status']) {
                            'Disetujui' => 'bg-green-100 text-green-700',
                            'Ditolak'   => 'bg-red-100 text-red-700',
                            default     => 'bg-yellow-100 text-yellow-700',
                        };
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-800">{{ $item['member']['nama_lengkap'] ?? '—' }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $item['member']['id_jemaat'] ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $item['tipe_surat'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ \Carbon\Carbon::parse($item['created_at'])->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClass }}">
                                {{ $item['status'] }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('pengajuan.show', $item['id']) }}"
                               class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                Detail →
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

@endsection
