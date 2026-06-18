<?php

namespace App\Http\Controllers;

use App\Services\ApiService;

// Mengelola tampilan dashboard utama dengan ringkasan statistik
class DashboardController extends Controller
{
    // Mengambil total jemaat dan 5 surat terbaru untuk ditampilkan di dashboard
    public function index(ApiService $api)
    {
        // Ambil meta pagination saja (per_page=1) untuk mendapatkan total jemaat aktif
        $memberResult = $api->getMembers(['per_page' => 1]);

        // Ambil 5 surat terbaru untuk ditampilkan di widget "Surat Terbaru"
        $letterResult = $api->getLetters(['per_page' => 5]);

        return view('dashboard', [
            'totalJemaat'  => $memberResult['data']['meta']['total'] ?? 0,  // total seluruh jemaat aktif
            'totalSurat'   => $letterResult['data']['meta']['total'] ?? 0,  // total seluruh surat yang pernah dibuat
            'suratTerbaru' => $letterResult['data']['data'] ?? [],           // 5 surat paling baru
        ]);
    }
}
