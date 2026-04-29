<?php

namespace App\Http\Controllers;

use App\Services\ApiService;

class DashboardController extends Controller
{
    public function index(ApiService $api)
    {
        $memberResult = $api->getMembers(['per_page' => 1]);
        $letterResult = $api->getLetters(['per_page' => 5]);

        return view('dashboard', [
            'totalJemaat'  => $memberResult['data']['meta']['total'] ?? 0,
            'totalSurat'   => $letterResult['data']['meta']['total'] ?? 0,
            'suratTerbaru' => $letterResult['data']['data'] ?? [],
        ]);
    }
}
