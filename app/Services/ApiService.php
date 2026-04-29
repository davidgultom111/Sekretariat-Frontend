<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiService
{
    private string $base;

    public function __construct()
    {
        $this->base = config('services.api.base_url');
    }

    private function http(): PendingRequest
    {
        return Http::baseUrl($this->base)
            ->acceptJson()
            ->withToken(session('api_token', ''));
    }

    private function handle(Response $response): array
    {
        if ($response->status() === 401) {
            session()->forget(['api_token', 'admin_name', 'admin_id']);
            return ['__unauthorized' => true];
        }
        if ($response->status() === 422) {
            return ['errors' => $response->json('errors', [])];
        }
        if ($response->status() === 204) {
            return ['success' => true];
        }
        if ($response->failed()) {
            return ['__error' => $response->status(), '__message' => $response->json('message', 'Server error')];
        }
        return $response->json() ?? [];
    }

    // --- Auth ---

    public function login(string $idJemaat, string $password): array
    {
        try {
            $response = Http::baseUrl($this->base)
                ->acceptJson()
                ->post('/auth/login', ['id_jemaat' => $idJemaat, 'password' => $password]);
            return $this->handle($response);
        } catch (ConnectionException) {
            return ['__error' => 0];
        }
    }

    public function logout(): void
    {
        $this->http()->delete('/me/logout');
    }

    // --- Members ---

    public function getMembers(array $params = []): array
    {
        $filtered = array_filter($params, fn($v) => $v !== null && $v !== '');
        return $this->handle($this->http()->get('/admin/members', $filtered));
    }

    public function getMember(int $id): array
    {
        return $this->handle($this->http()->get("/admin/members/{$id}"));
    }

    public function storeMember(array $data): array
    {
        return $this->handle($this->http()->post('/admin/members', $data));
    }

    public function updateMember(int $id, array $data): array
    {
        return $this->handle($this->http()->put("/admin/members/{$id}", $data));
    }

    public function deleteMember(int $id): array
    {
        return $this->handle($this->http()->delete("/admin/members/{$id}"));
    }

    // --- Letters ---

    public function getLetters(array $params = []): array
    {
        $filtered = array_filter($params, fn($v) => $v !== null && $v !== '');
        return $this->handle($this->http()->get('/admin/letters', $filtered));
    }

    public function getLetter(int $id): array
    {
        return $this->handle($this->http()->get("/admin/letters/{$id}"));
    }

    public function storeLetter(array $data): array
    {
        return $this->handle($this->http()->post('/admin/letters', $data));
    }

    public function deleteLetter(int $id): array
    {
        return $this->handle($this->http()->delete("/admin/letters/{$id}"));
    }

    public function downloadPdf(int $id): Response
    {
        return $this->http()->get("/admin/letters/{$id}/pdf");
    }
}
