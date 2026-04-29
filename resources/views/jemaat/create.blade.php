@extends('layouts.app')

@section('title', 'Tambah Jemaat — Sekretariat GPdI')
@section('page-title', 'Data Jemaat')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Tambah Data Jemaat</h1>
    <a href="{{ route('jemaat.index') }}" class="text-sm text-gray-500 hover:text-gray-700 flex items-center gap-1">
        ← Kembali ke Daftar
    </a>
</div>

{{-- Info box --}}
<div class="mb-6 flex items-start gap-3 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4 text-sm">
    <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <p>ID Jemaat dan password default (<strong>12345</strong>) akan dibuat otomatis oleh sistem berdasarkan tanggal lahir.</p>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <form method="POST" action="{{ route('jemaat.store') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Lengkap --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap') }}"
                       placeholder="Masukkan nama lengkap"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('nama_lengkap') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama_lengkap')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Jenis Kelamin <span class="text-red-500">*</span>
                </label>
                <select name="jenis_kelamin"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               {{ $errors->has('jenis_kelamin') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">-- Pilih --</option>
                    @foreach(['Laki-laki', 'Perempuan'] as $jk)
                        <option value="{{ $jk }}" {{ old('jenis_kelamin') === $jk ? 'selected' : '' }}>{{ $jk }}</option>
                    @endforeach
                </select>
                @error('jenis_kelamin')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Status Aktif <span class="text-red-500">*</span>
                </label>
                <select name="status_aktif"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               {{ $errors->has('status_aktif') ? 'border-red-400' : 'border-gray-300' }}">
                    <option value="">-- Pilih --</option>
                    @foreach(['Aktif', 'Tidak Aktif', 'Dipindahkan'] as $s)
                        <option value="{{ $s }}" {{ old('status_aktif') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                @error('status_aktif')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tanggal Lahir <span class="text-red-500">*</span>
                </label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('tanggal_lahir') ? 'border-red-400' : 'border-gray-300' }}">
                @error('tanggal_lahir')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tempat Lahir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Tempat Lahir <span class="text-red-500">*</span>
                </label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}"
                       placeholder="Kota tempat lahir"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('tempat_lahir') ? 'border-red-400' : 'border-gray-300' }}">
                @error('tempat_lahir')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- No Telepon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    No. Telepon <span class="text-red-500">*</span>
                </label>
                <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                       placeholder="08xx-xxxx-xxxx"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('no_telepon') ? 'border-red-400' : 'border-gray-300' }}">
                @error('no_telepon')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Alamat <span class="text-red-500">*</span>
                </label>
                <textarea name="alamat" rows="3"
                          placeholder="Alamat lengkap"
                          class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                 {{ $errors->has('alamat') ? 'border-red-400' : 'border-gray-300' }}">{{ old('alamat') }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Simpan Data Jemaat
            </button>
            <a href="{{ route('jemaat.index') }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
