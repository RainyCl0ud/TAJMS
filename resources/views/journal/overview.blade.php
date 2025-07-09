<div class="bg-gray-100 p-4 sm:p-6 rounded-lg shadow-lg shadow-black border border-gray-200 w-full h-full">
    <div class="flex md:justify-between items-start mb-4">
        {{-- Heading with responsive text size --}}
        <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-semibold text-gray-800 mb-2 sm:mb-0">
            Journal
        </h2>
        {{-- "View All" button with smaller size, positioned at the top right --}}
        <div class="ml-auto mt-2 sm:mt-0">
            <a href="{{ route('journal.index') }}" 
               class="bg-blue-500 px-2 py-1 rounded text-white text-xs font-semibold shadow hover:bg-blue-600 transition">
                View All
            </a>
        </div>
    </div>

    {{-- Subtext with responsive text size --}}
    <p class="text-xs sm:text-sm text-gray-500 mb-4">&#x24D8; Recent journal entries</p>

    {{-- Table with responsive text size --}}
    <div class="overflow-x-auto rounded-lg">
        <table class="table-auto min-w-full border-collapse text-xs sm:text-sm md:text-base">
            <thead class="sticky top-0 bg-gray-800 text-white">
                <tr>
                    <th class="px-2 sm:px-4 py-2 text-left">Day</th>
                    <th class="px-2 sm:px-4 py-2 text-left">Content</th>
                    <th class="px-2 sm:px-4 py-2 text-left">Date</th>
                </tr> 
            </thead>
            <tbody>
                @forelse($journals->take(3) as $journal)
                    <tr class="hover:bg-gray-100 bg-white transition-colors cursor-default">
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 text-xs sm:text-sm">{{ $journal->day }}</td>
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 max-w-[100px] overflow-hidden text-ellipsis whitespace-nowrap text-xs sm:text-sm">
                            {{ $journal->content }}
                        </td>                        
                        <td class="px-2 sm:px-4 py-2 border border-black text-gray-800 text-xs sm:text-sm whitespace-nowrap">
                            {{ $journal->created_at->format('d-m-Y') }}
                        </td>
                    </tr>
                @empty 
                    <tr>
                        <td colspan="3" class="text-center text-gray-500 py-4">No journal entries available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
