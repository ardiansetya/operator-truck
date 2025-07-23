<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function homeView(Request $request)
    {
        return view('dashboard.index');
    }
    public function rentView(Request $request)
    {
        return view('dashboard.rent');
    }
    public function transitView(Request $request)
    {
        return view('dashboard.transit');
    }
}
