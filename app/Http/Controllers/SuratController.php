<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class SuratController extends Controller
{
    private array $letterTypes = [
        'surat_tugas_pelayanan'           => 'Surat Tugas Pelayanan',
        'surat_pengantar'                 => 'Surat Pengantar',
        'surat_keterangan_jemaat_aktif'   => 'Surat Keterangan Jemaat Aktif',
        'surat_nilai_sekolah'             => 'Surat Nilai Sekolah',
        'surat_pengajuan_baptisan'        => 'Surat Pengajuan Baptisan',
        'surat_pengajuan_penyerahan_anak' => 'Surat Pengajuan Penyerahan Anak',
        'surat_pengajuan_pernikahan'      => 'Surat Pengajuan Pernikahan',
    ];

    public function index(Request $request, ApiService $api)
    {
        $result = $api->getLetters([
            'search'      => $request->query('search'),
            'letter_type' => $request->query('letter_type'),
            'per_page'    => $request->query('per_page', 15),
            'page'        => $request->query('page', 1),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('surat.index', [
            'letters'     => $result['data']['data'] ?? [],
            'meta'        => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0],
            'filters'     => $request->only(['search', 'letter_type', 'per_page']),
            'letterTypes' => $this->letterTypes,
        ]);
    }

    public function create()
    {
        return view('surat.create', ['letterTypes' => $this->letterTypes]);
    }

    public function store(Request $request, ApiService $api)
    {
        $data = array_filter(
            $request->except(['_token', '_method']),
            fn($v) => $v !== null && $v !== ''
        );

        $result = $api->storeLetter($data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal membuat surat. Coba lagi.')->withInput();

        $newId      = $result['data']['id']          ?? $result['id']          ?? null;
        $nomorSurat = $result['data']['nomor_surat'] ?? $result['nomor_surat'] ?? '';

        return redirect()->route($newId ? 'surat.show' : 'surat.index', $newId ? [$newId] : [])
            ->with('success', 'Surat berhasil dibuat' . ($nomorSurat ? ': ' . $nomorSurat : '.'));
    }

    public function show(int $id, ApiService $api)
    {
        $result = $api->getLetter($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('surat.show', [
            'surat'       => $result['data'],
            'letterTypes' => $this->letterTypes,
        ]);
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deleteLetter($id);
        return redirect()->route('surat.index')->with('success', 'Surat berhasil dihapus.');
    }

    public function downloadPdf(int $id, ApiService $api)
    {
        $response = $api->downloadPdf($id);

        if ($response->failed()) {
            return back()->with('error', 'PDF tidak tersedia atau gagal diunduh.');
        }

        return response($response->body(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat-' . $id . '.pdf"',
        ]);
    }
}
