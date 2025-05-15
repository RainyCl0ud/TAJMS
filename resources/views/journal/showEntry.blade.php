@extends('layouts.app')
@include('components.header')

@section('content')
<div class="bg-blue-100 min-h-screen pr-4 sm:pr-4 md:pr-4 lg:pr-4 overflow-auto">
    <div class="flex-1 overflow-y-auto p-5 max-h-[calc(100vh-64px)] sm:max-h-[calc(100vh-80px)]">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <div class="relative w-full max-w-[95vw] sm:max-w-[90vw] md:max-w-[80vw] lg:max-w-[70vw] xl:max-w-5xl mx-auto bg-yellow-100 shadow-lg rounded-lg p-6 min-h-[30rem] border border-gray-200 shadow-black px-4 sm:px-6 md:px-8 px-4 lg:px-10 overflow-y-auto">

            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center border-b pb-4 mb-4 space-y-3 sm:space-y-0 relative">
                <p class="text-gray-600 text-xs sm:text-sm md:text-base">
                    <strong>Created At:</strong> {{ $journal->created_at->setTimezone('Asia/Manila')->format('F j, Y, h:i A') }}
                      <!-- Back Button for Small Screens -->
<button id="back-button" 
class="absolute top-0 right-0 text-lg text-blue-500 hover:text-blue-600 focus:outline-none p-2 sm:hidden"
onclick="location.href='{{ auth()->user()->role === 'trainee' ? route('journal.index') : route('coordinator.trainee-journal-records', ['traineeId' => $trainee->id]) }}'">
❌
</button>

                </p>

              

                <!-- Back Button for Larger Screens -->
                <div class="hidden sm:block ml-auto">
                    <a href="{{ auth()->user()->role === 'trainee' ? route('journal.index') : route('coordinator.trainee-journal-records', ['traineeId' => $trainee->id]) }}" 
                       class="px-4 py-2 text-sm font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">
                        Back
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            @if(session('success') || session('error'))
            <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-sm sm:text-lg font-semibold transition-opacity duration-500"
                style="z-index: 9999;">
                {{ session('success') ?? session('error') }}
            </div>
            @endif

            <!-- Journal Content -->
            @if (request('edit'))
                <form id="journal-form" method="POST" action="{{ route('journal.update', ['id' => $journal->id]) }}" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <textarea name="content" class="bg-white w-full min-h-[15rem] max-h-[50vh] p-4 border resize-none overflow-y-auto text-sm sm:text-base break-words"></textarea>
                    <x-input-error class="mt-2 text-red-500 text-sm" :messages="$errors->get('content')" />
                </form>
            @else
            <div class="w-full max-h-[50vh] overflow-y-auto p-4 border border-gray-300 rounded-md bg-white text-black text-sm sm:text-base leading-relaxed whitespace-pre-wrap break-words">
                {{ $journal->content }}
                </div>
            @endif

            <!-- Display Attached Images -->
            @if($journal->image)
                @php $images = json_decode($journal->image, true) ?? []; @endphp
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2 w-full">
                    @foreach($images as $image)
                        <div class="border border-gray-300 rounded-md overflow-hidden">
                            <x-google-drive-image 
                                :url="$image"
                                :alt="'Journal Image'"
                                class="w-full h-24 sm:h-32 md:h-36 lg:h-40 object-cover rounded-md border shadow-sm"
                            />
                        </div>                        
                    @endforeach
                </div>                    
            @endif


            <!-- Action Buttons -->
            <div class="mt-6 flex flex-wrap justify-start gap-4">
                @if (request('edit') && auth()->user()->role === 'trainee')
                    <button type="submit" form="journal-form" class="px-4 py-2 text-sm font-medium text-white bg-indigo-500 rounded-md hover:bg-indigo-600 transition">
                        Save Changes
                    </button>
                    <a href="{{ route('journal.show', $journal->id) }}" class="px-4 py-2 text-sm font-medium text-white bg-red-500 rounded-md hover:bg-red-600 transition">
                        Cancel
                    </a>
                @elseif(auth()->user()->role === 'trainee')
                    <a href="{{ route('journal.show', [$journal->id, 'edit' => true]) }}" class="px-4 py-2 text-sm font-medium text-white bg-green-500 rounded-md hover:bg-green-600 transition">
                        Edit
                    </a>
                @endif
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500);
            }, 3000);
        }
    });
</script>
@endsection
