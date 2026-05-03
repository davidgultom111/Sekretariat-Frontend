<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PengajuanController extends Controller
{
    public function index(Request $request, ApiService $api)
    {
        $status = $request->query('status');
        $result = $api->getPengajuans(['status' => $status]);
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('pengajuan.index', [
            'pengajuans' => $result['data'] ?? [],
            'statusFilter' => $status,
        ]);
    }

    public function show(int $id, ApiService $api)
    {
        $result = $api->getPengajuan($id);
        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('pengajuan.show', [
            'pengajuan' => $result['data'] ?? [],
        ]);
    }

    public function approve(Request $request, int $id, ApiService $api)
    {
        $result = $api->setujuiPengajuan($id, [
            'tanggal_surat' => $request->input('tanggal_surat'),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) {
            return back()->with('error', $result['__message'] ?? 'Gagal menyetujui pengajuan.');
        }

        $letterId = $result['data']['letter_id'] ?? null;
        $nomorSurat = $result['data']['nomor_surat'] ?? '-';

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan disetujui. Surat {$nomorSurat} berhasil dibuat.")
            ->with('letter_id', $letterId);
    }

    public function reject(Request $request, int $id, ApiService $api)
    {
        $result = $api->tolakPengajuan($id, [
            'catatan' => $request->input('catatan'),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) {
            return back()->with('error', $result['__message'] ?? 'Gagal menolak pengajuan.');
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deletePengajuan($id);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
