# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**Sekretariat GPdI** adalah Laravel 12 Blade admin frontend untuk sistem manajemen sekretariat Gereja Pentekosta di Indonesia (GPdI) Jemaat Sahabat Allah Palembang. Mengkonsumsi REST API dari backend Laravel di `http://127.0.0.1:8000/api`. Dua fitur utama: manajemen data jemaat (CRUD) dan pembuatan 7 jenis surat gereja dengan download PDF.

## Menjalankan Aplikasi

**Kedua server harus berjalan di terminal terpisah yang tetap terbuka.**

```bash
# Terminal 1 — Backend API (wajib jalan duluan)
cd D:\Skripsi\sekretariat
php artisan serve --port=8000

# Terminal 2 — Frontend ini
php artisan serve --port=8001

# Terminal 3 — Vite HMR (opsional, untuk development)
npm run dev
```

> **PENTING:** Jangan jalankan backend sebagai background process (`&`). `php artisan serve` tidak persist antar-command di bash tool — selalu minta user menjalankan di terminal mereka sendiri.

MySQL (XAMPP) juga harus berjalan sebelum backend bisa menerima request.

> **PHP opcache:** Jika mengubah file PHP di backend (terutama Resources), perubahan mungkin tidak langsung aktif karena opcache. Restart `php artisan serve` di backend untuk memaksa reload.

```bash
# Build Vite assets (jika tidak pakai npm run dev)
npm run build

# Clear caches setelah ubah config/.env
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Migrate (tabel sessions/cache/jobs — bukan data aplikasi)
php artisan migrate
```

## Architecture

### Stack
- **Laravel 12** + Blade templating
- **Tailwind CSS v4** via `@tailwindcss/vite` — tidak butuh `tailwind.config.js`
- **Alpine.js** via CDN untuk interaktivitas (modal, form dinamis, autocomplete search)
- **SQLite** hanya untuk session/cache/jobs — semua data aplikasi dari API

### Alur Data

Tidak ada model Eloquent untuk data aplikasi. Semua data dari REST API:

```
Browser → Laravel Controller → ApiService → REST API (port 8000)
                ↑                                      ↓
           Blade View ←←←←←←←←←←←←←←←←←←←←←←← JSON Response
```

### Authentication

Session-based: token Sanctum dari API disimpan di PHP session (`session('api_token')`). Middleware `EnsureAuthenticated` (alias `auth.admin`) cek keberadaan session ini. Alias didaftarkan di `bootstrap/app.php` (bukan `Kernel.php` — ini Laravel 12). Login hanya untuk member dengan `role === 'admin'`.

Urutan pengecekan di `AuthController::login()`:
1. `__error` ada → backend/MySQL tidak bisa diakses → tampilkan "tidak dapat terhubung"
2. `__unauthorized` atau `status === 'error'` → credentials salah → tampilkan pesan dari API
3. `$member` null → respons malformed
4. `role !== 'admin'` → akun bukan admin → tampilkan role saat ini (berguna untuk debug)

### ApiService (`app/Services/ApiService.php`)

Satu-satunya titik komunikasi ke backend. Semua method mengembalikan array dengan konvensi:

| Return key | HTTP status | Arti |
|---|---|---|
| `['__unauthorized' => true]` | 401 | Token expired, session di-clear otomatis |
| `['errors' => [...]]` | 422 | Validasi gagal, key = nama field |
| `['success' => true]` | 204 | DELETE berhasil (body kosong) |
| `['__error' => $status]` | 4xx/5xx lain | Error server/koneksi |
| array normal | 200/201 | Sukses dengan data |

`downloadPdf()` satu-satunya method yang mengembalikan `Response` langsung (bukan array) karena binary stream perlu diteruskan langsung ke browser.

**Inkonsistensi response POST vs GET di backend:** Endpoint `GET /admin/members/{id}` dan `GET /admin/letters/{id}` membungkus data dalam `data` key (`{"status":"success","data":{...}}`), sehingga `handle()` mengembalikan array dengan `['data' => {...}]`. Namun `POST /admin/members` dan `POST /admin/letters` mengembalikan flat JSON langsung tanpa wrapper `data`. Karena itu `store()` di kedua controller menggunakan fallback: `$result['data']['id'] ?? $result['id'] ?? null`.

### Jemaat (Member) Fields

Field yang dikirim ke API saat `store`/`update`: `nama_lengkap`, `jenis_kelamin`, `tanggal_lahir`, `tempat_lahir`, `alamat`, `no_telepon`, `status_aktif`. Untuk `update`, `password` hanya disertakan jika diisi (`$request->filled('password')`). Default password member baru: `12345` (ditetapkan oleh backend).

### Tipe Surat & Field Spesifik

Semua tipe wajib mengirim `letter_type`, `tanggal_surat`, dan `keterangan`. `member_id` wajib untuk semua tipe **kecuali** `surat_pengajuan_pernikahan`. Field tambahan per tipe:

| Tipe (`letter_type`) | Field tambahan wajib |
|---|---|
| `surat_tugas_pelayanan` | `tgl_mulai_tugas`, `tgl_akhir_tugas`, `tujuan_tugas` (min 10 char) |
| `surat_pengantar` | `keterangan` wajib (min 10 char) — satu-satunya tipe di mana `keterangan` wajib |
| `surat_keterangan_jemaat_aktif` | `tahun_bergabung` |
| `surat_nilai_sekolah` | `asal_sekolah`, `kelas`, `semester`; `nilai` opsional (0–100) |
| `surat_pengajuan_baptisan` | — (hanya field umum) |
| `surat_pengajuan_penyerahan_anak` | `nama_ayah`, `nama_ibu`, `nama_anak`, `tempat_lahir_anak`, `tanggal_lahir_anak` |
| `surat_pengajuan_pernikahan` | `member_pria_id`, `member_wanita_id`, `tanggal_pernikahan` (ganti `member_id`) |

### Form Surat Dinamis (`resources/views/surat/create.blade.php`)

View paling kompleks. Alpine.js mengelola dua komponen terpisah:

1. **`suratForm()`** — reactive `tipe` yang di-bind ke `<select letter_type>`. Semua section field pakai `x-show="tipe === 'nama_tipe'"`, kecuali section umum yang pakai `x-show="tipe !== ''"`.

2. **`memberSearch(fieldName)`** — reusable autocomplete yang fetch `/ajax/cari-jemaat?q=...` (debounce 300ms). Diinstansiasi 3x: `member_id`, `member_pria_id`, `member_wanita_id`. State `selectedId`/`selectedName` dikelola per-instance; hidden input mengirim value ke form.

Surat pernikahan (`surat_pengajuan_pernikahan`) tidak punya `member_id` — hanya `member_pria_id` + `member_wanita_id`.

### Pagination

Dirender manual di Blade (bukan Laravel paginator) karena data dari API — tidak ada LengthAwarePaginator. Pattern di semua list view:

```blade
@for($p = 1; $p <= $meta['last_page']; $p++)
    <a href="{{ route('jemaat.index', array_merge($filters, ['page' => $p])) }}">{{ $p }}</a>
@endfor
```

`$filters` diambil dari `$request->only([...])` di controller, di-merge ke query string agar filter tetap aktif saat ganti halaman.

### Environment

```
APP_URL=http://localhost:8001
API_BASE_URL=http://127.0.0.1:8000/api  # → config('services.api.base_url')
SESSION_DRIVER=database                  # SQLite, tabel sessions ada di migrasi default
```
