@extends('layouts.app')
@include('components.header')
@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="container mx-auto p-6">
    <div class="max-w-5xl mx-auto bg-white shadow-lg rounded-lg p-6 h-[32rem]">

        <!-- Header Section -->
        <div class="flex justify-between items-center border-b pb-4 mb-4">
            <p class="text-gray-600 text-m">
                <strong>Created At:</strong> {{ $journal->created_at->setTimezone('Asia/Manila')->format('F j, Y, h:i A') }}
            </p>
            
            <!-- Flash Messages -->
            @if ($errors->any())
            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
            @endif
            <div>
                <a href="{{ route('coordinator.trainee-journal-records', ['traineeId' => $trainee->id]) }}" 
                    class="px-4 py-2 text-m font-medium text-white bg-blue-500 rounded-md hover:bg-blue-600 transition">
                     Back
                 </a>                 
            </div>
        </div>
        <!-- Journal Content -->
        <div class="bg-blue-50 max-h-[22rem] h-[22rem] p-3 rounded-lg shadow-inner overflow-y-auto">
            <p class="text-gray-800 text-lg leading-relaxed whitespace-pre-wrap break-words">
                {{ $journal->content }}
             </p>             
        </div>
    </div>
</div>
@endsection
