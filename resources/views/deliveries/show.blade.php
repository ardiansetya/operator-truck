@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Detail Pengiriman</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    <div class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Plat Nomor</label>
            <p class="mt-1 text-gray-700">{{ $delivery['truckLicensePlate'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Model Truk</label>
            <p class="mt-1 text-gray-700">{{ $delivery['truck']['model'] ?? 'Unknown' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Pekerja</label>
            <p class="mt-1 text-gray-700">{{ $delivery['worker']['name'] ?? 'Unknown' }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Kota Asal</label>
            <p class="mt-1 text-gray-700">{{ $delivery['startCityName'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Kota Tujuan</label>
            <p class="mt-1 text-gray-700">{{ $delivery['endCityName'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Harga Dasar (Rp)</label>
            <p class="mt-1 text-gray-700">{{ number_format($delivery['basePrice'], 0, ',', '.') }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Jarak (km)</label>
            <p class="mt-1 text-gray-700">{{ $delivery['distanceKm'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Estimasi Durasi (jam)</label>
            <p class="mt-1 text-gray-700">{{ $delivery['estimatedDurationHours'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Koordinat</label>
            <p class="mt-1 text-gray-700">Lat: {{ $delivery['latitude'] }}, Long: {{ $delivery['longitude'] }}</p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-600">Status</label>
            <p class="mt-1">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery['isActive'] ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    {{ $delivery['isActive'] ? 'Aktif' : 'Selesai' }}
                </span>
            </p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('deliveries.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Kembali</a>
            @if ($delivery['isActive'])
                <form method="POST" action="{{ route('deliveries.finish', $delivery['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menyelesaikan pengiriman ini?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Selesai</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection