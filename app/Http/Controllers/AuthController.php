<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    protected $javaBackend;

    public function __construct()
    {
        $this->javaBackend = env('JAVA_BACKEND_URL', 'http://localhost:8080');
    }

    public function login(Request $request)
    {
        $response = Http::post("{$this->javaBackend}/auth/login", $request->all());

        if ($response->successful()) {
            $data = $response->json('data');
            session([
                'access_token' => $data['access_token'],
                'refresh_token' => $data['refresh_token'],
                'token_type' => $data['token_type'],
            ]);

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'message' => $response->json('errors') ?? 'Login gagal!',
        ]);
    }


    public function register(Request $request)
    {
        $response = Http::post("{$this->javaBackend}/auth/register", $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function validateToken(Request $request)
    {
        $token = $request->header('Authorization');

        $response = Http::withHeaders([
            'Authorization' => $token,
        ])->get("{$this->javaBackend}/auth/validate");

        return response()->json($response->json(), $response->status());
    }

    public function refreshToken(Request $request)
    {
        $response = Http::post("{$this->javaBackend}/auth/refresh-token", $request->all());

        return response()->json($response->json(), $response->status());
    }

    public function logout(Request $request)
    {
        // Hapus semua data session
        $request->session()->flush();

        // Redirect ke halaman login
        return redirect()->route('login')->with('message', 'Berhasil logout!');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }
}
