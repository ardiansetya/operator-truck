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

            // Pastikan payload dikirim sebagai JSON object yang benar
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

            // Update profile menggunakan POST sesuai dengan endpoint backend
            $response = $this->makeRequest('post', $this->baseUrl . '/api/users/profile', $payload);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $status = $response->status();
                $responseBody = $response->body();
                $errorData = $response->json();

                // Enhanced error handling untuk JSON parse errors
                if (isset($errorData['errors']) && str_contains($errorData['errors'], 'JSON parse error')) {
                    Log::error('JSON parse error detected', [
                        'payload' => $payload,
                        'payload_json' => json_encode($payload),
                        'response' => $responseBody
                    ]);
                    $errorMessage = 'Format data tidak valid. Silakan periksa input Anda.';
                } else {
                    $errorMessage = $status === 403
                        ? 'Akses ditolak: Tidak memiliki izin untuk memperbarui profil'
                        : ($status === 401
                            ? 'Token tidak valid atau kadaluarsa'
                            : 'Gagal memperbarui profil: ' . $response->json('message', 'Kesalahan server'));
                }

                Log::error('Failed to update profile', [
                    'status' => $status,
                    'body' => $responseBody,
                    'payload' => $payload,
                    'headers' => $response->headers()
                ]);
                return back()->withErrors(['message' => $errorMessage]);
            }

            // ✅ IMPLEMENTASI FLOW SEPERTI REACT NATIVE
            $responseData = $response->json('data') ?? $response->json();

            // Jika backend mengembalikan refresh_token baru (seperti di React Native)
            if (isset($responseData['refresh_token'])) {
                Log::info('Backend returned new refresh_token, updating session');

                // Update refresh token di session
                session(['refresh_token' => $responseData['refresh_token']]);

                // Refresh access token menggunakan refresh token baru
                if ($this->refreshTokenAfterProfileUpdate()) {
                    Log::info('Successfully refreshed access token after profile update');
                } else {
                    Log::warning('Failed to refresh access token, but profile was updated');
                }
            }
            // Jika username berubah tapi tidak ada refresh_token baru dalam response
            else if ($currentUsername && $currentUsername !== $validated['username']) {
                Log::info('Username changed but no new refresh_token in response, attempting token refresh', [
                    'old_username' => $currentUsername,
                    'new_username' => $validated['username']
                ]);

                // Coba refresh dengan refresh token yang ada
                if (!$this->handleUsernameChangeTokenRefresh()) {
                    Log::warning('Token refresh failed after username change, user may need to re-login on next request');
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
     * Refresh access token after profile update (similar to React Native flow)
     * Menggunakan refresh token yang baru dari backend response
     */
    private function refreshTokenAfterProfileUpdate()
    {
        try {
            $refresh_token = session('refresh_token');
            if (!$refresh_token) {
                Log::warning('No refresh token found after profile update');
                return false;
            }

            Log::info('Attempting to refresh access token after profile update');

            // Menggunakan endpoint yang sama dengan middleware
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])
                ->timeout(15)
                ->post($this->baseUrl . '/auth/refresh-token', [
                    'refresh_token' => $refresh_token
                ]);

            if ($response->successful()) {
                $data = $response->json('data');

                if (isset($data['access_token'])) {
                    // Update session dengan access token baru
                    session([
                        'access_token' => $data['access_token'],
                        'token_type' => $data['tokenType'] ?? $data['token_type'] ?? 'Bearer',
                    ]);

                    Log::info('Access token refreshed successfully after profile update');
                    return true;
                } else {
                    Log::error('Invalid refresh response format after profile update', [
                        'response_data' => $data
                    ]);
                    return false;
                }
            }

            Log::error('Failed to refresh access token after profile update', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Exception during token refresh after profile update: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Handle token refresh after username change with better error handling
     */
    private function handleUsernameChangeTokenRefresh()
    {
        try {
            $refresh_token = session('refresh_token');
            if (!$refresh_token) {
                Log::warning('No refresh token found for username change refresh');
                return false;
            }

            Log::info('Attempting token refresh after username change');

            // Debug: Log refresh token (hashed untuk security)
            Log::debug('Using refresh token for username change', [
                'token_hash' => substr(md5($refresh_token), 0, 8)
            ]);

            // Coba beberapa endpoint yang mungkin
            $endpoints = [
                '/auth/refresh-token',  // Seperti di middleware
                '/api/auth/refresh-token'  // Alternative endpoint
            ];

            foreach ($endpoints as $endpoint) {
                Log::info("Trying refresh endpoint: {$endpoint}");

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                    ->timeout(15)
                    ->post($this->baseUrl . $endpoint, [
                        'refresh_token' => $refresh_token
                    ]);

                Log::info("Response from {$endpoint}", [
                    'status' => $response->status(),
                    'success' => $response->successful(),
                    'body' => $response->body()
                ]);

                if ($response->successful()) {
                    $data = $response->json('data');

                    if (isset($data['access_token'])) {
                        // Update session dengan token baru
                        session([
                            'access_token' => $data['access_token'],
                            'token_type' => $data['tokenType'] ?? $data['token_type'] ?? 'Bearer',
                        ]);

                        Log::info('Token successfully refreshed after username change with endpoint: ' . $endpoint);
                        return true;
                    } else {
                        Log::error('Invalid refresh response format from ' . $endpoint, [
                            'response_data' => $data
                        ]);
                    }
                }
            }

            Log::error('All refresh endpoints failed after username change');
            return false;
        } catch (\Exception $e) {
            Log::error('Exception during username change token refresh: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Validate current access token
     * Menggunakan method yang sama dengan middleware
     */
    private function validateAccessToken()
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withToken(session('access_token'))
                ->withHeaders(['Accept' => 'application/json'])
                ->timeout(10)
                ->get($this->baseUrl . '/api/users/profile');

            if ($response->successful()) {
                Log::debug('Access token validated successfully in ProfileController');
                return true;
            }

            Log::warning('Access token validation failed in ProfileController', [
                'status' => $response->status(),
            ]);
            return false;
        } catch (\Exception $e) {
            Log::error('Token validation error in ProfileController: ' . $e->getMessage());
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
                'new_password_confirmation' => 'required|string|min:8',
            ]);

            $payload = [
                'current_password' => $validated['current_password'],
                'new_password' => $validated['new_password'],
                'confirm_password' => $validated['new_password_confirmation'],
            ];

            Log::info('Updating password');

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
