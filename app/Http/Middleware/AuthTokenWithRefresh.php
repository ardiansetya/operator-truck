<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AuthTokenWithRefresh
{
    protected $javaBackend;

    public function __construct()
    {
        $this->javaBackend = config('services.java_backend.url', 'http://localhost:8080');
    }

    public function handle(Request $request, Closure $next)
    {
        // Check if access token exists
        if (!session('access_token')) {
            Log::warning('No access token in session', ['path' => $request->path()]);
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Check if refresh token exists
        if (!session('refresh_token')) {
            Log::warning('No refresh token in session', ['path' => $request->path()]);
            session()->flush();
            return redirect()->route('login')->with('error', 'Sesi tidak valid, silakan login kembali');
        }

        // Validate current access token
        if (!$this->validateAccessToken()) {
            Log::info('Access token invalid, attempting refresh', ['path' => $request->path()]);

            // Try to refresh token before redirecting to login
            if (!$this->refreshAccessToken()) {
                Log::error('Failed to refresh token, redirecting to login', ['path' => $request->path()]);
                session()->flush();
                return redirect()->route('login')->with('error', 'Sesi kadaluarsa, silakan login kembali');
            }

            // Token refreshed successfully, validate again
            if (!$this->validateAccessToken()) {
                Log::error('Token still invalid after refresh, redirecting to login', ['path' => $request->path()]);
                session()->flush();
                return redirect()->route('login')->with('error', 'Sesi tidak valid, silakan login kembali');
            }

            Log::info('Token refreshed and validated successfully, continuing with request');
        }

        return $next($request);
    }

    private function validateAccessToken()
    {
        try {
            $response = Http::withToken(session('access_token'))
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->get("{$this->javaBackend}/api/users/profile");

            if ($response->successful()) {
                Log::debug('Access token validated successfully');
                return true;
            }

            Log::warning('Access token validation failed', [
                'status' => $response->status(),
                'path' => request()->path()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Token validation error: ' . $e->getMessage(), [
                'path' => request()->path()
            ]);
            return false;
        }
    }

    private function refreshAccessToken()
    {
        try {
            $refresh_token = session('refresh_token');
            if (!$refresh_token) {
                Log::warning('No refresh token found in session');
                return false;
            }

            Log::info('Attempting to refresh access token', ['path' => request()->path()]);

            $response = Http::withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->post("{$this->javaBackend}/auth/refresh-token", [
                    'refresh_token' => $refresh_token
                ]);

            if ($response->successful()) {
                $data = $response->json('data');
                Log::info('Refresh token response', [$data['access_token']]);

                if (!isset($data['access_token'])) {
                    Log::error('Invalid refresh token response format', [
                        'body' => $response->body()
                    ]);
                    return false;
                }

                // Update session with new tokens
                session([
                    'access_token' => $data['access_token'],
                    // 'refresh_token' => $data['refresh_token'],
                    'token_type' => $data['tokenType'] ?? 'Bearer',
                ]);

                Log::info('Access token refreshed successfully', ['path' => request()->path()]);
                return true;
            }

            Log::error('Failed to refresh access token', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Token refresh error: ' . $e->getMessage(), [
                'path' => request()->path()
            ]);
            return false;
        }
    }
}
