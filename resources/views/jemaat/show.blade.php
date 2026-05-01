@extends('layouts.app')

@section('title', 'Detail Jemaat — Sekretariat GPdI')
@section('page-title', 'Data Jemaat')

@section('content')

<div x-data="{ show: false }">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Detail Jemaat</h1>
        <a href="{{ route('jemaat.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Daftar</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">

        <div class="flex items-center gap-4 mb-8 pb-6 border-b border-gray-100">
            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $member['nama_lengkap'] }}</h2>
                <p class="text-sm text-gray-500 font-mono mt-0.5">{{ $member['id_jemaat'] }}</p>
            </div>
            @if($member['status_aktif'] === 'Aktif')
                <span class="ml-auto px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">Aktif</span>
            @elseif($member['status_aktif'] === 'Tidak Aktif')
                <span class="ml-auto px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Tidak Aktif</span>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-5 gap-x-10">
            @php
                $fields = [
                    'Jenis Kelamin'  => $member['jenis_kelamin'],
                    'Tanggal Lahir'  => \Carbon\Carbon::parse($member['tanggal_lahir'])->format('d F Y'),
                    'Tempat Lahir'   => $member['tempat_lahir'],
                    'No. Telepon'    => $member['no_telepon'],
                    'Role'           => ucfirst($member['role']),
                    'Terdaftar Pada' => \Carbon\Carbon::parse($member['created_at'])->format('d F Y'),
                ];
            @endphp
            @foreach($fields as $label => $value)
                <div>
                    <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">{{ $label }}</p>
                    <p class="text-sm font-medium text-gray-800 mt-1">{{ $value }}</p>
                </div>
            @endforeach
            <div class="md:col-span-2">
                <p class="text-xs font-medium text-gray-400 uppercase tracking-wider">Alamat</p>
                <p class="text-sm font-medium text-gray-800 mt-1">{{ $member['alamat'] }}</p>
            </div>
        </div>

        <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
            <a href="{{ route('jemaat.edit', $member['id']) }}"
               class="px-5 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors">
                Edit Data
            </a>
            <button @click="show = true"
                    class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
                Hapus
            </button>
            <a href="{{ route('jemaat.index') }}"
               class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Kembali
            </a>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="show"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="display: none;">
        <div class="absolute inset-0 bg-black/40" @click="show = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="font-semibold text-gray-800 mb-2">Hapus Data Jemaat</h3>
            <p class="text-sm text-gray-600 mb-6">
                Yakin ingin menghapus data <strong>{{ $member['nama_lengkap'] }}</strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="show = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    Batal
                </button>
                <form method="POST" action="{{ route('jemaat.destroy', $member['id']) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>

@endsection
