@extends('layouts.dashboard')

@section('title', 'Driver Transit')

@section('content')
<div class="container mx-auto px-6 py-8">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Daftar Driver Transit</h1>
            <p class="text-gray-600">Kelola permohonan driver transit dan titik transit</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('transit-points.index') }}" class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Daftar Transit Point
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        @foreach ($errors->all() as $error)
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-red-700 font-medium">{{ $error }}</p>
                </div>
            </div>
        @endforeach
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 rounded-xl text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-yellow-100 text-sm font-medium mb-1">Menunggu Persetujuan</p>
                    <p class="text-3xl font-bold">{{ collect($drivers)->where('status', 'Menunggu')->count() }}</p>
                    <p class="text-yellow-100 text-xs mt-1">perlu ditindaklanjuti</p>
                </div>
                <div class="bg-yellow-400 bg-opacity-30 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium mb-1">Diterima</p>
                    <p class="text-3xl font-bold">{{ collect($drivers)->where('status', 'Diterima')->count() }}</p>
                    <p class="text-green-100 text-xs mt-1">driver aktif</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-xl text-white shadow-lg">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium mb-1">Ditolak</p>
                    <p class="text-3xl font-bold">{{ collect($drivers)->where('status', 'Ditolak')->count() }}</p>
                    <p class="text-red-100 text-xs mt-1">permohonan ditolak</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 p-3 rounded-lg">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Driver Transit Cards -->
    @forelse ($drivers as $index => $driver)
        <div class="bg-white rounded-xl shadow-lg mb-6 overflow-hidden hover:shadow-xl transition-shadow duration-300 border border-gray-100">
            <!-- Header -->
            <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-500 text-white w-10 h-10 rounded-full flex items-center justify-center font-bold text-lg">
                            {{ $index + 1 }}
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-800">{{ $driver['license_plate'] ?? 'N/A' }}</h3>
                            <p class="text-sm text-gray-600">{{ $driver['truck_model'] ?? 'Model tidak tersedia' }}</p>
                        </div>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        @if ($driver['status'] === 'Diterima')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $driver['status'] }}
                            </span>
                        @elseif($driver['status'] === 'Ditolak')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $driver['status'] ?? 'Ditolak' }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                                {{ $driver['status'] ?? 'Menunggu' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <!-- Route Info -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Rute Transit
                        </h4>
                         <div class="bg-blue-50 p-3 rounded-lg ">
                                <div class="flex items-center justify-center">
                                    <div class="flex items-center text-sm">
                                        <span class="w-3 h-3 bg-green-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $driver['route_start'] ?? 'Tidak tersedia' }}</span>
                                    </div>
                                    <div class="flex items-center my-2">
                                        <div class="w-16 border-b-2 border-dashed border-gray-300 mx-2"></div>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <span class="w-3 h-3 bg-red-500 rounded-full mr-2"></span>
                                        <span class="text-gray-700">{{ $driver['route_end'] ?? 'Tidak tersedia' }}</span>
                                    </div>
                                </div>
                                 <div class="flex justify-between mt-2">
                                    <span class="text-gray-600 text-sm">Jenis Muatan:</span>
                                    <span class="font-semibold text-sm text-gray-800">
                                        {{ $driver['type_cargo'] ?? 'Unknown' }}
                                    </span>
                                </div>

                            </div>

                    </div>

                    <!-- Vehicle Info -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Detail Kendaraan
                        </h4>
                        <div class="bg-purple-50 p-3 rounded-lg space-y-2">
                            <div class="text-center bg-white p-2 rounded border">
                                <p class="text-xs text-gray-500">Plat Nomor</p>
                                <p class="font-bold text-gray-800 tracking-wider">{{ $driver['license_plate'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <span class="text-gray-600 text-sm">Model:</span>
                                <span class="font-medium ml-2">{{ $driver['truck_model'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Tarif Info -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            Tarif Transit
                        </h4>
                        <div class="bg-green-50 p-3 rounded-lg">
                            <p class="text-2xl font-bold text-green-700">Rp {{ number_format($driver['tarif'] ?? 0, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600 mt-1">per pengiriman</p>
                        </div>
                    </div>

                    <!-- Operator Info -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Operator
                        </h4>
                        <div class="bg-orange-50 p-3 rounded-lg">
                            <p class="font-semibold text-orange-700">{{ $driver['operator'] ?? 'Tidak tersedia' }}</p>
                            <p class="text-sm text-gray-600 mt-1">Penanggung jawab</p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                @if ($driver['status'] === 'Menunggu')
                    <div class="flex flex-wrap gap-3 pt-4 border-t border-gray-200">
                        <form method="POST" action="{{ route('transit-drivers.accept-or-reject') }}" class="inline-flex">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="delivery_transit_id" value="{{ $driver['id'] }}">
                            <input type="hidden" name="is_accepted" value="true">
                            <input type="hidden" name="reason" value="Diterima oleh operator">
                            <button type="submit" 
                                    class="flex items-center px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Terima
                            </button>
                        </form>

                        <form method="POST" action="{{ route('transit-drivers.accept-or-reject') }}" class="inline-flex" 
                              onsubmit="return confirm('Yakin ingin menolak permohonan driver transit ini?')">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="delivery_transit_id" value="{{ $driver['id'] }}">
                            <input type="hidden" name="is_accepted" value="false">
                            <input type="hidden" name="reason" value="Ditolak oleh operator">
                            <button type="submit" 
                                    class="flex items-center px-6 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition duration-200 transform hover:scale-105 shadow-md">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Tolak
                            </button>
                        </form>
                    </div>
                @else
                    <div class="pt-4 border-t border-gray-200">
                        <div class="text-center text-gray-500 italic">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Permohonan sudah diproses
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <!-- Empty State -->
        <div class="text-center py-16">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-medium text-gray-800 mb-2">Belum Ada Permohonan Driver Transit</h3>
            <p class="text-gray-600 mb-6">Saat ini tidak ada permohonan driver transit yang perlu ditindaklanjuti</p>
        </div>
    @endforelse
</div>
@endsection