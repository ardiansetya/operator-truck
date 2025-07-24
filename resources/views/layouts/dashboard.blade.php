<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-900 font-['Inter'] antialiased">
    <div class="min-h-screen flex flex-col">
        {{-- header --}}
        <header class="bg-white shadow-sm border-b border-gray-100">
        <div class="container mx-auto px-6 py-4 flex justify-around items-center">
            <div class="flex items-center space-x-3">
                <span class="text-2xl">🚚</span>
                <h1 class="text-xl font-semibold text-gray-800">Dashboard</h1>
            </div>
            <div class="flex items-center space-x-6">
                <nav class="flex space-x-4">
                    <a href="{{ route('cities.index') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('cities.*') ? 'bg-blue-50 text-blue-600' : '' }}">Cities</a>
                    <a href="{{ route('trucks.index') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('trucks.*') ? 'bg-blue-50 text-blue-600' : '' }}">Trucks</a>
                    <a href="{{ route('deliveries.index') }}" class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('deliveries.*') ? 'bg-blue-50 text-blue-600' : '' }}">Deliveries</a>
                </nav>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                    Logout
                </button>
            </form>
        </div>
    </header>

        <!-- Content -->
        <main class="flex-grow container mx-auto px-6 py-8">
            <div class="bg-white rounded-xl shadow-sm p-8 transition-all duration-300 hover:shadow-md">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-100 text-gray-500 text-center text-sm py-4 mt-8">
            © {{ date('Y') }} <strong>Tracking Truck</strong>
        </footer>
    </div>
</body>
</html>