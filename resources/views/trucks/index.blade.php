@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Truk</h1>

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

    <div class="mb-6">
        <a href="{{ route('trucks.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Tambah Truk
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">No</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Plat Nomor</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Model</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Tipe Kargo</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kapasitas (kg)</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Ketersediaan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($trucks as $index => $truck)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $truck['license_plate'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $truck['model'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $truck['cargo_type'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $truck['capacity_kg'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $truck['is_available'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $truck['is_available'] ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                            <a href="{{ route('trucks.edit', $truck['id']) }}" class="bg-yellow-500 text-white px-4 py-1.5 rounded-lg hover:bg-yellow-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Edit</a>
                            <form method="POST" action="{{ route('trucks.destroy', $truck['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus truk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-sm text-gray-700 text-center">Tidak ada data truk</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection