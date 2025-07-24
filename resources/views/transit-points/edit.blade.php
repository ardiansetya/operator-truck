@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Edit Transit Point</h1>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <form action="{{ route('transit-points.update', $transitPoint['id']) }}" method="POST" class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="loading_city_name" class="block text-sm font-medium text-gray-600">Kota Pemuatan</label>
            <select name="loading_city_name" id="loading_city_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['id'] }}" {{ old('loading_city_name', $transitPoint['loading_city_name']) == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                @endforeach
            </select>
            @error('loading_city_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="unloading_city_name" class="block text-sm font-medium text-gray-600">Kota Pembongkaran</label>
            <select name="unloading_city_name" id="unloading_city_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                <option value="">Pilih Kota</option>
                @foreach ($cities as $city)
                    <option value="{{ $city['id'] }}" {{ old('unloading_city_name', $transitPoint['unloading_city_name']) == $city['id'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                @endforeach
            </select>
            @error('unloading_city_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="estimated_duration_minute" class="block text-sm font-medium text-gray-600">Estimasi Durasi (Menit)</label>
            <input type="number" name="estimated_duration_minute" id="estimated_duration_minute" value="{{ old('estimated_duration_minute', $transitPoint['estimated_duration_minute']) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="0">
            @error('estimated_duration_minute')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="extra_cost" class="block text-sm font-medium text-gray-600">Biaya Ekstra</label>
            <input type="number" name="extra_cost" id="extra_cost" value="{{ old('extra_cost', $transitPoint['extra_cost']) }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="0">
            @error('extra_cost')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex space-x-4">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Simpan</button>
            <a href="{{ route('transit-points.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Kembali</a>
        </div>
    </form>
</div>
@endsection