<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GaleriController;
use App\Http\Controllers\HistoryJemaatController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\JemaatController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\SuratController;
use App\Services\ApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ======================== AUTH (Publik) ========================

// Menampilkan halaman form login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');

// Memproses submit form login dan menyimpan token ke session
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Menghapus session dan memanggil endpoint logout API
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.admin');

// ======================== HALAMAN TERPROTEKSI (wajib login) ========================

Route::middleware('auth.admin')->group(function () {

    // Dashboard utama — menampilkan ringkasan jumlah jemaat dan surat terbaru
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ======================== JEMAAT ========================

    Route::prefix('jemaat')->name('jemaat.')->group(function () {
        // Daftar semua jemaat aktif dengan fitur pencarian dan pagination
        Route::get('/', [JemaatController::class, 'index'])->name('index');

        // Form tambah jemaat baru
        Route::get('/tambah', [JemaatController::class, 'create'])->name('create');

        // Simpan data jemaat baru ke API
        Route::post('/', [JemaatController::class, 'store'])->name('store');

        // Detail profil satu jemaat berdasarkan ID
        Route::get('/{id}', [JemaatController::class, 'show'])->name('show');

        // Form edit data jemaat
        Route::get('/{id}/edit', [JemaatController::class, 'edit'])->name('edit');

        // Simpan perubahan data jemaat ke API (PUT)
        Route::put('/{id}', [JemaatController::class, 'update'])->name('update');

        // Hapus data jemaat (DELETE — menonaktifkan/menghapus via API)
        Route::delete('/{id}', [JemaatController::class, 'destroy'])->name('destroy');
    });

    // ======================== HISTORY JEMAAT ========================

    Route::prefix('history')->name('history.')->group(function () {
        // Daftar jemaat tidak aktif (history keluar/dinonaktifkan)
        Route::get('/', [HistoryJemaatController::class, 'index'])->name('index');

        // Mengaktifkan kembali jemaat yang tidak aktif
        Route::post('/{id}/aktifkan', [HistoryJemaatController::class, 'activate'])->name('activate');

        // Hapus permanen data jemaat dari history
        Route::delete('/{id}', [HistoryJemaatController::class, 'destroy'])->name('destroy');
    });

    // ======================== SURAT ========================

    Route::prefix('surat')->name('surat.')->group(function () {
        // Daftar semua surat dengan filter tipe dan pencarian
        Route::get('/', [SuratController::class, 'index'])->name('index');

        // Form buat surat baru (pilih tipe, isi data jemaat, dll.)
        Route::get('/buat', [SuratController::class, 'create'])->name('create');

        // Proses pembuatan surat baru dan kirim ke API
        Route::post('/', [SuratController::class, 'store'])->name('store');

        // Detail satu surat termasuk data jemaat dan field khusus tipenya
        Route::get('/{id}', [SuratController::class, 'show'])->name('show');

        // Hapus surat beserta file PDF-nya via API
        Route::delete('/{id}', [SuratController::class, 'destroy'])->name('destroy');

        // Download atau tampilkan PDF surat di browser (inline)
        Route::get('/{id}/pdf', [SuratController::class, 'downloadPdf'])->name('pdf');
    });

    // ======================== JADWAL PELAYANAN ========================

    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        // Daftar semua jadwal kegiatan gereja
        Route::get('/', [JadwalController::class, 'index'])->name('index');

        // Form tambah jadwal baru
        Route::get('/tambah', [JadwalController::class, 'create'])->name('create');

        // Simpan jadwal baru ke API
        Route::post('/', [JadwalController::class, 'store'])->name('store');

        // Form edit jadwal
        Route::get('/{id}/edit', [JadwalController::class, 'edit'])->name('edit');

        // Simpan perubahan jadwal ke API (PUT)
        Route::put('/{id}', [JadwalController::class, 'update'])->name('update');

        // Hapus jadwal via API
        Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('destroy');
    });

    // ======================== GALERI FOTO ========================

    Route::prefix('galeri')->name('galeri.')->group(function () {
        // Daftar semua foto galeri gereja
        Route::get('/', [GaleriController::class, 'index'])->name('index');

        // Form upload foto baru ke galeri
        Route::get('/tambah', [GaleriController::class, 'create'])->name('create');

        // Upload foto dan simpan ke API (multipart/form-data)
        Route::post('/', [GaleriController::class, 'store'])->name('store');

        // Hapus foto dari galeri via API
        Route::delete('/{id}', [GaleriController::class, 'destroy'])->name('destroy');
    });

    // ======================== PENGUMUMAN ========================

    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        // Daftar semua pengumuman gereja
        Route::get('/', [PengumumanController::class, 'index'])->name('index');

        // Form tambah pengumuman baru
        Route::get('/tambah', [PengumumanController::class, 'create'])->name('create');

        // Simpan pengumuman baru ke API
        Route::post('/', [PengumumanController::class, 'store'])->name('store');

        // Form edit pengumuman
        Route::get('/{id}/edit', [PengumumanController::class, 'edit'])->name('edit');

        // Simpan perubahan pengumuman ke API (PUT)
        Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');

        // Hapus pengumuman via API
        Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
    });

    // ======================== PENGAJUAN SURAT ========================

    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        // Daftar semua pengajuan surat dari jemaat (bisa difilter by status)
        Route::get('/', [PengajuanController::class, 'index'])->name('index');

        // Detail satu pengajuan surat
        Route::get('/{id}', [PengajuanController::class, 'show'])->name('show');

        // Admin menyetujui pengajuan → otomatis buat surat di backend
        Route::put('/{id}/setujui', [PengajuanController::class, 'approve'])->name('approve');

        // Admin menolak pengajuan dengan catatan alasan
        Route::put('/{id}/tolak', [PengajuanController::class, 'reject'])->name('reject');

        // Hapus pengajuan via API
        Route::delete('/{id}', [PengajuanController::class, 'destroy'])->name('destroy');
    });

    // ======================== AJAX ========================

    // Endpoint pencarian jemaat aktif untuk autocomplete di form surat
    // Dipanggil oleh Alpine.js dengan debounce 300ms saat user mengetik nama
    Route::get('/ajax/cari-jemaat', function (Request $request, ApiService $api) {
        $result = $api->getMembers(['search' => $request->query('q'), 'per_page' => 10, 'status' => 'Aktif']);
        return response()->json($result['data']['data'] ?? []);
    })->name('ajax.cari-jemaat');
});
