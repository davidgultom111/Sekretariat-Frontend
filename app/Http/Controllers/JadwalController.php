<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(ApiService $api)
    {
        $result = $api->getJadwals();
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        $jadwals = $result['data'] ?? [];
        usort($jadwals, fn($a, $b) => $b['id'] <=> $a['id']);

        return view('jadwal.index', ['jadwals' => $jadwals]);
    }

    public function create()
    {
        return view('jadwal.create');
    }

    public function store(Request $request, ApiService $api)
    {
        $data = $request->only(['nama_kegiatan', 'kategori', 'deskripsi', 'hari', 'waktu']);
        $data['aktif'] = true;

        $result = $api->storeJadwal($data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal menambahkan jadwal.')->withInput();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(int $id, ApiService $api)
    {
        $result = $api->getJadwals();
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        $jadwal = collect($result['data'] ?? [])->firstWhere('id', $id);
        if (!$jadwal) abort(404);

        return view('jadwal.edit', ['jadwal' => $jadwal]);
    }

    public function update(Request $request, int $id, ApiService $api)
    {
        $data = $request->only(['nama_kegiatan', 'kategori', 'deskripsi', 'hari', 'waktu']);
        $data['aktif'] = true;

        $result = $api->updateJadwal($id, $data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal memperbarui jadwal.')->withInput();

        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deleteJadwal($id);
        return redirect()->route('jadwal.index')->with('success', 'Jadwal berhasil dihapus.');
    }
}
