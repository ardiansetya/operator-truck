@extends('layouts.dashboard')
@section('title', 'Live Tracking Truck')

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
            <div class="flex flex-col lg:flex-row h-screen max-h-[600px]">
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
                <div class="flex-1 relative">
                    <!-- Map Controls -->
                    <div class="absolute top-4 right-4 z-10 flex flex-col space-y-2">
                        <button onclick="fitAllTrucks()" 
                            class="bg-white text-gray-700 p-2 rounded-lg shadow-md hover:shadow-lg transition-shadow border border-gray-200 hover:bg-gray-50">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5v-4m0 4h-4m4 0l-5-5"></path>
                            </svg>
                        </button>
                        <button onclick="refreshTrucks()" 
                            class="bg-blue-500 text-white p-2 rounded-lg shadow-md hover:shadow-lg transition-shadow hover:bg-blue-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>

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
                    <div id="map" class="h-full w-full bg-gray-100">
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center">
                                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mb-4"></div>
                                <p class="text-gray-600">Memuat peta...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Legend -->
        <div class="mt-6 bg-white rounded-xl shadow-lg border border-gray-100 p-6">
            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Keterangan Status
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                    <div class="w-4 h-4 rounded-full bg-green-500"></div>
                    <div>
                        <p class="font-medium text-green-800">Online</p>
                        <p class="text-sm text-green-600">Update < 5 menit</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                    <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                    <div>
                        <p class="font-medium text-yellow-800">Delayed</p>
                        <p class="text-sm text-yellow-600">Update 5-15 menit</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-red-50 rounded-lg">
                    <div class="w-4 h-4 rounded-full bg-red-500"></div>
                    <div>
                        <p class="font-medium text-red-800">Offline</p>
                        <p class="text-sm text-red-600">Update > 15 menit</p>
                    </div>
                </div>
                <div class="flex items-center space-x-3 p-3 bg-blue-50 rounded-lg">
                    <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                    <div>
                        <p class="font-medium text-blue-800">Selected</p>
                        <p class="text-sm text-blue-600">Truck dipilih</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Leaflet CSS dan JS -->
   <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    <script>
        // Configuration - sesuaikan dengan backend Spring Boot Anda
        const CONFIG = {
            // Ganti dengan API key Geoapify Anda
            GEOAPIFY_API_KEY: 'YOUR_GEOAPIFY_API_KEY',
            // URL backend Spring Boot Anda
            BACKEND_URL: '{{ config("service.java.backend_url", "http://localhost:8080/api") }}',
            // Interval update posisi (ms)
            UPDATE_INTERVAL: 15000, // 15 detik
            // Delivery ID jika ada (untuk tracking spesifik)
            DELIVERY_ID: {{ request('delivery_id', 'null') }}
        };

        let map;
        let truckMarkers = {};
        let truckData = {};
        let selectedTruckId = null;
        let updateInterval;

        // Initialize map
        function initMap() {
            // Menggunakan Geoapify tiles
            map = L.map('map').setView([-6.2088, 106.8456], 10); // Jakarta sebagai center default

            // Geoapify tile layer
            L.tileLayer(`https://maps.geoapify.com/v1/tile/osm-bright/{z}/{x}/{y}.png?apiKey=${CONFIG.GEOAPIFY_API_KEY}`, {
                maxZoom: 18,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            // Load initial truck data
            loadTrucks();
            
            // Set up auto-refresh
            updateInterval = setInterval(refreshTrucks, CONFIG.UPDATE_INTERVAL);
            
            // Update status
            updateStatus('connected', 'Terhubung');
        }

        // Load trucks from backend
        async function loadTrucks() {
            showLoading(true);
            try {
                let url = `${CONFIG.BACKEND_URL}/trucks/positions`;
                if (CONFIG.DELIVERY_ID) {
                    url += `?delivery_id=${CONFIG.DELIVERY_ID}`;
                }
                
                // API call ke Spring Boot backend Anda
                const response = await fetch(url);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const trucks = await response.json();
                
                updateTruckData(trucks);
                updateTruckList();
                updateMap();
                updateStatistics();
                
            } catch (error) {
                console.error('Error loading trucks:', error);
                updateStatus('error', 'Error koneksi');
                // Fallback ke data dummy untuk demo
                loadDummyData();
            }
            showLoading(false);
        }

        // Load dummy data untuk demo
        function loadDummyData() {
            const dummyTrucks = [
                {
                    id: 'TRK001',
                    driverName: 'Budi Santoso',
                    plateNumber: 'B 1234 XY',
                    latitude: -6.2088,
                    longitude: 106.8456,
                    lastUpdate: new Date().toISOString(),
                    status: 'online',
                    speed: 45,
                    deliveryInfo: {
                        startCity: 'Jakarta',
                        endCity: 'Bandung',
                        cargoType: 'Elektronik'
                    }
                },
                {
                    id: 'TRK002',
                    driverName: 'Ahmad Wijaya',
                    plateNumber: 'D 5678 AB',
                    latitude: -6.1744,
                    longitude: 106.8227,
                    lastUpdate: new Date(Date.now() - 300000).toISOString(), // 5 menit yang lalu
                    status: 'online',
                    speed: 60,
                    deliveryInfo: {
                        startCity: 'Surabaya',
                        endCity: 'Malang',
                        cargoType: 'Fashion'
                    }
                },
                {
                    id: 'TRK003',
                    driverName: 'Siti Nurhaliza',
                    plateNumber: 'B 9012 CD',
                    latitude: -6.2293,
                    longitude: 106.8330,
                    lastUpdate: new Date(Date.now() - 1800000).toISOString(), // 30 menit yang lalu
                    status: 'offline',
                    speed: 0,
                    deliveryInfo: {
                        startCity: 'Yogyakarta',
                        endCity: 'Solo',
                        cargoType: 'Makanan'
                    }
                }
            ];
            
            updateTruckData(dummyTrucks);
            updateTruckList();
            updateMap();
            updateStatistics();
            updateStatus('connected', 'Terhubung (Demo)');
        }

        // Update truck data
        function updateTruckData(trucks) {
            truckData = {};
            trucks.forEach(truck => {
                truckData[truck.id] = {
                    ...truck,
                    lastUpdateTime: new Date(truck.lastUpdate)
                };
            });
        }

        // Update statistics
        function updateStatistics() {
            const trucks = Object.values(truckData);
            const onlineTrucks = trucks.filter(truck => {
                const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
                return timeDiff < 300000; // 5 menit
            });
            const offlineTrucks = trucks.filter(truck => {
                const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
                return timeDiff >= 300000;
            });

            document.getElementById('onlineTrucks').textContent = onlineTrucks.length;
            document.getElementById('offlineTrucks').textContent = offlineTrucks.length;
            document.getElementById('totalTrucks').textContent = trucks.length;
        }

        // Update truck list in sidebar
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
                let status = 'online';
                let statusColor = 'green';
                let statusText = 'Online';

                if (timeDiff >= 900000) { // 15 menit
                    status = 'offline';
                    statusColor = 'red';
                    statusText = 'Offline';
                } else if (timeDiff >= 300000) { // 5 menit
                    status = 'delayed';
                    statusColor = 'yellow';
                    statusText = 'Delayed';
                }

                const truckItem = document.createElement('div');
                truckItem.className = `truck-item p-4 bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow cursor-pointer ${selectedTruckId === truck.id ? 'ring-2 ring-blue-500 bg-blue-50' : ''}`;
                truckItem.onclick = () => selectTruck(truck.id);

                truckItem.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                🚛
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">${truck.driverName}</h4>
                                <p class="text-sm text-gray-600">${truck.plateNumber}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-3 h-3 rounded-full bg-${statusColor}-500"></div>
                            <span class="text-xs font-medium text-${statusColor}-700">${statusText}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Kecepatan:</span>
                            <span class="font-medium">${truck.speed} km/h</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Update terakhir:</span>
                            <span class="font-medium">${formatTime(truck.lastUpdateTime)}</span>
                        </div>
                        ${truck.deliveryInfo ? `
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    <span>${truck.deliveryInfo.startCity}</span>
                                </div>
                                <div class="w-8 border-b border-dashed border-gray-300"></div>
                                <div class="flex items-center">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>
                                    <span>${truck.deliveryInfo.endCity}</span>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Muatan: ${truck.deliveryInfo.cargoType}</p>
                        </div>
                        ` : ''}
                    </div>
                `;

                truckList.appendChild(truckItem);
            });
        }

        // Update map with truck positions
        function updateMap() {
            // Clear existing markers
            Object.values(truckMarkers).forEach(marker => {
                map.removeLayer(marker);
            });
            truckMarkers = {};

            // Add new markers
            Object.values(truckData).forEach(truck => {
                const timeDiff = Date.now() - truck.lastUpdateTime.getTime();
                let iconColor = '#4CAF50'; // green - online
                let status = 'Online';

                if (timeDiff >= 900000) { // 15 menit
                    iconColor = '#f44336'; // red - offline
                    status = 'Offline';
                } else if (timeDiff >= 300000) { // 5 menit
                    iconColor = '#ff9800'; // yellow - delayed
                    status = 'Delayed';
                }

                // Custom icon berdasarkan status
                const iconHtml = `
                    <div style="
                        background-color: ${iconColor};
                        width: 28px;
                        height: 28px;
                        border-radius: 50%;
                        border: 3px solid white;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: white;
                        font-size: 14px;
                        ${selectedTruckId === truck.id ? 'transform: scale(1.2); border-color: #2196F3; border-width: 4px;' : ''}
                    ">🚛</div>
                `;

                const customIcon = L.divIcon({
                    html: iconHtml,
                    className: 'custom-truck-icon',
                    iconSize: [34, 34],
                    iconAnchor: [17, 17]
                });

                const marker = L.marker([truck.latitude, truck.longitude], {
                    icon: customIcon
                }).addTo(map);

                // Popup info
                const popupContent = `
                    <div class="truck-popup p-2">
                        <h3 class="font-bold text-gray-800 mb-2">${truck.driverName}</h3>
                        <div class="space-y-1 text-sm">
                            <p><strong>Plat:</strong> ${truck.plateNumber}</p>
                            <p><strong>Kecepatan:</strong> ${truck.speed} km/h</p>
                            <p><strong>Status:</strong> <span class="font-medium" style="color: ${iconColor}">${status}</span></p>
                            <p><strong>Update:</strong> ${formatTime(truck.lastUpdateTime)}</p>
                            <p><strong>Posisi:</strong> ${truck.latitude.toFixed(6)}, ${truck.longitude.toFixed(6)}</p>
                            ${truck.deliveryInfo ? `
                            <div class="pt-2 border-t border-gray-200">
                                <p><strong>Rute:</strong> ${truck.deliveryInfo.startCity} → ${truck.deliveryInfo.endCity}</p>
                                <p><strong>Muatan:</strong> ${truck.deliveryInfo.cargoType}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent);
                
                truckMarkers[truck.id] = marker;

                // Click event
                marker.on('click', () => {
                    selectTruck(truck.id);
                });
            });
        }

        // Select truck
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

        // Refresh trucks
        function refreshTrucks() {
            updateStatus('connecting', 'Memperbarui...');
            loadTrucks();
        }

        // Fit all trucks in view
        function fitAllTrucks() {
            const positions = Object.values(truckData).map(truck => [truck.latitude, truck.longitude]);
            
            if (positions.length > 0) {
                const group = new L.featureGroup(Object.values(truckMarkers));
                map.fitBounds(group.getBounds().pad(0.1));
            }
        }

        // Show/hide loading
        function showLoading(show) {
            const loadingState = document.getElementById('loadingState');
            const truckList = document.getElementById('truckList');
            
            if (show) {
                loadingState.classList.remove('hidden');
                truckList.classList.add('hidden');
            }
        }

        // Update status indicator
        function updateStatus(status, text) {
            const indicator = document.getElementById('statusIndicator');
            const statusText = document.getElementById('statusText');
            
            statusText.textContent = text;
            
            indicator.className = 'w-3 h-3 rounded-full';
            switch (status) {
                case 'connected':
                    indicator.className += ' bg-green-500';
                    break;
                case 'connecting':
                    indicator.className += ' bg-yellow-500 animate-pulse';
                    break;
                case 'error':
                    indicator.className += ' bg-red-500';
                    break;
                default:
                    indicator.className += ' bg-gray-400';
            }
        }

        // Format time
        function formatTime(date) {
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);

            if (diffMins < 1) return 'Sekarang';
            if (diffMins < 60) return `${diffMins} menit lalu`;
            if (diffMins < 1440) return `${Math.floor(diffMins / 60)} jam lalu`;
            return date.toLocaleDateString('id-ID') + ' ' + date.toLocaleTimeString('id-ID');
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });

        // Cleanup interval on page unload
        window.addEventListener('beforeunload', function() {
            if (updateInterval) {
                clearInterval(updateInterval);
            }
        });
    </script>
@endsection