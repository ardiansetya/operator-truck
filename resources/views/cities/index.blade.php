@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Kota</h1>

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
        <a href="{{ route('cities.create') }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Tambah Kota
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">No</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Nama Kota</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Latitude</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Longitude</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Negara</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($cities as $index => $city)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $city['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $city['latitude'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $city['longitude'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">{{ $city['country'] }}</td>
                        <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                            <a href="{{ route('cities.edit', $city['id']) }}" class="bg-yellow-500 text-white px-4 py-1.5 rounded-lg hover:bg-yellow-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Edit</a>
                            <form method="POST" action="{{ route('cities.destroy', $city['id']) }}" class="inline" onsubmit="return confirm('Yakin ingin menghapus kota ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-sm text-gray-700 text-center">Tidak ada data kota</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection