@extends('layouts.app')
@include('components.header')

@section('content')
<div class="min-h-screen bg-blue-100 flex flex-col p-4 sm:p-6 overflow-auto">
    <div class="container mx-auto">
        @if($trainees->isNotEmpty())
        <div class="w-full max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 text-white text-center py-4 text-lg font-semibold">
                Today's Attendance
            </div>
            <!-- Table Wrapper -->
            <div class="overflow-x-auto table-container">
                <div class="max-h-[450px] overflow-y-auto">
                    <table class="min-w-full bg-white text-xs sm:text-sm md:text-base">
                        <thead class="bg-gray-800 text-white sticky top-0 z-20">
                            <tr>
                                <th class="px-4 py-3 text-left font-medium uppercase tracking-wider">Name</th>
                                <th class="px-4 py-3 text-left font-medium uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($trainees as $trainee)
                            <tr class="border-b cursor-pointer hover:bg-gray-100" 
                                onclick="window.location.href='{{ route('coordinator.trainee-records', ['traineeId' => $trainee->id, 'from' => request()->fullUrl()]) }}'">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    {{ Str::title($trainee->last_name . ' ' . $trainee->first_name . ', ' . substr($trainee->middle_name, 0, 1) . '.') }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-3 py-1 text-xs sm:text-sm rounded-lg text-white {{ $trainee->attendance_status === 'Present' ? 'bg-green-500' : 'bg-red-500' }}">
                                        {{ $trainee->attendance_status === 'Present' ? 'Present ✅' : 'Not working 🚫' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <p class="text-gray-500 text-center mt-8 text-sm sm:text-base">No trainees found.</p>
        @endif
    </div>
</div>
@endsection
