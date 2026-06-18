<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

// Mengelola data jemaat yang tidak aktif (history keluar/dinonaktifkan)
class HistoryJemaatController extends Controller
{
    // Menampilkan daftar jemaat berstatus "Tidak Aktif" dengan pencarian dan pagination
    public function index(Request $request, ApiService $api)
    {
        $result = $api->getMembers([
            'search'   => $request->query('search'),       // filter nama jemaat
            'status'   => 'Tidak Aktif',                   // hanya tampilkan jemaat tidak aktif
            'per_page' => $request->query('per_page', 15), // jumlah data per halaman
            'page'     => $request->query('page', 1),      // halaman saat ini
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('history.index', [
            'members' => $result['data']['data'] ?? [],                                                 // array data jemaat tidak aktif
            'meta'    => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0], // info pagination
            'filters' => $request->only(['search', 'per_page']),                                        // filter aktif
        ]);
    }

    // Mengaktifkan kembali jemaat yang sebelumnya tidak aktif
    // Mengubah status_aktif menjadi 'Aktif' melalui endpoint update member
    public function activate(int $id, ApiService $api)
    {
        $result = $api->updateMember($id, ['status_aktif' => 'Aktif']);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return redirect()->route('history.index')->with('success', 'Jemaat berhasil diaktifkan kembali.');
    }

    // Menghapus permanen data jemaat dari history via API
    public function destroy(int $id, ApiService $api)
    {
        $api->deleteMember($id);
        return redirect()->route('history.index')->with('success', 'Data jemaat berhasil dihapus dari history.');
    }
}
