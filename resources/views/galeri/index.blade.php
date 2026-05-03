@extends('layouts.app')
@section('title', 'Galeri Foto — Sekretariat GPdI')
@section('page-title', 'Galeri Foto')

@section('content')
<div x-data="{ show: false, deleteId: null, deleteJudul: '', previewSrc: '', previewShow: false }">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-800">Galeri Foto Pelayanan</h1>
            <p class="text-sm text-gray-500 mt-0.5">Kelola foto yang tampil di carousel website</p>
        </div>
        <a href="{{ route('galeri.create') }}"
           class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Foto
        </a>
    </div>

    @if(count($galeris) > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($galeris as $g)
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group">
            <div class="relative aspect-video cursor-pointer"
                 @click="previewSrc='{{ $g['foto_url'] }}'; previewShow=true">
                <img src="{{ $g['foto_url'] }}" alt="{{ $g['judul'] }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"/>
                    </svg>
                </div>
            </div>
            <div class="p-3">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $g['judul'] }}</p>
                @if($g['deskripsi'])
                    <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $g['deskripsi'] }}</p>
                @endif
                <button @click="show=true; deleteId={{ $g['id'] }}; deleteJudul='{{ $g['judul'] }}'"
                        class="mt-2 w-full text-xs font-medium text-red-500 hover:text-red-700 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                    Hapus
                </button>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-12 text-center">
        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <p class="text-gray-400 text-sm">Belum ada foto. Klik "Tambah Foto" untuk memulai.</p>
    </div>
    @endif

    {{-- Modal Preview Foto --}}
    <div x-show="previewShow" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div @click="previewShow=false" class="absolute inset-0 bg-black/80 backdrop-blur-sm"></div>
        <div class="relative max-w-4xl w-full">
            <img :src="previewSrc" class="w-full rounded-2xl object-contain max-h-[80vh]">
            <button @click="previewShow=false"
                    class="absolute top-3 right-3 w-8 h-8 bg-black/50 hover:bg-black/70 text-white rounded-full flex items-center justify-center transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
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
                    <h3 class="font-bold text-gray-800">Hapus Foto</h3>
                    <p class="text-sm text-gray-500">File foto juga akan dihapus dari server</p>
                </div>
            </div>
            <p class="text-sm text-gray-600 mb-5">Yakin ingin menghapus foto <strong x-text="deleteJudul"></strong>?</p>
            <div class="flex gap-3">
                <button @click="show=false" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">Batal</button>
                <form :action="'/galeri/' + deleteId" method="POST" class="flex-1">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
