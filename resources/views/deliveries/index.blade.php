@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Pengiriman</h1>
        <a href="{{ route('deliveries.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Tambah Pengiriman
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">No</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Plat Nomor</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Asal</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Tujuan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Pekerja</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Harga (Rp)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Jarak (km)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Durasi (jam)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($deliveries as $index => $delivery)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['truckLicensePlate'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['startCityName'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['endCityName'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['worker']['name'] ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ number_format($delivery['basePrice'], 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['distanceKm'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $delivery['estimatedDurationHours'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $delivery['isActive'] ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ $delivery['isActive'] ? 'Aktif' : 'Selesai' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                            <a href="{{ route('deliveries.show', $delivery['id']) }}" class="bg-blue-500 text-white px-4 py-1.5 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Detail</a>
                            @if ($delivery['isActive'])
                                <form method="POST" action="{{ route('deliveries.finish', $delivery['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menyelesaikan pengiriman ini?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-500 text-white px-4 py-1.5 rounded-lg hover:bg-green-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Selesai</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('deliveries.destroy', $delivery['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengiriman ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="px-6 py-4 text-sm text-gray-700 text-center">Tidak ada data pengiriman</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection