@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Detail Pengiriman</h1>
        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ is_null($delivery['finished_at']) ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                <div class="w-2 h-2 rounded-full {{ is_null($delivery['finished_at']) ? 'bg-blue-400' : 'bg-green-400' }} mr-2"></div>
                {{ is_null($delivery['finished_at']) ? 'Aktif' : 'Selesai' }}
            </span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ $errors->first('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Information Card -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white">Informasi Pengiriman</h2>
                </div>
                
                <div class="p-6 space-y-6">
                    <!-- Vehicle Information -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0115.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707L16 7.586A1 1 0 0015.414 7H14z"></path>
                            </svg>
                            Kendaraan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Plat Nomor</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['truck_license_plate'] ?? 'Tidak Diketahui' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ID Truk</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['truck_id'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Worker Information -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            Pekerja
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Nama Pekerja</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['worker_name'] ?? 'Tidak Diketahui' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ID Pekerja</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['worker_id'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Route Information -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                            </svg>
                            Rute Perjalanan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Kota Asal</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['start_city_name'] ?? 'Tidak Diketahui' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Kota Tujuan</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['end_city_name'] ?? 'Tidak Diketahui' }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">ID Rute</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['route_id'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Trip Details -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                            Detail Perjalanan
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Jarak</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['distance_km'] ?? 0 }} km</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Estimasi Durasi</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['estimated_duration_hours'] ?? 0 }} jam</p>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline Information -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Timeline Pengiriman
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Waktu Mulai</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">
                                    {{ $delivery['started_at'] ? date('d/m/Y H:i', $delivery['started_at'] / 1000) : 'Belum dimulai' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600 mb-1">Waktu Selesai</label>
                                <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">
                                    {{ $delivery['finished_at'] ? date('d/m/Y H:i', $delivery['finished_at'] / 1000) : 'Belum selesai' }}
                                </p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-600 mb-1">Ditambahkan Oleh</label>
                            <p class="text-gray-900 font-semibold bg-white px-3 py-2 rounded-lg border">{{ $delivery['add_by_operator_name'] ?? 'Tidak Diketahui' }}</p>
                        </div>
                    </div>

                    @if(!empty($delivery['transits']))
                    <!-- Transit Points -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Titik Transit ({{ count($delivery['transits']) }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($delivery['transits'] as $transit)
                            <div class="bg-white p-4 rounded-lg border">
                                <div class="flex justify-between items-start mb-3">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-800 mb-1">
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $transit['transit_point']['loading_city']['name'] ?? 'Tidak Diketahui' }}
                                            </span>
                                            →
                                            <span class="inline-flex items-center">
                                                <svg class="w-4 h-4 mr-1 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $transit['transit_point']['unloading_city']['name'] ?? 'Tidak Diketahui' }}
                                            </span>
                                        </p>
                                        <div class="flex flex-wrap gap-2 text-xs text-gray-500">
                                            @if(isset($transit['transit_point']['estimated_duration_minute']) && $transit['transit_point']['estimated_duration_minute'] !== null)
                                            <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                Estimasi: {{ $transit['transit_point']['estimated_duration_minute'] }} menit
                                            </span>
                                            @endif
                                            @if(isset($transit['transit_point']['extra_cost']) && $transit['transit_point']['extra_cost'] > 0)
                                            <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded">
                                                Biaya Extra: Rp {{ number_format($transit['transit_point']['extra_cost'], 0, ',', '.') }}
                                            </span>
                                            @endif
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ ($transit['is_accepted'] ?? false) ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ($transit['is_accepted'] ?? false) ? 'Diterima' : 'Menunggu' }}
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                                    @if($transit['arrived_at'])
                                    <div>
                                        <span class="font-medium text-gray-600">Waktu Tiba:</span>
                                        <p class="text-gray-800">{{ date('d/m/Y H:i', $transit['arrived_at'] / 1000) }}</p>
                                    </div>
                                    @endif
                                    
                                    @if($transit['actioned_at'])
                                    <div>
                                        <span class="font-medium text-gray-600">Waktu Aksi:</span>
                                        <p class="text-gray-800">{{ date('d/m/Y H:i', $transit['actioned_at'] / 1000) }}</p>
                                    </div>
                                    @endif
                                </div>
                                
                                @if($transit['reason'])
                                <div class="mt-2 p-2 bg-gray-50 rounded text-xs">
                                    <span class="font-medium text-gray-600">Alasan:</span>
                                    <p class="text-gray-700 mt-1">{{ $transit['reason'] }}</p>
                                </div>
                                @endif
                                
                                @if($transit['action_by_name'] !== 'N/A')
                                <div class="mt-2 text-xs text-gray-500">
                                    <span class="font-medium">Diproses oleh:</span> {{ $transit['action_by_name'] }}
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!empty($delivery['alerts']))
                    <!-- Alerts -->
                    <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                        <h3 class="text-lg font-semibold text-red-800 mb-3 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Peringatan ({{ count($delivery['alerts']) }})
                        </h3>
                        <div class="space-y-3">
                            @foreach($delivery['alerts'] as $alert)
                            <div class="bg-white p-3 rounded-lg border border-red-200">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center mb-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-2">
                                                {{ ucfirst($alert['type'] ?? 'Peringatan') }}
                                            </span>
                                            @if($alert['created_at'])
                                            <span class="text-xs text-red-600">
                                                {{ date('d/m/Y H:i', $alert['created_at'] / 1000) }}
                                            </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-red-700">{{ $alert['message'] ?? 'Tidak ada pesan' }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Invoice Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-green-600 to-green-700 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        Invoice Pengiriman
                    </h2>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- Invoice Header -->
                        <div class="text-center border-b border-gray-200 pb-4 mb-4">
                            <p class="text-sm text-gray-600">Invoice #</p>
                            <p class="text-lg font-bold text-gray-900">{{ str_pad($delivery['id'] ?? '0', 6, '0', STR_PAD_LEFT) }}</p>
                        </div>

                        <!-- Pricing Details -->
                        <div class="space-y-3">
                            @php
                                $base_price = $delivery['base_price'] ?? 0;
                                $distance_km = $delivery['distance_km'] ?? 0;
                                $estimated_duration_hours = $delivery['estimated_duration_hours'] ?? 0;
                                $fuel_cost = $distance_km * 1500; // Estimasi biaya BBM per km
                                $driver_fee = $estimated_duration_hours * 25000; // Fee sopir per jam
                                
                                // Tambahkan biaya extra dari transit points
                                $extra_cost = 0;
                                if (!empty($delivery['transits'])) {
                                    foreach ($delivery['transits'] as $transit) {
                                        $extra_cost += $transit['transit_point']['extra_cost'] ?? 0;
                                    }
                                }
                                
                                $total_price = $base_price + $extra_cost;
                            @endphp

                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-sm text-gray-600">Harga Dasar</span>
                                <span class="font-semibold text-gray-900">Rp {{ number_format($base_price, 0, ',', '.') }}</span>
                            </div>

                        </div>

                        <!-- Total -->
                        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-bold text-green-900">Total Harga</span>
                                <span class="text-2xl font-bold text-green-900">Rp {{ number_format($total_price, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ is_null($delivery['finished_at']) ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                {{ is_null($delivery['finished_at']) ? 'Menunggu Pembayaran' : 'Lunas' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mt-8 flex flex-wrap gap-4">
        <a href="{{ route('deliveries.index') }}" class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-lg">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Kembali
        </a>
        
        @if (is_null($delivery['finished_at']))
            <form method="POST" action="{{ route('deliveries.finish', $delivery['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menyelesaikan pengiriman ini?')">
                @csrf
                @method('PATCH')
                <button type="submit" class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition duration-200 ease-in-out transform hover:-translate-y-0.5 shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    Selesaikan Pengiriman
                </button>
            </form>
        @endif
    </div>
</div>
@endsection