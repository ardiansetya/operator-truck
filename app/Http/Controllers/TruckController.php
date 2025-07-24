<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TruckController extends Controller
{
    private $baseUrl = 'http://localhost:8080/api/trucks';

    public function index()
    {
        $response = Http::get($this->baseUrl);
        return view('trucks.index', ['trucks' => $response['data']]);
    }

    public function available()
    {
        $response = Http::get("$this->baseUrl/available");
        return response()->json($response['data']);
    }

    public function show($id)
    {
        $response = Http::get("$this->baseUrl/$id");
        return view('trucks.show', ['truck' => $response['data']]);
    }

    public function store(Request $request)
    {
        $response = Http::post($this->baseUrl, $request->all());
        return redirect('/trucks')->with('success', $response['data']);
    }

    public function update(Request $request, $id)
    {
        $response = Http::put("$this->baseUrl/$id", $request->all());
        return redirect("/trucks/$id")->with('success', $response['data']);
    }

    public function destroy($id)
    {
        $response = Http::delete("$this->baseUrl/$id");
        return redirect('/trucks')->with('success', $response['data']);
    }

    public function maintenance($id)
    {
        $response = Http::put("$this->baseUrl/maintenance/$id");
        return redirect('/trucks')->with('success', $response['data']);
    }
}
