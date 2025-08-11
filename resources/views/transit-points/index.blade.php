@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Transit Points</h1>
        <a href="{{ route('transit-points.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Tambah Transit Point
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
            {{ session('success') }}
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
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Pemuatan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Pembongkaran</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Estimasi Waktu</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Jenis Muatan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Biaya Ekstra</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transitPoints as $transitPoint)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $transitPoint['id'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $transitPoint['loading_city_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $transitPoint['unloading_city_name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $transitPoint['estimated_duration_minute'] }} Menit</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $transitPoint['cargo_type']}}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ number_format($transitPoint['extra_cost'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">
                            @if ($transitPoint['is_active'] == 1)
                                <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"> Aktif</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-redow-100 text-redow-800"> Non-Aktif</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                            <a href="{{ route('transit-points.edit', $transitPoint['id']) }}"  class="bg-yellow-500 text-white px-4 py-1.5 rounded-lg hover:bg-yellow-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Edit</a>
                            <form action="{{ route('transit-points.destroy', $transitPoint['id']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transit point ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 border-b border-gray-100">Tidak ada data transit point</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
