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

    public function __construct()
    {
        parent::__construct();
        $this->transitEndpoint = $this->baseUrl . '/api/delivery/transit';
        $this->deliveryEndpoint = $this->baseUrl . '/api/delivery/detail';
        $this->truckEndpoint = $this->baseUrl . '/api/trucks';
        $this->routeEndpoint = $this->baseUrl . '/api/transit-points';
        $this->userEndpoint = $this->baseUrl . '/api/users/profile';
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
                    Log::info('[DEBUG] Route response', ['status' => $routeResp->status()]);

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

                $drivers[] = [
                    'id' => $transit['id'],
                    'delivery_id' => $transit['delivery_id'],
                    'license_plate' => $truckPlate,
                    'truck_model' => $truckModel,
                    'route_start' => $routeStart,
                    'route_end' => $routeEnd,
                    'tarif' => $tarif,
                    'status' => $transit['is_accepted'] ? 'Diterima' : 'Menunggu',
                    'operator' => $operatorName,
                ];
            }

            

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
            'is_accepted' => 'required|boolean',
            'reason' => 'nullable|string'
        ]);

        try {
            $payload = [
                'delivery_transit_id' => $validated['delivery_transit_id'],
                'is_accepted' => $validated['is_accepted'],
                'reason' => $validated['reason'] ?? ''
            ];

            Log::info('[TransitDriver] Sending accept/reject request', $payload);

            $response = $this->getAuthenticatedHttpClient()
                ->patch("{$this->transitEndpoint}/accept-or-reject", $payload);

            if ($response->successful()) {
                Log::info('[TransitDriver] Action successful', [
                    'id' => $validated['delivery_transit_id'],
                    'status' => $validated['is_accepted'] ? 'accepted' : 'rejected'
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
}
