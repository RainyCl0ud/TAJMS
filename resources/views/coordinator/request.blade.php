@extends('layouts.app')
@include('components.header')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

  <!-- Flash Messages -->
  @if(session('success') || session('error'))
  <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
      style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
      {{ session('success') ?? session('error') }}
  </div>
  @endif
  
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Requests</h1>

    <!-- Request Cards Container with lower z-index -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 relative z-10">
        @foreach($requests as $request)
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200 hover:shadow-lg transition-shadow cursor-pointer"
             onclick="showRequestModal({{ json_encode($request) }})">
            <div class="p-4">
                <div class="flex items-center mb-4">
                    <img src="{{ $request->user->profile_picture ?? asset('storage/profile_pictures/default.png') }}" 
                         alt="Profile Picture" 
                         class="w-10 h-10 rounded-full mr-4">
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
        </div>
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
            <!-- Content will be populated by JavaScript -->
        </div>
        
        <div id="request-actions" class="mt-6 flex justify-end space-x-4">
            <!-- Actions will be populated by JavaScript -->
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => flashMessage.style.opacity = '0', 2000);
        }
    });

    function showRequestModal(request) {
        const modal = document.getElementById('request-details-modal');
        const content = document.getElementById('request-details-content');
        const actions = document.getElementById('request-actions');
        
        // Populate modal content
        content.innerHTML = `
            <div class="flex items-center mb-6">
                <img src="${request.user.profile_picture || '{{ asset("storage/profile_pictures/default.png") }}'}" 
                     alt="Profile Picture" 
                     class="w-16 h-16 rounded-full mr-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">${request.user.first_name} ${request.user.last_name}</h2>
                    <p class="text-gray-500">${request.time_elapsed}</p>
                </div>
            </div>
            <div class="space-y-4">
                <p class="text-gray-700"><span class="font-semibold">Type:</span> ${request.type}</p>
                <p class="text-gray-700"><span class="font-semibold">Date:</span> ${request.date}</p>
                <p class="text-gray-700"><span class="font-semibold">Time:</span> ${request.time}</p>
                <p class="text-gray-700"><span class="font-semibold">Reason:</span> ${request.reason}</p>
                ${request.image ? `
                    <div class="mt-4">
                        <h3 class="font-semibold text-gray-700 mb-2">Attached Image:</h3>
                        <img src="{{ asset('storage/') }}/${request.image}" alt="Request Image" class="max-w-full rounded-lg">
                    </div>
                ` : ''}
            </div>
        `;

        // Populate actions based on request status
        if (request.status === 'Pending') {
            actions.innerHTML = `
                <button onclick="rejectRequest(${request.id})" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                    Reject
                </button>
                <button onclick="approveRequest(${request.id})" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                    Approve
                </button>
            `;
        } else {
            actions.innerHTML = `
                <span class="px-6 py-2 rounded ${request.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                    ${request.status}
                </span>
            `;
        }

        modal.classList.remove('hidden');
    }

    function closeRequestModal() {
        document.getElementById('request-details-modal').classList.add('hidden');
    }

    function approveRequest(requestId) {
        // Show add hours modal and set the request ID
        document.getElementById('request_id').value = requestId;
        document.getElementById('add-hours-modal').classList.remove('hidden');
        closeRequestModal();
    }

    function rejectRequest(requestId) {
        if (confirm('Are you sure you want to reject this request?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `{{ url('request/reject') }}/${requestId}`;
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = document.querySelector('meta[name="csrf-token"]').content;
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    }

    function closeAddHoursModal() {
        document.getElementById('add-hours-modal').classList.add('hidden');
    }

    // Set up the add hours form submission
    document.getElementById('add-hours-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const requestId = document.getElementById('request_id').value;
        this.action = `{{ url('request/approve') }}/${requestId}`;
        this.submit();
    });
</script>
@include('components.forgot-time-modal')
@endsection
