@extends('layouts.app')

@section('title', 'Detail Surat — Sekretariat GPdI')
@section('page-title', 'Surat')

@php
    $backendUrl = rtrim(str_replace('/api', '', config('services.api.base_url')), '/');

    function tgl(string|null $date): string {
        return $date ? \Carbon\Carbon::parse($date)->locale('id')->isoFormat('D MMMM Y') : '-';
    }
@endphp

@section('content')

<div x-data="{ showDelete: false, showPreview: false }">

    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Detail Surat</h1>
        <a href="{{ route('surat.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Kembali ke Daftar</a>
    </div>

    {{-- Kartu Info --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Info Surat --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-5">Informasi Surat</h2>
            <dl class="space-y-4">
                <div class="flex gap-4">
                    <dt class="w-40 text-sm text-gray-500 shrink-0">Nomor Surat</dt>
                    <dd class="text-sm font-semibold text-gray-800 font-mono">{{ $surat['nomor_surat'] }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-40 text-sm text-gray-500 shrink-0">Tipe Surat</dt>
                    <dd class="text-sm font-medium text-gray-800">{{ $surat['tipe_surat'] }}</dd>
                </div>
                <div class="flex gap-4">
                    <dt class="w-40 text-sm text-gray-500 shrink-0">Tanggal Surat</dt>
                    <dd class="text-sm font-medium text-gray-800">{{ tgl($surat['tanggal_surat']) }}</dd>
                </div>
                @if($surat['keterangan'])
                    <div class="flex gap-4">
                        <dt class="w-40 text-sm text-gray-500 shrink-0">Keterangan</dt>
                        <dd class="text-sm text-gray-800">{{ $surat['keterangan'] }}</dd>
                    </div>
                @endif
                <div class="flex gap-4">
                    <dt class="w-40 text-sm text-gray-500 shrink-0">Dibuat Pada</dt>
                    <dd class="text-sm text-gray-500">
                        {{ \Carbon\Carbon::parse($surat['created_at'])->format('d F Y, H:i') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Info Jemaat --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-semibold text-gray-700 text-sm uppercase tracking-wider mb-5">Jemaat</h2>

            @if($surat['letter_type'] === 'surat_pengajuan_pernikahan')
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Mempelai Pria</p>
                        <p class="text-sm font-medium text-gray-800">{{ $surat['member_pria']['nama_lengkap'] ?? '-' }}</p>
                        @if(isset($surat['member_pria']['id_jemaat']))
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $surat['member_pria']['id_jemaat'] }}</p>
                        @endif
                    </div>
                    <div class="border-t border-gray-100 pt-4">
                        <p class="text-xs text-gray-400 mb-1">Mempelai Wanita</p>
                        <p class="text-sm font-medium text-gray-800">{{ $surat['member_wanita']['nama_lengkap'] ?? '-' }}</p>
                        @if(isset($surat['member_wanita']['id_jemaat']))
                            <p class="text-xs text-gray-400 font-mono mt-0.5">{{ $surat['member_wanita']['id_jemaat'] }}</p>
                        @endif
                    </div>
                </div>
            @else
                @if(isset($surat['member']))
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800">{{ $surat['member']['nama_lengkap'] }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $surat['member']['id_jemaat'] }}</p>
                        </div>
                    </div>
                    <a href="{{ route('jemaat.show', $surat['member']['id']) }}"
                       class="text-xs text-blue-600 hover:text-blue-800">Lihat profil →</a>
                @else
                    <p class="text-sm text-gray-400">Data jemaat tidak tersedia</p>
                @endif
            @endif
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="flex flex-wrap gap-3 mt-6">
        <button @click="showPreview = !showPreview"
                class="flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <span x-text="showPreview ? 'Tutup Preview' : 'Lihat Surat'"></span>
        </button>

        <a href="{{ route('surat.pdf', $surat['id']) }}" target="_blank"
           class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Download PDF
        </a>

        <button @click="showDelete = true"
                class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-lg transition-colors">
            Hapus Surat
        </button>

        <a href="{{ route('surat.index') }}"
           class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors">
            Kembali
        </a>
    </div>

    {{-- Preview A4 --}}
    <div x-show="showPreview" x-cloak x-transition class="mt-6">
        <div class="mb-3 flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Preview Surat — Format A4
        </div>

        <div class="overflow-x-auto pb-4">
            {{-- Kertas A4 --}}
            <div style="width:210mm; min-height:297mm; padding:10mm 15mm; background:#fff; font-family:'Times New Roman',Times,serif; font-size:11pt; position:relative; margin:0 auto; box-shadow:0 2px 16px rgba(0,0,0,0.12); box-sizing:border-box;">

                {{-- Kop Surat --}}
                <table style="width:100%;border-bottom:2pt solid #000;margin-bottom:5mm;padding-bottom:2mm;border-collapse:collapse;">
                    <tr>
                        <td style="text-align:center;padding:0;">
                            <img src="{{ $backendUrl }}/images/kop-surat.jpg"
                                 style="max-width:180mm;max-height:30mm;display:block;margin:0 auto;"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                            <div style="display:none;">
                                <div style="font-size:12pt;font-weight:bold;">GEREJA PENTEKOSTA DI INDONESIA</div>
                                <div style="font-size:14pt;font-weight:bold;">Jemaat "SAHABAT ALLAH" Palembang</div>
                                <div style="font-size:10pt;">JL. Sejahtera Lr. Sahabat, Sukabangun II Kel. Sukajaya Kec. Sukarami, Palembang 30152</div>
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Judul & Nomor Surat --}}
                <div style="text-align:center;margin-bottom:6mm;">
                    <div style="font-size:12pt;font-weight:bold;text-decoration:underline;text-transform:uppercase;">
                        {{ $surat['tipe_surat'] }}
                    </div>
                    <div style="font-size:11pt;margin-top:1mm;">No. {{ $surat['nomor_surat'] }}</div>
                </div>

                {{-- Isi Surat --}}
                <div style="line-height:1.4;text-align:justify;">

                    {{-- Pembuka + Identitas Jemaat (semua tipe kecuali pernikahan) --}}
                    @if($surat['letter_type'] !== 'surat_pengajuan_pernikahan' && isset($surat['member']))
                        @php
                            $openingText = match($surat['letter_type']) {
                                'surat_pengantar'           => 'Dengan hormat, kami memberitahukan bahwa:',
                                'surat_nilai_sekolah'       => 'Yang bertanda tangan di bawah ini adalah Gembala Sidang Jemaat yang menerangkan bahwa:',
                                'surat_pengajuan_baptisan',
                                'surat_pengajuan_penyerahan_anak' => 'Kepada Yth. Pemimpin Ibadah terkait di tempat,',
                                default                     => 'Yang bertanda tangan di bawah ini menerangkan bahwa:',
                            };
                        @endphp
                        <p style="margin-bottom:2mm;">{{ $openingText }}</p>
                        <table style="width:100%;margin-left:10mm;border-collapse:collapse;margin-bottom:3mm;">
                            <tr>
                                <td style="width:45mm;vertical-align:top;padding:0.5mm 0;">Nama</td>
                                <td style="width:5mm;vertical-align:top;padding:0.5mm 0;text-align:center;">:</td>
                                <td style="font-weight:bold;vertical-align:top;padding:0.5mm 0;">{{ $surat['member']['nama_lengkap'] }}</td>
                            </tr>
                            <tr>
                                <td style="width:45mm;vertical-align:top;padding:0.5mm 0;">No. Telepon</td>
                                <td style="width:5mm;vertical-align:top;padding:0.5mm 0;text-align:center;">:</td>
                                <td style="vertical-align:top;padding:0.5mm 0;">{{ $surat['member']['no_telepon'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="width:45mm;vertical-align:top;padding:0.5mm 0;">Tempat/Tgl Lahir</td>
                                <td style="width:5mm;vertical-align:top;padding:0.5mm 0;text-align:center;">:</td>
                                <td style="vertical-align:top;padding:0.5mm 0;">
                                    {{ $surat['member']['tempat_lahir'] ?? '-' }},
                                    {{ tgl($surat['member']['tanggal_lahir'] ?? null) }}
                                </td>
                            </tr>
                            <tr>
                                <td style="width:45mm;vertical-align:top;padding:0.5mm 0;">Alamat</td>
                                <td style="width:5mm;vertical-align:top;padding:0.5mm 0;text-align:center;">:</td>
                                <td style="vertical-align:top;padding:0.5mm 0;">{{ $surat['member']['alamat'] ?? '-' }}</td>
                            </tr>
                        </table>
                    @endif

                    {{-- Body per tipe surat --}}
                    <div style="margin-top:2mm;">
                        @switch($surat['letter_type'])

                            @case('surat_tugas_pelayanan')
                                <p style="margin-bottom:2mm;">Telah ditugaskan untuk melakukan pelayanan dengan perincian sebagai berikut:</p>
                                <table style="width:100%;margin-left:10mm;border-collapse:collapse;margin-bottom:2mm;">
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Tanggal Mulai</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ tgl($surat['tgl_mulai_tugas'] ?? null) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Tanggal Akhir</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ tgl($surat['tgl_akhir_tugas'] ?? null) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Tujuan Tugas</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['tujuan_tugas'] ?? '-' }}</td>
                                    </tr>
                                </table>
                                <p style="margin-top:2mm;">Semoga dapat melayani dengan sepenuh hati kepada Tuhan dan sesama.</p>
                                @break

                            @case('surat_pengantar')
                                <p>Adalah anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah dan terdaftar dalam daftar anggota kami.</p>
                                <p style="margin-top:3mm;">Adapun surat pengantar ini diberikan untuk keperluan: <strong>{{ $surat['keterangan'] }}</strong></p>
                                <p style="margin-top:3mm;">Demikian surat pengantar ini dibuat agar dapat dipergunakan sebagaimana mestinya.</p>
                                @break

                            @case('surat_keterangan_jemaat_aktif')
                                <p>Adalah benar anggota jemaat Gereja Pantekosta di Indonesia Jemaat Sahabat Allah Palembang yang aktif dan terdaftar dalam keikutsertaan agenda ibadah jemaat sejak tahun <strong>{{ $surat['tahun_bergabung'] }}</strong>.</p>
                                <p style="margin-top:4mm;">Demikian surat keterangan ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.</p>
                                @break

                            @case('surat_nilai_sekolah')
                                <table style="width:100%;margin-left:10mm;border-collapse:collapse;margin-bottom:2mm;">
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Asal Sekolah</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['asal_sekolah'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Kelas/Tingkat</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['kelas'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Semester</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['semester'] ?? '-' }}</td>
                                    </tr>
                                </table>
                                <p style="margin-top:3mm;">Siswa/siswi tersebut telah mengikuti kegiatan ibadah dan pembelajaran agama di GPdI Jemaat Sahabat Allah Palembang dengan nilai:</p>
                                <div style="text-align:center;font-size:14pt;font-weight:bold;margin-top:4mm;">[ {{ $surat['nilai'] ?? 90 }} ]</div>
                                @break

                            @case('surat_pengajuan_baptisan')
                                <p>Jemaat tersebut telah menyatakan komitmennya menerima Yesus Kristus sebagai Tuhan dan Juru Selamat pribadi, dan rindu untuk dibaptis air sebagai tanda pernyataan imannya.</p>
                                <p style="margin-top:3mm;">Kami memohon agar calon baptis ini dapat disertakan pada upacara pembaptisan berikutnya.</p>
                                @break

                            @case('surat_pengajuan_penyerahan_anak')
                                <table style="width:100%;margin-left:10mm;border-collapse:collapse;margin-bottom:2mm;">
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Nama Ayah</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['nama_ayah'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Nama Ibu</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['nama_ibu'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Nama Anak</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['nama_anak'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Tempat/Tgl Lahir Anak</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">
                                            {{ $surat['tempat_lahir_anak'] ?? '-' }} / {{ tgl($surat['tanggal_lahir_anak'] ?? null) }}
                                        </td>
                                    </tr>
                                </table>
                                <p style="margin-top:3mm;">Orang tua dari anak tersebut ingin menyerahkan anak mereka kepada Tuhan dan memohon doa restu dalam ibadah penyerahan anak.</p>
                                @break

                            @case('surat_pengajuan_pernikahan')
                                <p style="margin-bottom:2mm;">Calon mempelai telah siap untuk memasuki ikatan pernikahan kudus. Kami memohon agar proses bimbingan dan upacara pernikahan dapat dilaksanakan pada:</p>
                                <table style="width:100%;margin-left:10mm;border-collapse:collapse;margin-bottom:2mm;">
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Mempelai Pria</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['member_pria']['nama_lengkap'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Mempelai Wanita</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ $surat['member_wanita']['nama_lengkap'] ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Rencana Nikah</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">{{ tgl($surat['tanggal_pernikahan'] ?? null) }}</td>
                                    </tr>
                                    <tr>
                                        <td style="width:45mm;padding:0.5mm 0;vertical-align:top;">Tempat</td>
                                        <td style="width:5mm;padding:0.5mm 0;text-align:center;vertical-align:top;">:</td>
                                        <td style="padding:0.5mm 0;vertical-align:top;">GPdI Sahabat Allah Palembang</td>
                                    </tr>
                                </table>
                                @break

                        @endswitch
                    </div>
                </div>

                {{-- Tanda Tangan --}}
                <table style="width:100%;margin-top:8mm;border-collapse:collapse;">
                    <tr>
                        <td style="width:50%;"></td>
                        <td style="width:50%;text-align:center;">
                            <div style="margin-bottom:1mm;">Palembang, {{ tgl($surat['tanggal_surat']) }}</div>
                            <div style="font-weight:bold;margin-bottom:15mm;">Gembala Sidang</div>
                            <div style="font-weight:bold;text-decoration:underline;">( Tamrin Gultom, S.Th. )</div>
                        </td>
                    </tr>
                </table>

                {{-- Footer --}}
                <div style="position:absolute;bottom:10mm;left:15mm;right:15mm;text-align:center;font-size:8pt;color:#666;font-style:italic;border-top:0.5pt solid #ccc;padding-top:2mm;">
                    Dokumen ini dicetak otomatis dari Sistem Sekretariat Gereja Pantekosta Jemaat Sahabat Allah Palembang
                </div>

            </div>{{-- /A4 --}}
        </div>{{-- /overflow-x-auto --}}
    </div>{{-- /preview --}}

    {{-- Modal Hapus --}}
    <div x-show="showDelete"
         x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center"
         style="display:none;">
        <div class="absolute inset-0 bg-black/40" @click="showDelete = false"></div>
        <div class="relative bg-white rounded-xl shadow-xl p-6 w-full max-w-sm mx-4">
            <h3 class="font-semibold text-gray-800 mb-2">Hapus Surat</h3>
            <p class="text-sm text-gray-600 mb-6">
                Yakin ingin menghapus surat <strong>{{ $surat['nomor_surat'] }}</strong>? Tindakan ini tidak dapat dibatalkan.
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="showDelete = false"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    Batal
                </button>
                <form method="POST" action="{{ route('surat.destroy', $surat['id']) }}">
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
