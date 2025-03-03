@extends('layouts.app')
@include('components.header')

@section('content')
<div class="max-h-screen bg-blue-100 flex flex-col p-5">

    <!-- Flash Messages -->
    @if(session('success') || session('error'))
    <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
        style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
        {{ session('success') ?? session('error') }}
    </div>
    @endif

    {{-- Scrollable Content Container --}}
    <div class="flex-1 overflow-y-auto p-5 max-h-[calc(100vh-64px)] sm:max-h-[calc(100vh-80px)]">
        {{-- Grid Container --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Journal Overview --}}
            <div class="mb-5">
                @include('journal.overview')
            </div>
        
            {{-- Attendance Overview --}}
            <div class="mb-5">
                @include('attendance.overview')
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500); // Remove after fade out
            }, 3000); // Show for 3 seconds
        }
    });
</script>
@endsection
