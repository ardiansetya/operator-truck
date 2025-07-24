<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.java.backend.url') . '/api/delivery';
    }

    public function index()
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/active");
        $deliveries = $response->json('data');
        return view('deliveries.index', compact('deliveries'));
    }

    public function create()
    {
        return view('deliveries.create');
    }

    public function store(Request $request)
    {
        $token = session('access_token');

        $validated = $request->validate([
            'truck_id' => 'required|integer',
            'destination' => 'required|string',
        ]);

        $response = Http::withToken($token)->post($this->baseUrl, $validated);

        if ($response->successful()) {
            return redirect()->route('deliveries.index')->with('success', 'Pengiriman berhasil dibuat');
        }

        return back()->withErrors(['message' => 'Gagal membuat pengiriman']);
    }

    public function show(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/{$id}");
        $delivery = $response->json('data');
        return view('deliveries.show', compact('delivery'));
    }

    public function finish(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->post("{$this->baseUrl}/{$id}/finish");

        if ($response->successful()) {
            return redirect()->route('deliveries.index')->with('success', 'Pengiriman selesai');
        }

        return back()->withErrors(['message' => 'Gagal menyelesaikan pengiriman']);
    }

    public function destroy(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->delete("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return redirect()->route('deliveries.index')->with('success', 'Pengiriman berhasil dihapus');
        }

        return back()->withErrors(['message' => 'Gagal menghapus pengiriman']);
    }
}