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
        @if(Auth::check() && Auth::user()->role === 'coordinator')
        <!-- Notification Bell Dropdown -->
        <div class="relative group">
            <button class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                @php
                    $unreadCount = \App\Models\Notification::where('read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center animate-pulse">
                        {{ $unreadCount }}
                    </span>
                @endif
            </button>

            <!-- Dropdown Content - Shows on Hover -->
            <div class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 hidden group-hover:block">
                <div class="px-4 py-2 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    @php
                        $notifications = \App\Models\Notification::with(['user', 'request'])
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
                    @endphp

                    @forelse($notifications as $notification)
                        <a href="{{ route('coordinator.requests') }}" class="block">
                            <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <img class="h-10 w-10 rounded-full" 
                                             src="{{ $notification->user->profile_picture ?? asset('storage/profile_pictures/default.png') }}" 
                                             alt="{{ $notification->user->first_name }}">
                                    </div>
                                    <div class="ml-3 flex-1">
                                        <p class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $notification->user->first_name }} {{ $notification->user->last_name }}</span>
                                            requested a forgot 
                                            {{ $notification->request->type === 'time_in' ? 'time in' : 'time out' }}
                                            for {{ $notification->request->date }}
                                        </p>
                                        <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-3 text-sm text-gray-500 text-center">
                            No new notifications
                        </div>
                    @endforelse
                </div>
                <div class="px-4 py-2 border-t border-gray-200">
                    <a href="{{ route('coordinator.requests') }}" class="text-sm text-blue-600 hover:text-blue-800">View all requests</a>
                </div>
            </div>
        </div>
        @endif

        <x-dropdown align="right" width="48">
           <x-slot name="trigger">
    @if(Auth::check())
        @php
            $url = Auth::user()->profile_picture;
            $fileId = null;

            if (str_contains($url, 'id=')) {
                parse_str(parse_url($url, PHP_URL_QUERY), $query);
                $fileId = $query['id'] ?? null;
            } elseif (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
                $fileId = $matches[1];
            }

            $finalImageUrl = $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : null;
        @endphp

        <div class="flex items-center cursor-pointer">
            <div class="text-gray-700 hidden md:flex flex-col ml-2">
                <span class="text-sm">{{ ucfirst(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</span>
                <span class="block text-xs text-gray-500">{{ Auth::user()->role }}</span>
            </div>

            <img src="{{ $finalImageUrl ?? asset('storage/profile_pictures/default.png') }}"
                 alt="{{ Auth::user()->first_name }}'s Profile Picture"
                 class="h-10 w-10 rounded-full ml-2 object-cover cursor-pointer border border-black hover:shadow-lg hover:shadow-blue-300"
                 onerror="this.onerror=null;this.src='{{ asset('storage/profile_pictures/default.png') }}'; console.error('Failed to load profile picture');">
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

<style>
    /* Ensure dropdown stays visible while hovering */
    .group:hover .group-hover\:block {
        display: block;
    }
    
    /* Add transition for smooth hover effect */
    .group-hover\:block {
        transition: all 0.3s ease;
    }
    
    /* Custom scrollbar for notifications */
    .max-h-96::-webkit-scrollbar {
        width: 4px;
    }
    
    .max-h-96::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .max-h-96::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 2px;
    }
    
    .max-h-96::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
