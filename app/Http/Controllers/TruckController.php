<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TruckController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl . '/api/trucks';
    }

    public function index()
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get($this->endpoint);

            if (!$response->successful()) {
                return view('trucks.index', ['trucks' => [], 'error' => 'Gagal memuat data truk']);
            }

            $trucks = $response->json('data') ?? [];
            return view('trucks.index', compact('trucks'));
        } catch (\Exception $e) {
            Log::error('Error fetching trucks: ' . $e->getMessage());
            return view('trucks.index', ['trucks' => [], 'error' => 'Terjadi kesalahan sistem']);
        }
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20',
            'model' => 'required|string|max:255',
            'cargo_type' => 'required|string|max:255',
            'capacity_kg' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        try {
            $response = $this->getAuthenticatedHttpClient()->post($this->endpoint, [
                'licensePlate' => $validated['license_plate'],
                'model' => $validated['model'],
                'cargoType' => $validated['cargo_type'],
                'capacityKG' => $validated['capacity_kg'],
                'isAvailable' => $validated['is_available'],
            ]);
            return $this->handleApiResponse($response, 'Truk berhasil ditambahkan', 'Gagal menambahkan truk');
        } catch (\Exception $e) {
            Log::error('Error creating truck: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function edit(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get("{$this->endpoint}/{$id}");

            if (!$response->successful()) {
                return redirect()->route('trucks.index')->withErrors(['message' => 'Truk tidak ditemukan']);
            }

            $truck = $response->json('data');
            return view('trucks.edit', compact('truck'));
        } catch (\Exception $e) {
            Log::error('Error fetching truck: ' . $e->getMessage());
            return redirect()->route('trucks.index')->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'license_plate' => 'required|string|max:20',
            'model' => 'required|string|max:255',
            'cargo_type' => 'required|string|max:255',
            'capacity_kg' => 'required|numeric|min:0',
            'is_available' => 'required|boolean',
        ]);

        try {
            $response = $this->getAuthenticatedHttpClient()->put("{$this->endpoint}/{$id}", [
                'licensePlate' => $validated['license_plate'],
                'model' => $validated['model'],
                'cargoType' => $validated['cargo_type'],
                'capacityKG' => $validated['capacity_kg'],
                'isAvailable' => $validated['is_available'],
            ]);
            return $this->handleApiResponse($response, 'Truk berhasil diperbarui', 'Gagal memperbarui truk');
        } catch (\Exception $e) {
            Log::error('Error updating truck: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->delete("{$this->endpoint}/{$id}");
            return $this->handleApiResponse($response, 'Truk berhasil dihapus', 'Gagal menghapus truk');
        } catch (\Exception $e) {
            Log::error('Error deleting truck: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }
}
