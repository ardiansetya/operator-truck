<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.java.backend.url') . '/api/cities';
    }

    public function index()
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get($this->baseUrl);
        $cities = $response->json('data');
        return view('cities.index', compact('cities'));
    }

    public function create()
    {
        return view('cities.create');
    }

    public function store(Request $request)
    {
        $token = session('access_token');

        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $response = Http::withToken($token)->post($this->baseUrl, $validated);

        if ($response->successful()) {
            return redirect()->route('cities.index')->with('success', 'Kota berhasil ditambahkan');
        }

        return back()->withErrors(['message' => 'Gagal menambahkan kota']);
    }

    public function edit(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->get("{$this->baseUrl}/{$id}");
        $city = $response->json('data');
        return view('cities.edit', compact('city'));
    }

    public function update(Request $request, string $id)
    {
        $token = session('access_token');

        $validated = $request->validate([
            'name' => 'required|string',
        ]);

        $response = Http::withToken($token)->put("{$this->baseUrl}/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('cities.index')->with('success', 'Kota berhasil diperbarui');
        }

        return back()->withErrors(['message' => 'Gagal memperbarui kota']);
    }

    public function destroy(string $id)
    {
        $token = session('access_token');
        $response = Http::withToken($token)->delete("{$this->baseUrl}/{$id}");

        if ($response->successful()) {
            return redirect()->route('cities.index')->with('success', 'Kota berhasil dihapus');
        }

        return back()->withErrors(['message' => 'Gagal menghapus kota']);
    }
}
