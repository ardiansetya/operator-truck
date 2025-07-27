@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-6 py-8">
    
    

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-start">
            <svg class="w-5 h-5 mr-3 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
                <h3 class="font-medium">Terjadi Kesalahan</h3>
                <p class="text-sm mt-1">{{ $errors->first('message') }}</p>
            </div>
        </div>
    @endif

    <div class="max-w-2xl mx-auto">
        <!-- Main Form Card -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                    </svg>
                    Edit Data Kota
                </h2>
                <p class="text-orange-100 text-sm mt-1">Perbarui informasi kota: <span class="font-semibold">{{ $city['name'] ?? 'N/A' }}</span></p>
            </div>

            <!-- Current Data Preview -->
            <div class="bg-gray-50 px-8 py-4 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm font-medium text-gray-700">Data Saat Ini:</span>
                    </div>
                    <div class="text-sm text-gray-600">
                        {{ $city['country'] ?? 'N/A' }} • Lat: {{ $city['latitude'] ?? 'N/A' }}, Long: {{ $city['longitude'] ?? 'N/A' }}
                    </div>
                </div>
            </div>

            <!-- Form Content -->
            <form method="POST" action="{{ route('cities.update', $city['id']) }}" class="p-8">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- City Name Field -->
                    <div class="group">
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm12 6a3 3 0 11-6 0 3 3 0 016 0z" clip-rule="evenodd"></path>
                            </svg>
                            Nama Kota
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="name" 
                                id="name" 
                                value="{{ old('name', $city['name']) }}" 
                                class="block w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 hover:border-gray-400 @error('name') border-red-300 focus:ring-red-500 @enderror" 
                                placeholder="Contoh: Jakarta"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Coordinates Section -->
                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M12 1.586l-4 4v12.828l4-4V1.586zM3.707 3.293A1 1 0 002 4v10a1 1 0 00.293.707L6 18.414V5.586L3.707 3.293zM17.707 5.293L14 1.586v12.828l2.293 2.293A1 1 0 0018 16V6a1 1 0 00-.293-.707z" clip-rule="evenodd"></path>
                            </svg>
                            Koordinat Geografis
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Perbarui lokasi koordinat kota dengan tepat</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Latitude Field -->
                            <div class="group">
                                <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Latitude
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="any" 
                                    name="latitude" 
                                    id="latitude" 
                                    value="{{ old('latitude', $city['latitude']) }}" 
                                    class="block w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-gray-400 @error('latitude') border-red-300 focus:ring-red-500 @enderror" 
                                    placeholder="Contoh: -6.2000"
                                    required
                                >
                                @error('latitude')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            <!-- Longitude Field -->
                            <div class="group">
                                <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Longitude
                                    <span class="text-red-500 ml-1">*</span>
                                </label>
                                <input 
                                    type="number" 
                                    step="any" 
                                    name="longitude" 
                                    id="longitude" 
                                    value="{{ old('longitude', $city['longitude']) }}" 
                                    class="block w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 hover:border-gray-400 @error('longitude') border-red-300 focus:ring-red-500 @enderror" 
                                    placeholder="Contoh: 106.8167"
                                    required
                                >
                                @error('longitude')
                                    <p class="mt-2 text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        <!-- Coordinate Helper -->
                        <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                            <div class="flex items-start">
                                <svg class="w-5 h-5 text-orange-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-orange-800">Tips memperbarui koordinat:</p>
                                    <p class="text-sm text-orange-700 mt-1">Pastikan koordinat baru sudah benar sebelum menyimpan perubahan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Country Field -->
                    <div class="group">
                        <label for="country" class="block text-sm font-semibold text-gray-700 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Negara
                            <span class="text-red-500 ml-1">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="text" 
                                name="country" 
                                id="country" 
                                value="{{ old('country', $city['country']) }}" 
                                class="block w-full px-4 py-3 text-gray-900 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 hover:border-gray-400 @error('country') border-red-300 focus:ring-red-500 @enderror" 
                                placeholder="Contoh: Indonesia"
                                required
                            >
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400 group-hover:text-orange-500 transition-colors" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h4a1 1 0 010 2H6.414l2.293 2.293a1 1 0 01-1.414 1.414L5 6.414V8a1 1 0 01-2 0V4zm9 1a1 1 0 010-2h4a1 1 0 011 1v4a1 1 0 01-2 0V6.414l-2.293 2.293a1 1 0 11-1.414-1.414L13.586 5H12zm-9 7a1 1 0 012 0v1.586l2.293-2.293a1 1 0 111.414 1.414L6.414 15H8a1 1 0 010 2H4a1 1 0 01-1-1v-4zm13-1a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 010-2h1.586l-2.293-2.293a1 1 0 111.414-1.414L15.586 13H14a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                        </div>
                        @error('country')
                            <p class="mt-2 text-sm text-red-600 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-200">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white font-semibold rounded-xl shadow-lg hover:from-orange-700 hover:to-orange-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-xl"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7.707 10.293a1 1 0 10-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 11.586V6h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V8a2 2 0 012-2h5v5.586l-1.293-1.293zM9 4a1 1 0 012 0v2H9V4z"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                    
                    <a 
                        href="{{ route('cities.index') }}" 
                        class="inline-flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl border border-gray-300 hover:bg-gray-200 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200 transform hover:-translate-y-0.5"
                    >
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Changes Warning Card -->
        <div class="mt-6 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-200">
            <div class="flex items-start space-x-3">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Peringatan Perubahan Data</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Perubahan data kota akan mempengaruhi rute yang sudah ada
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Pastikan koordinat baru sudah diverifikasi dengan benar
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Backup data penting sebelum melakukan perubahan besar
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection