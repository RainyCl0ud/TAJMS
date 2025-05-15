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
                </tr>
                @endforeach
                @if ($journals->isEmpty())
                <tr>
                    <td colspan="3" class="text-center py-4 text-gray-500">
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
    window.open("{{ route('journal.preview-pdf') }}", '_blank');
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
