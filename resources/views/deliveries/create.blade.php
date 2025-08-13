@extends('layouts.dashboard')

@section('title', 'Tambah Pengiriman Baru')

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-6 max-w-2xl">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 text-white rounded-full mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Tambah Pengiriman Baru</h1>
            <p class="text-gray-600">Lengkapi informasi pengiriman untuk menambahkan data ke dalam sistem</p>
        </div>

        <!-- Error Message -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 font-medium">{{ $errors->first('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 font-medium">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Informasi Pengiriman
                </h2>
            </div>

            <form method="POST" action="{{ route('deliveries.store') }}" class="p-8">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <!-- Truk -->
                    <div>
                        <label for="truck_id" class="block text-sm font-semibold text-gray-700 mb-2">Truk</label>
                        <select name="truck_id" id="truck_id" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Truk</option>
                            @foreach ($trucks as $truck)
                                <option value="{{ $truck['id'] }}" {{ old('truck_id') == $truck['id'] ? 'selected' : '' }}>{{ $truck['license_plate'] }} - {{ $truck['model'] }}</option>
                            @endforeach
                        </select>
                        @error('truck_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Rute -->
                    <div>
                        <label for="route_id" class="block text-sm font-semibold text-gray-700 mb-2">Rute</label>
                        <select name="route_id" id="route_id" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Rute</option>
                            @foreach ($routes as $route)
                                <option value="{{ $route['id'] }}" {{ old('route_id') == $route['id'] ? 'selected' : '' }}>{{ $route['start_city_name'] }} - {{ $route['end_city_name'] }} ({{ $route['cargo_type'] }})</option>
                            @endforeach
                        </select>
                        @error('route_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Pengemudi -->
                    <div>
                        <label for="worker_id" class="block text-sm font-semibold text-gray-700 mb-2">Pengemudi</label>
                        <select name="worker_id" id="worker_id" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Pengemudi</option>
                            @foreach ($drivers as $driver)
                                <option value="{{ $driver['id'] }}" {{ old('worker_id') == $driver['id'] ? 'selected' : '' }}>{{ $driver['username'] }}</option>
                            @endforeach
                        </select>
                        @error('worker_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Latitude -->
                    <div class="hidden">
                        <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">Latitude</label>
                        <input type="number" step="any" name="latitude" id="latitude" value="{{ old('latitude', -6.200000) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                        @error('latitude')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Longitude -->
                    <div  class="hidden">
                        <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">Longitude</label>
                        <input type="number" step="any" name="longitude" id="longitude" value="{{ old('longitude', 106.816666) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                        @error('longitude')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Pengiriman
                    </button>
                    <a href="{{ route('deliveries.index') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Pastikan semua informasi pengiriman sudah benar sebelum menyimpan
            </p>
        </div>
    </div>
</div>
@endsection
