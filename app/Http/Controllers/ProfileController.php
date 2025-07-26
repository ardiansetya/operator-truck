<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProfileController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = config('services.java_backend.url');
    }

    public function show()
    {
        try {
            if (empty($this->baseUrl)) {
                Log::error('API base URL configuration is missing in ProfileController');
                return view('profile.show', ['profile' => null, 'error' => 'Konfigurasi server tidak lengkap']);
            }

            $response = $this->makeRequest('get', $this->baseUrl . '/api/users/profile');
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $status = $response->status();
                $errorMessage = $status === 403
                    ? 'Akses ditolak: Tidak memiliki izin untuk mengakses profil'
                    : ($status === 401
                        ? 'Token tidak valid atau kadaluarsa'
                        : 'Gagal memuat profil: ' . $response->json('message', 'Kesalahan server'));
                Log::error('Failed to fetch user profile in ProfileController', [
                    'status' => $status,
                    'body' => $response->body(),
                ]);
                return view('profile.show', ['profile' => null, 'error' => $errorMessage]);
            }

            $profile = $response->json('data') ?? [];

            return view('profile.show', compact('profile'));
        } catch (\Exception $e) {
            Log::error('Error fetching profile: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('profile.show', ['profile' => null, 'error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            }

            $validated = $request->validate([
                'username' => 'required|string|max:255|alpha_dash',
                'email' => 'required|email',
                'phone_number' => 'required|string|max:15',
                'age' => 'required|integer|min:18',
            ]);

            $payload = [
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone_number' => $validated['phone_number'],
                'age' => (int) $validated['age'],
            ];

            Log::info('Updating profile with payload', ['payload' => $payload]);

            // Fetch current profile to check for username change
            $currentProfileResponse = $this->makeRequest('get', $this->baseUrl . '/api/users/profile');
            if ($currentProfileResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $currentProfileResponse;
            }
            $currentUsername = $currentProfileResponse->successful() ? ($currentProfileResponse->json('data')['username'] ?? '') : '';

            $response = $this->makeRequest('post', $this->baseUrl . '/api/users/profile', $payload);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $status = $response->status();
                $errorMessage = $status === 403
                    ? 'Akses ditolak: Tidak memiliki izin untuk memperbarui profil'
                    : ($status === 401
                        ? 'Token tidak valid atau kadaluarsa'
                        : 'Gagal memperbarui profil: ' . $response->json('message', 'Kesalahan server'));
                Log::error('Failed to update profile', ['status' => $status, 'body' => $response->body()]);
                return back()->withErrors(['message' => $errorMessage]);
            }

            // SOLUSI 1: Jika username berubah, refresh token secara proaktif
            if ($currentUsername && $currentUsername !== $validated['username']) {
                Log::info('Username changed, refreshing access token proactively', ['new_username' => $validated['username']]);

                if (!$this->refreshAccessTokenProactively()) {
                    Log::warning('Failed to refresh token after username change, but profile was updated');
                    // Tidak redirect ke login, biarkan middleware handle di request selanjutnya
                }
            }

            return redirect()->route('profile.show')->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating profile: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Refresh access token proactively without invalidating current session
     */
    private function refreshAccessTokenProactively()
    {
        try {
            $refresh_token = session('refresh_token');
            if (!$refresh_token) {
                Log::warning('No refresh token found for proactive refresh');
                return false;
            }

            Log::info('Attempting proactive token refresh');
            $response = \Illuminate\Support\Facades\Http::withHeaders(['Accept' => 'application/json'])
                ->post($this->baseUrl . '/api/auth/refresh-token', [
                    'refresh_token' => $refresh_token
                ]);

            if ($response->successful()) {
                $data = $response->json('data') ?? $response->json();
                if (!isset($data['accessToken']) || !isset($data['refresh_token'])) {
                    Log::error('Invalid refresh token response format', ['body' => $response->body()]);
                    return false;
                }

                // Update session dengan token baru
                session([
                    'access_token' => $data['accessToken'],
                    'refresh_token' => $data['refresh_token'],
                    'token_type' => $data['tokenType'] ?? 'Bearer',
                ]);

                Log::info('Token refreshed proactively after username change');
                return true;
            }

            Log::error('Failed to refresh token proactively', ['status' => $response->status(), 'body' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('Proactive token refresh error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return false;
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            }

            $validated = $request->validate([
                'current_password' => 'required|string|min:8',
                'new_password' => 'required|string|min:8|confirmed',
            ]);

            $payload = [
                'currentPassword' => $validated['current_password'],
                'newPassword' => $validated['new_password'],
            ];

            Log::info('Updating password with payload', ['payload' => array_merge($payload, ['newPassword' => '****'])]);

            $response = $this->makeRequest('patch', $this->baseUrl . '/api/users/profile/password', $payload);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $status = $response->status();
                $errorMessage = $status === 403
                    ? 'Akses ditolak: Tidak memiliki izin untuk memperbarui kata sandi'
                    : ($status === 401
                        ? 'Token tidak valid atau kadaluarsa'
                        : 'Gagal memperbarui kata sandi: ' . $response->json('message', 'Kesalahan server'));
                Log::error('Failed to update password', ['status' => $status, 'body' => $response->body()]);
                return back()->withErrors(['message' => $errorMessage]);
            }

            return redirect()->route('profile.show')->with('success', 'Kata sandi berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating password: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
