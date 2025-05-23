@extends('layouts.app')
@include('components.header')
@section('content')

@php use Illuminate\Support\Str; @endphp

<div class="max-h-screen overflow-auto">
    <div class="flex-1 px-6 py-4 pb-20">
 
    <!-- Trainee Information -->
    <div class="container mx-auto pt-3 pr-5 pl-10 pb-10 shadow-lg shadow-black border border-black rounded-lg mb-8 bg-white">
        <a href="{{ request()->query('from', route('coordinator.trainees')) }}" 
            class="flex justify-end mb-5 text-gray-600 hover:text-gray-900">✖
         </a>
         
        <div class="grid sm:grid-cols-2 gap-1 items-center">
            <!-- Trainee Details -->
            <div>
           <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ Str::title($trainee->first_name . ' ' . $trainee->middle_name . ' ' . $trainee->last_name) }}</h2>
           <div class="space-y-1">
               <p class="text-gray-600"><span class="font-semibold">Student ID:</span> {{ $trainee->trainee->student_id }}</p>
               <p class="text-gray-600"><span class="font-semibold">Email:</span> {{ $trainee->email }}</p>
           </div>
       </div>
            <!-- Accumulated and Remaining Time -->
            <div class="bg-gray-50 border border-gray-300 p-4 rounded-lg shadow-md text-center w-full">
                <div class="mb-2">
                    <p class="text-xs font-semibold text-gray-600">Accumulated Hours</p>
                    <p class="text-xl font-bold text-green-600">{{ $totalHours }}h & {{ $totalMins }}m</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-gray-600">Remaining Hours</p>
                    <p class="text-xl font-bold text-red-500">{{ $remainingHours }}h & {{ $remainingMins }}m</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Records Section -->
    <div class="container mx-auto grid grid-cols-1 lg:grid-cols-2 gap-6">
       <!-- Journal Records -->
<div class="bg-white p-6 mb-5 shadow-lg shadow-black border border-black rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Journal Records</h2>
        <a href="{{ route('coordinator.trainee-journal-records', ['traineeId' => $trainee->id]) }}" 
           class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
           View All
        </a>
    </div>

    <p class="text-gray-500 mb-5">📌 Showing the latest journal entries</p>

    @if($journals->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse rounded-lg shadow">
                <thead class="bg-gray-800 text-white text-left">
                    <tr>
                        <th class="px-4 py-2 border-b">Day</th>
                        <th class="px-4 py-2 border-b">Journal</th>
                        <th class="px-4 py-2 border-b">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journals->take(1) as $journal)
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-2 border-t text-gray-800">{{ $journal->day }}</td>
                            <td class="px-4 py-2 border-t text-gray-800">{{ Str::limit($journal->content, 10) }}</td>
                            <td class="px-4 py-2 border-t text-gray-800">{{ $journal->created_at->format('d-m-Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else   
        <p class="text-gray-500 text-center">No journal entries available.</p>
    @endif
</div>

<!-- Attendance Records -->
<div class="bg-white p-6 mb-5 shadow-lg shadow-black border border-black rounded-lg">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Attendance Records</h2>
        <a href="{{ route('attendance.index', ['traineeId' => $trainee->id]) }}" 
           class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600 transition">
           View All
        </a>
    </div>

    <p class="text-gray-500 mb-5">📌 Showing the latest attendance records</p>

    @if($attendances->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="table-auto w-full border-collapse rounded-lg shadow">
                <thead class="bg-gray-800 text-white text-left">
                    <tr>
                        <th class="px-4 py-2 border-b">Date</th>
                        <th class="px-4 py-2 border-b">Time In</th>
                        <th class="px-4 py-2 border-b">Time Out</th>
                        <th class="px-4 py-2 border-b">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($attendances->take(1) as $attendance)
                        <tr class="hover:bg-gray-50 transition text-sm">
                            <td class="px-4 py-2 border-t text-gray-800" style="max-width: 100px; overflow: hidden; text-overflow: ellipsis;">{{ $attendance->date }}</td>
                            <td class="px-4 py-2 border-t text-gray-800" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">{{ $attendance->in_time ? $attendance->in_time->format('h:i A') : 'N/A' }}</td>
                            <td class="px-4 py-2 border-t text-gray-800" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">{{ $attendance->out_time ? $attendance->out_time->format('h:i A') : 'N/A' }}</td>
                            <td class="px-4 py-2 border-t text-gray-800" style="max-width: 100px; overflow: hidden; text-overflow: ellipsis;">
                                @if($attendance->status === 'Completed')
                                    <span class="icon-check">✔️</span>
                                @elseif($attendance->status === 'Working')
                                    <span class="icon-loading">⏳</span>
                                @elseif($attendance->status === 'Missing')
                                    <span class="icon-question">❓</span>
                                @elseif($attendance->status === 'Absent')
                                    <span class="icon-x">❌</span>
                                @else
                                    <span>N/A</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <p class="text-gray-500 text-center">No attendance records available.</p>
    @endif
</div>
    </div>
    </div>
</div>
@endsection