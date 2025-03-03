@extends('layouts.app')
@include('components.header')

@section('content')
<div class="overflow-y-auto max-h-[100vh] sm:max-h-screen px-2 sm:px-4 flex-wrap bg-blue-100 min-h-screen">

    <!-- Flash Messages -->
    @if(session('success') || session('error'))
    <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
        style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
        {{ session('success') ?? session('error') }}
    </div>
    @endif

    <div class="container w-full px-6 py-6">
        <div class="flex flex-col lg:flex-row gap-6 items-start">

            <!-- Take Attendance Section -->
            <div class="w-full">
                <div class="bg-white p-6 rounded-lg border border-gray-400 shadow-lg shadow-black sm:h-[32rem]">
                    <div class="text-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-800">Take Attendance</h1>
                        <p class="text-gray-500 mt-1 text-lg">Date: 
                            <span class="font-semibold">{{ now()->format('m / d / Y') }}</span>
                        </p>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="relative mb-6">
                            <video id="video" class="w-56 h-56 object-cover rounded-full border-4 border-gray-500" style="display: none;" autoplay></video> 
                            <canvas id="canvas" class="absolute top-0 left-0 w-56 h-56 object-cover rounded-full border-4 border-gray-500" style="display: none;"></canvas>
                        </div>
                        <div class="flex flex-col space-y-4">
                            <button id="startCameraButton" 
                            class="bg-green-500 text-white py-2 px-4 md:px-6 rounded-lg shadow hover:bg-green-600 transition w-full sm:w-auto text-sm md:text-base min-w-[120px]">
                            Start Camera
                            </button>
                            <form id="attendanceForm" action="{{ route('attendance.mark') }}" method="POST" style="display: none;">
                                @csrf
                                <input type="hidden" name="student_id" value="{{ auth()->id() }}">
                                <input type="hidden" name="image_data" id="image_data">
                                <button type="submit" id="captureButton" 
                                class="bg-blue-500 text-white py-2 px-4 md:px-6 rounded-lg shadow hover:bg-blue-600 transition w-full sm:w-auto text-sm md:text-base min-w-[120px]">
                                Take Photo
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Records Table -->
            <div class="w-full flex-grow">
                <div class="bg-white p-4 rounded-lg border border-gray-400 shadow-lg shadow-black overflow-hidden min-h-[32rem]">
                    <div class="flex flex-wrap justify-between items-center gap-3 w-full">
                        <h2 class="text-xs sm:text-sm md:text-lg font-semibold text-gray-800 text-center sm:text-left">
                            Attendance Records
                        </h2>
                        <button onclick="previewPdf()" 
                            class="px-2 sm:px-3 md:px-4 py-1 sm:py-1.5 text-xs sm:text-sm md:text-base 
                                bg-red-500 text-white rounded-lg hover:bg-red-800 transition 
                                w-[7rem] sm:w-[8rem] md:w-[9rem]">
                            Preview PDF
                        </button>
                    </div>
                    <p class="overV mb-5">&#x24D8; Recent attendance records</p>
                    <div class="overflow-x-auto">
                        <div class="overflow-y-auto max-h-[18rem] sm:max-h-[22rem] border border-black">
                            <table class="min-w-full table-auto border-collapse text-xs sm:text-sm mb-0">
                                @if ($attendanceRecords->isNotEmpty())
                                    <thead class="bg-gray-800 text-white text-left sticky top-0">
                                        <tr>
                                            <th class="px-2 py-2 border w-1/4">Date</th>
                                            <th class="px-2 py-2 border w-1/4">In</th>
                                            <th class="px-2 py-2 border w-1/4">Out</th>
                                            <th class="px-2 py-2 border w-1/6 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-700">
                                        @foreach ($attendanceRecords as $record)
                                            <tr class="hover:bg-gray-100">
                                                <td class="px-2 py-2 border">{{ $record->date }}</td>
                                                <td class="px-2 py-2 border">{{ $record->in_time ? $record->in_time->format('h:i A') : '--' }}</td>
                                                <td class="px-2 py-2 border">{{ $record->out_time ? $record->out_time->format('h:i A') : '--' }}</td>
                                                <td class="px-2 py-2 border text-center">
                                                    @if ($record->status === 'Completed')
                                                        <span class="px-2 py-1 text-green-800 rounded-full text-xs font-semibold">✔</span>
                                                    @elseif ($record->status === 'Working')
                                                        <span class="px-2 py-1 text-yellow-800 rounded-full text-xs font-semibold">⌛</span>
                                                    @elseif ($record->status === 'Absent')
                                                        <span class="px-2 py-1 text-red-500 rounded-full text-xs font-semibold">Absent</span>
                                                    @else
                                                        <span class="px-2 py-1 text-red-500 rounded-full text-xs font-semibold">Missing</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                @else
                                    <tbody>
                                        <tr>
                                            <td colspan="4" class="text-center text-gray-500 py-4">No attendance records found.</td>
                                        </tr>
                                    </tbody>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
      function previewPdf() {
        fetch("{{ route('attendance.preview-pdf') }}")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.url) {
                    window.open(data.url, '_blank');
                } else {
                    console.error("PDF URL not received");
                }
            })
            .catch(error => console.error("Error loading PDF:", error));
    }
    </script>

    <script src="{{ mix('js/attendance.js') }}"></script>
</div>
@endsection
