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
  
<div class="h-screen bg-blue-100 flex flex-col p-5 overflow-auto">
    <div class="w-full max-h-[32rem] bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-blue-600 text-white text-center py-4 text-lg font-semibold">
            Forgot Time In/Out Requests
        </div>

        <!-- Responsive Table Wrapper (Ensure Correct Scrolling) -->
        <div class="w-full overflow-x-auto max-h-[26rem]">
            <table class="min-w-full border-collapse table-auto"> 
                <thead class="bg-gray-800 text-white uppercase text-xs sm:text-sm sticky top-0 z-10">
                    <tr>
                        <th class="py-2 px-2 sm:py-3 sm:px-4 text-left truncate">Name</th>
                        <th class="py-2 px-2 sm:py-3 sm:px-4 text-left truncate">Status</th>
                        <th class="py-2 px-2 sm:py-3 sm:px-4 text-left truncate">Requested</th>
                        <th class="py-2 px-2 sm:py-3 sm:px-4 text-center truncate">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($requests as $request)
                    <tr class="hover:bg-gray-100 transition cursor-pointer request-row"
                        data-id="{{ $request->id }}"
                        data-name="{{ $request->user ? $request->user->first_name . ' ' . $request->user->last_name : 'No Trainee' }}"
                        data-type="{{ $request->type }}"
                        data-status="{{ $request->status ?? '--' }}"
                        data-date="{{ $request->date }}"
                        data-time="{{ $request->time }}"
                        data-reason="{{ $request->reason }}"
                        data-attachment="{{ $request->image_url ?? '' }}">

                        <td class="py-2 px-2 text-gray-800 truncate max-w-[80px] sm:max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap" 
                            title="{{ $request->user ? $request->user->first_name . ' ' . $request->user->last_name : 'Trainee not available' }}">
                            {{ Str::limit($request->user ? $request->user->first_name . ' ' . $request->user->last_name : 'Trainee not available', 20) }}
                        </td>

                        <!-- Status Column -->
                        <td class="py-2 px-2 truncate">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                            {{ $request->status == 'Approved' ? 'bg-green-100 text-green-600' : 
                            ($request->status == 'Rejected' ? 'bg-red-100 text-red-600' : 
                            'bg-yellow-100 text-yellow-600') }}">
                            {{ $request->status ?? 'Pending' }}
                            </span>
                        </td>

                        <!-- Requested Column -->
                        <td class="py-2 px-2 text-gray-800 truncate max-w-[80px] sm:max-w-[150px] overflow-hidden text-ellipsis whitespace-nowrap">
                            {{ $request->time_elapsed }}
                        </td>

                        <!-- Action Column -->
                        <td class="py-2 px-2 text-center truncate">
                            @if($request->status === 'Pending')
                            <div class="flex justify-center space-x-2">
                                <form action="{{ route('request.approve', $request->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-green-500 text-white px-3 py-1 rounded text-sm hover:bg-green-600">
                                        Approve
                                    </button>
                                </form>
                                <form action="{{ route('request.reject', $request->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="bg-red-500 text-white px-3 py-1 rounded text-sm hover:bg-red-600">
                                        Reject
                                    </button>
                                </form>
                            </div>
                            @elseif($request->status === 'Rejected')
                            <button type="button" class="text-gray-500 hover:text-red-700 font-semibold" onclick="showDeleteModal('{{ route('request.delete', $request->id) }}', event)">
                                ✖
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

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
