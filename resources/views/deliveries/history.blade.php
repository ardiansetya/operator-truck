@extends('layouts.dashboard')
@section('title', 'History Pengiriman')
@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">History Pengiriman</h1>
                    <p class="text-gray-600">Kelola dan pantau semua riwayat pengiriman Anda</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white px-6 py-3 rounded-xl shadow-lg">
                        <div class="text-sm font-medium">Total Pengiriman</div>
                        <div class="text-2xl font-bold">{{ count($deliveries) }}</div>
                    </div>
                    {{-- @if (count($deliveries) > 0)
                        <div class="bg-gradient-to-r from-green-500 to-teal-600 text-white px-6 py-3 rounded-xl shadow-lg">
                            <div class="text-sm font-medium">Total Pendapatan</div>
                            <div class="text-2xl font-bold">
                                Rp {{ number_format(collect($deliveries)->sum('total_price'), 0, ',', '.') }}
                            </div>
                        </div>
                    @endif --}}
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div
                class="mb-6 p-4 bg-gradient-to-r from-green-400 to-green-500 text-white rounded-xl shadow-lg transform animate-pulse">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-xl shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $errors->first('message') }}
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-6 p-4 bg-gradient-to-r from-red-400 to-red-500 text-white rounded-xl shadow-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    {{ $error }}
                </div>
            </div>
        @endif

        <!-- Cards Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($deliveries as $index => $delivery)
                <div
                    class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100">
                    <!-- Card Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">
                                        {{ $delivery['truck_license_plate'] ?? 'Unknown' }}
                                    </h3>
                                    <p class="text-sm text-gray-500">Plat Nomor</p>
                                    <div>
                                        <p class="text-gray-500 text-sm font-semibold">
                                            @if ($delivery['finished_at'])
                                                @php
                                                    $timestamp = $delivery['finished_at'];
                                                    if ($timestamp > 9999999999) {
                                                        $timestamp = $timestamp / 1000;
                                                    }
                                                    $date = new DateTime();
                                                    $date->setTimestamp($timestamp);
                                                    $date->setTimezone(new DateTimeZone('Asia/Jakarta'));
                                                @endphp
                                                Tanggal Selesai: {{ $date->format('d/m/Y H:i:s') }}
                                            @else
                                                Belum selesai
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $delivery['finished_at'] ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ $delivery['finished_at'] ? 'Selesai' : 'Aktif' }}
                                </span>
                                {{-- @if ($delivery['transit_count'] > 0)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        {{ $delivery['transit_count'] }} Transit
                                    </span>
                                @endif --}}
                            </div>
                        </div>
                    </div>

                    <!-- Route Section -->
                    <div class="px-6 py-4">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="flex flex-col items-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                    <div class="w-0.5 h-8 bg-gray-300"></div>
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                </div>
                                <div class="flex-1">
                                    <div class="text-sm font-medium text-gray-800 mb-1">
                                        {{ $delivery['start_city_name'] ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500 mb-3">Kota Asal</div>
                                    <div class="text-sm font-medium text-gray-800 mb-1">
                                        {{ $delivery['end_city_name'] ?? 'Unknown' }}
                                    </div>
                                    <div class="text-xs text-gray-500">Kota Tujuan</div>
                                </div>
                            </div>
                        </div>

                        <!-- Transit Points -->
                        @if (count($delivery['transits'] ?? []) > 0)
                            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                                <h4 class="text-sm font-semibold text-gray-700 mb-2 flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Titik Transit
                                </h4>
                                <div class="space-y-2 max-h-24 overflow-y-auto">
                                    @foreach ($delivery['transits'] as $transit)
                                        @if ($transit['transit_point'])
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center space-x-2">
                                                    <div class="w-2 h-2 bg-blue-400 rounded-full"></div>
                                                    <span class="text-gray-700">
                                                        {{ $transit['transit_point']['loading_city']['name'] ?? 'Unknown' }}
                                                        →
                                                        {{ $transit['transit_point']['unloading_city']['name'] ?? 'Unknown' }}
                                                    </span>
                                                </div>
                                                @if ($transit['transit_point']['extra_cost'] > 0)
                                                    <span class="text-green-600 font-medium">
                                                        +Rp
                                                        {{ number_format($transit['transit_point']['extra_cost'], 0, ',', '.') }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Details Grid -->
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $delivery['distance_km'] ?? 0 }}</div>
                                <div class="text-xs text-gray-500">km</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600">
                                    {{ $delivery['estimated_duration_hours'] ?? 0 }}
                                </div>
                                <div class="text-xs text-gray-500">jam</div>
                            </div>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Driver:</span>
                                <span class="font-medium text-gray-800">{{ $delivery['worker_name'] ?? 'Unknown' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Operator:</span>
                                <span
                                    class="font-medium text-gray-800">{{ $delivery['add_by_operator_name'] ?? 'N/A' }}</span>
                            </div>

                            <!-- Price Breakdown -->
                            @php
                                $base_price = $delivery['base_price'] ?? 0;

                                // Hitung total extra cost dari semua transit yang diterima
                                $total_extra_cost = 0;
                                $accepted_transits = [];

                                if (!empty($delivery['transits'])) {
                                    foreach ($delivery['transits'] as $transit) {
                                        // Cek apakah transit diterima
                                        if (!empty($transit['is_accepted']) && $transit['is_accepted'] === true) {
                                            $extra_cost = $transit['transit_point']['extra_cost'] ?? 0;
                                            $total_extra_cost += $extra_cost;
                                            $accepted_transits[] = [
                                                'loading_city' =>
                                                    $transit['transit_point']['loading_city']['name'] ?? 'Unknown',
                                                'unloading_city' =>
                                                    $transit['transit_point']['unloading_city']['name'] ?? 'Unknown',
                                                'cargo_type' => $transit['transit_point']['cargo_type'] ?? '-',
                                                'extra_cost' => $extra_cost,
                                            ];
                                        }
                                    }
                                }

                                $total_price = $base_price + $total_extra_cost;
                            @endphp
                            <div class="pt-2 border-t border-gray-200">
                                <!-- Base Price -->
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">Harga Dasar:</span>
                                    <span class="font-medium text-gray-800">
                                        Rp {{ number_format($base_price, 0, ',', '.') }}
                                    </span>
                                </div>

                                <!-- Transit Routes & Costs -->
                                @if (!empty($accepted_transits))
                                    <div class="mb-3">
                                        <h5 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Biaya
                                            Transit:</h5>
                                        @foreach ($accepted_transits as $index => $transit)
                                            <div
                                                class="flex justify-between items-start py-1.5 {{ $index < count($accepted_transits) - 1 ? 'border-b border-gray-100' : '' }}">
                                                <div class="flex-1 pr-2">
                                                    <div class="text-xs font-medium text-gray-700">
                                                        {{ $transit['loading_city'] }} → {{ $transit['unloading_city'] }}
                                                    </div>
                                                    @if ($transit['cargo_type'] && $transit['cargo_type'] !== '-')
                                                        <div class="text-xs text-gray-500 mt-0.5">
                                                            Jenis Muatan: {{ $transit['cargo_type'] }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <span class="text-xs font-semibold text-blue-600 whitespace-nowrap">
                                                    +Rp {{ number_format($transit['extra_cost'], 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <!-- Subtotal Extra Cost -->
                                    <div class="flex justify-between text-sm mb-2 py-1 bg-blue-50 px-2 rounded">
                                        <span class="text-blue-700 font-medium">Total Biaya Transit:</span>
                                        <span class="font-semibold text-blue-700">
                                            Rp {{ number_format($total_extra_cost, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @endif

                                <!-- Total Price -->
                                <div class="flex justify-between text-sm pt-2 border-t border-gray-300">
                                    <span class="text-gray-700 font-bold">TOTAL HARGA:</span>
                                    <span class="font-bold text-green-600 text-base">
                                        Rp {{ number_format($total_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('deliveries.show', $delivery['id']) }}"
                                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 text-white py-3 px-4 rounded-xl hover:from-blue-600 hover:to-blue-700 transition-all duration-200 text-center font-medium transform hover:scale-105">
                                <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                                Detail
                            </a>

                            <div class="flex space-x-2">
                                @if ($delivery['finished_at'] === null)
                                    <form method="POST" action="{{ route('deliveries.finish', $delivery['id']) }}"
                                        class="flex-1"
                                        onsubmit="return confirm('Yakin ingin menyelesaikan pengiriman ini?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full bg-gradient-to-r from-green-500 to-green-600 text-white py-2 px-4 rounded-xl hover:from-green-600 hover:to-green-700 transition-all duration-200 font-medium transform hover:scale-105">
                                            <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Selesai
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('deliveries.destroy', $delivery['id']) }}"
                                    class="flex-1" onsubmit="return confirm('Yakin ingin menghapus pengiriman ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-gradient-to-r from-red-500 to-red-600 text-white py-2 px-4 rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 font-medium transform hover:scale-105">
                                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
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
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full">
                    <div class="text-center py-20">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Tidak Ada Data Pengiriman</h3>
                        <p class="text-gray-500 mb-6">Belum ada riwayat pengiriman yang tercatat dalam sistem</p>
                        <a href="{{ route('deliveries.create') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Pengiriman Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>



        <!-- Custom CSS for animations -->
        <style>
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .card-enter {
                animation: fadeInUp 0.5s ease-out;
            }

            .hover-lift:hover {
                transform: translateY(-4px);
            }

            /* Custom scrollbar for transit list */
            .overflow-y-auto::-webkit-scrollbar {
                width: 4px;
            }

            .overflow-y-auto::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 2px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 2px;
            }

            .overflow-y-auto::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }
        </style>

        <script>
            // Add some interactivity
            document.addEventListener('DOMContentLoaded', function() {
                // Add loading animation to cards
                const cards = document.querySelectorAll('.bg-white.rounded-2xl');
                cards.forEach((card, index) => {
                    card.style.animationDelay = `${index * 0.1}s`;
                    card.classList.add('card-enter');
                });

                // Add search functionality
                const searchInput = document.querySelector('input[type="text"]');
                if (searchInput) {
                    searchInput.addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        cards.forEach(card => {
                            const plateNumber = card.querySelector('h3').textContent.toLowerCase();
                            const driverName = card.querySelector('.text-gray-800').textContent
                                .toLowerCase();
                            if (plateNumber.includes(searchTerm) || driverName.includes(searchTerm)) {
                                card.style.display = 'block';
                            } else {
                                card.style.display = 'none';
                            }
                        });
                    });
                }

                // Add tooltip functionality for transit points
                const transitElements = document.querySelectorAll('[data-tooltip]');
                transitElements.forEach(element => {
                    element.addEventListener('mouseenter', function() {
                        // Add tooltip logic here if needed
                    });
                });

                // Auto-refresh data every 30 seconds for active deliveries
                const hasActiveDeliveries =
                    {{ collect($deliveries)->where('finished_at', null)->count() > 0 ? 'true' : 'false' }};
                if (hasActiveDeliveries) {
                    setInterval(() => {
                        // Only refresh if user is still on the page
                        if (document.visibilityState === 'visible') {
                            window.location.reload();
                        }
                    }, 30000); // 30 seconds
                }
            });

            // Function to format currency
            function formatCurrency(amount) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(amount);
            }
        </script>
    @endsection
