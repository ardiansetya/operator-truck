@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Tambah Kota</h1>

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
            {{ $errors->first('message') }}
        </div>
    @endif

    <form method="POST" action="{{ route('cities.store') }}" class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-600">Nama Kota</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
            @error('name')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        </div>
        <div class="flex space-x-3">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Simpan</button>
            <a href="{{ route('cities.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Batal</a>
        </div>
    </form>
</div>
@endsection