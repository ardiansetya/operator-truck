<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;

abstract class BaseApiController extends Controller
{
    protected string $baseUrl;
    protected string $endpoint;
    protected $currentUser;

    public function __construct()
    {
        $baseUrl = config('services.java_backend.url');

        if (empty($baseUrl)) {
            Log::error('BaseApiController: JAVA_BACKEND_URL is not configured in .env');
            $this->baseUrl = '';
        } else {
            $this->baseUrl = rtrim($baseUrl, '/');
        }

        // Ambil token dari session
        $token = Session::get('access_token');

        if ($token) {
            try {
                $response = Http::withToken($token)
                    ->get("{$this->baseUrl}/api/users/profile");

                if ($response->successful()) {
                    $this->currentUser = $response->json('data');
                    // Share ke semua view
                    View::share('currentUser', $this->currentUser);
                }
            } catch (\Exception $e) {
                $this->currentUser = null;
            }
        }
    }

    protected function getAuthenticatedHttpClient()
    {
        $token = session('access_token');

        if (!$token) {
            Log::error('No access token found in session');
            return null; // Return null to trigger refresh or login
        }

        return Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->timeout(30)->retry(3, 100);
    }

    protected function makeRequest($method, $url, $data = [])
    {
        if (empty($this->baseUrl)) {
            Log::error('Cannot make request: JAVA_BACKEND_URL is not configured');
            throw new \Exception('API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.');
        }

        $client = $this->getAuthenticatedHttpClient();

        if (!$client) {
            Log::info('No access token, attempting to refresh');
            $refreshResult = $this->refreshAccessToken();
            if ($refreshResult instanceof \Illuminate\Http\RedirectResponse) {
                return $refreshResult; // Redirect to login if refresh fails
            }
            $client = $this->getAuthenticatedHttpClient();
        }

        try {
            $response = $client->$method($url, $data);
            Log::info("API {$method} Request", ['url' => $url, 'status' => $response->status(), 'body' => $response->body()]);
            return $response;
        } catch (\Illuminate\Http\Client\RequestException $e) {
            if ($e->response->status() === 401) {
                Log::info('Received 401, attempting to refresh token');
                $refreshResult = $this->refreshAccessToken();
                if ($refreshResult instanceof \Illuminate\Http\RedirectResponse) {
                    return $refreshResult; // Redirect to login if refresh fails
                }

                // Retry the request with the new token
                $client = $this->getAuthenticatedHttpClient();
                $response = $client->$method($url, $data);
                Log::info("Retry API {$method} Request", ['url' => $url, 'status' => $response->status(), 'body' => $response->body()]);
                return $response;
            }
            Log::error("API {$method} Request failed", ['url' => $url, 'status' => $e->response->status(), 'body' => $e->response->body()]);
            throw $e;
        }
    }

    protected function refreshAccessToken()
    {
        $refreshToken = session('refresh_token');

        if (!$refreshToken) {
            Log::error('No refresh token found in session');
            $this->logoutDueToInvalidToken();
            return redirect()->route('login')->withErrors(['message' => 'Sesi telah berakhir. Silakan login kembali.']);
        }

        $refreshResponse = Http::post($this->baseUrl . '/auth/refresh-token', [
            'refresh_token' => $refreshToken,
        ]);

        if ($refreshResponse->successful()) {
            $responseData = $refreshResponse->json();
            // Flexible parsing: check for 'data' or direct token fields
            $newTokens = isset($responseData['data']) ? $responseData['data'] : $responseData;

            if (!isset($newTokens['access_token']) || !isset($newTokens['refresh_token'])) {
                Log::error('Invalid refresh token response structure', ['body' => $refreshResponse->body()]);
                $this->logoutDueToInvalidToken();
                return redirect()->route('login')->withErrors(['message' => 'Gagal memperbarui token: Struktur respons tidak valid.']);
            }

            session([
                'access_token' => $newTokens['access_token'],
                'refresh_token' => $newTokens['refresh_token'],
                'token_type' => $newTokens['token_type'] ?? 'Bearer',
            ]);
            Log::info('Token refreshed successfully', ['new_token' => $newTokens['access_token']]);
            return true;
        }

        Log::error('Token refresh failed', ['status' => $refreshResponse->status(), 'body' => $refreshResponse->body()]);
        $this->logoutDueToInvalidToken();
        return redirect()->route('login')->withErrors(['message' => 'Gagal memperbarui token: ' . ($refreshResponse->json('error') ?? $refreshResponse->json('message') ?? 'Unknown error')]);
    }

    protected function handleApiResponse($response, $successMessage, $errorMessage)
    {
        if ($response instanceof \Illuminate\Http\RedirectResponse) {
            return $response; // Return redirect if token refresh failed
        }

        Log::info('API Response', ['status' => $response->status(), 'body' => $response->body()]);

        if ($response->successful()) {
            return redirect()->back()->with('success', $successMessage);
        }

        Log::error('API Error', ['status' => $response->status(), 'body' => $response->body()]);
        $errorDetail = $response->json('error') ?? $response->json('errors') ?? $response->json('message') ?? $errorMessage;
        return back()->withErrors(['message' => $errorDetail]);
    }

    protected function logoutDueToInvalidToken()
    {
        session()->flush();
        Log::info('Session flushed due to invalid or expired token');
    }
}
