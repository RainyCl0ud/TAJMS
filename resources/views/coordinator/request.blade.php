@extends('layouts.app')
@include('components.header')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

@if(session('success') || session('error'))
  <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
      style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
      {{ session('success') ?? session('error') }}
  </div>
@endif

<div class="container mx-auto px-4 py-6 flex flex-col">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Requests</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
        @foreach($requests as $request)
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

            <button class="text-left bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow cursor-pointer w-full"
                    onclick='showRequestModal(@json($request))'>
                <div class="p-4">
                    <div class="flex items-center mb-4">
                        <img src="{{ $imageUrl }}" alt="Profile Picture" class="w-10 h-10 rounded-full mr-4 object-cover">
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
    </div>
</div>

<!-- Request Details Modal -->
<div id="request-details-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 md:w-3/4 lg:w-1/2 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-end">
            <button onclick="closeRequestModal()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div id="request-details-content" class="mt-4">
            <!-- JavaScript will fill this -->
        </div>

        <div id="request-actions" class="mt-6 flex justify-end space-x-4">
            <!-- JavaScript will fill this -->
        </div>
    </div>
</div>

<!-- Add Rendered Hours Modal -->
<div id="add-hours-modal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center">
    <div class="bg-white rounded-lg shadow-lg max-w-lg w-full p-6 relative">
        <button onclick="closeAddHoursModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900">✖</button>
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Add Rendered Hours</h2>

        <form id="add-hours-form" method="POST">
            @csrf
            <input type="hidden" id="request_id" name="request_id">
            <label class="block text-gray-700 font-medium">Hours</label>
            <input type="number" name="hours" class="w-full p-2 border rounded-lg mb-3" min="0" required>

            <label class="block text-gray-700 font-medium">Minutes</label>
            <input type="number" name="minutes" class="w-full p-2 border rounded-lg mb-3" min="0" max="59" required>

            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg">
                Add Hours
            </button>
        </form>
    </div>
</div>

<!-- Full Screen Image Modal -->
<div id="fullscreen-modal" class="fixed inset-0 z-[9999]" style="background-color: rgba(0, 0, 0, 0.9);">
    <div class="absolute inset-0 flex items-center justify-center z-[10000]">
        <button onclick="closeFullscreen()" class="absolute top-4 right-4 text-white hover:text-gray-300 z-[10001]">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img id="fullscreen-image" src="" alt="Full Screen Image" 
             class="max-h-[90vh] max-w-[90vw] object-contain cursor-zoom-out z-[10000]"
             onclick="closeFullscreen()">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => flashMessage.style.opacity = '0', 2000);
        }
    });

    function openFullscreen(imageUrl) {
        const modal = document.getElementById('fullscreen-modal');
        const image = document.getElementById('fullscreen-image');
        image.src = imageUrl;
        modal.style.display = 'flex';
        modal.style.position = 'fixed';  // Ensure fixed positioning
        document.body.style.overflow = 'hidden';
    }

    function closeFullscreen() {
        const modal = document.getElementById('fullscreen-modal');
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function showRequestModal(request) {
        const modal = document.getElementById('request-details-modal');
        const content = document.getElementById('request-details-content');
        const actions = document.getElementById('request-actions');

        // Extract profile picture from Google Drive
        let profileUrl = request.user.profile_picture || '';
        let profileFileId = null;
        if (profileUrl.includes('id=')) {
            const urlParams = new URLSearchParams(profileUrl.split('?')[1]);
            profileFileId = urlParams.get('id');
        } else {
            const match = profileUrl.match(/\/d\/(.*?)\//);
            profileFileId = match ? match[1] : null;
        }
        const profileImageUrl = profileFileId
            ? `https://drive.google.com/thumbnail?id=${profileFileId}`
            : `{{ asset('storage/profile_pictures/default.png') }}`;

        // Extract request image from Google Drive
        let requestImageUrl = '';
        if (request.image) {
            let requestFileId = null;
            if (request.image.includes('id=')) {
                const urlParams = new URLSearchParams(request.image.split('?')[1]);
                requestFileId = urlParams.get('id');
            } else {
                const match = request.image.match(/\/d\/(.*?)\//);
                requestFileId = match ? match[1] : null;
            }
            requestImageUrl = requestFileId
                ? `https://drive.google.com/thumbnail?id=${requestFileId}`
                : '';
        }

        // Populate modal content
        content.innerHTML = `
            ${requestImageUrl ? `
                <div class="flex justify-center overflow-hidden">
                    <div class="relative group cursor-pointer" onclick="openFullscreen('${requestImageUrl}')">
                        <img src="${requestImageUrl}" 
                             alt="Request Image" 
                             class="max-w-full h-auto rounded-lg transition-all duration-300 hover:opacity-90">
                        <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-300">
                            <svg class="w-12 h-12 text-white opacity-0 group-hover:opacity-100 transform scale-50 group-hover:scale-100 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            ` : `
                <div class="text-center text-gray-500 py-8">
                    No image attached to this request
                </div>
            `}
        `;

        actions.innerHTML = request.status === 'pending' ? `
            <button onclick="rejectRequest(${request.id})" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600 transition duration-200">
                Reject
            </button>
            <button onclick="approveRequest(${request.id})" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600 transition duration-200">
                Approve
            </button>
        ` : `
            <span class="px-6 py-2 rounded ${request.status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${request.status}
            </span>
        `;

        modal.classList.remove('hidden');
    }

    function closeRequestModal() {
        document.getElementById('request-details-modal').classList.add('hidden');
    }

    function approveRequest(requestId) {
        document.getElementById('request_id').value = requestId;
        document.getElementById('add-hours-modal').classList.remove('hidden');
        closeRequestModal();
    }

    function rejectRequest(requestId) {
        if (confirm('Are you sure you want to reject this request?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('requests') }}/${requestId}/reject`;


            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;

            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }

    document.getElementById('add-hours-form').addEventListener('submit', function (e) {
        e.preventDefault();
        const requestId = document.getElementById('request_id').value;
        this.action = `{{ url('requests') }}/${requestId}/approve`;
        this.submit();
    });

    // Add event listeners for fullscreen modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeFullscreen();
        }
    });
</script>

@include('components.forgot-time-modal')
@endsection
