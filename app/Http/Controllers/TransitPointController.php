<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransitPointController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl ? $this->baseUrl . '/api/transit-points' : '';
    }

    public function index()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('transit-points.index', ['transitPoints' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            // Fetch transit points
            $transitResponse = $this->makeRequest('get', $this->endpoint);
            if ($transitResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $transitResponse; // Redirect to login if token refresh failed
            }
            if (!$transitResponse->successful()) {
                Log::error('API request failed for transit points', ['status' => $transitResponse->status(), 'body' => $transitResponse->body()]);
                return view('transit-points.index', ['transitPoints' => [], 'error' => 'Gagal memuat data transit point: ' . $transitResponse->json('error', $transitResponse->json('message', 'Kesalahan server'))]);
            }
            $transitPoints = $transitResponse->json('data') ?? [];
            log::debug('Transit point data:', $transitPoints);

            // Fetch cities
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse; // Redirect to login if token refresh failed
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return view('transit-points.index', ['transitPoints' => $transitPoints, 'error' => 'Gagal memuat data kota, menampilkan ID kota']);
            }
            $cities = collect($citiesResponse->json('data') ?? [])->keyBy('id');

            // Map city names to transit points
            foreach ($transitPoints as &$transitPoint) {
                $transitPoint['loading_city_name'] = $cities->get($transitPoint['loading_city_id'], ['name' => 'Unknown'])['name'];
                $transitPoint['unloading_city_name'] = $cities->get($transitPoint['unloading_city_id'], ['name' => 'Unknown'])['name'];
            }

            if (empty($transitPoints)) {
                Log::warning('API returned empty data for transit points', ['response' => $transitResponse->body()]);
            }

            return view('transit-points.index', compact('transitPoints'));
        } catch (\Exception $e) {
            Log::error('Error fetching transit points: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('transit-points.index', ['transitPoints' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('transit-points.create', ['cities' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $response = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            if (!$response->successful()) {
                Log::error('API request failed for cities', ['status' => $response->status(), 'body' => $response->body()]);
                return view('transit-points.create', ['cities' => [], 'error' => 'Gagal memuat data kota']);
            }
            $cities = $response->json('data') ?? [];
            return view('transit-points.create', compact('cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching cities for create: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('transit-points.create', ['cities' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $validated = $request->validate([
                'loading_city_id' => 'required|integer',
                'unloading_city_id' => 'required|integer',
                'estimated_duration_minute' => 'required|integer|min:0',
                'extra_cost' => 'required|numeric|min:0',
                'cargo_type' => 'required|string|max:255',
                'is_active' => 'required|boolean'

            ]);

            $validated['is_active'] = $validated['is_active'] == '1';


           



            $response = $this->makeRequest('post', $this->endpoint, $validated);
            return redirect()->route('transit-points.index')->with('success', 'Transit point berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating transit point: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('transit-points.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $transitResponse = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($transitResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $transitResponse;
            }
            if (!$transitResponse->successful()) {
                Log::error('API request failed', ['status' => $transitResponse->status(), 'body' => $transitResponse->body()]);
                return redirect()->route('transit-points.index')->withErrors(['message' => 'Transit point tidak ditemukan']);
            }
            $transitPoint = $transitResponse->json('data') ?? [];

            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return view('transit-points.show', ['transitPoint' => $transitPoint, 'cities' => [], 'error' => 'Gagal memuat data kota']);
            }
            $cities = $citiesResponse->json('data') ?? [];

            return view('transit-points.show', compact('transitPoint', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching transit point: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('transit-points.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('transit-points.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }
            $transitResponse = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($transitResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $transitResponse;
            }
            if (!$transitResponse->successful()) {
                Log::error('API request failed', ['status' => $transitResponse->status(), 'body' => $transitResponse->body()]);
                return redirect()->route('transit-points.index')->withErrors(['message' => 'Transit point tidak ditemukan']);
            }
            $transitPoint = $transitResponse->json('data') ?? [];
            Log::debug('Transit point data:', $transitPoint);

            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return view('transit-points.edit', ['transitPoint' => $transitPoint, 'cities' => [], 'error' => 'Gagal memuat data kota']);
            }
            $cities = $citiesResponse->json('data') ?? [];

            return view('transit-points.edit', compact('transitPoint', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching transit point: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('transit-points.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $validated = $request->validate([
                'loading_city_id' => 'required|integer',
                'unloading_city_id' => 'required|integer',
                'estimated_duration_minute' => 'required|integer|min:0',
                'extra_cost' => 'required|numeric|min:0',
                'cargo_type' => 'required|string|max:255',
                'is_active' => 'required|boolean'
            ]);



            $payload = [
                'loading_city_id' => $validated['loading_city_id'],
                'unloading_city_id' => $validated['unloading_city_id'],
                'estimated_duration_minute' => $validated['estimated_duration_minute'],
                'extra_cost' => $validated['extra_cost'],
                'cargo_type' => $validated['cargo_type'],
                'is_active' => (bool) $validated['is_active'],
            ];

            $response = $this->makeRequest('put', "{$this->endpoint}/{$id}", $payload);

            return redirect()->route('transit-points.index')->with('success', 'Transit point berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating transit point: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
            return $this->handleApiResponse($response, 'Transit point berhasil dihapus', 'Gagal menghapus transit point');
        } catch (\Exception $e) {
            Log::error('Error deleting transit point: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
