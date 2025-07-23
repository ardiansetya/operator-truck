@extends('layouts.app')

@section('title', 'Register')

@section('content')
    <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Register</h2>

    @if(session('error'))
        <div class="mb-4 text-sm text-red-600 bg-red-100 p-2 rounded">
            {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700">Username</label>
            <input type="text" name="username" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" name="password" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="text" name="phone_number" required
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400" />
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Age</label>
            <input type="number" name="age" required min="18" max="65"
                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200 focus:border-indigo-400" />
        </div>

        <button type="submit"
            class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded hover:bg-green-700 transition duration-200">
            Register
        </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Login di sini</a>
    </p>
@endsection
