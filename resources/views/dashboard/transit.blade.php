@extends('layouts.dashboard')
@section('content')
<div class="container mx-auto px-6 py-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Daftar Driver Transit</h1>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-100 rounded-xl shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">No</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Plat Nomor</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Driver</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Transit</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Kota Tujuan</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Tarif</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Status</th>
                    <th class="text-left px-6 py-4 text-sm font-medium text-gray-600">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">1</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">B 1234 AB</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">Yanto</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">Kramajati</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">Dieng</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">300.000</td>
                    <td class="px-6 py-4 text-sm text-gray-700 border-b border-gray-100">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Menunggu
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm border-b border-gray-100 space-x-3">
                        <form method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-green-500 text-white px-4 py-1.5 rounded-lg hover:bg-green-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Acc</button>
                        </form>
                        <form method="POST" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600 transition duration-200 ease-in-out transform hover:-translate-y-0.5">Tolak</button>
                        </form>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection