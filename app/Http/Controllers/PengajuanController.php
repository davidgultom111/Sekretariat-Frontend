<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

// Mengelola pengajuan surat dari jemaat yang perlu disetujui atau ditolak admin
class PengajuanController extends Controller
{
    // Menampilkan daftar semua pengajuan surat, dapat difilter berdasarkan status
    public function index(Request $request, ApiService $api)
    {
        $status = $request->query('status'); // misal: 'menunggu', 'disetujui', 'ditolak'
        $result = $api->getPengajuans(['status' => $status]);
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('pengajuan.index', [
            'pengajuans'   => $result['data'] ?? [],  // daftar pengajuan
            'statusFilter' => $status,                 // status aktif untuk highlight tab/filter
        ]);
    }

    // Menampilkan detail satu pengajuan surat beserta data jemaat pengaju
    public function show(int $id, ApiService $api)
    {
        $result = $api->getPengajuan($id);
        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('pengajuan.show', [
            'pengajuan' => $result['data'] ?? [],  // detail pengajuan dan data jemaat
        ]);
    }

    // Admin menyetujui pengajuan → backend otomatis membuat surat resmi
    public function approve(Request $request, int $id, ApiService $api)
    {
        // tanggal_surat wajib diisi admin saat menyetujui
        $result = $api->setujuiPengajuan($id, [
            'tanggal_surat' => $request->input('tanggal_surat'),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) {
            return back()->with('error', $result['__message'] ?? 'Gagal menyetujui pengajuan.');
        }

        // Ambil ID surat yang dibuat dan nomor suratnya dari response
        $letterId   = $result['data']['letter_id'] ?? null;
        $nomorSurat = $result['data']['nomor_surat'] ?? '-';

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan disetujui. Surat {$nomorSurat} berhasil dibuat.")
            ->with('letter_id', $letterId); // diteruskan ke view untuk menampilkan link ke surat
    }

    // Admin menolak pengajuan dengan catatan alasan penolakan
    public function reject(Request $request, int $id, ApiService $api)
    {
        $result = $api->tolakPengajuan($id, [
            'catatan' => $request->input('catatan'), // catatan penolakan dari admin
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) {
            return back()->with('error', $result['__message'] ?? 'Gagal menolak pengajuan.');
        }

        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil ditolak.');
    }

    // Menghapus data pengajuan via API (biasanya untuk pengajuan lama)
    public function destroy(int $id, ApiService $api)
    {
        $api->deletePengajuan($id);
        return redirect()->route('pengajuan.index')->with('success', 'Pengajuan berhasil dihapus.');
    }
}
