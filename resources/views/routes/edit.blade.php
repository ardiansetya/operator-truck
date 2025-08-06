@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-6 max-w-2xl">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 text-white rounded-full mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Rute</h1>
            <p class="text-gray-600">Perbarui informasi rute sesuai kebutuhan</p>
        </div>

        <!-- Error Messages -->
        @if (session('errors'))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        @foreach (session('errors')->all() as $error)
                            <p class="text-red-700 font-medium">{{ $error }}</p>
                        @endforeach
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Informasi Rute
                </h2>
            </div>

            <form action="{{ route('routes.update', $route['id']) }}" method="POST" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Start City -->
                    <div>
                        <label for="start_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Kota Awal
                            </span>
                        </label>
                        <select name="start_city_id" id="start_city_id" 
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Kota Awal</option>
                            @foreach ($cities as $city)
                                <option value="{{ (int) $city['id'] }}" {{ $city['name'] == $route['start_city_name'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                            @endforeach
                        </select>
                        @error('start_city_id')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- End City -->
                    <div>
                        <label for="end_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Kota Tujuan
                            </span>
                        </label>
                        <select name="end_city_id" id="end_city_id" 
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Kota Tujuan</option>
                            @foreach ($cities as $city)
                                <option value="{{ (int) $city['id'] }}" {{ $city['name'] == $route['end_city_name'] ? 'selected' : '' }}>{{ $city['name'] }}</option>
                            @endforeach
                        </select>
                        @error('end_city_id')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- type_cargo -->
                    <div class="md:col-span-2">
                        <label for="type_cargo" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Muatan
                            </span>
                        </label>
                        <input type="text" name="type_cargo" id="type_cargo" value="{{ $route['type_cargo'] ?? old('type_cargo') }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: Via Tol, Jalur Alternatif, dll" required>
                        @error('type_cargo')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Base Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Harga Dasar (Rp)
                            </span>
                        </label>
                        <input type="number" name="base_price" id="base_price" value="{{ (float) $route['base_price'] ?? old('base_price') }}" step="0.01"
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: 500000" required>
                        @error('base_price')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Status Rute
                            </span>
                        </label>
                        <select name="is_active" id="is_active" 
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" required>
                            <option value="1" {{ $route['is_active'] ? 'selected' : '' }}>✅ Aktif</option>
                            <option value="0" {{ ! $route['is_active'] ? 'selected' : '' }}>❌ Non-Aktif</option>
                        </select>
                        @error('is_active')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Perbarui Rute
                    </button>
                    <a href="{{ route('routes.index') }}" 
                       class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
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
                Pastikan perubahan informasi rute sudah sesuai sebelum memperbarui
            </p>
        </div>
    </div>
</div>
@endsection