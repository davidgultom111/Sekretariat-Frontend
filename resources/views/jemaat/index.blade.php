@extends('layouts.app')

@section('title', 'Data Jemaat — Sekretariat GPdI')
@section('page-title', 'Data Jemaat')

@section('content')

<div x-data="{ show: false, deleteId: null, deleteName: '' }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Data Jemaat</h1>
            <p class="text-sm text-gray-500 mt-1">Menampilkan {{ count($members) }} dari {{ number_format($meta['total']) }} jemaat</p>
        </div>
        <a href="{{ route('jemaat.create') }}"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Jemaat
        </a>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <form method="GET" action="{{ route('jemaat.index') }}" class="flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-48">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Cari</label>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama atau ID jemaat..."
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="w-44">
                <label class="block text-xs font-medium text-gray-500 mb-1.5">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Status</option>
                    @foreach(['Aktif', 'Tidak Aktif', 'Dipindahkan'] as $s)
                        <option value="{{ $s }}" {{ ($filters['status'] ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    Cari
                </button>
                @if(!empty($filters['search']) || !empty($filters['status']))
                    <a href="{{ route('jemaat.index') }}" class="px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Tabel --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">ID Jemaat</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Jenis Kelamin</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($members as $i => $m)
                        @php
                            $offset = (($meta['current_page'] - 1) * ($meta['per_page'] ?? 15));
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-gray-500">{{ $offset + $i + 1 }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-gray-700">{{ $m['id_jemaat'] }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $m['nama_lengkap'] }}</td>
                            <td class="px-6 py-4 text-gray-600">{{ $m['jenis_kelamin'] }}</td>
                            <td class="px-6 py-4">
                                @if($m['status_aktif'] === 'Aktif')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-green-100 text-green-700">Aktif</span>
                                @elseif($m['status_aktif'] === 'Tidak Aktif')
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-700">Tidak Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-600">Dipindahkan</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('jemaat.show', $m['id']) }}"
                                       class="text-blue-600 hover:text-blue-800 text-xs font-medium">Detail</a>
                                    <a href="{{ route('jemaat.edit', $m['id']) }}"
                                       class="text-orange-500 hover:text-orange-700 text-xs font-medium">Edit</a>
                                    <button type="button"
                                            @click="show = true; deleteId = {{ $m['id'] }}; deleteName = '{{ addslashes($m['nama_lengkap']) }}'"
                                            class="text-red-500 hover:text-red-700 text-xs font-medium">
                                        Hapus
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-400 text-sm">
                                Tidak ada data jemaat ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($meta['last_page'] > 1)
            <div class="flex items-center justify-between px-6 py-4 border-t border-gray-100">
                <p class="text-xs text-gray-500">
                    Halaman {{ $meta['current_page'] }} dari {{ $meta['last_page'] }}
                </p>
                <div class="flex gap-1">
                    @for($p = 1; $p <= $meta['last_page']; $p++)
                        <a href="{{ route('jemaat.index', array_merge($filters, ['page' => $p])) }}"
                           class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors
                                  {{ $p === $meta['current_page'] ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                            {{ $p }}
                        </a>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    {{-- Modal Hapus --}}
    <div x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="display: none;">
        <div class="absolute inset-0 bg-black/40" @click="show = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-800">Hapus Data Jemaat</h3>
                    <p class="text-sm text-gray-500 mt-0.5">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-6">
                Yakin ingin menghapus data jemaat <strong x-text="deleteName"></strong>?
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Batal
                </button>
                <form :action="'/jemaat/' + deleteId" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
