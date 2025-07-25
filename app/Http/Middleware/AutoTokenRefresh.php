<?php

// 1. Buat Middleware untuk Auto Token Refresh
// File: app/Http/Middleware/AutoTokenRefresh.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AutoTokenRefresh
{
    protected $javaBackend;

    public function __construct()
    {
        $this->javaBackend = env('JAVA_BACKEND_URL', 'http://localhost:8080');
    }

    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login (ada session token)
        if (!session('access_token')) {
            return redirect()->route('login');
        }

        // Validasi access token
        $isValid = $this->validateAccessToken();

        if (!$isValid) {
            // Jika access token tidak valid, coba refresh
            $refreshSuccess = $this->refreshAccessToken();

            if (!$refreshSuccess) {
                // Jika refresh gagal, redirect ke login
                session()->flush();
                return redirect()->route('login')->with('error', 'Session expired. Please login again.');
            }
        }

        return $next($request);
    }

    private function validateAccessToken()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . session('access_token'),
            ])->get("{$this->javaBackend}/auth/validate");

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Token validation error: ' . $e->getMessage());
            return false;
        }
    }

    private function refreshAccessToken()
    {
        try {
            $refreshToken = session('refresh_token');

            if (!$refreshToken) {
                return false;
            }

            $response = Http::post("{$this->javaBackend}/auth/refresh-token", [
                'refreshToken' => $refreshToken
            ]);

            if ($response->successful()) {
                $data = $response->json('data');

                // Update session dengan token baru
                session([
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? session('refresh_token'),
                    'token_type' => $data['token_type'] ?? session('token_type'),
                ]);

                Log::info('Token refreshed successfully');
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Token refresh error: ' . $e->getMessage());
            return false;
        }
    }
}
