<header class="h-20 flex justify-between items-center m-0 border-b-2 border-gray-300 px-6 shadow-lg sticky top-0 bg-white overlow-auto">
    <div class="flex items-center space-x-4">
        <!-- Hamburger Icon (Visible on Small Screens) -->
        <button id="menuToggle" class="text-gray-700 md:hidden focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>

        <!-- Page Title -->
        <h1 class="text-lg sm:text-sm md:text-2xl font-semibold text-gray-800 cursor-default break-words mr-5">
            {{ $pageTitle }}
        </h1>
    </div>

    <div class="flex items-center space-x-4 relative">
        <x-dropdown align="right" width="48">
            <x-slot name="trigger">
                @if(Auth::check())
                    <div class="flex items-center cursor-pointer">
                        <div class="text-gray-700 hidden md:flex flex-col ml-2">
                            <span class="text-sm">{{ ucfirst(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</span>
                            <span class="block text-xs text-gray-500">{{ Auth::user()->role }}</span>
                        </div>
                        <img src="{{ Auth::user()->profile_picture && file_exists(storage_path('app/public/' . Auth::user()->profile_picture)) 
                                    ? asset('storage/' . Auth::user()->profile_picture) 
                                    : asset('images/profile_empty.png') }}"  
                             alt="Profile picture" class="h-10 w-10 rounded-full ml-2 object-cover cursor-pointer border border-black hover:shadow-lg hover:shadow-blue-300">
                    </div>
                @endif
            </x-slot>
            <x-slot name="content">
                <x-dropdown-link href="{{ route('profile.edit') }}">
                    Profile
                </x-dropdown-link>
                <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                    @csrf
                </form>
                <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    Log Out
                </x-dropdown-link>
            </x-slot>
        </x-dropdown>
    </div>
</header>
