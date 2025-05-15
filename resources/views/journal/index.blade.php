@extends('layouts.app')
@include('components.header')

@section('content')
<div class="bg-blue-100 min-h-screen">
<div class="w-full px-4 sm:px-6 md:px-10 mx-auto p-4 sm:p-6 md:p-10">

   @if(session('success') || session('error'))
 <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
     style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
     {{ session('success') ?? session('error') }}
 </div>
 @endif
    
    <div class="flex justify-between items-center mb-5">
        <a href="{{ route('journal.create') }}" class="bg-blue-500 text-white py-2 px-4 rounded-md shadow-md hover:bg-blue-600 transition duration-300 text-sm sm:text-base">
            Create New Journal Entry
        </a>



        <button onclick="previewPdf()" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition text-sm sm:text-base">
            Preview PDF
        </button>

    </div>

   <!-- Journal Entries Table -->
   <div class="overflow-hidden rounded-lg shadow-lg border border-black shadow-lg shadow-black">
    <div class="max-h-[calc(100vh-20rem)] sm:max-h-[calc(100vh-15rem)] overflow-auto">
        <table class="w-full text-xs sm:text-sm md:text-base relative">
            <thead class="bg-gray-800 text-white sticky top-0 z-10">
                <tr>
                    <th class="px-2 sm:px-4 py-3 text-left font-medium whitespace-nowrap">Day</th>
                    <th class="px-2 sm:px-4 py-3 text-left font-medium whitespace-nowrap">Journal</th>
                    <th class="px-2 sm:px-4 py-3 text-left font-medium whitespace-nowrap">Date</th>
                    <th class="px-2 sm:px-4 py-3 text-left font-medium whitespace-nowrap">Image</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white text-black">
                @foreach($journals as $journal)
                <tr onclick="window.location='{{ route('journal.show', $journal) }}'" class="cursor-pointer hover:bg-gray-200 transition">
                    <td class="px-2 sm:px-4 py-4 whitespace-nowrap">{{ $journal->day }}</td>
                    <td class="px-2 sm:px-4 py-4 max-w-[80px] sm:max-w-[150px] md:max-w-[200px] lg:max-w-none truncate overflow-hidden text-ellipsis">
                        {{ Str::limit($journal->content, 30) }}
                    </td>
                    <td class="px-2 sm:px-4 py-4 whitespace-nowrap">{{ $journal->created_at->format('Y-m-d') }}</td>
                    <td class="px-2 sm:px-4 py-4 whitespace-nowrap">



                    @if($journal->image)
    @php
        $images = json_decode($journal->image, true);
        $totalImages = count($images);
        $displayImages = array_slice($images, 0, 3);
    @endphp
    <div class="relative flex items-center w-[70px] sm:w-[90px] h-10 sm:h-12 overflow-hidden">
        @foreach($displayImages as $index => $image)
            <img src="{{ $image }}" 
                 class="w-8 h-8 sm:w-10 sm:h-10 object-cover rounded-md border shadow-md"
                 style="position: absolute; left: {{ $index * 14 }}px; z-index: {{ 5 - $index }};">
        @endforeach

        @if($totalImages > 3)
            <div class="absolute flex items-center justify-center bg-gray-700 text-white font-semibold 
                        rounded-full shadow-md text-base"
                 style="left: {{ count($displayImages) * 14 + 5 }}px; z-index: 6;
                        width: clamp(20px, 2vw, 28px); 
                        height: clamp(20px, 2vw, 28px); 
                        font-size: clamp(10px, 1.5vw, 14px);">
                +{{ $totalImages - 3 }}
            </div>
        @endif
    </div>
@else
    No Images
@endif




                    </td>
                    
                    
                    
                    
                    
                    
                    
                </tr>
                @endforeach
                @if ($journals->isEmpty())
                <tr>
                    <td colspan="4" class="text-center py-4 text-gray-500">
                        No Journal entries found.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>


<script>
    function previewPdf() {
        fetch("{{ route('journal.preview-pdf') }}")
            .then(response => response.json())
            .then(data => {
                if (data.url) {
                    window.open(data.url, "_blank");
                } else {
                    alert("Error: No PDF URL found.");
                }
            })
            .catch(error => {
                console.error("Error loading PDF:", error);
                alert("Failed to load PDF. Please try again.");
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 300);
            }, 2000);
        }
    });
</script>

    </div>
@endsection
