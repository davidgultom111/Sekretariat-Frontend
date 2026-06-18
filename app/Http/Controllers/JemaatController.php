<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

// Mengelola CRUD data jemaat aktif melalui REST API backend
class JemaatController extends Controller
{
    // Menampilkan daftar jemaat aktif dengan fitur pencarian dan pagination
    public function index(Request $request, ApiService $api)
    {
        $result = $api->getMembers([
            'search'   => $request->query('search'),       // filter berdasarkan nama jemaat
            'status'   => 'Aktif',                         // hanya tampilkan jemaat berstatus aktif
            'per_page' => $request->query('per_page', 15), // jumlah data per halaman (default 15)
            'page'     => $request->query('page', 1),      // halaman saat ini
        ]);

        // Jika token expired, arahkan ke halaman login
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        return view('jemaat.index', [
            'members' => $result['data']['data'] ?? [],                                                // array data jemaat
            'meta'    => $result['data']['meta'] ?? ['current_page' => 1, 'last_page' => 1, 'total' => 0], // info pagination
            'filters' => $request->only(['search', 'per_page']),                                       // filter aktif (untuk mempertahankan state form)
        ]);
    }

    // Menampilkan form tambah jemaat baru
    public function create()
    {
        return view('jemaat.create');
    }

    // Menyimpan data jemaat baru ke API backend
    public function store(Request $request, ApiService $api)
    {
        // Ambil hanya field yang dibutuhkan dari form
        $result = $api->storeMember($request->only([
            'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
            'tempat_lahir', 'alamat', 'no_telepon', 'status_aktif',
        ]));

        if (isset($result['__unauthorized'])) return redirect()->route('login');

        // Kembalikan ke form dengan pesan error validasi dari API
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();

        // Jika ada error server lain
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal menyimpan data. Coba lagi.')->withInput();

        // Ambil ID jemaat baru dari response (bisa ada di data.id atau langsung di id)
        $newId = $result['data']['id'] ?? $result['id'] ?? null;

        // Redirect ke halaman detail jika ID tersedia, atau ke daftar jika tidak
        return redirect()->route($newId ? 'jemaat.show' : 'jemaat.index', $newId ? [$newId] : [])
            ->with('success', 'Data jemaat berhasil ditambahkan. Password default: 12345');
    }

    // Menampilkan detail profil satu jemaat berdasarkan ID
    public function show(int $id, ApiService $api)
    {
        $result = $api->getMember($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404); // jemaat tidak ditemukan

        return view('jemaat.show', ['member' => $result['data']]);
    }

    // Menampilkan form edit data jemaat yang sudah ada
    public function edit(int $id, ApiService $api)
    {
        $result = $api->getMember($id);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['__error'])) abort(404);

        return view('jemaat.edit', ['member' => $result['data']]);
    }

    // Menyimpan perubahan data jemaat ke API (PUT)
    public function update(Request $request, int $id, ApiService $api)
    {
        // Ambil field yang boleh diubah dari form
        $data = $request->only([
            'nama_lengkap', 'jenis_kelamin', 'tanggal_lahir',
            'tempat_lahir', 'alamat', 'no_telepon', 'status_aktif',
        ]);

        // Password hanya disertakan jika admin mengisi field password (tidak wajib diubah)
        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $result = $api->updateMember($id, $data);

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();

        return redirect()->route('jemaat.show', $id)->with('success', 'Data jemaat berhasil diperbarui.');
    }

    // Menghapus data jemaat via API (soft delete di backend)
    public function destroy(int $id, ApiService $api)
    {
        $api->deleteMember($id);
        return redirect()->route('jemaat.index')->with('success', 'Data jemaat berhasil dihapus.');
    }
}
