<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransitDriverController extends BaseApiController
{
    protected string $transitEndpoint;
    protected string $deliveryEndpoint;
    protected string $truckEndpoint;
    protected string $routeEndpoint;
    protected string $userEndpoint;
    protected string $deliveryActive;

    public function __construct()
    {
        parent::__construct();
        $this->deliveryActive = $this->baseUrl . '/api/delivery/active';
        $this->transitEndpoint = $this->baseUrl . '/api/delivery/transit';
        $this->deliveryEndpoint = $this->baseUrl . '/api/delivery/detail';
        $this->truckEndpoint = $this->baseUrl . '/api/trucks';
        $this->routeEndpoint = $this->baseUrl . '/api/transit-points';
        $this->userEndpoint = $this->baseUrl . '/api/users';
    }

    /**
     * Menampilkan daftar driver transit
     */
    public function index()
    {
        try {
            Log::info('[DEBUG] Mulai fetch transit list');

            $response = $this->getAuthenticatedHttpClient()->get("{$this->transitEndpoint}");
            Log::info('[DEBUG] Transit list status', ['status' => $response->status()]);

            if (!$response->successful()) {
                return view('transit-drivers.index', [
                    'drivers' => [],
                    'error' => 'Gagal memuat data driver transit'
                ]);
            }

            $transitList = $response->json('data') ?? [];
            Log::info('[DEBUG] Total data transit', ['count' => count($transitList)]);
            $drivers = [];

            foreach ($transitList as $transit) {
                $truckPlate = '-';
                $truckModel = '-';
                $routeStart = '-';
                $routeEnd = '-';
                $operatorName = '-';
                $tarif = 0;

                // --- 1️⃣ Fetch Delivery ---
                $deliveryUrl = "{$this->deliveryEndpoint}/{$transit['delivery_id']}";
                Log::info('[DEBUG] Fetching delivery', ['url' => $deliveryUrl]);
                $deliveryResp = $this->getAuthenticatedHttpClient()->get($deliveryUrl);
                Log::info('[DEBUG] Delivery response', ['status' => $deliveryResp->status()]);

                if ($deliveryResp->failed()) {
                    Log::warning('[DEBUG] Delivery request gagal', ['delivery_id' => $transit['delivery_id']]);
                    continue;
                }

                $deliveryData = $deliveryResp->json('data');
                $truckId = $deliveryData['truck_id'] ?? null;
                $routeId = $deliveryData['route_id'] ?? null;

                // ✅ Ambil tarif dari extra_cost
                $tarif = 0;
                if (!empty($deliveryData['transits']) && is_array($deliveryData['transits'])) {
                    foreach ($deliveryData['transits'] as $t) {
                        if (isset($t['transit_point']['extra_cost'])) {
                            $tarif = $t['transit_point']['extra_cost'];
                        }
                    }
                }
                if (!empty($deliveryData['transits']) && is_array($deliveryData['transits'])) {
                    Log::debug('[DEBUG] Delivery data:', $deliveryData);
                    foreach ($deliveryData['transits'] as $trans) {
                        if (isset($trans['transit_point']['cargo_type'])) {
                            $typeCargo = $trans['transit_point']['cargo_type'];
                        }
                    }
                }


                // --- 2️⃣ Fetch Truck ---
                if ($truckId) {
                    $truckUrl = "{$this->truckEndpoint}/{$truckId}";
                    Log::info('[DEBUG] Fetching truck', ['url' => $truckUrl]);
                    $truckResp = $this->getAuthenticatedHttpClient()->get($truckUrl);
                    Log::info('[DEBUG] Truck response', ['status' => $truckResp->status()]);

                    if ($truckResp->successful()) {
                        $truck = $truckResp->json('data');
                        $truckPlate = $truck['license_plate'] ?? '-';
                        $truckModel = $truck['model'] ?? '-';
                    }
                }

                // --- 3️⃣ Fetch Route ---
                if ($routeId) {
                    $routeUrl = "{$this->routeEndpoint}/{$transit['transit_point_id']}";
                    Log::info('[DEBUG] Fetching route', ['url' => $routeUrl]);
                    $routeResp = $this->getAuthenticatedHttpClient()->get($routeUrl);
                    Log::info('[DEBUG] Route response', ['data' => $routeResp->json('data')]);

                    if ($routeResp->successful()) {
                        $route = $routeResp->json('data');
                        $routeStart = $route['loading_city']['name'] ?? '-';
                        $routeEnd = $route['unloading_city']['name'] ?? '-';
                    }
                }

                // --- 4️⃣ Fetch Operator ---
                if (!empty($transit['action_by_operator_id'])) {
                    $operatorUrl = "{$this->userEndpoint}/{$transit['action_by_operator_id']}";
                    Log::info('[DEBUG] Fetching operator', ['url' => $operatorUrl]);
                    $operatorResp = $this->getAuthenticatedHttpClient()->get($operatorUrl);
                    Log::info('[DEBUG] Operator response', [
                        'status' => $operatorResp->status(),
                        'id' => $transit['action_by_operator_id']
                    ]);

                    if ($operatorResp->successful()) {
                        $operator = $operatorResp->json('data');
                        $operatorName = $operator['username'] ?? '-';
                    }
                }


                $status = 'Menunggu'; // default

                if (!is_null($transit['is_accepted'])) {
                    $status = $transit['is_accepted'] ? 'Diterima' : 'Ditolak';
                }




                $drivers[] = [
                    'id' => $transit['id'],
                    'delivery_id' => $transit['delivery_id'],
                    'license_plate' => $truckPlate,
                    'truck_model' => $truckModel,
                    'route_start' => $routeStart,
                    'route_end' => $routeEnd,
                    'type_cargo' => $typeCargo,
                    'tarif' => $tarif,
                    'status' => $status,
                    'operator' => $operatorName,
                ];
            }

            usort($drivers, function ($a, $b) {
                // Urutkan: yang statusnya "Menunggu" (belum diterima) di atas
                if ($a['status'] === 'Menunggu' && $b['status'] !== 'Menunggu') {
                    return -1;
                } elseif ($a['status'] !== 'Menunggu' && $b['status'] === 'Menunggu') {
                    return 1;
                }
                return 0; // jika sama
            });



            Log::info('[DEBUG] Final driver data', ['count' => count($drivers)]);

            return view('transit-drivers.index', compact('drivers'));
        } catch (\Exception $e) {
            Log::error('[DEBUG] Error fetching driver transit list', ['message' => $e->getMessage()]);
            return view('transit-drivers.index', [
                'drivers' => [],
                'error' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    /**
     * Aksi ACC atau Tolak driver transit (PATCH)
     */
    public function acceptOrReject(Request $request)
    {
        $validated = $request->validate([
            'delivery_transit_id' => 'required|string',
            'is_accepted' => 'required|in:true,false,1,0',
            'reason' => 'nullable|string'
        ]);


        try {
            // Konversi ke boolean
            $isAccepted = filter_var($validated['is_accepted'], FILTER_VALIDATE_BOOLEAN);

            $payload = [
                'delivery_transit_id' => $validated['delivery_transit_id'],
                'is_accepted' => $isAccepted,
                'reason' => $validated['reason'] ?? ''
            ];


            Log::info('[TransitDriver] Sending accept/reject request', $payload);

            $response = $this->getAuthenticatedHttpClient()
                ->patch("{$this->transitEndpoint}/accept-or-reject", $payload);

            if ($response->successful()) {
                Log::info('[TransitDriver] Action successful', [
                    'id' => $validated['delivery_transit_id'],
                    'status' => $isAccepted ? 'accepted' : 'rejected'
                ]);
                return redirect()->route('transit-drivers.index')
                    ->with('success', 'Aksi berhasil dilakukan');
            }

            Log::warning('[TransitDriver] Action failed', [
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return redirect()->route('transit-drivers.index')
                ->withErrors(['message' => 'Gagal melakukan aksi, coba lagi']);
        } catch (\Exception $e) {
            Log::error('[TransitDriver] Error processing transit action', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('transit-drivers.index')
                ->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function create()
    {
        try {
            Log::info('[DEBUG] Loading create transit driver form');

            // Fetch delivery list untuk dropdown
            $deliveryResponse = $this->getAuthenticatedHttpClient()->get($this->deliveryActive);
            $rawDeliveries = [];

            if ($deliveryResponse->successful()) {
                $rawDeliveries = $deliveryResponse->json('data') ?? [];
                Log::info('[DEBUG] Raw deliveries loaded', ['count' => count($rawDeliveries)]);
            } else {
                Log::warning('[DEBUG] Failed to load deliveries');
                return view('transit-drivers.create', [
                    'deliveries' => [],
                    'error' => 'Gagal memuat data pengiriman'
                ]);
            }

            // Enrich deliveries dengan data truck dan route seperti di index()
            $enrichedDeliveries = [];
            foreach ($rawDeliveries as $delivery) {
                $enrichedDelivery = $delivery;

                // Fetch truck data
                if (!empty($delivery['truck_id'])) {
                    $truckResponse = $this->getAuthenticatedHttpClient()->get("{$this->truckEndpoint}/{$delivery['truck_id']}");
                    if ($truckResponse->successful()) {
                        $truckData = $truckResponse->json('data');
                        $enrichedDelivery['truck'] = [
                            'license_plate' => $truckData['license_plate'] ?? 'N/A',
                            'model' => $truckData['model'] ?? 'N/A'
                        ];
                        Log::info('[DEBUG] Truck data added', [
                            'delivery_id' => $delivery['id'],
                            'license_plate' => $enrichedDelivery['truck']['license_plate']
                        ]);
                    } else {
                        $enrichedDelivery['truck'] = ['license_plate' => 'N/A', 'model' => 'N/A'];
                        Log::warning('[DEBUG] Failed to fetch truck', ['truck_id' => $delivery['truck_id']]);
                    }
                } else {
                    $enrichedDelivery['truck'] = ['license_plate' => 'N/A', 'model' => 'N/A'];
                }

                // Fetch route data
                if (!empty($delivery['route_id'])) {
                    $routeResponse = $this->getAuthenticatedHttpClient()->get("{$this->baseUrl}/api/routes/{$delivery['route_id']}");
                    if ($routeResponse->successful()) {
                        $routeData = $routeResponse->json('data');
                        $enrichedDelivery['route'] = [
                            'start_city_name' => $routeData['start_city_name'] ?? 'N/A',
                            'end_city_name' => $routeData['end_city_name'] ?? 'N/A'
                        ];
                        Log::info('[DEBUG] Route data added', [
                            'delivery_id' => $delivery['id'],
                            'route' => $enrichedDelivery['route']
                        ]);
                    } else {
                        $enrichedDelivery['route'] = ['start_city_name' => 'N/A', 'end_city_name' => 'N/A'];
                        Log::warning('[DEBUG] Failed to fetch route', ['route_id' => $delivery['route_id']]);
                    }
                } else {
                    $enrichedDelivery['route'] = ['start_city_name' => 'N/A', 'end_city_name' => 'N/A'];
                }

                $enrichedDeliveries[] = $enrichedDelivery;
            }

            // Fetch transit points untuk dropdown
            $transitPointResponse = $this->getAuthenticatedHttpClient()->get($this->routeEndpoint);
            $allTransitPoints = [];

            if ($transitPointResponse->successful()) {
                $allTransitPoints = $transitPointResponse->json('data') ?? [];
                Log::info('[DEBUG] All transit points loaded', ['count' => count($allTransitPoints)]);
            } else {
                Log::warning('[DEBUG] Failed to load transit points');
            }

            // Fetch cities data untuk mapping
            $citiesResponse = $this->getAuthenticatedHttpClient()->get("{$this->baseUrl}/api/cities");
            $cities = [];

            if ($citiesResponse->successful()) {
                $cities = $citiesResponse->json('data') ?? [];
                Log::info('[DEBUG] Cities loaded', ['count' => count($cities)]);
            } else {
                Log::warning('[DEBUG] Failed to load cities');
            }

            // Filter transit points untuk setiap delivery
            $filteredTransitPoints = $this->filterTransitPointsForDeliveries($enrichedDeliveries, $allTransitPoints, $cities);

            return view('transit-drivers.create', [
                'deliveries' => $enrichedDeliveries,
                'transitPoints' => $allTransitPoints, // Semua transit points untuk fallback
                'filteredTransitPoints' => $filteredTransitPoints, // Filtered per delivery
                'cities' => $cities
            ]);
        } catch (\Exception $e) {
            Log::error('[DEBUG] Error loading create form', ['message' => $e->getMessage()]);
            return redirect()->route('transit-drivers.index')
                ->withErrors(['message' => 'Gagal memuat form, coba lagi']);
        }
    }

    /**
     * AJAX endpoint untuk mendapatkan transit points berdasarkan delivery_id
     */
    public function getTransitPointsByDelivery(Request $request)
    {
        try {
            $deliveryId = $request->get('delivery_id');

            if (!$deliveryId) {
                return response()->json(['error' => 'delivery_id is required'], 400);
            }

            // Fetch all transit points
            $transitPointResponse = $this->getAuthenticatedHttpClient()->get($this->routeEndpoint);
            if (!$transitPointResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch transit points'], 500);
            }
            $allTransitPoints = $transitPointResponse->json('data') ?? [];

            // Fetch cities
            $citiesResponse = $this->getAuthenticatedHttpClient()->get("{$this->baseUrl}/api/cities");
            if (!$citiesResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch cities'], 500);
            }
            $cities = $citiesResponse->json('data') ?? [];

            // Fetch delivery detail dengan struktur API yang benar
            $deliveryDetailResponse = $this->getAuthenticatedHttpClient()
                ->get("{$this->deliveryEndpoint}/{$deliveryId}");

            if (!$deliveryDetailResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch delivery detail'], 500);
            }

            $deliveryDetail = $deliveryDetailResponse->json('data');
            $routeId = $deliveryDetail['route_id'] ?? null; // snake_case

            if (!$routeId) {
                return response()->json(['error' => 'No route found for delivery'], 400);
            }

            // Fetch route data
            $routeResponse = $this->getAuthenticatedHttpClient()
                ->get("{$this->baseUrl}/api/routes/{$routeId}");

            if (!$routeResponse->successful()) {
                return response()->json(['error' => 'Failed to fetch route data'], 500);
            }

            $routeData = $routeResponse->json('data');

            // Apply filtering logic dengan struktur API snake_case
            $acceptedTransits = array_filter(
                $deliveryDetail['transits'] ?? [],
                fn($transit) => isset($transit['is_accepted']) && $transit['is_accepted'] === true
            );

            $currentCityName = $this->determineCurrentCityName($acceptedTransits, $routeData, $cities);

            // Filter transit points dengan struktur API snake_case
            $validTransitPoints = array_filter($allTransitPoints, function ($point) use ($currentCityName, $cities) {
                if (!isset($point['is_active']) || !$point['is_active']) {
                    return false;
                }

                $loadingCityName = $this->getCityNameById($point['loading_city_id'] ?? null, $cities);
                return $loadingCityName === $currentCityName;
            });

            // Transform transit points untuk frontend dengan menambahkan informasi city names
            $transformedTransitPoints = array_map(function ($point) use ($cities) {
                $point['loading_city'] = [
                    'name' => $this->getCityNameById($point['loading_city_id'] ?? null, $cities)
                ];
                $point['unloading_city'] = [
                    'name' => $this->getCityNameById($point['unloading_city_id'] ?? null, $cities)
                ];
                // Field sudah dalam format snake_case
                return $point;
            }, $validTransitPoints);

            return response()->json([
                'data' => array_values($transformedTransitPoints),
                'current_city' => $currentCityName,
                'accepted_transits_count' => count($acceptedTransits)
            ]);
        } catch (\Exception $e) {
            Log::error('[DEBUG] Error in getTransitPointsByDelivery', [
                'error' => $e->getMessage(),
                'delivery_id' => $request->get('delivery_id')
            ]);

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    
    private function filterTransitPointsForDeliveries(array $deliveries, array $transitPoints, array $cities): array
    {
        $filteredData = [];

        foreach ($deliveries as $delivery) {
            try {
                $deliveryId = $delivery['id'];

                // Fetch delivery detail untuk mendapatkan transit data
                $deliveryDetailResponse = $this->getAuthenticatedHttpClient()
                    ->get("{$this->deliveryEndpoint}/{$deliveryId}");

                if (!$deliveryDetailResponse->successful()) {
                    Log::warning('[DEBUG] Failed to fetch delivery detail', ['delivery_id' => $deliveryId]);
                    continue;
                }

                $deliveryDetail = $deliveryDetailResponse->json('data');
                $routeId = $deliveryDetail['route_id'] ?? null;

                if (!$routeId) {
                    Log::warning('[DEBUG] No route_id found for delivery', ['delivery_id' => $deliveryId]);
                    continue;
                }

                // Fetch route data
                $routeResponse = $this->getAuthenticatedHttpClient()
                    ->get("{$this->baseUrl}/api/routes/{$routeId}");

                if (!$routeResponse->successful()) {
                    Log::warning('[DEBUG] Failed to fetch route data', ['route_id' => $routeId]);
                    continue;
                }

                $routeData = $routeResponse->json('data');

                // Dapatkan transit yang sudah diterima (accepted)
                $acceptedTransits = array_filter(
                    $deliveryDetail['transits'] ?? [],
                    fn($transit) => isset($transit['is_accepted']) && $transit['is_accepted'] === true
                );

                // Tentukan current city berdasarkan logika hooks
                $currentCityName = $this->determineCurrentCityName($acceptedTransits, $routeData, $cities);

                // Filter transit points yang sesuai
                $validTransitPoints = array_filter($transitPoints, function ($point) use ($currentCityName, $cities) {
                    // Check if transit point is active
                    if (!isset($point['is_active']) || !$point['is_active']) {
                        return false;
                    }

                    // Get loading city name
                    $loadingCityName = $this->getCityNameById($point['loading_city_id'] ?? null, $cities);

                    return $loadingCityName === $currentCityName;
                });

                $filteredData[$deliveryId] = array_values($validTransitPoints);

                Log::info('[DEBUG] Filtered transit points for delivery', [
                    'delivery_id' => $deliveryId,
                    'current_city' => $currentCityName,
                    'available_points' => count($validTransitPoints)
                ]);
            } catch (\Exception $e) {
                Log::error('[DEBUG] Error filtering transit points for delivery', [
                    'delivery_id' => $delivery['id'] ?? 'unknown',
                    'error' => $e->getMessage()
                ]);
                continue;
            }
        }

        return $filteredData;
    }

    /**
     * Tentukan current city name berdasarkan logika seperti React hooks
     */
    private function determineCurrentCityName(array $acceptedTransits, array $routeData, array $cities): string
    {
        if (empty($acceptedTransits)) {
            // Belum ada transit yang diterima, gunakan end_city_name dari route
            return $routeData['end_city_name'] ?? '';
        }

        // Sudah ada transit yang diterima, gunakan unloading city dari transit terakhir
        $lastAcceptedTransit = end($acceptedTransits);
        $unloadingCityId = $lastAcceptedTransit['transit_point']['unloading_city_id'] ?? null;

        return $this->getCityNameById($unloadingCityId, $cities);
    }

    /**
     * Get city name by ID dari array cities
     */
    private function getCityNameById(?int $cityId, array $cities): string
    {
        if (!$cityId) {
            return '';
        }

        foreach ($cities as $city) {
            if (isset($city['id']) && $city['id'] == $cityId) {
                return $city['name'] ?? '';
            }
        }

        return '';
    }

    /**
     * AJAX endpoint untuk mendapatkan transit points berdasarkan delivery_id
     */
  

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_id' => 'required|string',
            'transit_point_id' => 'required|integer'
        ]);

        try {
            $payload = [
                'delivery_id' => $validated['delivery_id'],
                'transit_point_id' => (int) $validated['transit_point_id']
            ];

            Log::info('[TransitDriver] Creating new transit driver', $payload);

            $response = $this->getAuthenticatedHttpClient()
                ->post($this->transitEndpoint, $payload);

            if ($response->successful()) {
                Log::info('[TransitDriver] Transit driver created successfully', [
                    'delivery_id' => $validated['delivery_id'],
                    'transit_point_id' => $validated['transit_point_id']
                ]);

                return redirect()->route('transit-drivers.index')
                    ->with('success', 'Transit driver berhasil dibuat');
            }

            Log::warning('[TransitDriver] Failed to create transit driver', [
                'status_code' => $response->status(),
                'response' => $response->json()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => 'Gagal membuat transit driver: ' . ($response->json('message') ?? 'Unknown error')]);
        } catch (\Exception $e) {
            Log::error('[TransitDriver] Error creating transit driver', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }
}
