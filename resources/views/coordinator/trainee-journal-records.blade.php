@extends('layouts.app')
@include('components.header')

@section('content')
<div class="container mx-auto p-4 md:p-8"> 
    <!-- Back Button -->
    <div class="flex justify-end items-center mb-3">
        <button onclick="window.location.href='{{ route('coordinator.trainee-records', ['traineeId' => $trainee->id ]) }}'" 
            class="bg-red-500 text-white px-4 md:px-5 py-2 md:py-3 rounded-lg hover:bg-red-700 transition duration-200 text-sm">
            ← GO BACK
        </button>
    </div>

   @if(count($journals) > 0)
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
                                   $displayImages = array_slice($images, 0, 3); // Show first 3 images
                               @endphp
                               <div class="relative flex items-center w-[70px] sm:w-[90px] h-10 sm:h-12 overflow-hidden">
                                   @foreach($displayImages as $index => $image)
                                       <img src="{{ asset('storage/' . $image) }}" 
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
               </tbody>
           </table>
       </div>
   </div>
   @else
   <div class="mt-6 md:mt-8 text-center">
       <p class="text-sm md:text-lg text-gray-500">No journal entries available for this trainee.</p>
   </div>
   @endif
</div>
@endsection
