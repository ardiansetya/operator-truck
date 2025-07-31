@extends('layouts.dashboard')
@section('content')
    <div class="container mx-auto px-6 py-8">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Driver Transit</h1>
            <a href="{{ route('transit-points.index') }}"
                class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">
                Daftar Transit Point
            </a>
        </div>
        {{-- ✅ Alert Message Section --}}
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 border border-green-300 rounded-lg shadow-sm">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow-sm">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        @if ($errors->any())
            @foreach ($errors->all() as $error)
                <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow-sm">
                    ⚠️ {{ $error }}
                </div>
            @endforeach
        @endif

        {{-- @if (isset($error))
            <div class="mb-6 p-4 bg-red-100 text-red-700 border border-red-300 rounded-lg shadow-sm">
                ⚠️ {{ $error }}
            </div>
        @endif --}}

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">No</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Plat Nomor</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Model</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Kota Awal</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Kota Tujuan</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Tarif</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Status</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Operator</th>
                        <th class="px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($drivers as $i => $driver)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4">{{ $i + 1 }}</td>
                            <td class="px-6 py-4">{{ $driver['license_plate'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $driver['truck_model'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $driver['route_start'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $driver['route_end'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ number_format($driver['tarif'] ?? 0, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $driver['status'] ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $driver['operator'] ?? '-' }}</td>
                            <td class="px-6 py-4 space-x-2">
                                {{-- Accept --}}
                                <form method="POST" action="{{ route('transit-drivers.accept-or-reject') }}"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="delivery_transit_id" value="{{ $driver['id'] }}">
                                    <input type="hidden" name="is_accepted" value="true">
                                    <input type="hidden" name="reason" value="Diterima oleh operator">
                                    <button type="submit" title="Terima"
                                        class="bg-green-500 text-white px-3 py-1 rounded-lg hover:bg-green-600">
                                        Acc
                                    </button>
                                </form>

                                {{-- Reject --}}
                                <form method="POST" action="{{ route('transit-drivers.accept-or-reject') }}"
                                    class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="delivery_transit_id" value="{{ $driver['id'] }}">
                                    <input type="hidden" name="is_accepted" value="false">
                                    <input type="hidden" name="reason" value="Ditolak oleh operator">
                                    <button type="submit" title="Tolak"
                                        class="bg-red-500 text-white px-3 py-1 rounded-lg hover:bg-red-600">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-gray-500 py-6">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
