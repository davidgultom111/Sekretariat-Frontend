@extends('layouts.app')
@section('title', 'Pengumuman — Sekretariat GPdI')
@section('page-title', 'Pengumuman')

@section('content')
<div x-data="{ show: false, deleteId: null, deleteJudul: '' }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Pengumuman Gereja</h1>
            <p class="text-sm text-gray-500 mt-0.5">Pengumuman aktif tampil di bawah banner website</p>
        </div>
        <a href="{{ route('pengumuman.create') }}"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Pengumuman
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider w-8">No</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode Tayang</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($pengumuman as $i => $p)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 text-gray-400">{{ $i + 1 }}</td>
                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-800">{{ $p['judul'] }}</div>
                        <div class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $p['isi'] }}</div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 text-xs">
                        <div>Mulai: {{ \Carbon\Carbon::parse($p['tanggal_mulai'])->format('d M Y') }}</div>
                        @if($p['tanggal_akhir'])
                            <div class="text-gray-400">Berakhir: {{ \Carbon\Carbon::parse($p['tanggal_akhir'])->format('d M Y') }}</div>
                        @else
                            <div class="text-gray-400">Tanpa batas</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($p['aktif'])
                            <span class="inline-block px-2.5 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Aktif</span>
                        @else
                            <span class="inline-block px-2.5 py-1 bg-gray-100 text-gray-500 text-xs font-semibold rounded-full">Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('pengumuman.edit', $p['id']) }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-800 px-3 py-1.5 rounded-lg hover:bg-blue-50 transition-colors">
                                Edit
                            </a>
                            <button @click="show=true; deleteId={{ $p['id'] }}; deleteJudul='{{ addslashes($p['judul']) }}'"
                                    class="text-xs font-medium text-red-500 hover:text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 text-sm">Belum ada pengumuman. Klik "Tambah Pengumuman" untuk memulai.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="show" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="show=false" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
        <div class="relative bg-white rounded-2xl shadow-xl p-6 w-full max-w-sm">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800">Hapus Pengumuman</h3>
                    <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-5">Yakin ingin menghapus pengumuman <strong x-text="deleteJudul"></strong>?</p>
            <div class="flex gap-3">
                <button @click="show=false" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                <form :action="'/pengumuman/' + deleteId" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
