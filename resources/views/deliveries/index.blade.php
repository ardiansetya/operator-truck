@extends('layouts.dashboard')
@section('title', 'Pengiriman')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Pengiriman</h1>
                <p class="text-gray-600">Kelola dan pantau semua pengiriman</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
                <a href="{{ route('deliveries.tracking') }}"
                    class="bg-gradient-to-r from-purple-500 to-purple-600 text-white px-6 py-3 rounded-xl hover:from-purple-600 hover:to-purple-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Tracking Live
                </a>
                <a href="{{ route('deliveries.history') }}"
                    class="bg-gradient-to-r from-yellow-500 to-yellow-600 text-white px-6 py-3 rounded-xl hover:from-yellow-600 hover:to-yellow-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    History Pengiriman
                </a>
                <a href="{{ route('deliveries.create') }}"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Pengiriman
                </a>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-red-700 font-medium">{{ $errors->first('message') }}</p>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-red-700 font-medium">{{ $error }}</p>
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8 max-w-4xl mx-auto ">
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-8 rounded-xl text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-2">Pengiriman Selesai</p>
                        <p class="text-4xl font-bold">{{ collect($deliveries)->where('finished_at', '!=', null)->count() }}
                        </p>
                        <p class="text-green-100 text-xs mt-1">dari {{ count($deliveries) }} total pengiriman</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 p-4 rounded-xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-8 rounded-xl text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-2">Pengiriman Aktif</p>
                        <p class="text-4xl font-bold">{{ collect($deliveries)->where('finished_at', null)->count() }}</p>
                        <p class="text-blue-100 text-xs mt-1">sedang dalam perjalanan</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 p-4 rounded-xl">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Cards -->
        @forelse ($deliveries as $index => $delivery)
            <div
                class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <div class="flex items-center space-x-3">
                            <div
                                class="bg-blue-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h3 class="text-xl font-semibold text-gray-800">
                                    {{ $delivery['truck_license_plate'] ?? 'Unknown' }}</h3>
                                <p class="text-sm text-gray-600">Operator: {{ $delivery['add_by_operator_name'] ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $delivery['finished_at'] ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    @if ($delivery['finished_at'])
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    @else
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd"></path>
                                    @endif
                                </svg>
                                {{ $delivery['finished_at'] ? 'Selesai' : 'Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                        <!-- Route Info -->
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                    </path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Rute Perjalanan
                            </h4>
                            <div class="bg-blue-50 p-3 rounded-lg ">
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center text-sm">
                                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $delivery['start_city_name'] ?? 'Unknown' }}</span>
                                    </div>
                                    <div class="flex items-center my-2">
                                        <div class="w-16 border-b-2 border-dashed border-gray-300 mx-2"></div>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $delivery['end_city_name'] ?? 'Unknown' }}</span>
                                    </div>
                                </div>
                                 <div class="flex justify-between mt-2">
                                    <span class="text-gray-600 text-sm">Jenis Muatan:</span>
                                    <span class="font-semibold text-sm text-gray-800">
                                        {{ $delivery['cargo_type'] ?? 'Unknown' }}
                                    </span>
                                </div>

                            </div>
                        </div>

                        <!-- Worker Info -->
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                Pekerja
                            </h4>
                            <div class="bg-green-50 p-3 rounded-lg">
                                <p class="font-semibold text-gray-800">{{ $delivery['worker_name'] ?? 'Unknown' }}</p>
                                <p class="text-sm text-gray-600 mt-1">Driver</p>
                            </div>
                        </div>

                        <!-- Price & Distance -->
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                Detail Pengiriman
                            </h4>
                            <div class="bg-purple-50 p-3 rounded-lg space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 text-sm">Harga:</span>
                                    <span class="font-semibold text-purple-700">Rp
                                        {{ number_format($delivery['base_price'] ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 text-sm">Jarak:</span>
                                    <span class="font-medium">{{ $delivery['distance_km'] ?? 0 }} km</span>
                                </div>
                            </div>
                        </div>

                        <!-- Duration -->
                        <div class="space-y-3">
                            <h4 class="font-medium text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Estimasi Waktu
                            </h4>
                            <div class="bg-orange-50 p-3 rounded-lg">
                                <p class="font-semibold text-orange-700 text-lg">
                                    {{ $delivery['estimated_duration_hours'] ?? 0 }} jam</p>
                                <p class="text-sm text-gray-600 mt-1">Perkiraan durasi</p>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('deliveries.show', $delivery['id']) }}"
                            class="flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-200 transform hover:scale-105 shadow-md">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                            Detail
                        </a>

                        @if (!$delivery['finished_at'])
                            <a href="{{ route('deliveries.tracking', ['delivery_id' => $delivery['id']]) }}"
                                class="flex items-center px-4 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600 transition duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Track
                            </a>
                        @endif

                        @if ($delivery['started_at'])
                            <form method="POST" action="{{ route('deliveries.finish', $delivery['id']) }}"
                                class="inline" onsubmit="return confirm('Yakin ingin menyelesaikan pengiriman ini?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="flex items-center px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 transform hover:scale-105 shadow-md">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Selesai
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('deliveries.destroy', $delivery['id']) }}" class="inline"
                            onsubmit="return confirm('Yakin ingin menghapus pengiriman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="flex items-center px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <!-- Empty State -->
            <div class="text-center py-16">
                <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-800 mb-2">Belum Ada Pengiriman</h3>
                <p class="text-gray-600 mb-6">Mulai dengan menambahkan pengiriman </p>
            </div>
        @endforelse
    </div>
@endsection