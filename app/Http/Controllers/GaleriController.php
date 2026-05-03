<?php

namespace App\Http\Controllers;

use App\Services\ApiService;
use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index(ApiService $api)
    {
        $result = $api->getGaleris();
        if (isset($result['__unauthorized'])) return redirect()->route('login');

        $galeris = $result['data'] ?? [];
        usort($galeris, fn($a, $b) => $b['id'] <=> $a['id']);

        return view('galeri.index', ['galeris' => $galeris]);
    }

    public function create()
    {
        return view('galeri.create');
    }

    public function store(Request $request, ApiService $api)
    {
        $request->validate([
            'judul' => 'required|string|max:150',
            'foto'  => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
        ]);

        $result = $api->storeGaleri(
            $request->only(['judul', 'deskripsi']),
            $request->file('foto')
        );

        if (isset($result['__unauthorized'])) return redirect()->route('login');
        if (isset($result['errors'])) return back()->withErrors($result['errors'])->withInput();
        if (isset($result['__error'])) return back()->with('error', $result['__message'] ?? 'Gagal mengunggah foto.')->withInput();

        return redirect()->route('galeri.index')->with('success', 'Foto berhasil ditambahkan.');
    }

    public function destroy(int $id, ApiService $api)
    {
        $api->deleteGaleri($id);
        return redirect()->route('galeri.index')->with('success', 'Foto berhasil dihapus.');
    }
}
