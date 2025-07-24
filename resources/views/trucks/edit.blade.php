@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Edit Truk</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('trucks.update', $truck['id']) }}" class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="license_plate" class="block text-sm font-medium text-gray-600">Plat Nomor</label>
            <input type="text" name="license_plate" id="license_plate" value="{{ old('license_plate', $truck['license_plate']) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('license_plate')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <label for="driver_name" class="block text-sm font-medium text-gray-600">Nama Driver</label>
            <input type="text" name="driver_name" id="driver_name" value="{{ old('driver_name', $truck['driver_name']) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('driver_name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Simpan</button>
            <a href="{{ route('trucks.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Batal</a>
        </div>
    </form>
</div>
@endsection