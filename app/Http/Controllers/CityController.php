<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CityController extends BaseApiController
{
    public function __construct()
    {
        parent::__construct();
        $this->endpoint = $this->baseUrl . '/api/cities';
    }

    public function index()
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get($this->endpoint);

            if (!$response->successful()) {
                return view('cities.index', ['cities' => [], 'error' => 'Gagal memuat data kota']);
            }

            $cities = $response->json('data') ?? [];
            return view('cities.index', compact('cities'));
        } catch (\Exception $e) {
            Log::error('Error fetching cities: ' . $e->getMessage());
            return view('cities.index', ['cities' => [], 'error' => 'Terjadi kesalahan sistem']);
        }
    }

    public function create()
    {
        return view('cities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $response = $this->getAuthenticatedHttpClient()->post($this->endpoint, $validated);
            return $this->handleApiResponse($response, 'Kota berhasil ditambahkan', 'Gagal menambahkan kota');
        } catch (\Exception $e) {
            Log::error('Error creating city: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function edit(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->get("{$this->endpoint}/{$id}");

            if (!$response->successful()) {
                return redirect()->route('cities.index')->withErrors(['message' => 'Kota tidak ditemukan']);
            }

            $city = $response->json('data');
            return view('cities.edit', compact('city'));
        } catch (\Exception $e) {
            Log::error('Error fetching city: ' . $e->getMessage());
            return redirect()->route('cities.index')->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        try {
            $response = $this->getAuthenticatedHttpClient()->put("{$this->endpoint}/{$id}", $validated);
            return $this->handleApiResponse($response, 'Kota berhasil diperbarui', 'Gagal memperbarui kota');
        } catch (\Exception $e) {
            Log::error('Error updating city: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }

    public function destroy(string $id)
    {
        try {
            $response = $this->getAuthenticatedHttpClient()->delete("{$this->endpoint}/{$id}");
            return $this->handleApiResponse($response, 'Kota berhasil dihapus', 'Gagal menghapus kota');
        } catch (\Exception $e) {
            Log::error('Error deleting city: ' . $e->getMessage());
            return back()->withErrors(['message' => 'Terjadi kesalahan sistem']);
        }
    }
}