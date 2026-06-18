<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ApiService;
use Illuminate\Http\Request;

// Mengelola autentikasi admin: login, logout, dan redirect berdasarkan status session
class AuthController extends Controller
{
    // Menampilkan halaman login
    // Jika sudah login (ada api_token di session), langsung redirect ke dashboard
    public function showLogin()
    {
        if (session()->has('api_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Memproses form login: validasi input, kirim ke API, simpan token ke session
    public function login(Request $request, ApiService $api)
    {
        // Validasi wajib: id_jemaat dan password harus diisi
        $request->validate([
            'id_jemaat' => 'required|string',
            'password'  => 'required|string',
        ]);

        // Kirim request login ke API backend
        $result = $api->login($request->id_jemaat, $request->password);

        // Jika backend tidak bisa diakses (koneksi error)
        if (isset($result['__error'])) {
            return back()
                ->withErrors(['id_jemaat' => 'Tidak dapat terhubung ke server. Pastikan backend API berjalan di port 8000.'])
                ->withInput();
        }

        // Jika credentials salah atau response error dari API
        if (isset($result['__unauthorized']) || ($result['status'] ?? '') === 'error') {
            return back()
                ->withErrors(['id_jemaat' => $result['message'] ?? 'ID Jemaat atau password salah'])
                ->withInput();
        }

        // Ambil data member dari response API
        $member = $result['data']['member'] ?? null;

        // Jika response tidak mengandung data member yang valid
        if (!$member) {
            return back()
                ->withErrors(['id_jemaat' => 'Respons server tidak valid. Coba lagi.'])
                ->withInput();
        }

        // Hanya akun dengan role admin yang boleh masuk ke panel ini
        if (($member['role'] ?? '') !== 'admin') {
            return back()
                ->withErrors(['id_jemaat' => 'Akun ini bukan admin. Role saat ini: ' . ($member['role'] ?? 'tidak diketahui')])
                ->withInput();
        }

        // Simpan token Sanctum dan info admin ke session PHP
        session([
            'api_token'  => $result['data']['token'],  // dipakai di setiap request API selanjutnya
            'admin_name' => $member['nama_lengkap'],    // ditampilkan di navbar
            'admin_id'   => $member['id'],              // ID jemaat admin yang sedang login
        ]);

        return redirect()->route('dashboard');
    }

    // Menghapus session dan memanggil endpoint logout API untuk mencabut token Sanctum
    public function logout(ApiService $api)
    {
        $api->logout(); // cabut token di sisi server
        session()->flush(); // hapus semua data session di sisi client
        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}
