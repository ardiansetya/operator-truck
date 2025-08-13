@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-8">Dashboard</h1>

    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg shadow-sm border border-red-200">
            {{ $error }}
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Rute -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg hover:-translate-y-1 transform transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-lg flex items-center justify-center text-white shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7m-9 5v6h4v-6m-7-5h10"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500">Total Rute</h2>
                    <p class="text-3xl font-bold text-gray-800">{{ $routesCount ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Rute yang terdaftar</p>
                </div>
            </div>
        </div>

        <!-- Truk Aktif -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg hover:-translate-y-1 transform transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-lg flex items-center justify-center text-white shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500">Truk Aktif</h2>
                    <p class="text-3xl font-bold text-gray-800">{{ $activeTrucksCount ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Truk yang tersedia</p>
                </div>
            </div>
        </div>

        <!-- Pengiriman Aktif -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg hover:-translate-y-1 transform transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg flex items-center justify-center text-white shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500">Pengiriman Aktif</h2>
                    <p class="text-3xl font-bold text-gray-800">{{ $activeDeliveriesCount ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Pengiriman sedang berlangsung</p>
                </div>
            </div>
        </div>

        <!-- Transit Belum Dikonfirmasi -->
        <div class="bg-white rounded-xl shadow-sm p-6 hover:shadow-lg hover:-translate-y-1 transform transition-all duration-300">
            <div class="flex items-center space-x-4">
                <div class="flex-shrink-0 w-14 h-14 bg-gradient-to-br from-red-500 to-red-700 rounded-lg flex items-center justify-center text-white shadow-md">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-sm font-medium text-gray-500">Transit Belum Dikonfirmasi</h2>
                    <p class="text-3xl font-bold text-gray-800">{{ $unconfirmedTransits ?? 0 }}</p>
                    <p class="text-xs text-gray-400">Menunggu persetujuan</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
