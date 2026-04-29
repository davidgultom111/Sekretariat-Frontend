<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class JemaatController extends Controller
{
    public function index(Request $request, ApiService $api)
    {
        $result = $api->getMembers([
            'search'   => $request->query('search'),
            'status'   => $request->query('status'),
            'per_page' => $request->query('per_page', 15),
            'page'     => $request->query('page', 1),
        ]);

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('jemaat.index', [
            'members' => $result['data']['data'] ?? [],
            'meta'    => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0],
            'filters' => $request->only(['search', 'status', 'per_page']),
        ]);
    }

    public function create()
    {
        return view('jemaat.create');
    }

    public function store(Request $request, ApiService $api)
    {
        $result = $api->storeMember($request->only([
            'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
            'tempat_lahir', 'alamat', 'no_telepon', 'status_aktif',
        ]));

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal menyimpan data. Coba lagi.')->withInput();

        $newId = $result['data']['id'] ?? $result['id'] ?? null;

        return redirect()->route($newId ? 'jemaat.show' : 'jemaat.index', $newId ? [$newId] : [])
            ->with('success', 'Data jemaat berhasil ditambahkan. Password default: 12345');
    }

    public function show(int $id, ApiService $api)
    {
        $result = $api->getMember($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('jemaat.show', ['member' => $result['data']]);
    }

    public function edit(int $id, ApiService $api)
    {
        $result = $api->getMember($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('jemaat.edit', ['member' => $result['data']]);
    }

    public function update(Request $request, int $id, ApiService $api)
    {
        $data = $request->only([
            'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
            'tempat_lahir', 'alamat', 'no_telepon', 'status_aktif',
        ]);

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $result = $api->updateMember($id, $data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();

        return redirect()->route('jemaat.show', $id)->with('success', 'Data jemaat berhasil diperbarui.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deleteMember($id);
        return redirect()->route('jemaat.index')->with('success', 'Data jemaat berhasil dihapus.');
    }
}
