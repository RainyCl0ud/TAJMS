<div class="bg-gray-100 p-4 sm:p-6 rounded-lg shadow-lg border border-gray-200 shadow-black shadow-lg w-full h-full">
    <div class="flex md:justify-between items-start mb-4">
        {{-- Heading with responsive text size --}}
        <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-semibold text-gray-800 mb-2 sm:mb-0">
            Attendance
        </h2>
        {{-- "View All" button with smaller size, positioned at the top right --}}
        <div class="ml-auto mt-2 sm:mt-0">
            <a href="{{ route('attendance.create') }}" 
               class="bg-blue-500 px-2 py-1 rounded text-white text-xs font-semibold shadow hover:bg-blue-600 transition whitespace-nowrap">
                View All
            </a>
        </div>
    </div>

    {{-- Subtext with responsive text size --}}
    <p class="text-xs sm:text-sm text-gray-500 mb-4">&#x24D8; Recent attendance records</p>

    {{-- Table with responsive text size --}}
    <div class="overflow-x-auto rounded-lg table-container">
        <table class="table-auto min-w-full border-collapse text-xs sm:text-sm md:text-base">
            <thead class="sticky top-0 bg-gray-800 text-white z-20">
                <tr>
                    <th class="px-2 sm:px-4 py-2 text-left">Day</th>
                    <th class="px-2 sm:px-4 py-2 text-left">Date</th>
                    <th class="px-2 sm:px-4 py-2 text-left">Status</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($attendances->take(3) as $attendance)
                    <tr class="hover:bg-gray-100 bg-white transition-colors cursor-default">
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 text-xs sm:text-sm">{{ $attendance->day }}</td>
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 text-xs sm:text-sm">{{ $attendance->date }}</td>
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 whitespace-nowrap text-xs sm:text-sm">
                            @if($attendance->status === 'Completed')
                                <span class="px-2 py-1 bg-green-400 text-green-800 rounded-lg text-xs font-semibold">Present</span>
                            @elseif($attendance->status === 'Working')
                                <span class="px-2 py-1 bg-yellow-200 text-yellow-800 rounded-lg text-xs font-semibold">Working</span>
                            @elseif($attendance->status === 'Missing')
                                <span class="px-2 py-1 bg-red-400 text-red-800 rounded-lg text-xs font-semibold">Missing</span>
                            @else
                                <span class="px-2 py-1 bg-red-400 text-red-800 text-xs font-semibold">Error</span>
                            @endif
                        </td>
                    </tr>
                @empty 
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-4">No attendance record yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
