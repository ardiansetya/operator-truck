@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Detail Rute</h1>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (session('errors'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            @foreach (session('errors')->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm font-medium text-gray-500">ID</p>
                <p class="text-sm text-gray-900">{{ $route['id'] }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Kota Awal</p>
                <p class="text-sm text-gray-900">{{ $route['start_city_name'] }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Kota Tujuan</p>
                <p class="text-sm text-gray-900">{{ $route['end_city_name'] }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Detail</p>
                <p class="text-sm text-gray-900">{{ $route['details'] ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Harga Dasar</p>
                <p class="text-sm text-gray-900">{{ number_format($route['base_price'], 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Jarak (km)</p>
                <p class="text-sm text-gray-900">{{ number_format($route['distance_km'], 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Durasi (Jam)</p>
                <p class="text-sm text-gray-900">{{ number_format($route['estimated_duration_hours'], 2) }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <p class="text-sm text-gray-900">{{ $route['is_active'] ? 'Aktif' : 'Non-Aktif' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500">Dibuat Pada</p>
                <p class="text-sm text-gray-900">{{ \Carbon\Carbon::createFromTimestamp($route['created_at'] / 1000)->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ route('routes.edit', $route['id']) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
                Edit
            </a>
            <form action="{{ route('routes.destroy', $route['id']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="ml-4 text-red-600 hover:text-red-800">Hapus</button>
            </form>
            <a href="{{ route('routes.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">Kembali</a>
        </div>
    </div>
</div>
@endsection