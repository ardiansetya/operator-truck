@extends('layouts.dashboard')

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-6 max-w-2xl">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-orange-500 text-white rounded-full mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Truk</h1>
            <p class="text-gray-600">Perbarui informasi truk yang sudah ada dalam sistem</p>
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

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Perbarui Informasi Truk
                </h2>
            </div>

            <form method="POST" action="{{ route('trucks.update', $truck['id']) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- License Plate -->
                    <div class="md:col-span-2">
                        <label for="license_plate" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Plat Nomor
                            </span>
                        </label>
                        <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate', $truck['license_plate']) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: B 1234 XYZ" required>
                        @error('license_plate')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Model -->
                    <div>
                        <label for="model" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                Model Truk
                            </span>
                        </label>
                        <input type="text" name="model" id="model" value="{{ old('model', $truck['model']) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: Hino 300" required>
                        @error('model')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Cargo Type -->
                    <div>
                        <label for="cargo_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                                Tipe Kargo
                            </span>
                        </label>
                        <input type="text" name="cargo_type" id="cargo_type" value="{{ old('cargo_type', $truck['cargo_type']) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: Box, Tangki, Flatbed" required>
                        @error('cargo_type')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Capacity -->
                    <div>
                        <label for="capacity_kg" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16l-3-3m3 3l3-3"></path>
                                </svg>
                                Kapasitas (kg)
                            </span>
                        </label>
                        <input type="number" name="capacity_kg" id="capacity_kg" value="{{ old('capacity_kg', $truck['capacity_kg']) }}" 
                               class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" 
                               placeholder="Contoh: 5000" required>
                        @error('capacity_kg')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <!-- Availability -->
                    <div>
                        <label for="is_available" class="block text-sm font-semibold text-gray-700 mb-2">
                            <span class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Status Ketersediaan
                            </span>
                        </label>
                        <select name="is_available" id="is_available" 
                                class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-orange-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white" required>
                            <option value="1" {{ old('is_available', $truck['is_available']) ? 'selected' : '' }}>✅ Tersedia</option>
                            <option value="0" {{ old('is_available', $truck['is_available']) ? '' : 'selected' }}>❌ Tidak Tersedia</option>
                        </select>
                        @error('is_available')
                            <span class="text-red-500 text-sm mt-1 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                </div>

                <!-- Current Data Info -->
                <div class="mt-6 p-4 bg-orange-50 border-l-4 border-orange-400 rounded-r-lg">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-orange-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-orange-700 font-medium">
                                Sedang mengedit: <span class="font-bold">{{ $truck['license_plate'] }}</span> - {{ $truck['model'] }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" 
                            class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-orange-600 hover:to-orange-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                    <a href="{{ route('trucks.index') }}" 
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
                Perubahan akan disimpan secara permanen setelah menekan tombol "Simpan Perubahan"
            </p>
        </div>
    </div>
</div>

@endsection