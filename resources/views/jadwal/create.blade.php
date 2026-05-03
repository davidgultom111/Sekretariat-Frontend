@extends('layouts.app')
@section('title', 'Tambah Jadwal — Sekretariat GPdI')
@section('page-title', 'Tambah Jadwal')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('jadwal.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Tambah Jadwal Pelayanan</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('jadwal.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="nama_kegiatan" value="{{ old('nama_kegiatan') }}"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nama_kegiatan') border-red-400 @enderror"
                       placeholder="cth: Ibadah Raya Minggu" required>
                @error('nama_kegiatan') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kategori / Label <span class="text-red-500">*</span></label>
                <input type="text" name="kategori" value="{{ old('kategori') }}"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('kategori') border-red-400 @enderror"
                       placeholder="cth: Ibadah" required>
                @error('kategori') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Hari <span class="text-red-500">*</span></label>
                    <select name="hari"
                            class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hari') border-red-400 @enderror"
                            required>
                        <option value="">Pilih hari</option>
                        @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu','Tidak Tetap'] as $hari)
                            <option value="{{ $hari }}" {{ old('hari') === $hari ? 'selected' : '' }}>{{ $hari }}</option>
                        @endforeach
                    </select>
                    @error('hari') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Waktu <span class="text-red-500">*</span></label>
                    <input type="time" name="waktu" value="{{ old('waktu') }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('waktu') border-red-400 @enderror"
                           required>
                    @error('waktu') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="deskripsi" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                          placeholder="Keterangan singkat tentang kegiatan ini...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('jadwal.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>
@endsection
