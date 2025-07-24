<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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

            // Fetch deliveries
            $deliveryResponse = $this->makeRequest('get', $this->endpoint . '/active');
            if ($deliveryResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $deliveryResponse; // Redirect to login if token refresh failed
            }
            if (!$deliveryResponse->successful()) {
                $errorMessage = $deliveryResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk mengakses daftar pengiriman'
                    : 'Gagal memuat data pengiriman: ' . $deliveryResponse->json('error', $deliveryResponse->json('message', 'Kesalahan server'));
                Log::error('API request failed for deliveries', ['status' => $deliveryResponse->status(), 'body' => $deliveryResponse->body()]);
                return view('deliveries.index', ['deliveries' => [], 'error' => $errorMessage]);
            }
            $deliveries = $deliveryResponse->json('data') ?? [];

            // Fetch cities
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            $cities = $citiesResponse->successful() ? collect($citiesResponse->json('data') ?? [])->keyBy('id') : collect([]);

            // Fetch trucks
            $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks');
            if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $trucksResponse;
            }

            

            $trucks = $trucksResponse->successful() ? collect($trucksResponse->json('data') ?? [])->keyBy('id') : collect([]);

            // Map city and truck names
            foreach ($deliveries as &$delivery) {
                $delivery['start_city_name'] = $cities->get($delivery['startCityId'], ['name' => 'Unknown'])['name'];
                $delivery['end_city_name'] = $cities->get($delivery['endCityId'], ['name' => 'Unknown'])['name'];
                $delivery['truck_name'] = $trucks->get($delivery['truckId'], ['license_plate' => 'Unknown'])['license_plate'];
            }

            if (empty($deliveries)) {
                Log::warning('API returned empty data for deliveries', ['response' => $deliveryResponse->body()]);
            }

            return view('deliveries.index', compact('deliveries'));
        } catch (\Exception $e) {
            Log::error('Error fetching deliveries: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.index', ['deliveries' => [], 'error' =>  $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.create', ['trucks' => [], 'cities' => [], 'error' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks');
            if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $trucksResponse;
            }
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }

            $trucks = $trucksResponse->successful() ? $trucksResponse->json('data') ?? [] : [];
            $cities = $citiesResponse->successful() ? $citiesResponse->json('data') ?? [] : [];

            if (!$trucksResponse->successful() || !$citiesResponse->successful()) {
                $error = !$trucksResponse->successful() && !$citiesResponse->successful()
                    ? 'Gagal memuat data truk dan kota'
                    : (!$trucksResponse->successful() ? 'Gagal memuat data truk' : 'Gagal memuat data kota');
                Log::error('API request failed for create', [
                    'trucks_status' => $trucksResponse->status(),
                    'trucks_body' => $trucksResponse->body(),
                    'cities_status' => $citiesResponse->status(),
                    'cities_body' => $citiesResponse->body(),
                ]);
                return view('deliveries.create', compact('trucks', 'cities') + ['error' => $error]);
            }

            return view('deliveries.create', compact('trucks', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching data for delivery creation: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.create', ['trucks' => [], 'cities' => [], 'error' =>  $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $validated = $request->validate([
                'truck_id' => 'required|integer',
                'start_city_id' => 'required|integer',
                'end_city_id' => 'required|integer',
                'details' => 'required|string|max:255',
                'base_price' => 'required|numeric|min:0',
                'distance_km' => 'required|numeric|min:0',
                'estimated_duration_hours' => 'required|numeric|min:0',
            ]);

            // Fetch cities for name mapping
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            if (!$citiesResponse->successful()) {
                Log::error('API request failed for cities in store', ['status' => $citiesResponse->status(), 'body' => $citiesResponse->body()]);
                return back()->withErrors(['message' => 'Gagal memuat data kota']);
            }
            $cities = collect($citiesResponse->json('data') ?? [])->keyBy('id');

            $response = $this->makeRequest('post', $this->endpoint, [
                'truckId' => (int) $validated['truck_id'],
                'startCityName' => $cities->get($validated['start_city_id'], ['name' => 'Unknown'])['name'],
                'endCityName' => $cities->get($validated['end_city_id'], ['name' => 'Unknown'])['name'],
                'details' => $validated['details'],
                'basePrice' => $validated['base_price'],
                'distanceKM' => $validated['distance_km'],
                'estimatedDurationHours' => $validated['estimated_duration_hours'],
            ]);

            return $this->handleApiResponse($response, 'Pengiriman berhasil dibuat', 'Gagal membuat pengiriman');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' =>  $e->getMessage()]);
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

            // Fetch cities and trucks for name mapping
            $citiesResponse = $this->makeRequest('get', $this->baseUrl . '/api/cities');
            if ($citiesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $citiesResponse;
            }
            $cities = $citiesResponse->successful() ? collect($citiesResponse->json('data') ?? [])->keyBy('id') : collect([]);

            $trucksResponse = $this->makeRequest('get', $this->baseUrl . '/api/trucks');
            if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $trucksResponse;
            }
            $trucks = $trucksResponse->successful() ? collect($trucksResponse->json('data') ?? [])->keyBy('id') : collect([]);

            $delivery['start_city_name'] = $cities->get($delivery['startCityId'], ['name' => 'Unknown'])['name'];
            $delivery['end_city_name'] = $cities->get($delivery['endCityId'], ['name' => 'Unknown'])['name'];
            $delivery['truck_name'] = $trucks->get($delivery['truckId'], ['license_plate' => 'Unknown'])['license_plate'];

            return view('deliveries.show', compact('delivery'));
        } catch (\Exception $e) {
            Log::error('Error fetching delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->route('deliveries.index')->withErrors(['message' =>  $e->getMessage()]);
        }
    }

    public function finish(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'API base URL configuration is missing. Please set JAVA_BACKEND_URL in your .env file.']);
            }

            $response = $this->makeRequest('post', "{$this->endpoint}/{$id}/finish");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }
            $successMessage = $response->status() === 403
                ? 'Anda tidak memiliki izin untuk menyelesaikan pengiriman ini'
                : 'Pengiriman selesai';
            return $this->handleApiResponse($response, $successMessage, 'Gagal menyelesaikan pengiriman');
        } catch (\Exception $e) {
            Log::error('Error finishing delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' =>  $e->getMessage()]);
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
            return $this->handleApiResponse($response, 'Pengiriman berhasil dihapus', $errorMessage);
        } catch (\Exception $e) {
            Log::error('Error deleting delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->withErrors(['message' =>  $e->getMessage()]);
        }
    }
}
