<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends BaseApiController
{
    public function homeView(Request $request)
    {
        try {
            // Fetch cities
            $routesResponse = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/routes');
            $routesCount = $routesResponse->successful() ? count($routesResponse->json('data') ?? []) : 0;

            // Fetch trucks
            $trucksResponse = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/trucks');
            $activeTrucksCount = $trucksResponse->successful() ? count(array_filter($trucksResponse->json('data') ?? [], fn($truck) => $truck['is_available'])) : 0;

            // Fetch active deliveries (unconfirmed transits)
            $deliveriesResponse = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/delivery/active');
            $activeDeliveriesCount = $deliveriesResponse->successful() ? count($deliveriesResponse->json('data') ?? []) : 0;

            $transitPending = $this->getAuthenticatedHttpClient()->get($this->baseUrl . '/api/delivery/transit/pending');
            $activeDeliveriesCount = $transitPending->successful() ? count($transitPending->json('data') ?? []) : 0;

            return view('dashboard.index', [
                'routesCount' => $routesCount,
                'activeTrucksCount' => $activeTrucksCount,
                'activeDeliveriesCount' => $activeDeliveriesCount,
                'unconfirmedTransits' => $activeDeliveriesCount, 
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            return view('dashboard.index', [
                'routesCount' => 0,
                'activeTrucksCount' => 0,
                'activeDeliveriesCount' => 0,
                'unconfirmedTransits' => 0,
                'error' => 'Terjadi kesalahan saat memuat data dashboard'
            ]);
        }
    }
}
