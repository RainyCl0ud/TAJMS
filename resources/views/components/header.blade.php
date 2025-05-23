@php use Illuminate\Support\Str; @endphp
<header class="h-20 flex justify-between items-center m-0 border-b-2 border-gray-300 px-6 shadow-lg sticky top-0 bg-white overlow-auto relative" style="z-index: 40;">
    <div class="flex items-center space-x-4">
        <!-- Hamburger Icon (Visible on Small Screens) -->
        <button id="menuToggle" class="text-gray-700 md:hidden focus:outline-none">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
            </svg>
        </button>
        <!-- Page Title -->
        <h1 class="text-lg sm:text-sm md:text-2xl font-semibold text-gray-800 cursor-default break-words mr-5">
            {{ $pageTitle }}
        </h1>
    </div>

    <div class="flex items-center space-x-4">
        @if(Auth::check() && Auth::user()->role === 'coordinator')
        <!-- Notification Bell Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
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

            <!-- Dropdown Content -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95"
                 class="absolute right-0 w-[32rem] bg-white rounded-lg shadow-xl"
                 style="top: calc(100% + 0.5rem); z-index: 100;">
                <div class="bg-gray-50 px-4 py-3 border-b border-gray-200 rounded-t-lg">
                    <h3 class="text-sm font-semibold text-gray-800">Notifications</h3>
                </div>
                <div class="max-h-[calc(100vh-200px)] overflow-y-auto">
                    @php
                        $notifications = \App\Models\Notification::with(['user', 'request'])
                            ->orderBy('created_at', 'desc')
                            ->take(10)
                            ->get();
                    @endphp

                    @forelse($notifications as $notification)
                        @php
                            $url = $notification->user->profile_picture;
                            $fileId = null;

                            if (str_contains($url, 'id=')) {
                                parse_str(parse_url($url, PHP_URL_QUERY), $query);
                                $fileId = $query['id'] ?? null;
                            } elseif (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
                                $fileId = $matches[1];
                            }

                            $imageUrl = $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : asset('storage/profile_pictures/default.png');
                        @endphp

                        <div class="hover:bg-gray-50 transition-colors duration-150 border-b border-gray-100 last:border-b-0">
                            <a href="{{ route('coordinator.requests', ['highlight' => $notification->request->id]) }}" class="block">
                                <div class="px-4 py-3">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            <img class="h-full w-full rounded-full object-cover border border-gray-200"
                                                 src="{{ $imageUrl }}"
                                                 alt="{{ $notification->user->first_name }}'s profile picture"
                                                 onerror="this.src='{{ asset('storage/profile_pictures/default.png') }}'"
                                                 loading="lazy">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex justify-between items-start">
                                                <p class="text-sm text-gray-900 font-medium truncate">
                                                    {{ $notification->user->first_name }} {{ $notification->user->last_name }}
                                                </p>
                                                <p class="text-xs text-gray-500 ml-2 whitespace-nowrap">{{ $notification->created_at->diffForHumans() }}</p>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1 break-words">
                                                Requested a time 
                                                @if($notification->request)
                                                    {{ $notification->request->type === 'time_in' ? 'in' : 'out' }}
                                                    entry for {{ \Carbon\Carbon::parse($notification->request->date)->format('M d, Y') }}
                                                @else
                                                    entry
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="px-4 py-6 text-sm text-gray-500 text-center">
                            No new notifications
                        </div>
                    @endforelse
                </div>
                <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 rounded-b-lg">
                    <a href="{{ route('coordinator.requests') }}" 
                       class="block text-sm text-center font-medium text-blue-600 hover:text-blue-800 transition-colors duration-150">
                        View all requests
                    </a>
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

                        $finalImageUrl = $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : asset('storage/profile_pictures/default.png');
                    @endphp

                    <div class="flex items-center cursor-pointer">
                        <div class="text-gray-700 hidden md:flex flex-col ml-2">
                            <span class="text-sm">{{ Str::title(Auth::user()->first_name . ' ' . Auth::user()->last_name) }}</span>
                            <span class="block text-xs text-gray-500">{{ Auth::user()->role }}</span>
                        </div>

                        <img src="{{ $finalImageUrl }}"
                             alt="{{ Auth::user()->first_name }}'s Profile Picture"
                             class="h-10 w-10 rounded-full ml-2 object-cover cursor-pointer border border-black hover:shadow-lg hover:shadow-blue-300"
                             onerror="this.onerror=null;this.src='{{ asset('storage/profile_pictures/default.png') }}';">
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

    .z-50 {
        z-index: 9999 !important;
    }

    .dashboard-card {
        z-index: 10;
    }

    .peer-checked\:block {
        transition: all 0.2s ease-in-out;
    }
</style>
