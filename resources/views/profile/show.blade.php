@extends('layouts.dashboard')
@section('title', 'Profil Pengguna')
@section('content')
<div class="min-h-screen  py-8">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <!-- Header Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Profil Admin </h1>
                    <p class="text-gray-600">Kelola informasi akun dan keamanan Anda</p>
                </div>
                {{-- <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-semibold text-lg shadow-lg">
                        {{ substr($profile['username'] ?? 'U', 0, 1) }}
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">{{ $profile['username'] ?? 'Unknown User' }}</p>
                        <p class="text-sm text-gray-500">{{ $profile['role'] ?? 'User' }}</p>
                    </div>
                </div> --}}
            </div>
        </div>

        <!-- Alert Messages -->
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-800 font-medium">{{ $errors->first('message') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($error))
            <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-red-800 font-medium">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (isset($profile))
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Information Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-8 text-center">
                            <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center text-white font-bold text-2xl mx-auto mb-4 backdrop-blur-sm">
                                {{ substr($profile['username'] ?? 'U', 0, 1) }}
                            </div>
                            <h2 class="text-xl font-semibold text-white mb-1">{{ $profile['username'] ?? 'Unknown' }}</h2>
                            <p class="text-blue-100">{{ $profile['email'] ?? 'No email provided' }}</p>
                            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white/20 text-white backdrop-blur-sm">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                                {{ $profile['role'] ?? 'User' }}
                            </div>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Nomor Telepon</p>
                                    <p class="font-medium text-gray-900">{{ $profile['phone_number'] ?? 'Belum diisi' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4h6m-6 0l-2 8h10l-2-8m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v2"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Umur</p>
                                    <p class="font-medium text-gray-900">{{ $profile['age'] ?? 'Belum diisi' }} tahun</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="mt-6 bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Akun</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Status Akun</span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1"></span>
                                    Aktif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Forms Section -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Update Profile Form -->
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900">Perbarui Profil</h2>
                                    <p class="text-sm text-gray-600">Ubah informasi pribadi Anda</p>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                                    <div class="relative">
                                        <input type="text" name="username" id="username" value="{{ old('username', $profile['username'] ?? '') }}" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('username')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <div class="relative">
                                        <input type="email" name="email" id="email" value="{{ old('email', $profile['email'] ?? '') }}" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                    <div class="relative">
                                        <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $profile['phone_number'] ?? '') }}" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required>
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('phone_number')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div>
                                    <label for="age" class="block text-sm font-medium text-gray-700 mb-2">Umur</label>
                                    <div class="relative">
                                        <input type="number" name="age" id="age" value="{{ old('age', $profile['age'] ?? '') }}" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200" required min="18">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 4h6m-6 0l-2 8h10l-2-8m-6 0V9a2 2 0 012-2h2a2 2 0 012 2v2"/>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('age')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-100">
                                <a href="{{ route('dashboard.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-200 transform hover:-translate-y-0.5">Kembali</a>
                                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>

                    <!-- Password Reset Form - Fixed nested form issue -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
    <div class="px-6 py-4 bg-red-50 border-b border-red-100">
        <div class="flex items-center">
            <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Keamanan & Kata Sandi</h2>
                <p class="text-sm text-gray-600">Perbarui kata sandi untuk menjaga keamanan akun</p>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('profile.update-password') }}" class="p-6">
        @csrf
        @method('PATCH')
        <div class="space-y-6">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Saat Ini</label>
                <div class="relative">
                    <input type="password" name="current_password" id="current_password" class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" required>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" name="new_password" id="new_password" class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" required>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
                @error('new_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-2 text-xs text-gray-500">Gunakan minimal 8 karakter dengan kombinasi huruf, angka, dan simbol</p>
            </div>
            <div>
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                <div class="relative">
                    <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all duration-200" required>
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-100">
            <a href="{{ route('dashboard.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition-all duration-200 transform hover:-translate-y-0.5">Batal</a>
            <button type="submit" class="px-6 py-3 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl font-medium hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg">Perbarui Kata Sandi</button>
        </div>
    </form>
</div>
                </div>
            </div>
        @else
            <div class="flex flex-col items-center justify-center py-12">
                <div class="max-w-md w-full bg-white rounded-2xl shadow-lg border border-gray-100 p-8 text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Gagal Memuat Profil</h3>
                    <p class="text-gray-600 mb-6">Terjadi kesalahan saat memuat data profil pengguna. Silakan coba lagi nanti.</p>
                    <a href="{{ route('dashboard.index') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-indigo-700 transition-all duration-200 transform hover:-translate-y-0.5 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection