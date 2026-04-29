@extends('layouts.app')

@section('title', 'Dashboard — Sekretariat GPdI')
@section('page-title', 'Dashboard')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">Selamat datang, {{ session('admin_name') }}!</p>
</div>

{{-- Stat Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Jemaat</p>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalJemaat) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center gap-4">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Surat</p>
            <p class="text-3xl font-bold text-gray-800">{{ number_format($totalSurat) }}</p>
        </div>
    </div>
</div>

{{-- Surat Terbaru --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-100">
    <div class="flex items-center justify-between p-6 border-b border-gray-100">
        <h2 class="font-semibold text-gray-800">Surat Terbaru</h2>
        <a href="{{ route('surat.index') }}" class="text-sm text-blue-600 hover:text-blue-800">Lihat semua →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 text-left">
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nomor Surat</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe Surat</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Jemaat</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($suratTerbaru as $i => $surat)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-gray-500">{{ $i + 1 }}</td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-700">{{ $surat['nomor_surat'] }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $surat['tipe_surat'] }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $surat['member']['nama_lengkap'] ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ \Carbon\Carbon::parse($surat['tanggal_surat'])->format('d M Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('surat.show', $surat['id']) }}"
                               class="text-blue-600 hover:text-blue-800 font-medium text-xs">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400 text-sm">Belum ada surat</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
