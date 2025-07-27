@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Profil Pengguna</h1>

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

    @if (isset($profile))
        <div class="max-w-lg bg-white p-6 rounded-xl shadow-sm mb-8">
            <h2 class="text-xl font-medium text-gray-700 mb-4">Detail Profil</h2>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600">Username</label>
                <p class="mt-1 text-gray-700">{{ $profile['username'] ?? 'Unknown' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600">Email</label>
                <p class="mt-1 text-gray-700">{{ $profile['email'] ?? 'Unknown' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600">Nomor Telepon</label>
                <p class="mt-1 text-gray-700">{{ $profile['phone_number'] ?? 'Unknown' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600">Umur</label>
                <p class="mt-1 text-gray-700">{{ $profile['age'] ?? 'Unknown' }}</p>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-600">Role</label>
                <p class="mt-1 text-gray-700">{{ $profile['role'] ?? 'Unknown' }}</p>
            </div>
        </div>

        <div class="max-w-lg bg-white p-6 rounded-xl shadow-sm mb-8">
            <h2 class="text-xl font-medium text-gray-700 mb-4">Perbarui Profil</h2>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-600">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username', $profile['username'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('username')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-600">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $profile['email'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-600">Nomor Telepon</label>
                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $profile['phone_number'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('phone_number')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="age" class="block text-sm font-medium text-gray-600">Umur</label>
                    <input type="number" name="age" id="age" value="{{ old('age', $profile['age'] ?? '') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required min="18">
                    @error('age')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Simpan</button>
                    <a href="{{ route('dashboard.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Kembali</a>
                </div>
            </form>
        </div>

        <div class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
            <h2 class="text-xl font-medium text-gray-700 mb-4">Reset Kata Sandi</h2>
            <form method="POST" action="{{ route('profile.update-password') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-600">Kata Sandi Saat Ini</label>
                    <input type="text" name="password" id="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('current_password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-600">Kata Sandi Baru</label>
                    <input type="password" name="password" id="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    @error('new_password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label for="new_password_confirmation" class="block text-sm font-medium text-gray-600">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="new_password_confirmation" class="mt-1 block w-full rounded-md border-gray-50" required>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Reset</button>
                    <a href="{{ route('dashboard.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Kembali</a>
                </div>
            </form>
        </div>
    @else
        <div class="max-w-lg bg-white p-6 rounded-xl shadow-sm">
            <p class="text-red-500">Gagal memuat profil pengguna.</p>
            <a href="{{ route('dashboard.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Kembali</a>
        </div>
    @endif
</div>
@endsection