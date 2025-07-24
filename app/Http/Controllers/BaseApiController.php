<?php


namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseApiController extends Controller
{
    protected string $baseUrl;
    protected string $endpoint;

    public function __construct()
    {
        $this->baseUrl = config('services.java.backend.url');
    }

    protected function getAuthenticatedHttpClient()
    {
        $token = session('access_token');
        
        if (!$token) {
            throw new \Exception('Token tidak ditemukan');
        }

        return Http::withToken($token)
                  ->timeout(30)
                  ->retry(3, 100);
    }

    protected function handleApiResponse($response, $successMessage, $errorMessage)
    {
        if ($response->successful()) {
            return redirect()->back()->with('success', $successMessage);
        }

        // Log error untuk debugging
        Log::error('API Error: ' . $response->body());
        
        $errorDetail = $response->json('message') ?? $errorMessage;
        return back()->withErrors(['message' => $errorDetail]);
    }
}
