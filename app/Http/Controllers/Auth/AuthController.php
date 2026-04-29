<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (session()->has('api_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request, ApiService $api)
    {
        $request->validate([
            'id_jemaat' => 'required|string',
            'password'  => 'required|string',
        ]);

        $result = $api->login($request->id_jemaat, $request->password);

        if (isset($result['__error'])) {
            return back()
                ->withErrors(['id_jemaat' => 'Tidak dapat terhubung ke server. Pastikan backend API berjalan di port 8000.'])
                ->withInput();
        }

        if (isset($result['__unauthorized']) || ($result['status'] ?? '') === 'error') {
            return back()
                ->withErrors(['id_jemaat' => $result['message'] ?? 'ID Jemaat atau password salah'])
                ->withInput();
        }

        $member = $result['data']['member'] ?? null;

        if (!$member) {
            return back()
                ->withErrors(['id_jemaat' => 'Respons server tidak valid. Coba lagi.'])
                ->withInput();
        }

        if (($member['role'] ?? '') !== 'admin') {
            return back()
                ->withErrors(['id_jemaat' => 'Akun ini bukan admin. Role saat ini: ' . ($member['role'] ?? 'tidak diketahui')])
                ->withInput();
        }

        session([
            'api_token'  => $result['data']['token'],
            'admin_name' => $member['nama_lengkap'],
            'admin_id'   => $member['id'],
        ]);

        return redirect()->route('dashboard');
    }

    public function logout(ApiService $api)
    {
        $api->logout();
        session()->flush();
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
