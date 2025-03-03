@extends('layouts.app')
@include('components.header')
@section('content')
<div class="pl-5 pr-5">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6 mt-5">
        <h2 class="text-lg sm:text-2xl md:text-3xl font-bold text-black bg-yellow-100 
        p-3 shadow-black shadow-md rounded-lg 
        text-center sm:text-left w-auto max-w-full sm:max-w-lg break-words">
 {{$trainee->first_name .' '.$trainee->middle_name.' '. $trainee->last_name}}
</h2>

        <button onclick="window.history.back()"  class="mt-3 sm:mt-0 px-3 sm:px-4 py-1 sm:py-2 bg-red-500 text-white 
                   rounded-lg shadow hover:bg-red-700 transition-all 
                   text-xs sm:text-sm md:text-base">
            ← GO BACK
        </button> 
    </div>

    <!-- Attendance Records Table -->
    <div class="bg-white p-5 pt-10 rounded-lg shadow-lg shadow-black">
        @if($attendanceRecords->isNotEmpty())
        <div class="overflow-x-auto overflow-y-auto rounded-lg bg-white" style="max-height: 300px;">
            <table class="table-auto w-full border-collapse shadow-lg rounded-lg">
                <thead class="sticky top-0 bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-3 border text-left">Date</th>
                        <th class="px-4 py-3 border text-left">Time In</th>
                        <th class="px-4 py-3 border text-left">Time Out</th>
                        <th class="px-4 py-3 border text-left">Status</th>
                    </tr>
                </thead>
                <tbody>           
                    @foreach($attendanceRecords as $record)
                        <tr class="hover:bg-gray-100 transition-colors">
                            <td class="px-4 py-3 border text-gray-800">{{ $record->date }}</td>
                            <td class="px-4 py-3 border text-gray-800">{{ $record->in_time ? $record->in_time->format('h:i A') : 'N/A' }}</td>
                            <td class="px-4 py-3 border text-gray-800">{{ $record->out_time ? $record->out_time->format('h:i A') : '' }}</td>
                            <td class="px-4 py-3 border text-gray-800 text-center">
                                @if ($record->status === 'Completed')
                                    <span class="hidden sm:inline px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                                        Completed
                                    </span>
                                    <span class="sm:hidden flex justify-center">
                                        ✅ <!-- Check icon -->
                                    </span>
                                @elseif ($record->status === 'Working')
                                    <span class="hidden sm:inline px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-semibold">
                                        Working
                                    </span>
                                    <span class="sm:hidden flex justify-center">
                                        ⏳ <!-- Loading icon -->
                                    </span>
                                @elseif ($record->status === 'Missing')
                                    <span class="hidden sm:inline px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm font-semibold">
                                        Missing
                                    </span>
                                    <span class="sm:hidden flex justify-center">
                                        ❓ <!-- Question mark icon -->
                                    </span>
                                @elseif ($record->status === 'Absent')
                                    <span class="hidden sm:inline px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        Absent
                                    </span>
                                    <span class="sm:hidden flex justify-center">
                                        ❌ <!-- X icon -->
                                    </span>
                                @else
                                    <span class="hidden sm:inline px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-sm font-semibold">
                                        Error
                                    </span>
                                    <span class="sm:hidden flex justify-center">
                                        ⚠️ <!-- Error icon -->
                                    </span>
                                @endif
                            </td>
                                                                              
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500 text-center mt-10">No attendance records available for this trainee.</p>
        @endif
    </div>
</div>
@endsection 
