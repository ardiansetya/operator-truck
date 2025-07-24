@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Detail Transit Point</h1>
        <a href="{{ route('transit-points.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
            Kembali
        </a>
    </div>

    @if (isset($error))
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $error }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm p-6">
        <dl class="space-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">ID</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $transitPoint['id'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kota Pemuatan</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @php
                        $loadingCity = collect($cities)->firstWhere('id', $transitPoint['loading_city_name']);
                    @endphp
                    {{ $loadingCity['name'] ?? 'Unknown City' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Kota Pembongkaran</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    @php
                        $unloadingCity = collect($cities)->firstWhere('id', $transitPoint['unloading_city_name']);
                    @endphp
                    {{ $unloadingCity['name'] ?? 'Unknown City' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Estimasi Durasi (Menit)</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $transitPoint['estimated_duration_minute'] }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Biaya Ekstra</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ number_format($transitPoint['extra_cost'], 2) }}</dd>
            </div>
        </dl>
        <div class="mt-6 flex space-x-4">
            <a href="{{ route('transit-points.edit', $transitPoint['id']) }}" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Edit</a>
            <form action="{{ route('transit-points.destroy', $transitPoint['id']) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transit point ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Hapus</button>
            </form>
        </div>
    </div>
</div>
@endsection