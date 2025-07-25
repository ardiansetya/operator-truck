@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Edit Rute</h1>

    @if (session('errors'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            @foreach (session('errors')->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif
    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <form action="{{ route('routes.update', $route['id']) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_city_id" class="block text-sm font-medium text-gray-700">Kota Awal</label>
                    <select name="start_city_id" id="start_city_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Pilih Kota</option>
                        @foreach ($cities as $city)
                            <option value="{{ (int) $city['id'] }}" {{ $city['name'] == $route['start_city_name'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                        @endforeach
                    </select>
                    @error('start_city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="end_city_id" class="block text-sm font-medium text-gray-700">Kota Tujuan</label>
                    <select name="end_city_id" id="end_city_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="">Pilih Kota</option>
                        @foreach ($cities as $city)
                            <option value="{{ (int) $city['id'] }}" {{ $city['name'] == $route['end_city_name'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                        @endforeach
                    </select>
                    @error('end_city_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="details" class="block text-sm font-medium text-gray-700">Detail</label>
                    <input type="text" name="details" id="details" value="{{ $route['details'] ?? old('details') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('details')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="base_price" class="block text-sm font-medium text-gray-700">Harga Dasar</label>
                    <input type="number" name="base_price" id="base_price" value="{{ (float) $route['base_price'] ?? old('base_price') }}" step="0.01" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    @error('base_price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700">Status</label>
                    <select name="is_active" id="is_active" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        <option value="1" {{ $route['is_active'] ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ ! $route['is_active'] ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-6">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
                    Simpan
                </button>
                <a href="{{ route('routes.index') }}" class="ml-4 text-gray-600 hover:text-gray-800">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection