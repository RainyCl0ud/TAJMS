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
        <div class="bg-white rounded-lg shadow-md overflow-hidden border border-gray-200">
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
                    <p class="text-gray-700"><span class="font-semibold">Reason:</span> {{ $request->reason }}</p>
                </div>

                @if($request->image)
                <div class="mb-4">
                    <img src="{{ asset('storage/' . $request->image) }}" alt="Request Image" class="w-full rounded">
                </div>
                @endif

                <div class="flex justify-end space-x-2">
                    @if($request->status === 'Pending')
                    <form action="{{ route('request.approve', $request->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                            Approve
                        </button>
                    </form>
                    <form action="{{ route('request.reject', $request->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded hover:bg-red-600">
                            Reject
                        </button>
                    </form>
                    @else
                    <span class="px-4 py-2 rounded {{ $request->status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $request->status }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<style>
    /* Ensure request cards stay below dropdown */
    .request-card {
        z-index: 10;
    }
</style>

<!-- Delete Confirmation Modal -->
<div id="delete-confirmation-modal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-11/12 sm:w-96">
        <h3 class="text-lg font-semibold">Are you sure you want to delete this request?</h3>
        <div class="flex flex-col sm:flex-row sm:justify-between mt-4 gap-2 sm:gap-4">
            <form id="delete-form" method="POST" action="" class="inline-block w-full sm:w-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded text-sm sm:text-base h-full w-full sm:w-32">
                    Delete
                </button>
            </form>
            <button id="cancel-delete" class="bg-gray-300 text-gray-800 px-4 py-2 rounded text-sm sm:text-base h-full w-full sm:w-32">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => flashMessage.style.opacity = '0', 2000);
        }
    });

    function showDeleteModal(deleteUrl, event) {
        event.stopPropagation();
        document.getElementById('delete-form').action = deleteUrl;
        document.getElementById('delete-confirmation-modal').classList.remove('hidden');
    }

    document.getElementById('cancel-delete').addEventListener('click', function () {
        document.getElementById('delete-confirmation-modal').classList.add('hidden');
    });
</script>
@include('components.forgot-time-modal')
</div>
@endsection
