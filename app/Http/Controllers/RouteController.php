<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RouteController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl ? $this->baseUrl . '/api/routes' : '';
    }

    public function index()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('routes.index', ['routes' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            // Fetch routes
            $routesResponse = $this->makeRequest('get', $this->endpoint);
            if ($routesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $routesResponse; // Redirect to login if token refresh failed
            }
            if (!$routesResponse->successful()) {
                $errorMessage = $routesResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk mengakses daftar rute'
                    : 'Gagal memuat data rute: ' . $routesResponse->json('error', $routesResponse->json('message', 'Kesalahan server'));
                Log::error('API request failed for routes', ['status' => $routesResponse->status(), 'body' => $routesResponse->body()]);
                return view('routes.index', ['routes' => [], 'error' => $errorMessage]);
            }
            $routes = $routesResponse->json('data') ?? [];



            // Fetch cities
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            $cities = $citiesResponse->successful() ? collect($citiesResponse->json('data') ?? [])->keyBy('id') : collect([]);



            if (empty($routes)) {
                Log::warning('API returned empty data for routes', ['response' => $routesResponse->body()]);
            }

            return view('routes.index', compact('routes'));
        } catch (\Exception $e) {
            Log::error('Error fetching routes: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('routes.index', ['routes' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('routes.create', ['cities' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return view('routes.create', ['cities' => [], 'error' => 'Gagal memuat data kota']);
            }
            $cities = $citiesResponse->json('data') ?? [];

            return view('routes.create', compact('cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching cities for route creation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('routes.create', ['cities' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $validated = $request->validate([
                'start_city_id' => 'required|integer',
                'end_city_id' => 'required|integer',
                'details' => 'nullable|string|max:255',
                'base_price' => 'required|numeric|gt:0',
                'is_active' => 'required|boolean',
            ]);

            $response = $this->makeRequest('post', $this->endpoint, [
                'start_city_id' => (int) $validated['start_city_id'],
                'end_city_id' => (int) $validated['end_city_id'],
                'details' => $validated['details'],
                'base_price' => (float) $validated['base_price'],
                'is_active' => (bool) $validated['is_active'],
            ]);

            $validated['is_active'] = $validated['is_active'] == '1';
            


            return redirect()->route('routes.index')->with('success', 'Rute berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating route: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('routes.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $routeResponse = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($routeResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $routeResponse;
            }
            if (!$routeResponse->successful()) {
                $errorMessage = $routeResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk melihat rute ini'
                    : 'Rute tidak ditemukan';
                Log::error('API request failed for route', ['status' => $routeResponse->status(), 'body' => $routeResponse->body()]);
                return redirect()->route('routes.index')->withErrors(['message' => $errorMessage]);
            }
            $route = $routeResponse->json('data') ?? [];

            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            $cities = $citiesResponse->successful() ? collect($citiesResponse->json('data') ?? [])->keyBy('id') : collect([]);


            return view('routes.show', compact('route'));
        } catch (\Exception $e) {
            Log::error('Error fetching route: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('routes.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function edit(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('routes.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $routeResponse = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($routeResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $routeResponse;
            }
            if (!$routeResponse->successful()) {
                $errorMessage = $routeResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk mengedit rute ini'
                    : 'Rute tidak ditemukan';
                Log::error('API request failed for route', ['status' => $routeResponse->status(), 'body' => $routeResponse->body()]);
                return redirect()->route('routes.index')->withErrors(['message' => $errorMessage]);
            }
            $route = $routeResponse->json('data') ?? [];

            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return view('routes.edit', ['route' => $route, 'cities' => [], 'error' => 'Gagal memuat data kota']);
            }

            
            $cities = $citiesResponse->json('data') ?? [];

            return view('routes.edit', compact('route', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching route for edit: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('routes.index')->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, string $id)
    {
       

        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $validated = $request->validate([
                'start_city_id' => 'required|integer',
                'end_city_id' => 'required|integer',
                'details' => 'nullable|string|max:255',
                'base_price' => 'required|numeric|gt:0',
                'is_active' => 'required|boolean',
            ]);

            $response = $this->makeRequest('put', "{$this->endpoint}/{$id}", [
                'start_city_id' => (int) $validated['start_city_id'],
                'end_city_id' => (int) $validated['end_city_id'],
                'details' => $validated['details'],
                'base_price' => (float) $validated['base_price'],
                'is_active' => (bool) $validated['is_active'],
            ]);

            $validated['is_active'] = $validated['is_active'] == '1';       

            Log::info('Sending payload to PUT /api/routes/{id}', ['id' => $id, 'payload' => [
                'start_city_id' => (int) $validated['start_city_id'],
                'end_city_id' => (int) $validated['end_city_id'],
                'details' => $validated['details'],
                'base_price' => (float) $validated['base_price'],
                'is_active' => (bool) $validated['is_active'],
            ]]);


            return redirect()->route('routes.index')->with('success', 'Rute berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Error updating route: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            $errorMessage = $response->status() === 403
                ? 'Anda tidak memiliki izin untuk menghapus rute ini'
                : 'Gagal menghapus rute';
            return $this->handleApiResponse($response, 'Rute berhasil dihapus', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Error deleting route: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }
}
