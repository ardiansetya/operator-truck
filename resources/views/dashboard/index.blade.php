@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 transition-all duration-300 hover:shadow-md flex items-center space-x-4">
            <div class="flex-shrink-0">
                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01m-.01 4h.01"></path>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-medium text-gray-700">Transit Belum Dikonfirmasi</h2>
                <p class="text-3xl font-semibold text-gray-800">{{ $unconfirmedTransits ?? 0 }}</p>
                <p class="text-sm text-gray-500">Menunggu persetujuan</p>
            </div>
        </div>
    </div>
</div>
@endsection