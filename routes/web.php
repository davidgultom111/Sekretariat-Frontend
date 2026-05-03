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

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth.admin');

Route::middleware('auth.admin')->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('jemaat')->name('jemaat.')->group(function () {
        Route::get('/', [JemaatController::class, 'index'])->name('index');
        Route::get('/tambah', [JemaatController::class, 'create'])->name('create');
        Route::post('/', [JemaatController::class, 'store'])->name('store');
        Route::get('/{id}', [JemaatController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [JemaatController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JemaatController::class, 'update'])->name('update');
        Route::delete('/{id}', [JemaatController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('history')->name('history.')->group(function () {
        Route::get('/', [HistoryJemaatController::class, 'index'])->name('index');
        Route::post('/{id}/aktifkan', [HistoryJemaatController::class, 'activate'])->name('activate');
        Route::delete('/{id}', [HistoryJemaatController::class, 'destroy'])->name('destroy');
    });

    Route::prefix('surat')->name('surat.')->group(function () {
        Route::get('/', [SuratController::class, 'index'])->name('index');
        Route::get('/buat', [SuratController::class, 'create'])->name('create');
        Route::post('/', [SuratController::class, 'store'])->name('store');
        Route::get('/{id}', [SuratController::class, 'show'])->name('show');
        Route::delete('/{id}', [SuratController::class, 'destroy'])->name('destroy');
        Route::get('/{id}/pdf', [SuratController::class, 'downloadPdf'])->name('pdf');
    });

    // Jadwal Pelayanan
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/', [JadwalController::class, 'index'])->name('index');
        Route::get('/tambah', [JadwalController::class, 'create'])->name('create');
        Route::post('/', [JadwalController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [JadwalController::class, 'edit'])->name('edit');
        Route::put('/{id}', [JadwalController::class, 'update'])->name('update');
        Route::delete('/{id}', [JadwalController::class, 'destroy'])->name('destroy');
    });

    // Galeri Foto
    Route::prefix('galeri')->name('galeri.')->group(function () {
        Route::get('/', [GaleriController::class, 'index'])->name('index');
        Route::get('/tambah', [GaleriController::class, 'create'])->name('create');
        Route::post('/', [GaleriController::class, 'store'])->name('store');
        Route::delete('/{id}', [GaleriController::class, 'destroy'])->name('destroy');
    });

    // Pengumuman
    Route::prefix('pengumuman')->name('pengumuman.')->group(function () {
        Route::get('/', [PengumumanController::class, 'index'])->name('index');
        Route::get('/tambah', [PengumumanController::class, 'create'])->name('create');
        Route::post('/', [PengumumanController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [PengumumanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PengumumanController::class, 'update'])->name('update');
        Route::delete('/{id}', [PengumumanController::class, 'destroy'])->name('destroy');
    });

    // Pengajuan Surat
    Route::prefix('pengajuan')->name('pengajuan.')->group(function () {
        Route::get('/', [PengajuanController::class, 'index'])->name('index');
        Route::get('/{id}', [PengajuanController::class, 'show'])->name('show');
        Route::put('/{id}/setujui', [PengajuanController::class, 'approve'])->name('approve');
        Route::put('/{id}/tolak', [PengajuanController::class, 'reject'])->name('reject');
        Route::delete('/{id}', [PengajuanController::class, 'destroy'])->name('destroy');
    });

    // AJAX endpoint untuk pencarian jemaat di form surat
    Route::get('/ajax/cari-jemaat', function (Request $request, ApiService $api) {
        $result = $api->getMembers(['search' => $request->query('q'), 'per_page' => 10, 'status' => 'Aktif']);
        return response()->json($result['data']['data'] ?? []);
    })->name('ajax.cari-jemaat');
});
