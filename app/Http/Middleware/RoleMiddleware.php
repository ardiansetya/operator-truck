<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    protected $baseUrl;

    public function __construct()
    {
        $this->baseUrl = env('JAVA_BACKEND_URL', 'http://localhost:8080');
    }

    public function handle(Request $request, Closure $next, ...$roles)
    {
        try {
            // Cek konfigurasi URL backend
            if (empty($this->baseUrl)) {
                Log::error('Konfigurasi base URL API tidak ditemukan di RoleMiddleware');
                return $this->redirectWithError($request, 'Konfigurasi server tidak lengkap', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Ambil token dari session
            $token = $request->session()->get('access_token');
            if (!$token) {
                Log::warning('Token akses tidak ditemukan di sesi', ['path' => $request->path()]);
                return $this->redirectWithError($request, 'Silakan login terlebih dahulu', Response::HTTP_UNAUTHORIZED);
            }

            // Request profil user dari backend
            $response = Http::withToken($token)
                ->withHeaders(['Accept' => 'application/json'])
                ->get("{$this->baseUrl}/api/users/profile");

            if (!$response->successful()) {
                $status = $response->status();
                $message = match ($status) {
                    401 => 'Token tidak valid atau kadaluarsa',
                    403 => 'Akses ditolak: Tidak memiliki izin untuk mengakses profil',
                    default => 'Gagal memuat profil: ' . $response->json('message', 'Kesalahan server'),
                };

                Log::error('Gagal memuat profil user di RoleMiddleware', [
                    'status' => $status,
                    'body' => $response->body(),
                    'path' => $request->path(),
                ]);

                return $this->redirectWithError($request, $message, $status);
            }

            // Ambil role dari response
            $user = $response->json('data') ?? [];
            $userRole = $user['role'] ?? null;

            // Log role untuk debugging
            Log::info('Role user terdeteksi dari backend', [
                'role' => $userRole,
                'required_roles' => $roles,
                'user' => $user,
                'path' => $request->path(),
            ]);

            // Validasi role
            if (!$userRole || !in_array($userRole, $roles)) {
                Log::warning('User tidak memiliki role yang dibutuhkan', [
                    'user_role' => $userRole,
                    'required_roles' => $roles,
                    'path' => $request->path(),
                ]);
                return $this->redirectWithError($request, 'Akses ditolak: Anda tidak memiliki izin', Response::HTTP_FORBIDDEN);
            }

            // Simpan role di request jika ingin digunakan di controller
            $request->merge(['user_role' => $userRole]);

            return $next($request);
        } catch (\Exception $e) {
            Log::error('Kesalahan di RoleMiddleware: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->redirectWithError($request, 'Terjadi kesalahan: ' . $e->getMessage(), Response::HTTP_UNAUTHORIZED);
        }
    }

    /**
     * Helper untuk mengarahkan ulang dengan error message.
     */
    private function redirectWithError(Request $request, string $message, int $status)
    {
        return $request->expectsJson()
            ? response()->json(['error' => $message], $status)
            : redirect()->route('login')->withErrors(['message' => $message]);
    }
}
