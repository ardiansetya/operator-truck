<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CityController extends Controller
{
    private $baseUrl = 'http://localhost:8080/api/cities';

    public function index()
    {
        $response = Http::get($this->baseUrl);
        return view('cities.index', ['cities' => $response['data']]);
    }

    public function show($id)
    {
        $response = Http::get("$this->baseUrl/$id");
        return view('cities.show', ['city' => $response['data']]);
    }

    public function store(Request $request)
    {
        $response = Http::post($this->baseUrl, $request->all());
        return redirect('/cities')->with('success', $response['data']);
    }

    public function update(Request $request, $id)
    {
        $response = Http::put("$this->baseUrl/$id", $request->all());
        return redirect("/cities/$id")->with('success', $response['data']);
    }

    public function destroy($id)
    {
        $response = Http::delete("$this->baseUrl/$id");
        return redirect('/cities')->with('success', $response['data']);
    }
}
