@extends('layouts.app')
@include('components.header')

@section('content')
<style>
    .hours-card {
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        gap: 1.5rem;
    }
    .hours-section {
        flex: 1 1 0;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        min-width: 0;
    }
    .hours-label {
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 0.25rem;
    }
    .hours-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.2rem;
    }
    .progress-bar {
        width: 100%;
        height: 0.5rem;
        border-radius: 0.25rem;
        background: #e5e7eb;
        margin-top: 0.2rem;
        overflow: hidden;
    }
    .progress-bar-inner {
        height: 100%;
    }
    @media (max-width: 640px) {
        .hours-card {
            flex-direction: column;
            gap: 0.75rem;
            padding: 1rem;
        }
        .hours-value {
            font-size: 1.3rem;
        }
    }
</style>

<div class="max-h-screen bg-blue-100 flex flex-col p-3 sm:p-5">
    <!-- Flash Messages -->
    @if(session('success') || session('error'))
    <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-4 py-2 rounded-lg shadow text-white text-base font-semibold transition-opacity duration-500"
        style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
        {{ session('success') ?? session('error') }}
    </div>
    @endif

    <div class="flex-1 overflow-y-auto p-2 sm:p-4 max-h-[calc(100vh-64px)] sm:max-h-[calc(100vh-80px)]">
        {{-- Hours Tracking Card --}}
        <div class="hours-card">
            <div class="hours-section">
                <span class="hours-label">Accumulated Hours</span>
                <span class="hours-value text-green-600">{{ $totalHours }}h & {{ $totalMins }}m</span>
                <div class="progress-bar">
                    <div class="progress-bar-inner bg-green-600" style="width: {{ min(100, ($totalHours * 60 + $totalMins) / (438 * 60) * 100) }}%"></div>
                </div>
            </div>
            <div class="hours-section">
                <span class="hours-label">Remaining Hours</span>
                <span class="hours-value text-red-500">{{ $remainingHours }}h & {{ $remainingMins }}m</span>
                <div class="progress-bar">
                    <div class="progress-bar-inner bg-red-500" style="width: {{ min(100, ($remainingHours * 60 + $remainingMins) / (438 * 60) * 100) }}%"></div>
                </div>
            </div>
        </div>

        {{-- Grid Container for Overviews --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-2">
            {{-- Journal Overview --}}
            <div class="mb-3">
                @include('journal.overview')
            </div>
            {{-- Attendance Overview --}}
            <div class="mb-3">
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
