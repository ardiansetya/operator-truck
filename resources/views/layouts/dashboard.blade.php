<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @hasSection('title')
            @yield('title') | Tracking Truck
        @else
            Dashboard | Tracking Truck
        @endif
    </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}">

</head>

<body class="bg-gray-50 text-gray-900 font-['Inter'] antialiased">
    <div class="min-h-screen flex flex-col">
        {{-- header --}}
        <header class="bg-white shadow-sm border-b border-gray-100">
            <div class="container mx-auto px-6 py-4 flex justify-around items-center">
                <div class="flex items-center space-x-3">
                    <span class="text-2xl">🚚</span>
                    <h1 class="text-xl font-semibold text-gray-800">Tracking Truck</h1>
                </div>
                <div class="flex items-center space-x-6">
                    <nav class="flex space-x-4">
                        <a href="{{ route('dashboard.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('dashboard.*') ? 'bg-blue-50 text-blue-600' : '' }}">Dashboard</a>
                        <a href="{{ route('deliveries.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('deliveries.*') ? 'bg-blue-50 text-blue-600' : '' }}">Pengiriman</a>
                        <a href="{{ route('transit-drivers.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('transit-drivers.*') ? 'bg-blue-50 text-blue-600' : '' }}">Transit
                            Drivers</a>


                        {{-- <a href="{{ route('cities.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('cities.*') ? 'bg-blue-50 text-blue-600' : '' }}">Cities</a> --}}
                        <a href="{{ route('trucks.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('trucks.*') ? 'bg-blue-50 text-blue-600' : '' }}">Trucks</a>
                        <a href="{{ route('routes.index') }}"
                            class="text-gray-600 hover:text-blue-600 font-medium text-sm py-2 px-3 rounded-lg transition duration-200 ease-in-out {{ Route::is('routes.*') ? 'bg-blue-50 text-blue-600' : '' }}">Rute</a>

                    </nav>
                </div>
                <div class="flex items-center space-x-4">
                    {{-- Profile Link --}}
                    <a href="{{ route('profile.show') }}"
                        class="flex items-center space-x-3 text-gray-600 hover:text-blue-600 py-3 px-4 rounded-lg transition duration-200 ease-in-out group {{ Route::is('profile.*') ? 'bg-blue-50 text-blue-600 shadow-sm' : 'hover:bg-gray-50' }}"
                        title="Profile">

                        {{-- Profile Icon --}}
                        <div class="flex-shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 transition-colors duration-200"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a8.25 8.25 0 0115 0" />
                            </svg>
                        </div>

                        {{-- User Info --}}
                        @if (isset($currentUser))
                            <div class="flex-1 min-w-0">
                                <p
                                    class="text-sm font-medium truncate group-hover:text-blue-600 transition-colors duration-200">
                                    {{ $currentUser['username'] }}
                                </p>
                                <p class="text-xs text-gray-500 truncate capitalize">
                                    {{ $currentUser['role'] }}
                                </p>
                            </div>
                        @endif

                        {{-- Optional: Arrow indicator for active state --}}
                        <div
                            class="flex-shrink-0 {{ Route::is('profile.*') ? 'opacity-100' : 'opacity-0 group-hover:opacity-100' }} transition-opacity duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </a>
                    <form action="{{ route('logout') }}" method="POST">

                        @csrf
                        <button
                            class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-300 ease-in-out transform hover:-translate-y-0.5">
                            Logout
                        </button>
                    </form>
                </div>
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
