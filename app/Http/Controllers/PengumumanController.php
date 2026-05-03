<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index(ApiService $api)
    {
        $result = $api->getPengumumans();
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('pengumuman.index', [
            'pengumuman' => $result['data'] ?? [],
        ]);
    }

    public function create()
    {
        return view('pengumuman.create');
    }

    public function store(Request $request, ApiService $api)
    {
        $data = $request->only(['judul', 'isi', 'tanggal_mulai', 'tanggal_akhir']);
        $data['aktif'] = true;

        $result = $api->storePengumuman($data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal menambahkan pengumuman.')->withInput();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil ditambahkan.');
    }

    public function edit(int $id, ApiService $api)
    {
        $result = $api->getPengumumans();
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        $item = collect($result['data'] ?? [])->firstWhere('id', $id);
        if (!$item) abort(404);

        return view('pengumuman.edit', ['pengumuman' => $item]);
    }

    public function update(Request $request, int $id, ApiService $api)
    {
        $data = $request->only(['judul', 'isi', 'tanggal_mulai', 'tanggal_akhir']);
        $data['aktif'] = true;

        $result = $api->updatePengumuman($id, $data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal memperbarui pengumuman.')->withInput();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil diperbarui.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deletePengumuman($id);
        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }
}
