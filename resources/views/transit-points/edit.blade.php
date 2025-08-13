@extends('layouts.dashboard')

@section('title', 'Edit Transit Point')

@section('content')
    <div class="min-h-screen py-8">
        <div class="container mx-auto px-6 max-w-2xl">
            <!-- Header Section -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 text-white rounded-full mb-4 shadow-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Transit Point</h1>
                <p class="text-gray-600">Perbarui informasi transit point sesuai kebutuhan</p>
            </div>

            <!-- Success Message -->
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg shadow-sm text-green-700">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Error Message -->
            @if (isset($error))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm text-red-700">
                    ⚠️ {{ $error }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                    <div class="flex items-center mb-2">
                        <svg class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-red-700 font-medium">Terdapat kesalahan pada input:</p>
                    </div>
                    <ul class="list-disc pl-6 text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Form -->
            <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-8 py-6">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Formulir Edit Transit Point
                    </h2>
                </div>

                <form action="{{ route('transit-points.update', $transitPoint['id']) }}" method="POST" class="p-8">
                    @csrf
                    @method('PUT')

                    <!-- Kota Pemuatan -->
                    <div class="mb-6">
                        <label for="loading_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kota Pemuatan
                        </label>
                        <select name="loading_city_id" id="loading_city_id"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            required>
                            <option value="">Pilih Kota</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city['id'] }}"
                                    {{ old('loading_city_id', $transitPoint['loading_city']['id']) == $city['id'] ? 'selected' : '' }}>
                                    {{ $city['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('loading_city_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Kota Pembongkaran -->
                    <div class="mb-6">
                        <label for="unloading_city_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Kota Pembongkaran
                        </label>
                        <select name="unloading_city_id" id="unloading_city_id"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            required>
                            <option value="">Pilih Kota</option>
                            @foreach ($cities as $city)
                                <option value="{{ $city['id'] }}"
                                    {{ old('unloading_city_id', $transitPoint['unloading_city']['id']) == $city['id'] ? 'selected' : '' }}>
                                    {{ $city['name'] }}
                                </option>
                            @endforeach
                        </select>
                        @error('unloading_city_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Estimasi Durasi -->
                    <div class="mb-6">
                        <label for="estimated_duration_minute" class="block text-sm font-semibold text-gray-700 mb-2">
                            Estimasi Durasi (Menit)
                        </label>
                        <input type="number" name="estimated_duration_minute" id="estimated_duration_minute"
                            value="{{ old('estimated_duration_minute', $transitPoint['estimated_duration_minute']) }}"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            placeholder="Masukkan durasi (menit)" required min="0">
                        @error('estimated_duration_minute')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>
                    <!-- Cargo Type -->
                    <div class="mb-6">
                        <label for="cargo_type" class="block text-sm font-semibold text-gray-700 mb-2">
                            Jenis Muatan
                        </label>
                        <input type="text" name="cargo_type" id="cargo_type"
                            value="{{ old('cargo_type', $transitPoint['cargo_type']) }}"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            placeholder="Pakan, Pasir, dll" required min="0">
                        @error('cargo_type')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Biaya Ekstra -->
                    <div class="mb-6">
                        <label for="extra_cost" class="block text-sm font-semibold text-gray-700 mb-2">
                            Biaya Ekstra
                        </label>
                        <input type="number" name="extra_cost" id="extra_cost" step="0.01"
                            value="{{ old('extra_cost', $transitPoint['extra_cost']) }}"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            placeholder="Masukkan biaya tambahan" required min="0">
                        @error('extra_cost')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Status Aktif -->
                    <div class="mb-6">
                        <label for="is_active" class="block text-sm font-semibold text-gray-700 mb-2">
                            Status Aktif
                        </label>
                        <select name="is_active" id="is_active"
                            class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-blue-500 focus:ring-0 transition-colors duration-200 bg-gray-50 focus:bg-white"
                            required>
                             <option value="0"
                                {{ old('is_active', $transitPoint['is_active']) == 0 ? 'selected' : '' }}>Tidak Aktif
                            </option>
                            <option value="1"
                                {{ old('is_active', $transitPoint['is_active']) == 1 ? 'selected' : '' }}>Aktif</option>
                           
                        </select>
                        @error('is_active')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                        <button type="submit"
                            class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-blue-600 hover:to-blue-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            Simpan Perubahan
                        </button>
                        <a href="{{ route('transit-points.index') }}"
                            class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Batal
                        </a>
                    </div>
                </form>
            </div>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Pastikan semua informasi yang dimasukkan sudah benar sebelum menyimpan perubahan
                </p>
            </div>
        </div>
    </div>
@endsection
