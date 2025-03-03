@extends('layouts.app')
@include('components.header')

@section('content')

<!-- Flash Messages -->
@if(session('success') || session('error'))
<div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
     style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
     {{ session('success') ?? session('error') }}
</div>
@endif

<div class="container mx-auto p-4 sm:p-6 w-full">
    <div class="bg-yellow-100 p-6 sm:p-8 rounded-lg shadow-lg h-auto sm:h-[32rem] border border-black">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl sm:text-2xl font-semibold text-gray-800">New Journal Entry 📝</h2>
            <a href="{{ route('journal.index') }}" class="px-4 py-2 bg-red-500 text-white rounded-lg shadow-sm hover:bg-red-600 transition duration-300 text-sm sm:text-base">
                &larr; Back
            </a> 
        </div>
        <form action="{{ route('journal.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4 sm:space-y-6">
            @csrf
            <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5">
                <!-- File input -->
                <div class="w-full sm:w-auto">
                    <input type="file" name="images[]" multiple class="border border-black bg-white p-2 rounded-lg w-full sm:w-auto" required>
                </div>
            </div>

            <div>
                <textarea name="content" id="content" class="w-full bg-blue-100 border border-black focus:ring-blue-500 focus:border-blue-500 rounded-lg shadow-sm text-gray-800 p-4 h-[12rem] sm:h-[16rem] resize-none" placeholder="How was your day? 😊 . . ." required></textarea>
            </div>

            <div class="flex justify-start">
                <button type="submit" class="px-5 sm:px-6 py-2 bg-green-500 text-white font-medium rounded-lg shadow-md hover:bg-green-600 transition duration-200 text-sm sm:text-base">
                    Save Entry
                </button>
            </div>
        </form>
    </div> 
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500); // Remove after fade out
            }, 3000); // Show for 3 seconds
        }
    });
</script>
@endsection
