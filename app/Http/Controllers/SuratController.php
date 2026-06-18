<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

// Mengelola pembuatan, penampilan, dan penghapusan 7 jenis surat gereja
class SuratController extends Controller
{
    // Daftar semua tipe surat beserta label tampilan (slug => nama)
    // Digunakan untuk mengisi dropdown tipe surat dan menampilkan label di tabel
    private array $letterTypes = [
        'surat_tugas_pelayanan'           => 'Surat Tugas Pelayanan',
        'surat_pengantar'                 => 'Surat Pengantar',
        'surat_keterangan_jemaat_aktif'   => 'Surat Keterangan Jemaat Aktif',
        'surat_nilai_sekolah'             => 'Surat Nilai Sekolah',
        'surat_pengajuan_baptisan'        => 'Surat Pengajuan Baptisan',
        'surat_pengajuan_penyerahan_anak' => 'Surat Pengajuan Penyerahan Anak',
        'surat_pengajuan_pernikahan'      => 'Surat Pengajuan Pernikahan',
    ];

    // Menampilkan daftar surat dengan filter tipe dan pencarian nama jemaat/nomor surat
    public function index(Request $request, ApiService $api)
    {
        $result = $api->getLetters([
            'search'      => $request->query('search'),       // cari berdasarkan nama jemaat atau nomor surat
            'letter_type' => $request->query('letter_type'),  // filter berdasarkan slug tipe surat
            'per_page'    => $request->query('per_page', 15), // jumlah data per halaman
            'page'        => $request->query('page', 1),      // halaman saat ini
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('surat.index', [
            'letters'     => $result['data']['data'] ?? [],                                                 // array data surat
            'meta'        => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0], // info pagination
            'filters'     => $request->only(['search', 'letter_type', 'per_page']),                         // filter aktif (untuk mempertahankan state form)
            'letterTypes' => $this->letterTypes,                                                             // pilihan dropdown tipe surat
        ]);
    }

    // Menampilkan form buat surat baru dengan semua pilihan tipe surat
    public function create()
    {
        return view('surat.create', ['letterTypes' => $this->letterTypes]);
    }

    // Menyimpan surat baru ke API backend setelah form disubmit
    public function store(Request $request, ApiService $api)
    {
        // Hapus token CSRF dan method spoofing, lalu buang field yang kosong
        $data = array_filter(
            $request->except(['_token', '_method']),
            fn($v) => $v !== null && $v !== ''
        );

        $result = $api->storeLetter($data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        // Kembalikan ke form dengan error validasi dari API
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();

        // Jika ada error server lain
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal membuat surat. Coba lagi.')->withInput();

        // Ambil ID dan nomor surat dari response (POST bisa return flat atau nested)
        $newId      = $result['data']['id']          ?? $result['id']          ?? null;
        $nomorSurat = $result['data']['nomor_surat'] ?? $result['nomor_surat'] ?? '';

        // Redirect ke detail surat jika ID tersedia, atau ke daftar jika tidak
        return redirect()->route($newId ? 'surat.show' : 'surat.index', $newId ? [$newId] : [])
            ->with('success', 'Surat berhasil dibuat' . ($nomorSurat ? ': ' . $nomorSurat : '.'));
    }

    // Menampilkan detail satu surat beserta semua field khusus tipenya
    public function show(int $id, ApiService $api)
    {
        $result = $api->getLetter($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404); // surat tidak ditemukan

        return view('surat.show', [
            'surat'       => $result['data'],        // data surat lengkap dari API
            'letterTypes' => $this->letterTypes,     // untuk menampilkan label tipe surat
        ]);
    }

    // Menghapus surat dan file PDF-nya via API
    public function destroy(int $id, ApiService $api)
    {
        $api->deleteLetter($id);
        return redirect()->route('surat.index')->with('success', 'Surat berhasil dihapus.');
    }

    // Mengunduh atau menampilkan PDF surat langsung di browser (inline)
    public function downloadPdf(int $id, ApiService $api)
    {
        // downloadPdf mengembalikan Response langsung (bukan array) karena binary stream
        $response = $api->downloadPdf($id);

        if ($response->failed()) {
            return back()->with('error', 'PDF tidak tersedia atau gagal diunduh.');
        }

        // Stream konten PDF ke browser dengan header yang sesuai
        return response($response->body(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat-' . $id . '.pdf"', // inline = tampil di browser
        ]);
    }
}
