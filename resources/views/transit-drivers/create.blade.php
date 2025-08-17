@extends('layouts.dashboard')

@section('title', 'Tambah Transit Driver Baru')

@section('content')
<div class="min-h-screen py-8">
    <div class="container mx-auto px-6 max-w-2xl">
        <!-- Header Section -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-green-500 text-white rounded-full mb-4 shadow-lg">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Tambah Transit Driver Baru</h1>
            <p class="text-gray-600">Lengkapi informasi transit driver untuk menambahkan data ke dalam sistem</p>
        </div>

        <!-- Error Message -->
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 font-medium">{{ $errors->first('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-700 font-medium">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Main Form -->
        <div class="bg-white rounded-2xl shadow-xl border-0 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-green-600 px-8 py-6">
                <h2 class="text-xl font-semibold text-white flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Informasi Transit Driver
                </h2>
            </div>

            <form method="POST" action="{{ route('transit-drivers.store') }}" class="p-8">
                @csrf
                <div class="grid grid-cols-1 gap-6">
                    <!-- Delivery -->
                    <div>
                        <label for="delivery_id" class="block text-sm font-semibold text-gray-700 mb-2">Pengiriman</label>
                        <select name="delivery_id" id="delivery_id" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-green-500 focus:ring-0 bg-gray-50 focus:bg-white" required>
                            <option value="">Pilih Pengiriman</option>
                            @foreach ($deliveries as $delivery)
                                <option value="{{ $delivery['id'] }}" {{ old('delivery_id') == $delivery['id'] ? 'selected' : '' }}>
                                   {{ $delivery['truck']['license_plate'] ?? 'N/A' }} 
                                    @if(isset($delivery['route']))
                                        ({{ $delivery['route']['start_city_name'] ?? 'N/A' }} - {{ $delivery['route']['end_city_name'] ?? 'N/A' }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('delivery_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">Pilih pengiriman yang membutuhkan transit</p>
                    </div>

                    <!-- Transit Point -->
                    <div>
                        <label for="transit_point_id" class="block text-sm font-semibold text-gray-700 mb-2">Transit Point</label>
                        <select name="transit_point_id" id="transit_point_id" class="w-full px-4 py-3 rounded-lg border-2 border-gray-200 focus:border-green-500 focus:ring-0 bg-gray-50 focus:bg-white" required disabled>
                            <option value="">Pilih pengiriman terlebih dahulu</option>
                        </select>
                        @error('transit_point_id')
                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1" id="transit_point_help">Pilih pengiriman untuk melihat transit point yang tersedia</p>
                        
                        <!-- Loading indicator -->
                        <div id="transit_point_loading" class="hidden mt-2">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Memuat transit points...
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 mt-8 pt-6 border-t border-gray-100">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-green-600 hover:to-green-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Transit Driver
                    </button>
                    <a href="{{ route('transit-drivers.index') }}" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 text-white font-semibold px-6 py-3 rounded-lg hover:from-gray-600 hover:to-gray-700 transform hover:-translate-y-0.5 transition-all duration-200 ease-in-out shadow-lg hover:shadow-xl flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Batal
                    </a>
                </div>
            </form>
        </div>

        <!-- Help Text -->
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-500">
                Pastikan pengiriman dan kota transit sudah benar sebelum menyimpan
            </p>
        </div>

        <!-- Info Card -->
        {{-- <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Informasi Transit Driver
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Transit driver adalah pengemudi yang menangani perpindahan barang di titik transit</li>
                            <li>Setiap pengiriman dapat memiliki beberapa transit point</li>
                            <li>Transit driver akan menerima atau menolak permintaan transit</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>

<script>
// Dynamic filtering untuk transit points berdasarkan delivery selection
document.getElementById('delivery_id').addEventListener('change', function() {
    const deliveryId = this.value;
    const transitPointSelect = document.getElementById('transit_point_id');
    const loadingIndicator = document.getElementById('transit_point_loading');
    const helpText = document.getElementById('transit_point_help');
    
    // Reset transit point dropdown
    transitPointSelect.innerHTML = '<option value="">Loading...</option>';
    transitPointSelect.disabled = true;
    loadingIndicator.classList.remove('hidden');
    
    if (deliveryId) {
        // Fetch transit points untuk delivery yang dipilih
        fetch(`/api/transit-points-by-delivery?delivery_id=${deliveryId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Clear existing options
            transitPointSelect.innerHTML = '<option value="">Pilih Kota Transit</option>';
            
            if (data.data && data.data.length > 0) {
                // Populate dengan filtered transit points
                data.data.forEach(point => {
                    const option = document.createElement('option');
                    option.value = point.id;
                    
                    let optionText = `${point.loading_city?.name || 'N/A'} - ${point.unloading_city?.name || 'N/A'}`;
                    
                    if (point.cargo_type) {
                        optionText += ` (${point.cargo_type})`;
                    }
                    
                    if (point.extra_cost) {
                        const formattedCost = new Intl.NumberFormat('id-ID').format(point.extra_cost);
                        optionText += ` - Rp ${formattedCost}`;
                    }
                    
                    option.textContent = optionText;
                    transitPointSelect.appendChild(option);
                });
                
                transitPointSelect.disabled = false;
                helpText.textContent = `Tersedia ${data.data.length} transit point dari kota: ${data.current_city || 'N/A'}`;
                helpText.className = 'text-sm text-green-600 mt-1';
                
                if (data.accepted_transits_count > 0) {
                    helpText.textContent += ` (Transit ke-${data.accepted_transits_count + 1})`;
                }
            } else {
                transitPointSelect.innerHTML = '<option value="">Tidak ada transit point tersedia</option>';
                helpText.textContent = `Tidak ada transit point yang tersedia dari kota: ${data.current_city || 'N/A'}`;
                helpText.className = 'text-sm text-yellow-600 mt-1';
            }
        })
        .catch(error => {
            console.error('Error fetching transit points:', error);
            transitPointSelect.innerHTML = '<option value="">Error memuat data</option>';
            helpText.textContent = 'Terjadi kesalahan saat memuat transit points';
            helpText.className = 'text-sm text-red-600 mt-1';
        })
        .finally(() => {
            loadingIndicator.classList.add('hidden');
        });
    } else {
        // Reset jika tidak ada delivery yang dipilih
        transitPointSelect.innerHTML = '<option value="">Pilih pengiriman terlebih dahulu</option>';
        transitPointSelect.disabled = true;
        loadingIndicator.classList.add('hidden');
        helpText.textContent = 'Pilih pengiriman untuk melihat transit point yang tersedia';
        helpText.className = 'text-sm text-gray-500 mt-1';
    }
});

// Restore selected values jika ada old input
document.addEventListener('DOMContentLoaded', function() {
    const deliverySelect = document.getElementById('delivery_id');
    const oldDeliveryId = '{{ old("delivery_id") }}';
    
    if (oldDeliveryId) {
        deliverySelect.value = oldDeliveryId;
        deliverySelect.dispatchEvent(new Event('change'));
        
        // Restore transit point selection setelah data dimuat
        setTimeout(() => {
            const transitPointSelect = document.getElementById('transit_point_id');
            const oldTransitPointId = '{{ old("transit_point_id") }}';
            
            if (oldTransitPointId) {
                transitPointSelect.value = oldTransitPointId;
            }
        }, 1000);
    }
});
</script>
@endsection