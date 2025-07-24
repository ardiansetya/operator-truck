@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Pengiriman</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('deliveries.store') }}" class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf
        <div class="mb-4">
            <label for="truck_id" class="block text-sm font-medium text-gray-600">Truk</label>
            <select name="truck_id" id="truck_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Truk</option>
                @foreach ($trucks as $truck)
                    <option value="{{ (int) $truck['id'] }}" {{ old('truck_id') == $truck['id'] ? 'selected' : '' }}>{{ $truck['license_plate'] }} - {{ $truck['model'] }}</option>
                @endforeach
            </select>
            @error('truck_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="start_city_id" class="block text-sm font-medium text-gray-600">Kota Asal</label>
            <select name="start_city_id" id="start_city_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Kota Asal</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['id'] }}" {{ old('start_city_id') == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                @endforeach
            </select>
            @error('start_city_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="end_city_id" class="block text-sm font-medium text-gray-600">Kota Tujuan</label>
            <select name="end_city_id" id="end_city_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Kota Tujuan</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['id'] }}" {{ old('end_city_id') == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                @endforeach
            </select>
            @error('end_city_id')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="details" class="block text-sm font-medium text-gray-600">Detail</label>
            <input type="text" name="details" id="details" value="{{ old('details') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('details')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="base_price" class="block text-sm font-medium text-gray-600">Harga Dasar (Rp)</label>
            <input type="number" name="base_price" id="base_price" value="{{ old('base_price') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('base_price')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="distance_km" class="block text-sm font-medium text-gray-600">Jarak (km)</label>
            <input type="number" name="distance_km" id="distance_km" value="{{ old('distance_km') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('distance_km')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="estimated_duration_hours" class="block text-sm font-medium text-gray-600">Estimasi Durasi (jam)</label>
            <input type="number" name="estimated_duration_hours" id="estimated_duration_hours" value="{{ old('estimated_duration_hours') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('estimated_duration_hours')
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