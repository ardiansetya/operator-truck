@extends('layouts.dashboard')
@section('title', 'Live Tracking Truck')

@section('css')
   <!-- Include Leaflet CSS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
@endsection

@section('content')
    <div class="container mx-auto px-6 py-8">
        <!-- Header Section -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">🚛 Tracking Live Truck</h1>
                <p class="text-gray-600">Pantau posisi real-time semua truck yang sedang dalam perjalanan</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
                <a href="{{ route('deliveries.index') }}"
                    class="bg-gradient-to-r from-gray-500 to-gray-600 text-white px-6 py-3 rounded-xl hover:from-gray-600 hover:to-gray-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali ke Pengiriman
                </a>
                <button onclick="refreshTrucks()"
                    class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-3 rounded-xl hover:from-blue-600 hover:to-blue-700 transition duration-300 ease-in-out transform hover:scale-105 shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Refresh Data
                </button>
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

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-green-500 to-green-600 p-6 rounded-xl text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium mb-2">Truck Online</p>
                        <p class="text-3xl font-bold" id="onlineTrucks">0</p>
                        <p class="text-green-100 text-xs mt-1">aktif dalam perjalanan</p>
                    </div>
                    <div class="bg-green-400 bg-opacity-30 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-red-600 p-6 rounded-xl text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-100 text-sm font-medium mb-2">Truck Offline</p>
                        <p class="text-3xl font-bold" id="offlineTrucks">0</p>
                        <p class="text-red-100 text-xs mt-1">tidak mengirim sinyal</p>
                    </div>
                    <div class="bg-red-400 bg-opacity-30 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L4.316 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-blue-600 p-6 rounded-xl text-white shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium mb-2">Total Truck</p>
                        <p class="text-3xl font-bold" id="totalTrucks">0</p>
                        <p class="text-blue-100 text-xs mt-1">terdaftar dalam sistem</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 p-3 rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map and Sidebar Container -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
            <div class="flex flex-col lg:flex-row h-screen max-h-[700px]">
                <!-- Sidebar -->
                <div class="lg:w-1/3 bg-gray-50 border-r border-gray-200 overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Daftar Truck Aktif
                            </h3>
                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full" id="truckCount">0 truck</span>
                        </div>
                        
                        <!-- Loading state -->
                        <div id="loadingState" class="text-center py-8">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                            <p class="text-gray-600 mt-2">Memuat data truck...</p>
                        </div>

                        <!-- Truck List -->
                        <div id="truckList" class="space-y-4 hidden">
                            <!-- Truck items akan ditambahkan melalui JavaScript -->
                        </div>

                        <!-- Empty state -->
                        <div id="emptyState" class="text-center py-8 hidden">
                            <div class="mx-auto w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-medium text-gray-800 mb-2">Tidak Ada Truck Aktif</h4>
                            <p class="text-gray-600">Belum ada truck yang sedang dalam perjalanan</p>
                        </div>
                    </div>
                </div>

                <!-- Map Container -->
                <div class="flex-1 relative z-0">
                    <!-- Status indicator -->
                    <div class="absolute top-4 left-4 z-10">
                        <div class="bg-white rounded-lg shadow-md border border-gray-200 px-3 py-2">
                            <div class="flex items-center space-x-2">
                                <div id="statusIndicator" class="w-3 h-3 rounded-full bg-gray-400"></div>
                                <span id="statusText" class="text-sm font-medium text-gray-600">Connecting...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Map -->
                    <div id="map" class="h-full w-full"></div>
                </div>
            </div>
        </div>
        
        <!-- Transit Details Modal -->
<div id="transitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[1050] flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-xl max-w-4xl w-full max-h-[80vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">Detail Transit Truck</h3>
                        <button onclick="closeTransitModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div id="modalContent" class="p-6">
                    <!-- Content will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
@endsection



@push('js')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""></script>

<script>
    // Inisialisasi peta
    let map;
    let truckMarkers = {};
    let truckData = {};
    let selectedTruckId = null;
    let updateInterval;

    // Data awal dari controller
    const INITIAL_DATA = @json($initialData ?? []);

    // Initialize map
    function initMap() {
        map = L.map('map').setView([-2.5489, 118.0149], 5); // Center of Indonesia

        const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(map);

        if (INITIAL_DATA && INITIAL_DATA.length > 0) {
            updateTruckData(INITIAL_DATA);
            updateTruckList();
            updateMap();
            updateStatistics();
            updateStatus('connected', 'Terhubung dengan data awal');
            fitAllTrucks();
        } else {
            loadTrucks();
        }
        
        // Aktifkan auto-refresh jika diinginkan
        // updateInterval = setInterval(refreshTrucks, 15000); // Refresh setiap 15 detik
    }
    
    // Fungsi untuk memuat data truck dari API
    async function loadTrucks() {
        showLoading(true);
        updateStatus('connecting', 'Mengambil data truck...');
        
        try {
            const response = await fetch('/api/deliveries/tracking', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            
            if (result.status !== 'success') {
                throw new Error(result.message || 'Failed to load truck data');
            }

            const trucksData = result.data || [];
            
            updateTruckData(trucksData);
            updateTruckList();
            updateMap();
            updateStatistics();
            updateStatus('connected', `Berhasil memuat ${trucksData.length} truck aktif`);
            
        } catch (error) {
            console.error('Error loading trucks:', error);
            updateStatus('error', 'Error memuat data');
            
            if (Object.keys(truckData).length === 0) {
                showEmptyState();
            }
        }
        
        showLoading(false);
    }

    // Fungsi untuk memperbarui data truck
    function updateTruckData(trucks) {
        truckData = {};
        let trucksArray = Array.isArray(trucks) ? trucks : [];
        
        trucksArray.forEach((truck) => {
            const lastUpdateTime = truck.last_update ? new Date(truck.last_update * 1000) : new Date();
            truckData[truck.id] = { ...truck, lastUpdateTime: lastUpdateTime };
        });
    }

    // Fungsi untuk memperbarui daftar truck di sidebar
    function updateTruckList() {
        const truckList = document.getElementById('truckList');
        const truckCount = document.getElementById('truckCount');
        const loadingState = document.getElementById('loadingState');
        const emptyState = document.getElementById('emptyState');

        const trucks = Object.values(truckData);
        
        if (trucks.length === 0) {
            truckList.classList.add('hidden');
            loadingState.classList.add('hidden');
            emptyState.classList.remove('hidden');
            truckCount.textContent = '0 truck';
            return;
        }

        truckList.classList.remove('hidden');
        loadingState.classList.add('hidden');
        emptyState.classList.add('hidden');
        truckCount.textContent = `${trucks.length} truck`;
        truckList.innerHTML = '';

        trucks.forEach(truck => {
            const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
            let status = 'online', statusColor = 'green', statusText = 'Online';
            if (timeDiff >= 900000) { status = 'offline'; statusColor = 'red'; statusText = 'Offline'; } 
            else if (timeDiff >= 300000) { status = 'delayed'; statusColor = 'yellow'; statusText = 'Delayed'; }

            const truckItem = document.createElement('div');
            truckItem.className = `truck-item p-4 bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer ${selectedTruckId === truck.id ? 'ring-2 ring-blue-500 bg-blue-50' : ''}`;
            truckItem.onclick = () => selectTruck(truck.id);

            let baseRouteHtml = '';
            if (truck.delivery_info) {
                baseRouteHtml = `
                    <div class="pt-2 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Rute Dasar:</span>
                            <span class="font-medium text-gray-700 truncate">${truck.delivery_info.start_city} → ${truck.delivery_info.end_city}</span>
                        </div>
                    </div>
                `;
            }

            let transitsHtml = '';
            if (truck.transits && truck.transits.length > 0) {
                const activeTransits = truck.transits.filter(t => t.transit_point && t.transit_point.is_active);
                if (activeTransits.length > 0) {
                    transitsHtml = `
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs mb-2">
                                <span class="text-gray-500">Rute Transit:</span>
                                <button onclick="event.stopPropagation(); showTransitDetails('${truck.id}')" class="text-blue-600 hover:text-blue-800 font-semibold">
                                    Lihat Detail
                                </button>
                            </div>
                            <div class="space-y-1">
                                ${activeTransits.slice(0, 1).map((transit) => `
                                    <div class="flex items-center text-xs">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                                        <span class="text-gray-600">${transit.transit_point.loading_city?.name || '...'} → ${transit.transit_point.unloading_city?.name || '...'}  (${transit.transit_point.cargo_type || '...'})</span>
                                        ${transit.is_accepted ? '<span class="ml-2 text-green-600">✓</span>' : '<span class="ml-2 text-yellow-600">⏳</span>'}
                                    </div>
                                `).join('')}
                                ${activeTransits.length > 1 ? `<div class="text-xs text-gray-500 pl-4">+${activeTransits.length - 1} transit lainnya</div>` : ''}
                            </div>
                        </div>
                    `;
                }
            }
            // --- Akhir Perbaikan 1 ---

            truckItem.innerHTML = `
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            🚛
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800">${truck.driver_name}</h4>
                            <p class="text-sm text-gray-600">${truck.plate_number}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <div class="w-3 h-3 rounded-full bg-${statusColor}-500"></div>
                        <span class="text-xs font-medium text-${statusColor}-700">${statusText}</span>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Update terakhir:</span>
                        <span class="font-medium text-gray-700">${formatTime(truck.lastUpdateTime)}</span>
                    </div>
                    ${baseRouteHtml}
                    ${transitsHtml}
                </div>
            `;
            truckList.appendChild(truckItem);
        });
    }

    // Fungsi untuk memperbarui peta
    function updateMap() {
        Object.values(truckMarkers).forEach(marker => map.removeLayer(marker));
        truckMarkers = {};

        Object.values(truckData).forEach(truck => {
            if (!truck.latitude || !truck.longitude) return;

            const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
            let iconColor = '#4CAF50'; let status = 'Online';
            if (timeDiff >= 900000) { iconColor = '#f44336'; status = 'Offline'; } 
            else if (timeDiff >= 300000) { iconColor = '#ff9800'; status = 'Delayed'; }

            const iconHtml = `<div style="background-color: ${iconColor}; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; ${selectedTruckId === truck.id ? 'transform: scale(1.3); border-color: #2196F3; border-width: 4px;' : ''}">🚛</div>`;
            const customIcon = L.divIcon({ html: iconHtml, className: 'custom-truck-icon', iconSize: [34, 34], iconAnchor: [17, 17] });
            const marker = L.marker([truck.latitude, truck.longitude], { icon: customIcon }).addTo(map);

            // --- PERBAIKAN 2: Konten Popup Dibuat Lebih Lengkap dan Akurat ---
            let routeInfoForPopup = '';
            if (truck.delivery_info) {
                routeInfoForPopup += `
                    <div class="pt-2 border-t border-gray-200">
                        <p><strong>Rute Dasar:</strong></p>
                        <div class="text-xs mb-1">
                            ${truck.delivery_info.start_city} → ${truck.delivery_info.end_city} ( ${truck.delivery_info.cargo_type})
                        </div>
                    </div>`;
            }

            if (truck.transits && truck.transits.length > 0) {
                const activeTransits = truck.transits.filter(t => t.transit_point && t.transit_point.is_active);
                if (activeTransits.length > 0) {
                    routeInfoForPopup += `
                        <div class="pt-2 ${truck.delivery_info ? '' : 'border-t border-gray-200'}">
                            <p><strong>Transit Aktif:</strong></p>
                            ${activeTransits.map(transit => `
                                <div class="text-xs mb-1">
                                    • ${transit.transit_point.loading_city?.name || '...'} → ${transit.transit_point.unloading_city?.name || '...'} (${transit.transit_point.cargo_type || '...'})
                                    ${transit.is_accepted ? ' <span style="color:green;">✓</span>' : ' <span style="color:orange;">⏳</span>'}
                                </div>
                            `).join('')}
                        </div>`;
                }
            }

            const popupContent = `
                <div class="truck-popup p-1 max-w-xs">
                    <h3 class="font-bold text-gray-800 mb-2">${truck.driver_name}</h3>
                    <div class="space-y-1 text-sm">
                        <p><strong>Plat:</strong> ${truck.plate_number}</p>
                        <p><strong>Status:</strong> <span class="font-medium" style="color: ${iconColor}">${status}</span></p>
                        <p><strong>Kecepatan:</strong> ${truck.speed ?? 'N/A'} km/h</p>
                        <p><strong>Update:</strong> ${formatTime(truck.lastUpdateTime)}</p>
                        <p><strong>Posisi:</strong> ${truck.latitude.toFixed(5)}, ${truck.longitude.toFixed(5)}</p>
                        ${routeInfoForPopup}
                    </div>
                </div>
            `;
            // --- Akhir Perbaikan 2 ---

            marker.bindPopup(popupContent);
            truckMarkers[truck.id] = marker;
            marker.on('click', () => selectTruck(truck.id));
        });

        if (Object.keys(truckMarkers).length > 0 && !selectedTruckId) {
            fitAllTrucks();
        }
    }

    // Fungsi untuk menampilkan detail transit
    function showTransitDetails(truckId) {
        const truck = truckData[truckId];
        if (!truck || !truck.transits || truck.transits.length === 0) return;
        
        const modal = document.getElementById('transitModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalContent = document.getElementById('modalContent');
        
        modalTitle.textContent = `Detail Transit - ${truck.driver_name} (${truck.plate_number})`;
        
        let content = `
            <div class="mb-6">
                <h4 class="text-lg font-medium text-gray-800 mb-3">Informasi Truck</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div><strong>Driver:</strong> ${truck.driver_name}</div>
                    <div><strong>Plat Nomor:</strong> ${truck.plate_number}</div>
                    <div><strong>Model:</strong> ${truck.model || 'N/A'}</div>
                    <div><strong>Status:</strong> <span class="px-2 py-1 rounded text-xs ${getStatusColor(truck)}">Online</span></div>
                </div>
            </div>
            
            <div class="mb-6">
                <h4 class="text-lg font-medium text-gray-800 mb-3">Daftar Transit</h4>
                <div class="space-y-4">
        `;
        
        truck.transits.forEach((transit, index) => {
            const tp = transit.transit_point || {};
            const loadingCity = tp.loading_city || {};
            const unloadingCity = tp.unloading_city || {};
            
            const statusBadge = transit.is_accepted ? '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">✓ Diterima</span>' : '<span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full">⏳ Pending</span>';
            const arrivedAt = transit.arrived_at ? `<div class="text-sm text-gray-600 mt-2"><strong>Tiba:</strong> ${formatDateTime(transit.arrived_at * 1000)}</div>` : '';
            const actionedAt = transit.actioned_at ? `<div class="text-sm text-gray-600"><strong>Diproses:</strong> ${formatDateTime(transit.actioned_at * 1000)}</div>` : '';
            
            content += `
                <div class="border rounded-lg p-4 ${tp.is_active ? 'border-blue-300 bg-blue-50' : 'border-gray-200'}">
                    <div class="flex justify-between items-start mb-3">
                        <h5 class="font-medium text-gray-800">Transit ${index + 1}</h5>
                        <div class="flex space-x-2">
                            ${tp.is_active ? '<span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">Aktif</span>' : ''}
                            ${statusBadge}
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <div>
                            <p class="font-medium text-gray-700">Kota Muat</p>
                            <p class="font-semibold">${loadingCity.name || 'Unknown'}</p>
                        </div>
                        <div>
                            <p class="font-medium text-gray-700">Kota Bongkar</p>
                            <p class="font-semibold">${unloadingCity.name || 'Unknown'}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        ${tp.cargo_type ? `<div><strong>Jenis Muatan:</strong> ${tp.cargo_type}</div>` : ''}
                        ${tp.estimated_duration_minute ? `<div><strong>Est. Durasi:</strong> ${tp.estimated_duration_minute} menit</div>` : ''}
                    </div>
                    
                    ${arrivedAt}
                    ${actionedAt}
                </div>
            `;
        });
        
        content += `</div></div>`;
        
        modalContent.innerHTML = content;
        modal.classList.remove('hidden');
    }
    
    function closeTransitModal() { document.getElementById('transitModal').classList.add('hidden'); }
    
    function getStatusColor(truck) {
        const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
        if (timeDiff >= 900000) return 'bg-red-100 text-red-800';
        if (timeDiff >= 300000) return 'bg-yellow-100 text-yellow-800';
        return 'bg-green-100 text-green-800';
    }
    
    function formatDateTime(dateString) {
        if (!dateString) return 'N/A';
        const date = new Date(dateString);
        return date.toLocaleString('id-ID', { year: 'numeric', month: '2-digit', day: '2-digit', hour: '2-digit', minute: '2-digit' });
    }
    
    function selectTruck(truckId) {
        selectedTruckId = truckId;
        updateTruckList();
        updateMap();

        if (truckMarkers[truckId]) {
            const truck = truckData[truckId];
            map.setView([truck.latitude, truck.longitude], 15);
            truckMarkers[truckId].openPopup();
        }
    }

    function refreshTrucks() {
        updateStatus('connecting', 'Memperbarui...');
        loadTrucks();
    }

    function fitAllTrucks() {
        if (Object.keys(truckMarkers).length > 0) {
            const group = new L.featureGroup(Object.values(truckMarkers));
            map.fitBounds(group.getBounds().pad(0.1));
        }
    }

    function updateStatistics() {
        const trucks = Object.values(truckData);
        const onlineTrucks = trucks.filter(t => (Date.now() - t.lastUpdateTime.getTime()) < 900000);
        const offlineTrucks = trucks.length - onlineTrucks.length;

        document.getElementById('onlineTrucks').textContent = onlineTrucks.length;
        document.getElementById('offlineTrucks').textContent = offlineTrucks;
        document.getElementById('totalTrucks').textContent = trucks.length;
    }

    function updateStatus(status, text) {
        const indicator = document.getElementById('statusIndicator');
        const statusText = document.getElementById('statusText');
        statusText.textContent = text;
        indicator.className = 'w-3 h-3 rounded-full';
        switch (status) {
            case 'connected': indicator.className += ' bg-green-500'; break;
            case 'connecting': indicator.className += ' bg-yellow-500 animate-pulse'; break;
            case 'error': indicator.className += ' bg-red-500'; break;
            default: indicator.className += ' bg-gray-400';
        }
    }

    function showLoading(show) {
        const loadingState = document.getElementById('loadingState');
        const truckList = document.getElementById('truckList');
        if (show) {
            loadingState.classList.remove('hidden');
            truckList.classList.add('hidden');
        } else {
            loadingState.classList.add('hidden');
        }
    }

    function showEmptyState() {
        document.getElementById('loadingState').classList.add('hidden');
        document.getElementById('truckList').classList.add('hidden');
        document.getElementById('emptyState').classList.remove('hidden');
    }

    function formatTime(date) {
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.round(diffMs / 60000);
        if (diffMins < 1) return 'Baru saja';
        if (diffMins < 60) return `${diffMins} menit lalu`;
        const diffHours = Math.round(diffMins / 60);
        if (diffHours < 24) return `${diffHours} jam lalu`;
        return date.toLocaleDateString('id-ID');
    }

    document.getElementById('transitModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeTransitModal();
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        initMap();
    });

    window.addEventListener('beforeunload', function() {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });
</script>
@endpush