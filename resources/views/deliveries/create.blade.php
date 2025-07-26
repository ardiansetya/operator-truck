@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Pengiriman</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif

    <form method="POST" action="{{ route('deliveries.store') }}" class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf
        <div class="mb-4">
            <label for="truck_id" class="block text-sm font-medium text-gray-600">Truk</label>
            <select name="truck_id" id="truck_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Truk</option>
                @foreach ($trucks as $truck)
                    <option value="{{ $truck['id'] }}" {{ old('truck_id') == $truck['id'] ? 'selected' : '' }}>{{ $truck['license_plate'] }} - {{ $truck['model'] }}</option>
                @endforeach
            </select>
            @error('truck_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="route_id" class="block text-sm font-medium text-gray-600">Rute</label>
            <select name="route_id" id="route_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Rute</option>
                @foreach ($routes as $route)
                    <option value="{{ $route['id'] }}" {{ old('route_id') == $route['id'] ? 'selected' : '' }}>{{ $route['start_city_name'] }} - {{ $route['end_city_name'] }}</option>
                @endforeach
            </select>
            @error('route_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="worker_id" class="block text-sm font-medium text-gray-600">Pengemudi</label>
            <select name="worker_id" id="worker_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Pengemudi</option>
                @foreach ($drivers as $driver)
                    <option value="{{ $driver['id'] }}" {{ old('worker_id') == $driver['id'] ? 'selected' : '' }}>{{ $driver['username'] }}</option>
                @endforeach
            </select>
            @error('worker_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="latitude" class="block text-sm font-medium text-gray-600">Latitude</label>
            <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', -6.200000) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('latitude')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="longitude" class="block text-sm font-medium text-gray-600">Longitude</label>
            <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', 106.816666) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('longitude')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Simpan</button>
            <a href="{{ route('deliveries.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Batal</a>
        </div>
    </form>
</div>
@endsection