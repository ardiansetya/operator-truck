@extends('layouts.dashboard')

@section('title', 'Transit Points')

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="flex items-center space-x-4">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 mb-2">Transit Points</h1>
                        <p class="text-gray-600">Kelola titik transit untuk optimasi rute pengiriman</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">

                    <a href="{{ route('transit-points.create') }}"
                        class="bg-gradient-to-r from-emerald-500 to-teal-600 text-white px-6 py-3 rounded-xl hover:from-emerald-600 hover:to-teal-700 transition-all duration-200 transform hover:scale-105 shadow-lg font-medium flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Tambah Transit Point</span>
                    </a>
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 text-white px-6 py-3 rounded-xl shadow-lg">
                        <div class="text-sm font-medium">Total Transit</div>
                        <div class="text-2xl font-bold">{{ count($transitPoints) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-8 p-4 bg-gradient-to-r from-green-400 to-emerald-500 text-white rounded-xl shadow-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-8 p-4 bg-gradient-to-r from-red-400 to-rose-500 text-white rounded-xl shadow-lg">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">{{ $error }}</span>
                </div>
            </div>
        @endif

        <!-- Filter Section -->
       <div class="mb-8 bg-white rounded-2xl shadow-lg p-6">
    <div class="flex flex-col lg:flex-row gap-6 items-center justify-between">
        <div class="flex flex-col sm:flex-row gap-6 w-full lg:w-auto">
            @if (count($transitPoints) > 0)
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-6 w-full">
                    <!-- Transit Aktif -->
                    <div class="bg-gradient-to-br from-emerald-400 via-teal-500 to-teal-600 p-6 rounded-2xl text-white shadow-lg transform hover:scale-[1.02] transition-all duration-200">
                        <div class="text-base font-semibold opacity-90">Transit Aktif</div>
                        <div class="text-4xl font-extrabold mt-2">
                            {{ collect($transitPoints)->where('is_active', 1)->count() }}
                        </div>
                    </div>

                    <!-- Transit Non-Aktif -->
                    <div class="bg-gradient-to-br from-rose-400 via-pink-500 to-pink-600 p-6 rounded-2xl text-white shadow-lg transform hover:scale-[1.02] transition-all duration-200">
                        <div class="text-base font-semibold opacity-90">Transit Non-Aktif</div>
                        <div class="text-4xl font-extrabold mt-2">
                            {{ collect($transitPoints)->where('is_active', 0)->count() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>


        <!-- Transit Points Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6" id="transitPointsGrid">
            @forelse ($transitPoints as $transitPoint)
                <div class="transit-card bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden"
                    data-status="{{ $transitPoint['is_active'] ? 'active' : 'inactive' }}"
                    data-cargo="{{ strtolower($transitPoint['cargo_type']) }}"
                    data-search="{{ strtolower($transitPoint['loading_city_name'] . ' ' . $transitPoint['unloading_city_name'] . ' ' . $transitPoint['cargo_type']) }}">

                    <!-- Card Header -->
                    <div class="relative p-6 bg-gradient-to-r from-slate-50 to-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div
                                    class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    #{{ $transitPoint['id'] }}
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800">{{ $transitPoint['cargo_type'] }}</h3>
                                    <p class="text-sm text-gray-500">Jenis Muatan</p>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $transitPoint['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $transitPoint['is_active'] ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                        </div>
                    </div>

                    <!-- Route Visualization -->
                    <div class="px-6 py-4">
                        <div class="relative">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex flex-col items-center">
                                        <div class="w-4 h-4 bg-emerald-500 rounded-full shadow-lg"></div>
                                        <div class="w-1 h-12 bg-gradient-to-b from-emerald-300 to-rose-300"></div>
                                        <div class="w-4 h-4 bg-rose-500 rounded-full shadow-lg"></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="mb-4">
                                            <div class="text-sm font-bold text-gray-800 mb-1 truncate">
                                                {{ $transitPoint['loading_city_name'] }}</div>
                                            <div class="text-xs text-emerald-600 font-medium">Kota Pemuatan</div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800 mb-1 truncate">
                                                {{ $transitPoint['unloading_city_name'] }}</div>
                                            <div class="text-xs text-rose-600 font-medium">Kota Pembongkaran</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Route Info -->
                            <div class="mt-4 p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-indigo-600">
                                            {{ $transitPoint['estimated_duration_minute'] }}</div>
                                        <div class="text-xs text-gray-600">Menit</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-2xl font-bold text-emerald-600">Rp
                                            {{ number_format($transitPoint['extra_cost'], 0, ',', '.') }}</div>
                                        <div class="text-xs text-gray-600">Biaya Ekstra</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer with Actions -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="{{ route('transit-points.edit', $transitPoint['id']) }}"
                                class="flex-1 bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2 px-4 rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all duration-200 text-center font-medium transform hover:scale-105 flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                    </path>
                                </svg>
                                <span>Edit</span>
                            </a>
                          @if ($transitPoint['is_active'])
                                <form action="{{ route('transit-points.destroy', $transitPoint['id']) }}" method="POST"
                                class="flex-1"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus transit point ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full bg-gradient-to-r from-red-500 to-rose-600 text-white py-2 px-4 rounded-xl hover:from-red-600 hover:to-rose-700 transition-all duration-200 font-medium transform hover:scale-105 flex items-center justify-center space-x-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                    <span>Nonaktifkan</span>
                                </button>
                            </form>
                          @endif
                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="col-span-full">
                    <div class="text-center py-20">
                        <div
                            class="w-32 h-32 mx-auto mb-8 bg-gradient-to-r from-gray-100 to-gray-200 rounded-3xl flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-600 mb-4">Belum Ada Transit Point</h3>
                        <p class="text-gray-500 mb-8 max-w-md mx-auto">Mulai dengan menambahkan transit point pertama Anda
                            untuk mengoptimalkan rute pengiriman</p>
                        <a href="{{ route('transit-points.create') }}"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Tambah Transit Point Baru
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Statistics Card -->

    </div>

    <!-- Custom CSS -->
    <style>
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .transit-card {
            animation: slideInUp 0.6s ease-out;
        }

        .route-line {
            background: linear-gradient(to bottom, #10b981, #f43f5e);
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const statusFilter = document.getElementById('statusFilter');
            const cargoFilter = document.getElementById('cargoFilter');
            const cards = document.querySelectorAll('.transit-card');

            // Populate cargo filter options
            const cargoTypes = [...new Set(Array.from(cards).map(card => card.dataset.cargo))];
            cargoTypes.forEach(cargo => {
                const option = document.createElement('option');
                option.value = cargo;
                option.textContent = cargo.charAt(0).toUpperCase() + cargo.slice(1);
                cargoFilter.appendChild(option);
            });

            // Filter function
            function filterCards() {
                const searchTerm = searchInput.value.toLowerCase();
                const statusValue = statusFilter.value;
                const cargoValue = cargoFilter.value.toLowerCase();

                cards.forEach(card => {
                    const matchesSearch = card.dataset.search.includes(searchTerm);
                    const matchesStatus = !statusValue || card.dataset.status === statusValue;
                    const matchesCargo = !cargoValue || card.dataset.cargo === cargoValue;

                    if (matchesSearch && matchesStatus && matchesCargo) {
                        card.style.display = 'block';
                        card.style.animation = 'slideInUp 0.3s ease-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            }

            // Add event listeners
            searchInput.addEventListener('input', filterCards);
            statusFilter.addEventListener('change', filterCards);
            cargoFilter.addEventListener('change', filterCards);

            // Add stagger animation to cards
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });
        });
    </script>
@endsection
