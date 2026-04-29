@extends('layouts.app')

@section('title', 'Buat Surat — Sekretariat GPdI')
@section('page-title', 'Surat')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-bold text-gray-800">Buat Surat Baru</h1>
    <a href="{{ route('surat.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali</a>
</div>

<div x-data="suratForm()" class="bg-white shadow-sm rounded-xl border border-gray-100 p-8">
    <form method="POST" action="{{ route('surat.store') }}" id="surat-form">
        @csrf

        {{-- TIPE SURAT --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Tipe Surat <span class="text-red-500">*</span>
            </label>
            <select name="letter_type" x-model="tipe"
                    class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           {{ $errors->has('letter_type') ? 'border-red-400' : 'border-gray-300' }}">
                <option value="">-- Pilih Tipe Surat --</option>
                @foreach($letterTypes as $key => $label)
                    <option value="{{ $key }}" {{ old('letter_type') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('letter_type')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- TANGGAL SURAT --}}
        <div class="mb-6" x-show="tipe !== ''" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Tanggal Surat <span class="text-red-500">*</span>
            </label>
            <input type="date" name="tanggal_surat"
                   value="{{ old('tanggal_surat', date('Y-m-d')) }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('tanggal_surat')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- PENCARIAN JEMAAT (semua tipe kecuali pernikahan) --}}
        <div class="mb-6" x-show="tipe !== '' && tipe !== 'surat_pengajuan_pernikahan'" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Jemaat <span class="text-red-500">*</span>
            </label>
            <div x-data="memberSearch('member_id')" class="relative">
                <input type="text" x-model="query"
                       @input.debounce.300ms="doSearch()"
                       @focus="if(query.length >= 2) doSearch()"
                       @keydown.escape="results = []"
                       placeholder="Ketik min. 2 karakter untuk mencari..."
                       class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div x-show="results.length > 0"
                     @click.outside="results = []"
                     class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                    <template x-for="m in results" :key="m.id">
                        <div @click="pilih(m)"
                             class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm border-b border-gray-50 last:border-0"
                             x-text="m.nama_lengkap + ' (' + m.id_jemaat + ')'"></div>
                    </template>
                </div>
                <p x-show="selectedName !== ''" class="text-sm text-green-600 mt-1.5">
                    ✓ Dipilih: <span x-text="selectedName" class="font-semibold"></span>
                    <button type="button" @click="clear()" class="ml-2 text-gray-400 hover:text-gray-600 text-xs">× Ganti</button>
                </p>
                <input type="hidden" name="member_id" :value="selectedId">
            </div>
            @error('member_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- KHUSUS PERNIKAHAN: dua field jemaat --}}
        <div x-show="tipe === 'surat_pengajuan_pernikahan'" x-cloak class="space-y-6 mb-6 p-5 bg-pink-50 border border-pink-100 rounded-xl">
            <h3 class="font-semibold text-pink-800">Data Pernikahan</h3>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mempelai Pria <span class="text-red-500">*</span></label>
                <div x-data="memberSearch('member_pria_id')" class="relative">
                    <input type="text" x-model="query"
                           @input.debounce.300ms="doSearch()" @keydown.escape="results = []"
                           placeholder="Cari mempelai pria..."
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div x-show="results.length > 0" @click.outside="results = []"
                         class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                        <template x-for="m in results" :key="m.id">
                            <div @click="pilih(m)" class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm"
                                 x-text="m.nama_lengkap + ' (' + m.id_jemaat + ')'"></div>
                        </template>
                    </div>
                    <p x-show="selectedName !== ''" class="text-sm text-green-600 mt-1.5">
                        ✓ <span x-text="selectedName" class="font-semibold"></span>
                        <button type="button" @click="clear()" class="ml-2 text-gray-400 text-xs">× Ganti</button>
                    </p>
                    <input type="hidden" name="member_pria_id" :value="selectedId">
                </div>
                @error('member_pria_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Mempelai Wanita <span class="text-red-500">*</span></label>
                <div x-data="memberSearch('member_wanita_id')" class="relative">
                    <input type="text" x-model="query"
                           @input.debounce.300ms="doSearch()" @keydown.escape="results = []"
                           placeholder="Cari mempelai wanita..."
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <div x-show="results.length > 0" @click.outside="results = []"
                         class="absolute z-10 w-full bg-white border border-gray-200 rounded-lg shadow-lg mt-1 max-h-48 overflow-y-auto">
                        <template x-for="m in results" :key="m.id">
                            <div @click="pilih(m)" class="px-4 py-2.5 cursor-pointer hover:bg-blue-50 text-sm"
                                 x-text="m.nama_lengkap + ' (' + m.id_jemaat + ')'"></div>
                        </template>
                    </div>
                    <p x-show="selectedName !== ''" class="text-sm text-green-600 mt-1.5">
                        ✓ <span x-text="selectedName" class="font-semibold"></span>
                        <button type="button" @click="clear()" class="ml-2 text-gray-400 text-xs">× Ganti</button>
                    </p>
                    <input type="hidden" name="member_wanita_id" :value="selectedId">
                </div>
                @error('member_wanita_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Pernikahan <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_pernikahan" value="{{ old('tanggal_pernikahan') }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('tanggal_pernikahan')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- SURAT TUGAS PELAYANAN --}}
        <div x-show="tipe === 'surat_tugas_pelayanan'" x-cloak class="mb-6 p-5 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
            <h3 class="font-semibold text-blue-800">Detail Tugas Pelayanan</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Mulai Tugas <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_mulai_tugas" value="{{ old('tgl_mulai_tugas') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tgl_mulai_tugas')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Akhir Tugas <span class="text-red-500">*</span></label>
                    <input type="date" name="tgl_akhir_tugas" value="{{ old('tgl_akhir_tugas') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tgl_akhir_tugas')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tujuan Tugas <span class="text-red-500">*</span></label>
                <textarea name="tujuan_tugas" rows="3" placeholder="Min. 10 karakter..."
                          class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('tujuan_tugas') }}</textarea>
                @error('tujuan_tugas')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- SURAT KETERANGAN JEMAAT AKTIF --}}
        <div x-show="tipe === 'surat_keterangan_jemaat_aktif'" x-cloak class="mb-6 p-5 bg-blue-50 border border-blue-100 rounded-xl">
            <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun Bergabung <span class="text-red-500">*</span></label>
            <input type="number" name="tahun_bergabung" value="{{ old('tahun_bergabung') }}"
                   min="1900" max="{{ date('Y') }}" placeholder="{{ date('Y') }}"
                   class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('tahun_bergabung')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
        </div>

        {{-- SURAT NILAI SEKOLAH --}}
        <div x-show="tipe === 'surat_nilai_sekolah'" x-cloak class="mb-6 p-5 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
            <h3 class="font-semibold text-blue-800">Data Sekolah</h3>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Asal Sekolah <span class="text-red-500">*</span></label>
                <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}"
                       placeholder="Nama sekolah"
                       class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('asal_sekolah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Kelas <span class="text-red-500">*</span></label>
                    <input type="text" name="kelas" value="{{ old('kelas') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('kelas')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Semester <span class="text-red-500">*</span></label>
                    <input type="text" name="semester" value="{{ old('semester') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('semester')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nilai (0–100)</label>
                    <input type="number" name="nilai" value="{{ old('nilai') }}" min="0" max="100"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nilai')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        {{-- SURAT PENYERAHAN ANAK --}}
        <div x-show="tipe === 'surat_pengajuan_penyerahan_anak'" x-cloak class="mb-6 p-5 bg-blue-50 border border-blue-100 rounded-xl space-y-4">
            <h3 class="font-semibold text-blue-800">Data Anak &amp; Orang Tua</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Ayah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama_ayah')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Ibu <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama_ibu')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Anak <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_anak" value="{{ old('nama_anak') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('nama_anak')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tempat Lahir Anak <span class="text-red-500">*</span></label>
                    <input type="text" name="tempat_lahir_anak" value="{{ old('tempat_lahir_anak') }}"
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('tempat_lahir_anak')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tanggal Lahir Anak <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_lahir_anak" value="{{ old('tanggal_lahir_anak') }}"
                       class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('tanggal_lahir_anak')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- KETERANGAN (semua tipe) --}}
        <div class="mb-8" x-show="tipe !== ''" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Keterangan
                <span x-show="tipe === 'surat_pengantar'" class="text-red-500 text-xs font-normal ml-1">* wajib, min. 10 karakter</span>
            </label>
            <textarea name="keterangan" rows="3"
                      class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('keterangan') }}</textarea>
            @error('keterangan')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- TOMBOL SUBMIT --}}
        <div x-show="tipe !== ''" x-cloak class="flex gap-3 pt-4 border-t border-gray-100">
            <button type="submit"
                    class="px-8 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Buat Surat
            </button>
            <a href="{{ route('surat.index') }}"
               class="px-8 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                Batal
            </a>
        </div>

    </form>
</div>

<script>
function suratForm() {
    return {
        tipe: '{{ old('letter_type', '') }}',
    }
}

function memberSearch(fieldName) {
    return {
        query: '',
        results: [],
        selectedId: null,
        selectedName: '',
        async doSearch() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            try {
                const res = await fetch(`/ajax/cari-jemaat?q=${encodeURIComponent(this.query)}`);
                this.results = await res.json();
            } catch (e) {
                this.results = [];
            }
        },
        pilih(m) {
            this.selectedId = m.id;
            this.selectedName = m.nama_lengkap + ' (' + m.id_jemaat + ')';
            this.query = m.nama_lengkap;
            this.results = [];
        },
        clear() {
            this.selectedId = null;
            this.selectedName = '';
            this.query = '';
            this.results = [];
        }
    }
}
</script>

@endsection
