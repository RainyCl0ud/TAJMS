<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to TAJMS</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="font-sans antialiased bg-blue-100 text-black flex items-center justify-center min-h-screen p-4">
    
    <div class="w-full max-w-3xl px-6 text-center animate-fadeIn">
        
        <!-- Logo -->
        <div class="flex justify-center">
            <img src="{{ asset('images/logo.png') }}" alt="TAJMS Logo" class="w-32 h-32 sm:w-40 sm:h-40 md:w-48 md:h-48 transition-transform duration-500 hover:scale-110">
        </div>

        <!-- Welcome Section -->
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mt-4">
            <span class="text-white stroke-black drop-shadow-[1px_1px_2px_rgba(0,0,0,0.8)]">Welcome to</span> 
            <span class="text-blue-800">TAJMS</span>
        </h1>
        
        <p class="mt-4 text-gray-700 text-base sm:text-lg md:text-xl leading-relaxed animate-slideIn">
            <span class="text-blue-800 font-semibold">TAJMS</span> (Time and Attendance Job Management System) helps you manage work schedules, track attendance, and streamline your workflow efficiently.
        </p>

        <!-- Authentication Buttons -->
        <div class="mt-8 flex flex-col sm:flex-row justify-center gap-4 animate-fadeIn delay-200">
            @if (Route::has('login'))
                @auth
                    <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="px-6 py-3 bg-blue-500 text-white text-base sm:text-lg font-semibold rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                        Go to Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 bg-blue-500 text-white text-base sm:text-lg font-semibold rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                        Log in
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="px-6 py-3 border bg-white border-blue-500 text-blue-500 text-base sm:text-lg font-semibold rounded-lg shadow-md transform hover:scale-105 transition-transform duration-300">
                            Register
                        </a>
                    @endif
                @endauth
            @endif
        </div>

        <!-- Footer -->
        <footer class="mt-12 text-gray-600 text-xs sm:text-sm animate-fadeIn delay-300">
            © {{ date('Y') }} <span class="text-blue-800 font-semibold">TAJMS</span>. All rights reserved.
        </footer>
    </div>

    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.8s ease-out; }
        .animate-slideIn { animation: slideIn 0.8s ease-out; }
    </style>
</body>
</html>
