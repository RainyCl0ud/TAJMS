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
        <!-- Notification Bell -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="relative p-2 text-gray-600 hover:text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                </svg>
                <span id="notification-badge" class="hidden absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs flex items-center justify-center">0</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div x-show="open" 
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50">
                <div class="px-4 py-2 border-b border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700">Notifications</h3>
                </div>
                <div id="notification-list" class="max-h-96 overflow-y-auto">
                    <!-- Notifications will be dynamically inserted here -->
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

<!-- Add Alpine.js -->
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    // Function to update notification badge
    function updateNotificationBadge(count) {
        const badge = document.getElementById('notification-badge');
        if (count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Function to add a new notification
    function addNotification(notification) {
        const notificationList = document.getElementById('notification-list');
        const notificationElement = document.createElement('div');
        notificationElement.className = 'px-4 py-3 hover:bg-gray-50 border-b border-gray-100';
        notificationElement.innerHTML = `
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <img class="h-10 w-10 rounded-full" src="${notification.user_image || '{{ asset('storage/profile_pictures/default.png') }}'}" alt="${notification.user_name}">
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm text-gray-900">
                        <span class="font-medium">${notification.user_name}</span> requested a forgot 
                        ${notification.type === 'time_in' ? 'time in' : 'time out'} for ${notification.date}
                    </p>
                    <p class="text-xs text-gray-500">${notification.created_at}</p>
                </div>
            </div>
        `;
        notificationList.prepend(notificationElement);
    }

    // Initialize Echo for real-time notifications
    window.Echo.private('requests')
        .listen('NewRequestNotification', (e) => {
            addNotification(e.notification);
            const currentCount = parseInt(document.getElementById('notification-badge').textContent || '0');
            updateNotificationBadge(currentCount + 1);
        });

    // Load initial notifications on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('notification-list')) {
            fetch('/api/notifications')
                .then(response => response.json())
                .then(data => {
                    updateNotificationBadge(data.unread_count);
                    data.notifications.forEach(notification => {
                        addNotification(notification);
                    });
                });
        }
    });
</script>
