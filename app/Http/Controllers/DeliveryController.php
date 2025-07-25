<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DeliveryController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl ? $this->baseUrl . '/api/delivery' : '';
    }

    public function index()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.index', ['deliveries' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            // Fetch active deliveries
            $deliveryResponse = $this->makeRequest('get', $this->endpoint . '/active');
            if ($deliveryResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $deliveryResponse;
            }
            if (!$deliveryResponse->successful()) {
                $errorMessage = $deliveryResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk mengakses daftar pengiriman'
                    : 'Gagal memuat data pengiriman: ' . $deliveryResponse->json('error', $deliveryResponse->json('message', 'Kesalahan server'));
                Log::error('API request failed for deliveries', ['status' => $deliveryResponse->status(), 'body' => $deliveryResponse->body()]);
                return view('deliveries.index', ['deliveries' => [], 'error' => $errorMessage]);
            }
            $deliveries = $deliveryResponse->json('data') ?? [];

            // Cache cities for 1 hour
            $cities = Cache::remember('cities', 3600, function () {
                $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
                if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse || !$citiesResponse->successful()) {
                    Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                    return collect([]);
                }
                return collect($citiesResponse->json('data') ?? [])->keyBy('id');
            });

            // Cache trucks for 1 hour
            $trucks = Cache::remember('trucks', 3600, function () {
                $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks');
                if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse || !$trucksResponse->successful()) {
                    Log::error('API request failed for trucks', ['status' => $trucksResponse->status(), 'body' => $trucksResponse->body()]);
                    return collect([]);
                }
                return collect($trucksResponse->json('data') ?? [])->keyBy('id');
            });

            // Map city and truck names
            foreach ($deliveries as &$delivery) {
                $delivery['startCityName'] = $cities->get($delivery['route']['startCityId'], ['name' => 'Unknown'])['name'];
                $delivery['endCityName'] = $cities->get($delivery['route']['endCityId'], ['name' => 'Unknown'])['name'];
                $delivery['truckLicensePlate'] = $trucks->get($delivery['truckId'], ['licensePlate' => 'Unknown'])['licensePlate'];
            }

            if (empty($deliveries)) {
                Log::warning('API returned empty data for deliveries', ['response' => $deliveryResponse->body()]);
            }

            return view('deliveries.index', compact('deliveries'));
        } catch (\Exception $e) {
            Log::error('Error fetching deliveries: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.index', ['deliveries' => [], 'error' => $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.create', ['trucks' => [], 'routes' => [], 'drivers' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            // Fetch available trucks
            $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks/available');
            if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $trucksResponse;
            }
            $trucks = $trucksResponse->successful() ? $trucksResponse->json('data') ?? [] : [];

            // Fetch routes
            $routesResponse = $this->makeRequest('get', $this->baseUrl . '/api/routes');
            if ($routesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $routesResponse;
            }
            $routes = $routesResponse->successful() ? $routesResponse->json('data') ?? [] : [];

            // Fetch drivers
            $driversResponse = $this->makeRequest('get', $this->baseUrl . '/api/drivers');
            if ($driversResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $driversResponse;
            }
            $drivers = $driversResponse->successful() ? $driversResponse->json('data') ?? [] : [];

            if (!$trucksResponse->successful() || !$routesResponse->successful() || !$driversResponse->successful()) {
                $error = [];
                if (!$trucksResponse->successful()) $error[] = 'Gagal memuat data truk';
                if (!$routesResponse->successful()) $error[] = 'Gagal memuat data rute';
                if (!$driversResponse->successful()) $error[] = 'Gagal memuat data pengemudi';
                Log::error('API request failed for create', [
                    'trucks_status' => $trucksResponse->status(),
                    'trucks_body' => $trucksResponse->body(),
                    'routes_status' => $routesResponse->status(),
                    'routes_body' => $routesResponse->body(),
                    'drivers_status' => $driversResponse->status(),
                    'drivers_body' => $driversResponse->body(),
                ]);
                return view('deliveries.create', compact('trucks', 'routes', 'drivers') + ['error' => implode(', ', $error)]);
            }

            return view('deliveries.create', compact('trucks', 'routes', 'drivers'));
        } catch (\Exception $e) {
            Log::error('Error fetching data for delivery creation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.create', ['trucks' => [], 'routes' => [], 'drivers' => [], 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $validated = $request->validate([
                'truck_id' => 'required|uuid',
                'route_id' => 'required|uuid',
                'worker_id' => 'required|uuid',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $payload = [
                'truck_id' => $validated['truck_id'],
                'route_id' => $validated['route_id'],
                'worker_id' => $validated['worker_id'],
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
            ];

            Log::info('Sending payload to POST /api/delivery', ['payload' => $payload]);

            $response = $this->makeRequest('post', $this->endpoint, $payload);

            Cache::forget('deliveries');

            return $this->handleApiResponse($response, 'Pengiriman berhasil dibuat', 'Gagal membuat pengiriman');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return redirect()->route('deliveries.index')->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $deliveryResponse = $this->makeRequest('get', "{$this->endpoint}/{$id}");
            if ($deliveryResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $deliveryResponse;
            }
            if (!$deliveryResponse->successful()) {
                $errorMessage = $deliveryResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk melihat pengiriman ini'
                    : 'Pengiriman tidak ditemukan';
                Log::error('API request failed for delivery', ['status' => $deliveryResponse->status(), 'body' => $deliveryResponse->body()]);
                return redirect()->route('deliveries.index')->withErrors(['message' => $errorMessage]);
            }
            $delivery = $deliveryResponse->json('data') ?? [];

            // Cache cities and trucks
            $cities = Cache::remember('cities', 3600, function () {
                $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
                if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse || !$citiesResponse->successful()) {
                    Log::error('API request failed for cities', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                    return collect([]);
                }
                return collect($citiesResponse->json('data') ?? [])->keyBy('id');
            });

            $trucks = Cache::remember('trucks', 3600, function () {
                $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks');
                if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse || !$trucksResponse->successful()) {
                    Log::error('API request failed for trucks', ['status' => $trucksResponse->status(), 'body' => $trucksResponse->body()]);
                    return collect([]);
                }
                return collect($trucksResponse->json('data') ?? [])->keyBy('id');
            });

            $delivery['startCityName'] = $cities->get($delivery['route']['startCityId'], ['name' => 'Unknown'])['name'];
            $delivery['endCityName'] = $cities->get($delivery['route']['endCityId'], ['name' => 'Unknown'])['name'];
            $delivery['truckLicensePlate'] = $trucks->get($delivery['truckId'], ['licensePlate' => 'Unknown'])['licensePlate'];

            return view('deliveries.show', compact('delivery'));
        } catch (\Exception $e) {
            Log::error('Error fetching delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('deliveries.index')->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function finish(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $response = $this->makeRequest('patch', "{$this->endpoint}/{$id}/finish");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            $errorMessage = $response->status() === 403
                ? 'Anda tidak memiliki izin untuk menyelesaikan pengiriman ini'
                : 'Gagal menyelesaikan pengiriman';
            Cache::forget('deliveries');
            return $this->handleApiResponse($response, 'Pengiriman berhasil diselesaikan', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Error finishing delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => $e->getMessage()]);
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
                ? 'Anda tidak memiliki izin untuk menghapus pengiriman ini'
                : 'Gagal menghapus pengiriman';
            Cache::forget('deliveries');
            return $this->handleApiResponse($response, 'Pengiriman berhasil dihapus', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Error deleting delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }
}
