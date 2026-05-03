@extends('layouts.app')
@section('title', 'Tambah Foto — Sekretariat GPdI')
@section('page-title', 'Tambah Foto Galeri')

@section('content')
<div class="max-w-2xl" x-data="{ preview: null }">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('galeri.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Tambah Foto Galeri</h1>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form action="{{ route('galeri.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Preview & Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Foto <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-200 rounded-xl overflow-hidden">
                    <div x-show="!preview" class="p-10 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-sm text-gray-400">JPG, PNG, atau WebP · Maks. 5 MB</p>
                    </div>
                    <img x-show="preview" :src="preview" x-cloak class="w-full max-h-72 object-contain bg-gray-50">
                </div>
                <label class="mt-3 flex items-center gap-2 cursor-pointer w-fit">
                    <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="sr-only"
                           @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null"
                           required>
                    <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        Pilih Foto
                    </span>
                </label>
                @error('foto') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Judul Foto <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul') }}"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('judul') border-red-400 @enderror"
                       placeholder="cth: Ibadah Raya Minggu" required>
                @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi</label>
                <input type="text" name="deskripsi" value="{{ old('deskripsi') }}"
                       class="w-full border border-gray-300 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="Keterangan singkat (opsional)">
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('galeri.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Upload Foto</button>
            </div>
        </form>
    </div>
</div>
@endsection
