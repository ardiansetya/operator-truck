@extends('layouts.dashboard')

@section('title', 'Rute')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Daftar Rute</h1>
                <p class="text-gray-600 mt-2">Kelola rute pengiriman</p>
            </div>
            <a href="{{ route('routes.create') }}"
                class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-lg flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Rute</span>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-8 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if (session('errors'))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl">
                <div class="flex items-center mb-2">
                    <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Terjadi kesalahan:</span>
                </div>
                @foreach (session('errors')->all() as $error)
                    <p class="ml-8">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        @if (isset($error))
            <div class="mb-8 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl flex items-center">
                <svg class="w-5 h-5 mr-3 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                {{ $error }}
            </div>
        @endif

        <!-- Statistics Cards -->
        @if (!empty($routes))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Rute Aktif -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium mb-1">Rute Aktif</p>
                            <p class="text-3xl font-bold">{{ collect($routes)->where('is_active', true)->count() }}</p>
                            <p class="text-green-100 text-xs mt-1">sedang digunakan</p>
                        </div>
                        <div class="bg-green-400 bg-opacity-30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Rute Non-Aktif -->
                <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-xl text-white shadow-lg">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-red-100 text-sm font-medium mb-1">Rute Non-Aktif</p>
                            <p class="text-3xl font-bold">{{ collect($routes)->where('is_active', false)->count() }}</p>
                            <p class="text-red-100 text-xs mt-1">tidak digunakan</p>
                        </div>
                        <div class="bg-red-400 bg-opacity-30 p-3 rounded-lg">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Search Form -->
<!-- Search Form -->
<div class="mb-8 bg-white rounded-xl shadow-lg border border-gray-100 p-6">
    <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-800 flex items-center space-x-2">
            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <span>Cari Rute</span>
        </h3>
        <p class="text-sm text-gray-600 mt-1">Filter rute berdasarkan kota awal dan tujuan</p>
    </div>

    <form method="GET" action="{{ route('routes.index') }}" class="space-y-4 md:space-y-0 md:flex md:items-end md:space-x-4">
        <!-- Start City Input -->
        <div class="flex-1">
            <label for="start_city" class="block text-sm font-medium text-gray-700 mb-2">
                Kota Awal
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="start_city"
                    name="start_city" 
                    value="{{ request('start_city') }}" 
                    placeholder="Masukkan kota awal"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 
                           bg-gray-50 focus:bg-white hover:border-gray-400"
                >
            </div>
        </div>

        <!-- End City Input -->
        <div class="flex-1">
            <label for="end_city" class="block text-sm font-medium text-gray-700 mb-2">
                Kota Tujuan
            </label>
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"></path>
                    </svg>
                </div>
                <input 
                    type="text" 
                    id="end_city"
                    name="end_city" 
                    value="{{ request('end_city') }}" 
                    placeholder="Masukkan kota tujuan"
                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-500 
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 
                           bg-gray-50 focus:bg-white hover:border-gray-400"
                >
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex space-x-3 md:flex-shrink-0">
            <!-- Search Button -->
            <button 
                type="submit" 
                class="flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 
                       text-white font-semibold rounded-lg shadow-md hover:from-blue-600 hover:to-blue-700 
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
                       transform hover:-translate-y-0.5 transition-all duration-200 space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span>Cari</span>
            </button>

            <!-- Reset Button -->
            @if(request('start_city') || request('end_city'))
                <a 
                    href="{{ route('routes.index') }}" 
                    class="flex items-center justify-center px-4 py-3 bg-gray-100 text-gray-700 
                           font-medium rounded-lg border border-gray-300 hover:bg-gray-200 
                           focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 
                           transition-all duration-200 space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    <span>Reset</span>
                </a>
            @endif
        </div>
    </form>

    <!-- Search Results Info -->
    @if(request('start_city') || request('end_city'))
        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm text-blue-800">
                    Menampilkan hasil pencarian untuk: 
                    @if(request('start_city'))
                        <span class="font-medium">{{ request('start_city') }}</span>
                    @endif
                    @if(request('start_city') && request('end_city'))
                        →
                    @endif
                    @if(request('end_city'))
                        <span class="font-medium">{{ request('end_city') }}</span>
                    @endif
                </p>
            </div>
        </div>
    @endif
</div>



        <!-- Routes Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($routes as $route)
                <div
                    class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
                    <!-- Header with Status -->
                    <div
                        class="bg-gradient-to-r {{ $route['is_active'] ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600' }} p-4">
                        <div class="flex justify-between items-start">
                            <div class="text-white">
                             <h3 class="text-lg font-bold">
  {{ $route['start_city_name'] ?? 'Kota Awal #' . $route['start_city_id'] }}
  -
  {{ $route['end_city_name'] ?? 'Kota Tujuan #' . $route['end_city_id'] }}
</h3>

                                {{-- <p class="text-sm opacity-90">
                                   Rute #{{ $route['id'] }} 
                                </p> --}}
                            </div>
                            <div class="text-right">
                                <span
                                    class="inline-block px-3 py-1 text-xs font-semibold text-white bg-white bg-opacity-20 rounded-full">
                                    {{ $route['is_active'] ? 'Aktif' : 'Non-Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <!-- Cargo Type -->
                        @if (isset($route['cargo_type']))
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-blue-800">Jenis Muatan</p>
                                        <p class="text-sm text-blue-600">
                                            {{ $route['cargo_type'] }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Route Details Grid -->
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-2xl font-bold text-gray-800">
                                    Rp {{ number_format($route['base_price'], 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">Harga Dasar</p>
                            </div>
                            <div class="text-center p-3 bg-gray-50 rounded-lg">
                                <p class="text-2xl font-bold text-gray-800">
                                    {{ isset($route['distance_km']) ? number_format($route['distance_km'], 1) : 'N/A' }}
                                </p>
                                <p class="text-xs text-gray-600 mt-1">Jarak (km)</p>
                            </div>
                        </div>

                        <!-- Duration -->
                        @if (isset($route['estimated_duration_hours']))
                            <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-orange-800">Estimasi Durasi</p>
                                        <p class="text-sm text-orange-600">
                                            {{ number_format($route['estimated_duration_hours'], 1) }} jam
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- <div class="mb-4 p-3 bg-purple-50 rounded-lg border border-purple-200">
                                <p class="text-sm font-medium text-purple-800 mb-1">Muatan</p>
                                <p class="text-sm text-purple-600">{{ $route['cargo_type'] }}</p>
                            </div> --}}

                        <!-- Action Buttons -->
                        <div class="flex space-x-2 pt-4 border-t border-gray-200">
                            <a href="{{ route('routes.show', $route['id']) }}"
                                class="flex-1 bg-blue-500 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors duration-200 text-sm font-medium">
                                Detail
                            </a>
                            <a href="{{ route('routes.edit', $route['id']) }}"
                                class="flex-1 bg-yellow-500 text-white text-center py-2 px-4 rounded-lg hover:bg-yellow-600 transition-colors duration-200 text-sm font-medium">
                                Edit
                            </a>
                            @if (!!$route['is_active'])
                                 <form action="{{ route('routes.destroy', $route['id']) }}" method="POST" class="flex-1"
                                onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan rute {{ $route['start_city_name'] ?? 'ini' }} → {{ $route['end_city_name'] ?? 'tujuan' }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-red-500 text-white py-2 px-4 rounded-lg hover:bg-red-600 transition-colors duration-200 text-sm font-medium">
                                    Nonaktifkan
                                </button>
                            </form>
                            @endif
                           
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center py-16 bg-white rounded-xl shadow-lg border border-gray-100">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-1.447-.894L15 4m0 13V4m0 0L9 7">
                            </path>
                        </svg>
                        <h3 class="text-xl font-medium text-gray-700 mb-2">Belum ada rute tersedia</h3>
                        <p class="text-gray-500 mb-6">Mulai dengan menambahkan rute pengiriman pertama Anda</p>
                        <a href="{{ route('routes.create') }}"
                            class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-lg inline-flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            <span>Tambah Rute</span>
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
