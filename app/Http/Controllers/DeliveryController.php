<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DeliveryController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl . '/api/delivery';
    }

    public function index()
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get("{$this->endpoint}/active");

            if (!$response->successful()) {
                return view('deliveries.index', ['deliveries' => [], 'error' => 'Gagal memuat data pengiriman']);
            }

            $deliveries = $response->json('data') ?? [];
            return view('deliveries.index', compact('deliveries'));
        } catch (\Exception $e) {
            Log::error('Error fetching deliveries: ' . $e->getMessage());
            return view('deliveries.index', ['deliveries' => [], 'error' => 'Terjadi kesalahan sistem']);
        }
    }

    public function create()
    {
        try {
            $trucksResponse = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/trucks');
            $citiesResponse = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/cities');
            $trucks = $trucksResponse->successful() ? $trucksResponse->json('data') : [];
            $cities = $citiesResponse->successful() ? $citiesResponse->json('data') : [];
            return view('deliveries.create', compact('trucks', 'cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching data for delivery creation: ' . $e->getMessage());
            return view('deliveries.create', ['trucks' => [], 'cities' => []]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'truck_id' => 'required|integer',
            'start_city_id' => 'required|integer',
            'end_city_id' => 'required|integer',
            'details' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'distance_km' => 'required|numeric|min:0',
            'estimated_duration_hours' => 'required|numeric|min:0',
        ]);

        try {
            $response = $this->getAuthenticatedHttpClient()->post($this->endpoint, [
                'truckId' => $validated['truck_id'],
                'startCityName' => $this->getCityName($validated['start_city_id']),
                'endCityName' => $this->getCityName($validated['end_city_id']),
                'details' => $validated['details'],
                'basePrice' => $validated['base_price'],
                'distanceKM' => $validated['distance_km'],
                'estimatedDurationHours' => $validated['estimated_duration_hours'],
            ]);
            return $this->handleApiResponse($response, 'Pengiriman berhasil dibuat', 'Gagal membuat pengiriman');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function show(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get("{$this->endpoint}/{$id}");

            if (!$response->successful()) {
                return redirect()->route('deliveries.index')->withErrors(['message' => 'Pengiriman tidak ditemukan']);
            }

            $delivery = $response->json('data');
            return view('deliveries.show', compact('delivery'));
        } catch (\Exception $e) {
            Log::error('Error fetching delivery: ' . $e->getMessage());
            return redirect()->route('deliveries.index')->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function finish(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->post("{$this->endpoint}/{$id}/finish");
            return $this->handleApiResponse($response, 'Pengiriman selesai', 'Gagal menyelesaikan pengiriman');
        } catch (\Exception $e) {
            Log::error('Error finishing delivery: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->delete("{$this->endpoint}/{$id}");
            return $this->handleApiResponse($response, 'Pengiriman berhasil dihapus', 'Gagal menghapus pengiriman');
        } catch (\Exception $e) {
            Log::error('Error deleting delivery: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    private function getCityName($cityId)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get("{$this->baseUrl}/api/cities/{$cityId}");
            return $response->successful() ? $response->json('data')['name'] : '';
        } catch (\Exception $e) {
            Log::error('Error fetching city name: ' . $e->getMessage());
            return '';
        }
    }
}
