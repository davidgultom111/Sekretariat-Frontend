@extends('layouts.app')
@section('title', 'Edit Pengumuman — Sekretariat GPdI')
@section('page-title', 'Edit Pengumuman')

@section('content')
<div class="max-w-2xl">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pengumuman.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Edit Pengumuman</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('pengumuman.update', $pengumuman['id']) }}" method="POST" class="space-y-5">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Pengumuman <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $pengumuman['judul']) }}"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('judul') border-red-400 @enderror"
                       required>
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Isi Pengumuman <span class="text-red-500">*</span></label>
                <textarea name="isi" rows="5"
                          class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('isi') border-red-400 @enderror"
                          required>{{ old('isi', $pengumuman['isi']) }}</textarea>
                @error('isi') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_mulai"
                           value="{{ old('tanggal_mulai', \Carbon\Carbon::parse($pengumuman['tanggal_mulai'])->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_akhir"
                           value="{{ old('tanggal_akhir', $pengumuman['tanggal_akhir'] ? \Carbon\Carbon::parse($pengumuman['tanggal_akhir'])->format('Y-m-d') : '') }}"
                           class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan jika tanpa batas waktu</p>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('pengumuman.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
