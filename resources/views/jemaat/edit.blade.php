@extends('layouts.app')

@section('title', 'Edit Jemaat — Sekretariat GPdI')
@section('page-title', 'Data Jemaat')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Edit Data Jemaat: <span class="text-blue-600">{{ $member['nama_lengkap'] }}</span></h1>
    <a href="{{ route('jemaat.show', $member['id']) }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Detail</a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
    <form method="POST" action="{{ route('jemaat.update', $member['id']) }}">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Nama Lengkap --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" name="nama_lengkap"
                       value="{{ old('nama_lengkap', $member['nama_lengkap']) }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('nama_lengkap') ? 'border-red-400' : 'border-gray-300' }}">
                @error('nama_lengkap')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Jenis Kelamin --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Kelamin <span class="text-red-500">*</span></label>
                <select name="jenis_kelamin"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               {{ $errors->has('jenis_kelamin') ? 'border-red-400' : 'border-gray-300' }}">
                    @foreach(['Laki-laki', 'Perempuan'] as $jk)
                        <option value="{{ $jk }}"
                                {{ (old('jenis_kelamin', $member['jenis_kelamin'])) === $jk ? 'selected' : '' }}>
                            {{ $jk }}
                        </option>
                    @endforeach
                </select>
                @error('jenis_kelamin')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Status Aktif --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status Aktif <span class="text-red-500">*</span></label>
                <select name="status_aktif"
                        class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                               {{ $errors->has('status_aktif') ? 'border-red-400' : 'border-gray-300' }}">
                    @foreach(['Aktif', 'Tidak Aktif'] as $s)
                        <option value="{{ $s }}"
                                {{ (old('status_aktif', $member['status_aktif'])) === $s ? 'selected' : '' }}>
                            {{ $s }}
                        </option>
                    @endforeach
                </select>
                @error('status_aktif')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tanggal Lahir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir', $member['tanggal_lahir']) }}"
                       max="{{ date('Y-m-d') }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('tanggal_lahir') ? 'border-red-400' : 'border-gray-300' }}">
                @error('tanggal_lahir')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tempat Lahir --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir <span class="text-red-500">*</span></label>
                <input type="text" name="tempat_lahir"
                       value="{{ old('tempat_lahir', $member['tempat_lahir']) }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('tempat_lahir') ? 'border-red-400' : 'border-gray-300' }}">
                @error('tempat_lahir')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- No Telepon --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon <span class="text-red-500">*</span></label>
                <input type="text" name="no_telepon"
                       value="{{ old('no_telepon', $member['no_telepon']) }}"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('no_telepon') ? 'border-red-400' : 'border-gray-300' }}">
                @error('no_telepon')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password Baru
                    <span class="text-xs text-gray-400 font-normal ml-1">(kosongkan jika tidak ingin mengubah)</span>
                </label>
                <input type="password" name="password"
                       placeholder="Min. 6 karakter"
                       class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                              {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300' }}">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat <span class="text-red-500">*</span></label>
                <textarea name="alamat" rows="3"
                          class="w-full px-4 py-2.5 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                                 {{ $errors->has('alamat') ? 'border-red-400' : 'border-gray-300' }}">{{ old('alamat', $member['alamat']) }}</textarea>
                @error('alamat')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

        </div>

        <div class="flex gap-3 mt-8 pt-6 border-t border-gray-100">
            <button type="submit"
                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Simpan Perubahan
            </button>
            <a href="{{ route('jemaat.show', $member['id']) }}"
               class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

@endsection
