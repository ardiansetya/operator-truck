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
                'user_role' => $data['role'] ?? null, // Simpan role jika ada
            ]);

            return redirect()->route('dashboard.index');
        }

        return back()->withErrors([
            'message' => $response->json('errors') ?? 'Login gagal!',
        ]);
    }

    public function register(Request $request)
    {
        $response = Http::post("{$this->javaBackend}/auth/register", $request->all());
        return redirect()->route('login');
    }

    // Fungsi helper untuk validasi token dengan auto refresh
    public function validateTokenWithRefresh()
    {
        // Cek access token dulu
        $validateResponse = Http::withHeaders([
            'Authorization' => 'Bearer ' . session('access_token'),
        ])->get("{$this->javaBackend}/auth/validate");

        if ($validateResponse->successful()) {
            return [
                'valid' => true,
                'data' => $validateResponse->json('data')
            ];
        }

        // Jika tidak valid, coba refresh
        $refreshResponse = $this->attemptTokenRefresh();
        
        if ($refreshResponse['success']) {
            // Coba validasi lagi dengan token baru
            $revalidateResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . session('access_token'),
            ])->get("{$this->javaBackend}/auth/validate");

            if ($revalidateResponse->successful()) {
                return [
                    'valid' => true,
                    'data' => $revalidateResponse->json('data'),
                    'refreshed' => true
                ];
            }
        }

        return ['valid' => false];
    }

    private function attemptTokenRefresh()
    {
        try {
            $refreshToken = session('refresh_token');
            
            if (!$refreshToken) {
                return ['success' => false, 'message' => 'No refresh token'];
            }

            $response = Http::post("{$this->javaBackend}/auth/refresh-token", [
                'refreshToken' => $refreshToken
            ]);

            if ($response->successful()) {
                $data = $response->json('data');
                
                session([
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'] ?? session('refresh_token'),
                    'token_type' => $data['token_type'] ?? session('token_type'),
                ]);

                return ['success' => true, 'data' => $data];
            }

            return ['success' => false, 'message' => 'Refresh failed'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
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
        $request->session()->flush();
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

