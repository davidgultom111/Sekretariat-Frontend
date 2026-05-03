@extends('layouts.app')

@section('title', 'Detail Pengajuan — Sekretariat GPdI')
@section('page-title', 'Pengajuan Surat')

@php
    function tglP(?string $date): string {
        return $date ? \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') : '-';
    }

    $p = $pengajuan;
    $isProses = ($p['status'] ?? '') === 'Dalam Proses';
    $statusClass = match($p['status'] ?? '') {
        'Disetujui' => 'bg-green-100 text-green-700 border-green-200',
        'Ditolak'   => 'bg-red-100 text-red-700 border-red-200',
        default     => 'bg-yellow-100 text-yellow-700 border-yellow-200',
    };
@endphp

@section('content')

<div x-data="{ showApprove: false, showReject: false, showDelete: false }">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Detail Pengajuan</h1>
        <a href="{{ route('pengajuan.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Daftar</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Informasi Pengajuan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Status & Tipe --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-start justify-between mb-4">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider">Informasi Pengajuan</h2>
                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border {{ $statusClass }}">
                        {{ $p['status'] }}
                    </span>
                </div>
                <dl class="space-y-3">
                    <div class="flex gap-4">
                        <dt class="w-40 text-sm text-gray-500 shrink-0">Tipe Surat</dt>
                        <dd class="text-sm font-semibold text-gray-800">{{ $p['tipe_surat'] }}</dd>
                    </div>
                    <div class="flex gap-4">
                        <dt class="w-40 text-sm text-gray-500 shrink-0">Tanggal Pengajuan</dt>
                        <dd class="text-sm text-gray-700">{{ tglP($p['created_at']) }}</dd>
                    </div>
                    @if(!empty($p['catatan']))
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Catatan Penolakan</dt>
                            <dd class="text-sm text-red-700 font-medium">{{ $p['catatan'] }}</dd>
                        </div>
                    @endif
                    @if(!empty($p['letter_id']))
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Surat Dibuat</dt>
                            <dd class="text-sm">
                                <a href="{{ route('surat.show', $p['letter_id']) }}"
                                   class="text-blue-600 hover:text-blue-800 font-medium">
                                    Lihat surat →
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Field Khusus per Tipe --}}
            @php $type = $p['letter_type'] ?? ''; @endphp

            @if($type === 'surat_tugas_pelayanan')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Detail Tugas Pelayanan</h2>
                    <dl class="space-y-3">
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Tanggal Mulai</dt>
                            <dd class="text-sm text-gray-800">{{ tglP($p['tgl_mulai_tugas'] ?? null) }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Tanggal Akhir</dt>
                            <dd class="text-sm text-gray-800">{{ tglP($p['tgl_akhir_tugas'] ?? null) }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Tujuan Tugas</dt>
                            <dd class="text-sm text-gray-800">{{ $p['tujuan_tugas'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            @if($type === 'surat_pengantar' && !empty($p['keterangan']))
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Keterangan</h2>
                    <p class="text-sm text-gray-800">{{ $p['keterangan'] }}</p>
                </div>
            @endif

            @if($type === 'surat_keterangan_jemaat_aktif')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Detail Keanggotaan</h2>
                    <dl class="space-y-3">
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Tahun Bergabung</dt>
                            <dd class="text-sm text-gray-800">{{ $p['tahun_bergabung'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            @if($type === 'surat_nilai_sekolah')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Detail Sekolah</h2>
                    <dl class="space-y-3">
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Asal Sekolah</dt>
                            <dd class="text-sm text-gray-800">{{ $p['asal_sekolah'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Kelas/Semester</dt>
                            <dd class="text-sm text-gray-800">{{ $p['kelas'] ?? '-' }} / {{ $p['semester'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Nilai</dt>
                            <dd class="text-sm text-gray-800 font-semibold">{{ $p['nilai'] ?? '-' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

            @if($type === 'surat_pengajuan_penyerahan_anak')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Detail Penyerahan Anak</h2>
                    <dl class="space-y-3">
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Nama Ayah</dt>
                            <dd class="text-sm text-gray-800">{{ $p['nama_ayah'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Nama Ibu</dt>
                            <dd class="text-sm text-gray-800">{{ $p['nama_ibu'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Nama Anak</dt>
                            <dd class="text-sm text-gray-800">{{ $p['nama_anak'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Tgl Lahir Anak</dt>
                            <dd class="text-sm text-gray-800">
                                {{ $p['tempat_lahir_anak'] ?? '-' }}, {{ tglP($p['tanggal_lahir_anak'] ?? null) }}
                            </dd>
                        </div>
                    </dl>
                </div>
            @endif

            @if($type === 'surat_pengajuan_pernikahan')
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Detail Pernikahan</h2>
                    <dl class="space-y-3">
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Mempelai Pria</dt>
                            <dd class="text-sm text-gray-800 font-medium">{{ $p['member_pria']['nama_lengkap'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Mempelai Wanita</dt>
                            <dd class="text-sm text-gray-800 font-medium">{{ $p['member_wanita']['nama_lengkap'] ?? '-' }}</dd>
                        </div>
                        <div class="flex gap-4">
                            <dt class="w-40 text-sm text-gray-500 shrink-0">Rencana Nikah</dt>
                            <dd class="text-sm text-gray-800">{{ tglP($p['tanggal_pernikahan'] ?? null) }}</dd>
                        </div>
                    </dl>
                </div>
            @endif

        </div>

        {{-- Sidebar: Jemaat + Aksi --}}
        <div class="space-y-6">

            {{-- Jemaat --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-4">Jemaat Pengaju</h2>
                @if($type === 'surat_pengajuan_pernikahan')
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Mempelai Pria</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $p['member_pria']['nama_lengkap'] ?? '-' }}</p>
                            <p class="text-xs font-mono text-gray-400">{{ $p['member_pria']['id_jemaat'] ?? '' }}</p>
                        </div>
                        <div class="border-t border-gray-100 pt-4">
                            <p class="text-xs text-gray-400 mb-1">Mempelai Wanita</p>
                            <p class="text-sm font-semibold text-gray-800">{{ $p['member_wanita']['nama_lengkap'] ?? '-' }}</p>
                            <p class="text-xs font-mono text-gray-400">{{ $p['member_wanita']['id_jemaat'] ?? '' }}</p>
                        </div>
                    </div>
                @else
                    @if(isset($p['member']))
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">{{ $p['member']['nama_lengkap'] }}</p>
                                <p class="text-xs font-mono text-gray-400">{{ $p['member']['id_jemaat'] }}</p>
                            </div>
                        </div>
                        <a href="{{ route('jemaat.show', $p['member']['id']) }}"
                           class="text-xs text-blue-600 hover:text-blue-800">Lihat profil →</a>
                    @else
                        <p class="text-sm text-gray-400">Data jemaat tidak tersedia</p>
                    @endif
                @endif
            </div>

            {{-- Aksi --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-3">
                <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-2">Tindakan</h2>

                @if($isProses)
                    {{-- Setujui --}}
                    <button @click="showApprove = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Setujui & Buat Surat
                    </button>

                    {{-- Tolak --}}
                    <button @click="showReject = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-semibold rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Tolak Pengajuan
                    </button>
                @endif

                {{-- Hapus --}}
                <button @click="showDelete = true"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Pengajuan
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Setujui --}}
    <div x-show="showApprove" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showApprove = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="font-semibold text-gray-800 mb-1">Setujui Pengajuan</h3>
            <p class="text-sm text-gray-500 mb-5">Surat akan dibuat otomatis sesuai data yang diajukan jemaat.</p>
            <form method="POST" action="{{ route('pengajuan.approve', $p['id']) }}">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Surat</label>
                    <input type="date" name="tanggal_surat" value="{{ today()->toDateString() }}"
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 focus:border-transparent outline-none">
                    <p class="text-xs text-gray-400 mt-1">Kosongkan untuk menggunakan tanggal hari ini</p>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showApprove = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg">
                        Ya, Setujui
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Tolak --}}
    <div x-show="showReject" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showReject = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="font-semibold text-gray-800 mb-1">Tolak Pengajuan</h3>
            <p class="text-sm text-gray-500 mb-5">Jemaat akan melihat catatan alasan penolakan ini.</p>
            <form method="POST" action="{{ route('pengajuan.reject', $p['id']) }}">
                @csrf
                @method('PUT')
                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Penolakan <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" rows="3"
                              placeholder="Contoh: Data tidak lengkap, harap lengkapi keterangan..."
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none resize-none"></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                    <button type="button" @click="showReject = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg">
                        Ya, Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Hapus --}}
    <div x-show="showDelete" x-cloak class="fixed inset-0 z-50 flex items-center justify-center" style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showDelete = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="font-semibold text-gray-800 mb-2">Hapus Pengajuan</h3>
            <p class="text-sm text-gray-600 mb-6">Yakin ingin menghapus pengajuan ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="flex gap-3 justify-end">
                <button @click="showDelete = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    Batal
                </button>
                <form method="POST" action="{{ route('pengajuan.destroy', $p['id']) }}">
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
