<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class HistoryJemaatController extends Controller
{
    public function index(Request $request, ApiService $api)
    {
        $result = $api->getMembers([
            'search'   => $request->query('search'),
            'status'   => 'Tidak Aktif',
            'per_page' => $request->query('per_page', 15),
            'page'     => $request->query('page', 1),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('history.index', [
            'members' => $result['data']['data'] ?? [],
            'meta'    => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0],
            'filters' => $request->only(['search', 'per_page']),
        ]);
    }

    public function activate(int $id, ApiService $api)
    {
        $result = $api->updateMember($id, ['status_aktif' => 'Aktif']);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return redirect()->route('history.index')->with('success', 'Jemaat berhasil diaktifkan kembali.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deleteMember($id);
        return redirect()->route('history.index')->with('success', 'Data jemaat berhasil dihapus dari history.');
    }
}
