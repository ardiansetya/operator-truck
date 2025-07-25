@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Daftar Rute</h1>
        <a href="{{ route('routes.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Tambah Rute
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    @if (session('errors'))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            @foreach (session('errors')->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
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
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">ID</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Awal</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Tujuan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Harga Dasar</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Jarak (km)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Durasi (Jam)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($routes as $route)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $route['id'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $route['start_city_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $route['end_city_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ number_format($route['base_price'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ number_format($route['distance_km'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ number_format($route['estimated_duration_hours'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $route['is_active'] ? 'Aktif' : 'Non-Aktif' }}</td>
                        <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                            <a href="{{ route('routes.show', $route['id']) }}" class="bg-blue-500 text-white px-4 py-1.5 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Lihat</a>
                            <a href="{{ route('routes.edit', $route['id']) }}" class="bg-yellow-500 text-white px-4 py-1.5 rounded-lg hover:bg-yellow-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Edit</a>
                            <form action="{{ route('routes.destroy', $route['id']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus rute ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500 border-b border-gray-100">Tidak ada data rute</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
