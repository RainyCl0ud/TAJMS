@foreach($requests as $request)
    <button class="text-left bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow cursor-pointer w-full"
            onclick="showRequestModal({{ json_encode($request) }})">
        <div class="p-4">
            <div class="flex items-center mb-4">
                @php
                    $url = $request->user->profile_picture;
                    $fileId = null;

                    if (str_contains($url, 'id=')) {
                        parse_str(parse_url($url, PHP_URL_QUERY), $query);
                        $fileId = $query['id'] ?? null;
                    } elseif (preg_match('/\/d\/(.*?)\//', $url, $matches)) {
                        $fileId = $matches[1];
                    }

                    $imageUrl = $fileId ? "https://drive.google.com/thumbnail?id={$fileId}" : asset('storage/profile_pictures/default.png');
                @endphp

                <img src="{{ $imageUrl }}" 
                     alt="Profile Picture" 
                     class="w-10 h-10 rounded-full mr-4 object-cover"
                     onerror="this.src='{{ asset('storage/profile_pictures/default.png') }}'">
                <div>
                    <h3 class="font-semibold text-gray-800">{{ $request->user->first_name }} {{ $request->user->last_name }}</h3>
                    <p class="text-sm text-gray-500">{{ $request->time_elapsed }}</p>
                </div>
            </div>

            <div class="mb-4">
                <p class="text-gray-700"><span class="font-semibold">Type:</span> {{ ucfirst($request->type) }}</p>
                <p class="text-gray-700"><span class="font-semibold">Date:</span> {{ $request->date }}</p>
                <p class="text-gray-700"><span class="font-semibold">Time:</span> {{ $request->time }}</p>
            </div>

            <div class="flex justify-end">
                <span class="px-4 py-2 rounded {{ $request->status === 'Pending' ? 'bg-yellow-100 text-yellow-800' : ($request->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                    {{ $request->status }}
                </span>
            </div>
        </div>
    </button>
@endforeach 