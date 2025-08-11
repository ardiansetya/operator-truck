<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class DeliveryController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl ? $this->baseUrl . '/api/delivery' : '';
    }
      private function clearDeliveryRelatedCaches()
    {
        $cacheKeys = [
            'deliveries_active',
            'routes_for_deliveries',
            'trucks_for_deliveries', 
            'workers_for_deliveries',
            'deliveries_history',
            'routes_for_history',
            'trucks_for_history',
            'workers_for_history',
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        Log::info('Delivery related caches cleared', ['keys' => $cacheKeys]);
    }



    private function getOperatorName($operator_id)
    {
        try {
            $response = $this->makeRequest('GET', $this->baseUrl . "/api/users/{$operator_id}");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return 'N/A';
            }
            return $response->successful() ? $response->json('data')['username'] ?? 'N/A' : 'N/A';
        } catch (\Exception $e) {
            Log::error('Error fetching operator name: ' . $e->getMessage(), ['operator_id' => $operator_id]);
            return 'N/A';
        }
    }

    private function getAuthenticatedUserId()
    {
        try {
            $token = Session::get('access_token');
            if (!$token) {
                throw new \Exception('No access token found in session');
            }
            $cacheKey = 'user_profile_' . hash('sha256', $token);

            $profile = Cache::remember($cacheKey, 60, function () use ($token) {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/users/profile');
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during profile fetch');
                }
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch user profile: ' . $response->json('message', 'Unknown error'), $response->status());
                }
                $data = $response->json('data') ?? [];
                if (!isset($data['id'])) {
                    throw new \Exception('User profile missing ID field');
                }
                return $data;
            });

            return $profile['id'];
        } catch (\Exception $e) {
            Log::error('Error fetching authenticated user ID: ' . $e->getMessage(), [
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString(),
            ]);
            return null;
        }
    }

    public function index()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.index', ['deliveries' => [], 'error' => 'Konfigurasi server tidak lengkap']);
            }

            // Cache deliveries
            $deliveries = Cache::remember('deliveries_active', 300, function () { // Naikan ke 5 menit
                $response = $this->makeRequest('GET', "{$this->endpoint}/active");
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during deliveries fetch');
                }
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch deliveries: ' . $response->json('message', 'Kesalahan server'));
                }

                $data = $response->json('data') ?? [];
                Log::info('Raw deliveries API response', [
                    'data' => $data,
                    'response_status' => $response->status(),
                ]);

                return $data;
            });

            // Extract unique IDs from deliveries
            $route_ids = collect($deliveries)->pluck('route_id')->filter()->unique()->values()->toArray();
            $truck_ids = collect($deliveries)->pluck('truck_id')->filter()->unique()->values()->toArray();
            $worker_ids = collect($deliveries)->pluck('worker_id')->filter()->unique()->values()->toArray();

            // Cache supporting data based on delivery IDs
            $routes = Cache::remember('routes_for_deliveries', 300, function () use ($route_ids) {
                if (empty($route_ids)) {
                    return collect([]);
                }
                $routes = [];
                foreach ($route_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/routes/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during route fetch', ['route_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $route = $response->json('data') ?? [];
                        if (!empty($route)) {
                            $routes[] = $route;
                        }
                    } else {
                        Log::warning('Failed to fetch route', ['route_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($routes)->keyBy('id');
            });

            $trucks = Cache::remember('trucks_for_deliveries', 300, function () use ($truck_ids) {
                if (empty($truck_ids)) {
                    return collect([]);
                }
                $trucks = [];
                foreach ($truck_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/trucks/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during truck fetch', ['truck_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $truck = $response->json('data') ?? [];
                        if (!empty($truck)) {
                            $trucks[] = $truck;
                        }
                    } else {
                        Log::warning('Failed to fetch truck', ['truck_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($trucks)->keyBy('id');
            });

            $workers = Cache::remember('workers_for_deliveries', 300, function () use ($worker_ids) {
                if (empty($worker_ids)) {
                    return collect([]);
                }
                $workers = [];
                foreach ($worker_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/users/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during worker fetch', ['worker_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $worker = $response->json('data') ?? [];
                        if (!empty($worker)) {
                            $workers[] = $worker;
                        }
                    } else {
                        Log::warning('Failed to fetch worker', ['worker_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($workers)->keyBy('id');
            });

            // Cache cities for fallback
            $cities = Cache::remember('cities', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/cities');
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during cities fetch');
                }
                if (!$response->successful()) {
                    Log::error('Failed to fetch cities', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            // Enrich deliveries
            foreach ($deliveries as &$delivery) {
                $route = $routes->get($delivery['route_id'] ?? '', []) ?: [];
                $truck = $trucks->get($delivery['truck_id'] ?? '', []) ?: [];
                $worker = $workers->get($delivery['worker_id'] ?? '', []) ?: [];

                $delivery['start_city_name'] = $route['start_city_name'] ?? $cities->get($route['start_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['end_city_name'] = $route['end_city_name'] ?? $cities->get($route['end_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['base_price'] = $route['base_price'] ?? 0;
                $delivery['cargo_type'] = $route['cargo_type'] ?? null;
                $delivery['truck_license_plate'] = $truck['license_plate'] ?? 'Unknown';
                $delivery['distance_km'] = $route['distance_km'] ?? 0;
                $delivery['estimated_duration_hours'] = $route['estimated_duration_hours'] ?? 0;
                $delivery['add_by_operator_name'] = isset($delivery['add_by_operator_id']) ? $this->getOperatorName($delivery['add_by_operator_id']) : 'N/A';
                $delivery['worker_name'] = $worker['username'] ?? 'Unknown';
                $delivery['finished_at'] = $delivery['finished_at'] ?? null;
            }

            return view('deliveries.index', ['deliveries' => $deliveries]);
        } catch (\Throwable $e) {
            Log::error('Error fetching deliveries: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.index', ['deliveries' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.create', ['trucks' => [], 'routes' => [], 'drivers' => [], 'error' => 'Konfigurasi server tidak lengkap']);
            }

            $trucksResponse = $this->makeRequest('GET', $this->baseUrl . '/api/trucks/available');
            if ($trucksResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $trucksResponse;
            }
            $trucks = $trucksResponse->successful() ? $trucksResponse->json('data') ?? [] : [];

            $routesResponse = $this->makeRequest('GET', $this->baseUrl . '/api/routes');
            if ($routesResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $routesResponse;
            }
            $routes = $routesResponse->successful() ? $routesResponse->json('data') ?? [] : [];

            $driversResponse = $this->makeRequest('GET', $this->baseUrl . '/api/users/drivers/available');
            if ($driversResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $driversResponse;
            }
            $drivers = $driversResponse->successful() ? $driversResponse->json('data') ?? [] : [];

            if (!$trucksResponse->successful() || !$routesResponse->successful() || !$driversResponse->successful()) {
                $error = [];
                if (!$trucksResponse->successful()) $error[] = 'Gagal memuat data truk';
                if (!$routesResponse->successful()) $error[] = 'Gagal memuat data rute';
                if (!$driversResponse->successful()) $error[] = 'Gagal memuat data pengemudi';
                return view('deliveries.create', compact('trucks', 'routes', 'drivers') + ['error' => implode(', ', $error)]);
            }

            return view('deliveries.create', compact('trucks', 'routes', 'drivers'));
        } catch (\Exception $e) {
            Log::error('Error fetching data for delivery creation: ' . $e->getMessage());
            return view('deliveries.create', ['trucks' => [], 'routes' => [], 'drivers' => [], 'error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
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

            $response = $this->makeRequest('POST', $this->endpoint, $payload);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $errorMessage = $response->json('errors') ?? $response->json('message') ?? 'Gagal membuat pengiriman';
                if ($response->status() === 400 && str_contains($errorMessage, 'Worker already has an active delivery')) {
                    return back()->withErrors(['worker_id' => 'Pengemudi sudah memiliki pengiriman aktif']);
                }
                return back()->withErrors(['message' => $errorMessage]);
            }

            // ✅ Clear cache setelah berhasil create
            $this->clearDeliveryRelatedCaches();

            return redirect()->route('deliveries.index')->with('success', 'Pengiriman berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            $this->clearDeliveryRelatedCaches();
            // Ambil data pengiriman dari API
            $deliveryResponse = $this->makeRequest('GET', "{$this->endpoint}/detail/{$id}");
            if ($deliveryResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $deliveryResponse;
            }
            if (!$deliveryResponse->successful()) {
                $errorMessage = $deliveryResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk melihat pengiriman ini'
                    : 'Pengiriman tidak ditemukan';
                return redirect()->route('deliveries.index')->withErrors(['message' => $errorMessage]);
            }

            $apiData = $deliveryResponse->json('data') ?? [];

            // ✅ Cache dengan key yang KONSISTEN
            $cities = Cache::remember('cities', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/cities');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $trucks = Cache::remember('trucks_for_deliveries', 300, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/trucks');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $routes = Cache::remember('routes_for_deliveries', 300, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/routes');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $workers = Cache::remember('workers_for_deliveries', 300, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/users/drivers');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            // ✅ Optimasi: Collect city IDs untuk batch fetch
            $cityIds = [];
            foreach ($apiData['transits'] ?? [] as $transit) {
                $transitPoint = $transit['transit_point'] ?? [];
                if (!empty($transitPoint['loading_city_id'])) {
                    $cityIds[] = $transitPoint['loading_city_id'];
                }
                if (!empty($transitPoint['unloading_city_id'])) {
                    $cityIds[] = $transitPoint['unloading_city_id'];
                }
            }
            $cityIds = array_unique($cityIds);

            // Fetch missing cities
            $transitCities = $cities;
            foreach ($cityIds as $cityId) {
                if (!$cities->has($cityId)) {
                    try {
                        $cityResp = $this->makeRequest('GET', $this->baseUrl . '/api/cities/' . $cityId);
                        if ($cityResp->successful()) {
                            $cityData = $cityResp->json('data') ?? [];
                            $transitCities->put($cityId, $cityData);
                        }
                    } catch (\Exception $e) {
                        Log::warning('Gagal mengambil data city', ['id' => $cityId, 'error' => $e->getMessage()]);
                    }
                }
            }

            // ✅ Pemetaan data dengan optimasi
            $delivery = [
                'id' => $apiData['id'] ?? null,
                'worker_id' => $apiData['worker_id'] ?? null,
                'truck_id' => $apiData['truck_id'] ?? null,
                'route_id' => $apiData['route_id'] ?? null,
                'started_at' => $apiData['started_at'] ?? null,
                'finished_at' => $apiData['finished_at'] ?? null,
                'add_by_operator_id' => $apiData['add_by_operator_id'] ?? null,
                'alerts' => array_map(function ($alert) {
                    return [
                        'id' => $alert['id'] ?? null,
                        'type' => $alert['type'] ?? null,
                        'message' => $alert['message'] ?? null,
                        'created_at' => $alert['created_at'] ?? null,
                    ];
                }, $apiData['alerts'] ?? []),
                'transits' => array_map(function ($transit) use ($transitCities) {
                    $transit_point = $transit['transit_point'] ?? [];

                    // ✅ Gunakan collection yang sudah di-prepare
                    $loadingCity = $transitCities->get($transit_point['loading_city_id'] ?? null, [
                        'id' => null,
                        'name' => 'Unknown'
                    ]);
                    $unloadingCity = $transitCities->get($transit_point['unloading_city_id'] ?? null, [
                        'id' => null,
                        'name' => 'Unknown'
                    ]);

                    return [
                        'id' => $transit['id'] ?? null,
                        'transit_point' => [
                            'id' => $transit_point['id'] ?? null,
                            'loading_city' => [
                                'id' => $loadingCity['id'] ?? null,
                                'name' => $loadingCity['name'] ?? 'Unknown',
                                'latitude' => $loadingCity['latitude'] ?? null,
                                'longitude' => $loadingCity['longitude'] ?? null,
                                'country' => $loadingCity['country'] ?? null,
                            ],
                            'unloading_city' => [
                                'id' => $unloadingCity['id'] ?? null,
                                'name' => $unloadingCity['name'] ?? 'Unknown',
                                'latitude' => $unloadingCity['latitude'] ?? null,
                                'longitude' => $unloadingCity['longitude'] ?? null,
                                'country' => $unloadingCity['country'] ?? null,
                            ],
                            'estimated_duration_minute' => $transit_point['estimated_duration_minute'] ?? null,
                            'extra_cost' => $transit_point['extra_cost'] ?? null,
                            'type_cargo' => $transit_point['type_cargo'] ?? null,
                            'is_active' => $transit_point['is_active'] ?? false,
                        ],
                        'arrived_at' => $transit['arrived_at'] ?? null,
                        'is_accepted' => $transit['is_accepted'] ?? false,
                        'actioned_at' => $transit['actioned_at'] ?? null,
                        'reason' => $transit['reason'] ?? null,
                        'action_by_id' => $transit['action_by_operator_id'] ?? null,
                        'action_by_name' => $transit['action_by_operator_id']
                            ? $this->getOperatorName($transit['action_by_operator_id'])
                            : 'N/A',
                    ];
                }, $apiData['transits'] ?? []),
            ];

            // Enrich delivery dengan data tambahan
            $route = $routes->get($delivery['route_id'] ?? '', []) ?: [];
            if (!is_array($route)) {
                $route = [];
            }

            $delivery['start_city_name'] = $route['start_city_name'] ?? $cities->get($route['start_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
            $delivery['end_city_name'] = $route['end_city_name'] ?? $cities->get($route['end_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
            $delivery['base_price'] = $route['base_price'] ?? 0;
            $delivery['distance_km'] = $route['distance_km'] ?? 0;
            $delivery['cargo_type'] = $route['cargo_type'] ?? 'Unknown';
            $delivery['estimated_duration_hours'] = $route['estimated_duration_hours'] ?? 0;
            $delivery['truck_model'] = $trucks->get($delivery['truck_id'] ?? null, ['model' => 'Unknown'])['model'] ?? 'Unknown';
            $delivery['truck_license_plate'] = $trucks->get($delivery['truck_id'] ?? null, ['license_plate' => 'Unknown'])['license_plate'] ?? 'Unknown';
            $delivery['worker_name'] = $workers->get($delivery['worker_id'] ?? null, ['username' => 'Unknown'])['username'] ?? 'Unknown';
            $delivery['add_by_operator_name'] = isset($delivery['add_by_operator_id']) ? $this->getOperatorName($delivery['add_by_operator_id']) : 'N/A';

            Log::info('Delivery fetched successfully', ['transit' => $delivery['transits']]);

            return view('deliveries.show', compact('delivery'));
        } catch (\Exception $e) {
            Log::error('Error fetching delivery: ' . $e->getMessage(), ['id' => $id]);
            return redirect()->route('deliveries.index')->with(['error' => 'Gagal mengambil detail pengiriman: ' . $e->getMessage()]);
        }
    }


    public function finish(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            }

            $response = $this->makeRequest('PATCH', "{$this->endpoint}/finish/{$id}");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            $this->clearDeliveryRelatedCaches();
            return $this->handleApiResponse($response, 'Pengiriman berhasil diselesaikan', 'Gagal menyelesaikan pengiriman');
        } catch (\Exception $e) {
            Log::error('Error finishing delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'id' => $id]);
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function destroy(string $id)
    {
        try {
            if (empty($this->baseUrl)) {
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            }

            $response = $this->makeRequest('DELETE', "{$this->endpoint}/{$id}");
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            Cache::forget('deliveries_active');
            return $this->handleApiResponse($response, 'Pengiriman berhasil dihapus', 'Gagal menghapus pengiriman');
        } catch (\Exception $e) {
            Log::error('Error deleting delivery: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'id' => $id]);
            return back()->withErrors(['message' => $e->getMessage()]);
        }
    }

    public function history()
    {
        try {
            if (empty($this->baseUrl)) {
                return view('deliveries.history', ['deliveries' => [], 'error' => 'Konfigurasi server tidak lengkap']);
            }

            // ✅ Cache dengan key yang berbeda untuk history
            $deliveries = Cache::remember('deliveries_history', 300, function () {
                $response = $this->makeRequest('GET', "{$this->endpoint}/history");
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during deliveries history fetch');
                }
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch deliveries: ' . $response->json('message', 'Kesalahan server'));
                }

                $data = $response->json('data') ?? [];
                Log::info('Raw deliveries history API response', [
                    'data' => $data,
                    'response_status' => $response->status(),
                ]);

                return $data;
            });

            // ✅ Jika tidak ada data history, kembalikan response 200 dengan empty array
            if (empty($deliveries)) {
                return view('deliveries.history', [
                    'deliveries' => [],
                    'message' => 'Belum ada riwayat pengiriman'
                ]);
            }

            // Extract unique IDs from deliveries
            $route_ids = collect($deliveries)->pluck('route_id')->filter()->unique()->values()->toArray();
            $truck_ids = collect($deliveries)->pluck('truck_id')->filter()->unique()->values()->toArray();
            $worker_ids = collect($deliveries)->pluck('worker_id')->filter()->unique()->values()->toArray();

            // ✅ Cache dengan key khusus untuk history
            $routes = Cache::remember('routes_for_history', 300, function () use ($route_ids) {
                if (empty($route_ids)) {
                    return collect([]);
                }
                $routes = [];
                foreach ($route_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/routes/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during route fetch', ['route_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $route = $response->json('data') ?? [];
                        if (!empty($route)) {
                            $routes[] = $route;
                        }
                    } else {
                        Log::warning('Failed to fetch route', ['route_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($routes)->keyBy('id');
            });

            $trucks = Cache::remember('trucks_for_history', 300, function () use ($truck_ids) {
                if (empty($truck_ids)) {
                    return collect([]);
                }
                $trucks = [];
                foreach ($truck_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/trucks/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during truck fetch', ['truck_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $truck = $response->json('data') ?? [];
                        if (!empty($truck)) {
                            $trucks[] = $truck;
                        }
                    } else {
                        Log::warning('Failed to fetch truck', ['truck_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($trucks)->keyBy('id');
            });

            $workers = Cache::remember('workers_for_history', 300, function () use ($worker_ids) {
                if (empty($worker_ids)) {
                    return collect([]);
                }
                $workers = [];
                foreach ($worker_ids as $id) {
                    $response = $this->makeRequest('GET', $this->baseUrl . "/api/users/{$id}");
                    if ($response instanceof \Illuminate\Http\RedirectResponse) {
                        Log::warning('Redirect received during worker fetch', ['worker_id' => $id]);
                        continue;
                    }
                    if ($response->successful()) {
                        $worker = $response->json('data') ?? [];
                        if (!empty($worker)) {
                            $workers[] = $worker;
                        }
                    } else {
                        Log::warning('Failed to fetch worker', ['worker_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($workers)->keyBy('id');
            });

            // ✅ Cache cities dengan TTL yang lebih lama karena jarang berubah
            $cities = Cache::remember('cities', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/cities');
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during cities fetch');
                }
                if (!$response->successful()) {
                    Log::error('Failed to fetch cities', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            // Enrich deliveries
            foreach ($deliveries as &$delivery) {
                $route = $routes->get($delivery['route_id'] ?? '', []) ?: [];
                $truck = $trucks->get($delivery['truck_id'] ?? '', []) ?: [];
                $worker = $workers->get($delivery['worker_id'] ?? '', []) ?: [];

                $delivery['start_city_name'] = $route['start_city_name'] ?? $cities->get($route['start_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['end_city_name'] = $route['end_city_name'] ?? $cities->get($route['end_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['base_price'] = $route['base_price'] ?? 0;
                $delivery['truck_license_plate'] = $truck['license_plate'] ?? 'Unknown';
                $delivery['distance_km'] = $route['distance_km'] ?? 0;
                $delivery['estimated_duration_hours'] = $route['estimated_duration_hours'] ?? 0;
                $delivery['add_by_operator_name'] = isset($delivery['add_by_operator_id']) ? $this->getOperatorName($delivery['add_by_operator_id']) : 'N/A';
                $delivery['worker_name'] = $worker['username'] ?? 'Unknown';
                $delivery['started_at'] = $delivery['started_at'] ?? null;
                $delivery['finished_at'] = $delivery['finished_at'] ?? null; // ✅ Tambahkan finished_at untuk history
            }

            Log::info('Deliveries history processed successfully', [
                'deliveries_count' => count($deliveries),
            ]);

            return view('deliveries.history', ['deliveries' => $deliveries]);
        } catch (\Throwable $e) {
            Log::error('Error fetching deliveries history: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return view('deliveries.history', ['deliveries' => [], 'error' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

}
