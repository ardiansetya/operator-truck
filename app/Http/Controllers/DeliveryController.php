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

            $profile = Cache::remember($cacheKey, 3600, function () use ($token) {
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

            // Cache deliveries with shorter cache time for debugging
            $deliveries = Cache::remember('deliveries_active', 60, function () {
                $response = $this->makeRequest('GET', "{$this->endpoint}/active");
                if ($response instanceof \Illuminate\Http\RedirectResponse) {
                    throw new \Exception('Redirect received during deliveries fetch');
                }
                if (!$response->successful()) {
                    throw new \Exception('Failed to fetch deliveries: ' . $response->json('message', 'Kesalahan server'));
                }

                $data = $response->json('data') ?? [];

                // Log raw API response for debugging
                Log::info('Raw deliveries API response', [
                    'data' => $data,
                    'response_status' => $response->status(),
                ]);

                return $data;
            });

            // Log the deliveries data structure
            Log::info('Deliveries data structure analysis', [
                'count' => count($deliveries),
                'sample_delivery' => !empty($deliveries) ? $deliveries[0] : null,
                'all_worker_ids' => collect($deliveries)->pluck('worker_id')->toArray(),
                'all_truck_ids' => collect($deliveries)->pluck('truck_id')->toArray(),
                'all_route_ids' => collect($deliveries)->pluck('route_id')->toArray(),
            ]);

            // Extract unique IDs from deliveries
            $route_ids = collect($deliveries)->pluck('route_id')->filter()->unique()->values()->toArray();
            $truck_ids = collect($deliveries)->pluck('truck_id')->filter()->unique()->values()->toArray();
            $worker_ids = collect($deliveries)->pluck('worker_id')->filter()->unique()->values()->toArray();

            // Cache supporting data based on delivery IDs
            $routes = Cache::remember('routes_for_deliveries', 60, function () use ($route_ids) {
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
                            Log::info('Route fetched successfully', ['route_id' => $id, 'route_data' => $route]);
                        }
                    } else {
                        Log::warning('Failed to fetch route', ['route_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($routes)->keyBy('id');
            });

            $trucks = Cache::remember('trucks_for_deliveries', 60, function () use ($truck_ids) {
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
                            Log::info('Truck fetched successfully', ['truck_id' => $id, 'truck_data' => $truck]);
                        }
                    } else {
                        Log::warning('Failed to fetch truck', ['truck_id' => $id, 'status' => $response->status()]);
                    }
                }
                return collect($trucks)->keyBy('id');
            });

            $workers = Cache::remember('workers_for_deliveries', 60, function () use ($worker_ids) {
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
                            Log::info('Worker fetched successfully', ['worker_id' => $id, 'worker_data' => $worker]);
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

                // Log the matching process for debugging
                Log::info('Enriching delivery data', [
                    'delivery_id' => $delivery['id'] ?? 'unknown',
                    'delivery_route_id' => $delivery['route_id'] ?? 'missing',
                    'delivery_truck_id' => $delivery['truck_id'] ?? 'missing',
                    'delivery_worker_id' => $delivery['worker_id'] ?? 'missing',
                    'found_route' => !empty($route),
                    'found_truck' => !empty($truck),
                    'found_worker' => !empty($worker),
                ]);

                // Use start_city_name and end_city_name from route, fallback to cities
                $delivery['start_city_name'] = $route['start_city_name'] ?? $cities->get($route['start_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['end_city_name'] = $route['end_city_name'] ?? $cities->get($route['end_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
                $delivery['base_price'] = $route['base_price'] ?? 0;
                $delivery['truck_license_plate'] = $truck['license_plate'] ?? 'Unknown';
                $delivery['distance_km'] = $route['distance_km'] ?? 0;
                $delivery['estimated_duration_hours'] = $route['estimated_duration_hours'] ?? 0;
                $delivery['add_by_operator_name'] = isset($delivery['add_by_operator_id']) ? $this->getOperatorName($delivery['add_by_operator_id']) : 'N/A';
                $delivery['worker_name'] = $worker['username'] ?? 'Unknown';
                $delivery['started_at'] = $delivery['started_at'] ?? null;
            }

            Log::info('Deliveries processed successfully', [
                'deliveries_count' => count($deliveries),
                'enriched_deliveries' => $deliveries,
            ]);

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

            Log::info('Fetched data for delivery creation', [
                'trucks' => $trucks,
                'routes' => $routes,
                'drivers' => $drivers,
            ]);

            if (!$trucksResponse->successful() || !$routesResponse->successful() || !$driversResponse->successful()) {
                $error = [];
                if (!$trucksResponse->successful()) $error[] = 'Gagal memuat data truk';
                if (!$routesResponse->successful()) $error[] = 'Gagal memuat data rute';
                if (!$driversResponse->successful()) $error[] = 'Gagal memuat data pengemudi';
                Log::error('API request failed for create', [
                    'trucks_status' => $trucksResponse->status(),
                    'routes_status' => $routesResponse->status(),
                    'drivers_status' => $driversResponse->status(),
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
                return back()->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            }

            $validated = $request->validate([
                'truck_id' => 'required|uuid',
                'route_id' => 'required|uuid',
                'worker_id' => 'required|uuid',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            $operator_id = $this->getAuthenticatedUserId();
            if (!$operator_id) {
                return back()->withErrors(['message' => 'Gagal mengambil ID pengguna yang sedang login']);
            }

            $payload = [
                'truck_id' => $validated['truck_id'],
                'route_id' => $validated['route_id'],
                'worker_id' => $validated['worker_id'],
                'latitude' => (float) $validated['latitude'],
                'longitude' => (float) $validated['longitude'],
            ];



            Log::info('Sending payload to POST /api/delivery', [
                'payload' => $payload,
                'operator_id' => $operator_id,
            ]);

            $response = $this->makeRequest('POST', $this->endpoint, $payload);
            if ($response instanceof \Illuminate\Http\RedirectResponse) {
                return $response;
            }

            if (!$response->successful()) {
                $errorMessage = $response->json('errors') ?? $response->json('message') ?? 'Gagal membuat pengiriman';
                Log::error('Failed to create delivery', [
                    'status' => $response->status(),
                    'response' => $response->json(),
                    'payload' => $payload,
                ]);
                if ($response->status() === 400 && str_contains($errorMessage, 'Worker already has an active delivery')) {
                    return back()->withErrors(['worker_id' => 'Pengemudi sudah memiliki pengiriman aktif']);
                }
                return back()->withErrors(['message' => $errorMessage]);
            }

            Cache::forget('deliveries_active');

            return redirect()->route('deliveries.index')->with('success', 'Pengiriman berhasil dibuat');
        } catch (\Exception $e) {
            Log::error('Error creating delivery: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'payload' => $payload ?? [],
            ]);
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()]);
        }
    }

    public function show(string $id)
    {
        try {
            // if (empty($this->baseUrl)) {
            //     return redirect()->route('deliveries.index')->withErrors(['message' => 'Konfigurasi server tidak lengkap']);
            // }

            // Ambil data pengiriman dari API
            $deliveryResponse = $this->makeRequest('GET', "{$this->endpoint}/detail/{$id}");
            if ($deliveryResponse instanceof \Illuminate\Http\RedirectResponse) {
                return $deliveryResponse;
            }
            if (!$deliveryResponse->successful()) {
                $errorMessage = $deliveryResponse->status() === 403
                    ? 'Anda tidak memiliki izin untuk melihat pengiriman ini'
                    : 'Pengiriman tidak ditemukan';
                Log::error('API request failed for delivery', [
                    'status' => $deliveryResponse->status(),
                    'id' => $id,
                    'response' => $deliveryResponse->json(),
                ]);
                return redirect()->route('deliveries.index')->withErrors(['message' => $errorMessage]);
            }

            $apiData = $deliveryResponse->json('data') ?? [];

            // Log raw API data untuk debugging
            Log::info('Raw delivery API response', [
                'delivery_id' => $id,
                'data' => $apiData,
            ]);

            // Pemetaan camelCase ke snake_case
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
                'transits' => array_map(function ($transit) {
                    $transit_point = $transit['transit_point'] ?? [];
                    $action_by = $transit['action_by_operator_id'] ?? [];
                    return [
                        'id' => $transit['id'] ?? null,
                        'transit_point' => [
                            'id' => $transit_point['id'] ?? null,
                            'loading_city' => [
                                'id' => $transit_point['loading_city']['id'] ?? null,
                                'name' => $transit_point['loading_city']['name'] ?? 'Unknown',
                                'latitude' => $transit_point['loading_city']['latitude'] ?? null,
                                'longitude' => $transit_point['loading_city']['longitude'] ?? null,
                                'country' => $transit_point['loading_city']['country'] ?? null,
                                'created_at' => $transit_point['loading_city']['created_at'] ?? null,
                            ],
                            'unloading_city' => [
                                'id' => $transit_point['unloading_city']['id'] ?? null,
                                'name' => $transit_point['unloading_city']['name'] ?? 'Unknown',
                                'latitude' => $transit_point['unloading_city']['latitude'] ?? null,
                                'longitude' => $transit_point['unloading_city']['longitude'] ?? null,
                                'country' => $transit_point['unloading_city']['country'] ?? null,
                                'created_at' => $transit_point['unloading_city']['created_at'] ?? null,
                            ],
                            'estimated_duration_minute' => $transit_point['estimated_duration_minute'] ?? null,
                            'extra_cost' => $transit_point['extra_cost'] ?? null,
                            'created_at' => $transit_point['created_at'] ?? null,
                            'is_active' => $transit_point['is_active'] ?? false,
                        ],
                        'arrived_at' => $transit['arrived_at'] ?? null,
                        'is_accepted' => $transit['is_accepted'] ?? false,
                        'actioned_at' => $transit['actioned_at'] ?? null,
                        'reason' => $transit['reason'] ?? null,
                        'action_by_id' => $action_by['id'] ?? null,
                        'action_by_name' => isset($action_by['id']) ? $action_by['username'] ?? $this->getOperatorName($action_by['id']) : 'N/A',
                    ];
                }, $apiData['transits'] ?? []),
            ];


            // Cache data pendukung
            $cities = Cache::remember('cities', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/cities');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    Log::error('API request failed for cities', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $trucks = Cache::remember('trucks', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/trucks');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    Log::error('API request failed for trucks', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $routes = Cache::remember('routes', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/routes');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    Log::error('API request failed for routes', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            $workers = Cache::remember('workers', 3600, function () {
                $response = $this->makeRequest('GET', $this->baseUrl . '/api/users/drivers');
                if ($response instanceof \Illuminate\Http\RedirectResponse || !$response->successful()) {
                    Log::error('API request failed for workers', ['status' => $response->status()]);
                    return collect([]);
                }
                return collect($response->json('data') ?? [])->keyBy('id');
            });

            // Enrich delivery dengan data tambahan
            $route = $routes->get($delivery['route_id'] ?? '', []) ?: [];
            if (!is_array($route)) {
                Log::warning('Invalid route data for delivery', [
                    'delivery_id' => $delivery['id'] ?? 'unknown',
                    'route_id' => $delivery['route_id'] ?? 'missing',
                ]);
                $route = [];
            }

            $delivery['start_city_name'] = $route['start_city_name'] ?? $cities->get($route['start_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
            $delivery['end_city_name'] = $route['end_city_name'] ?? $cities->get($route['end_id'] ?? null, ['name' => 'Unknown'])['name'] ?? 'Unknown';
            $delivery['base_price'] = $route['base_price'] ?? 0;
            $delivery['distance_km'] = $route['distance_km'] ?? 0;
            $delivery['estimated_duration_hours'] = $route['estimated_duration_hours'] ?? 0;
            $delivery['truck_license_plate'] = $trucks->get($delivery['truck_id'] ?? null, ['license_plate' => 'Unknown'])['license_plate'] ?? 'Unknown';
            $delivery['worker_name'] = $workers->get($delivery['worker_id'] ?? null, ['username' => 'Unknown'])['username'] ?? 'Unknown';
            $delivery['add_by_operator_name'] = isset($delivery['add_by_operator_id']) ? $this->getOperatorName($delivery['add_by_operator_id']) : 'N/A';

            // Log data akhir yang akan dikirim ke view
            Log::info('Final delivery data sent to view', [
                'delivery_id' => $delivery['id'] ?? 'unknown',
                'delivery' => $delivery,
            ]);

            return view('deliveries.show', compact('delivery'));
        } catch (\Exception $e) {
            Log::error('Error fetching delivery: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id' => $id,
            ]);
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

            Cache::forget('deliveries_active');
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
}
