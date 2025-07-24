<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;

class DeliveryController extends Controller
{
    private $baseUrl = 'http://localhost:8080/api/delivery';

    public function active()
    {
        $response = Http::get("$this->baseUrl/active");
        return view('deliveries.active', ['deliveries' => $response['data']]);
    }
}
