<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TruckController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl ? $this->baseUrl . '/api/trucks' : '';
    }

    public function index()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('trucks.index', ['trucks' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $response = $this->makeRequest('get', $this->endpoint);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response; // Redirect to login if token refresh failed
            }
            if (!$response->successful()) {
                Log::error('API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return view('trucks.index', ['trucks' => [], 'error' => 'Gagal memuat data truk: ' . $response->json('error', $response->json('message', 'Kesalahan server'))]);
            }
            $trucks = $response->json('data') ?? [];
            if (empty($trucks)) {
                Log::warning('API returned empty data for trucks', ['response' => $response->body()]);
            }
            return view('trucks.index', compact('trucks'));
        } catch (\Exception $e) {
            Log::error('Error fetching trucks: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('trucks.index', ['trucks' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $validated = $request->validate([
                'license_plate' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'cargo_type' => 'required|string|max:255',
                'capacity_kg' => 'required|numeric|min:0',
                'is_available' => 'boolean',
            ]);

            $validated['is_available'] = $validated['is_available'] == '1'; 

            $response = $this->makeRequest('post', $this->endpoint, $validated);
            return redirect()->route('trucks.index')->with('success', 'Truk berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating truck: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('trucks.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $response = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response; // Redirect to login if token refresh failed
            }
            if (!$response->successful()) {
                Log::error('API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return redirect()->route('trucks.index')->withErrors(['message' => 'Truk tidak ditemukan']);
            }
            $truck = $response->json('data') ?? [];
            return view('trucks.show', compact('truck'));
        } catch (\Exception $e) {
            Log::error('Error fetching truck: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('trucks.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('trucks.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $response = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response; // Redirect to login if token refresh failed
            }
            if (!$response->successful()) {
                Log::error('API request failed', ['status' => $response->status(), 'body' => $response->body()]);
                return redirect()->route('trucks.index')->withErrors(['message' => 'Truk tidak ditemukan']);
            }
            $truck = $response->json('data') ?? [];
            return view('trucks.edit', compact('truck'));
        } catch (\Exception $e) {
            Log::error('Error fetching truck: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('trucks.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $validated = $request->validate([
                'license_plate' => 'required|string|max:255',
                'model' => 'required|string|max:255',
                'capacity' => 'required|numeric|min:0',
            ]);

            $response = $this->makeRequest('put', "{$this->endpoint}/{$id}", $validated);
            return $this->handleApiResponse($response, 'Truk berhasil diperbarui', 'Gagal memperbarui truk');
        } catch (\Exception $e) {
            Log::error('Error updating truck: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $response = $this->makeRequest('delete', "{$this->endpoint}/{$id}");
            return $this->handleApiResponse($response, 'Truk berhasil dihapus', 'Gagal menghapus truk');
        } catch (\Exception $e) {
            Log::error('Error deleting truck: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
