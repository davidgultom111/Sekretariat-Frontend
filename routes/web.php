<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryJemaatController;
use App\Http\Controllers\JemaatController;
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

    // AJAX endpoint untuk pencarian jemaat di form surat
    Route::get('/ajax/cari-jemaat', function (Request $request, ApiService $api) {
        $result = $api->getMembers(['search' => $request->query('q'), 'per_page' => 10, 'status' => 'Aktif']);
        return response()->json($result['data']['data'] ?? []);
    })->name('ajax.cari-jemaat');
});
