<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TruckController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.java.backend.url') . '/api/trucks';
    }

    public function index()
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get($this->baseUrl);
        $trucks = $response->json('data');
        return view('trucks.index', compact('trucks'));
    }

    public function create()
    {
        return view('trucks.create');
    }

    public function store(Request $request)
    {
        $token = session('access_token');

        $validated = $request->validate([
            'license_plate' => 'required|string',
            'driver_name' => 'required|string',
        ]);

        $response = Http::withToken($token)->post($this->baseUrl, $validated);

        if ($response->successful()) {
            return redirect()->route('trucks.index')->with('success', 'Truk berhasil ditambahkan');
        }

        return back()->withErrors(['message' => 'Gagal menambahkan truk']);
    }

    public function edit(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/{$id}");
        $truck = $response->json('data');
        return view('trucks.edit', compact('truck'));
    }

    public function update(Request $request, string $id)
    {
        $token = session('access_token');

        $validated = $request->validate([
            'license_plate' => 'required|string',
            'driver_name' => 'required|string',
        ]);

        $response = Http::withToken($token)->put("{$this->baseUrl}/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('trucks.index')->with('success', 'Truk berhasil diperbarui');
        }

        return back()->withErrors(['message' => 'Gagal memperbarui truk']);
    }

    public function destroy(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->delete("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return redirect()->route('trucks.index')->with('success', 'Truk berhasil dihapus');
        }

        return back()->withErrors(['message' => 'Gagal menghapus truk']);
    }
}
