@extends('layouts.app')
@include('components.header')

@section('content')
<!-- Main Container -->
<div class="h-screen bg-blue-100 flex flex-col p-5 overflow-auto">
    <div class="container mx-auto p-6 flex flex-col space-y-6">

        <!-- Dashboard Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-lg shadow-md">
            <h1 class="text-3xl font-bold">Dashboard</h1>
            <p class="text-sm mt-1">Welcome back! Here's what's happening today.</p>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Total Users Box -->
            <div class="dashboard-card bg-purple-500 text-white rounded-lg shadow-lg p-6 relative overflow-hidden">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-semibold">Total Users</h1>
                        <p class="text-sm mt-2">All registered users.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl font-bold">{{ $preUserCount + $traineeCount }}</p>
                        <i class="fas fa-user-friends text-3xl opacity-75"></i>
                    </div>
                </div>
            </div>

            <!-- Pre-Users Box -->
            <a class="dashboard-card bg-blue-500 text-white rounded-lg shadow-lg p-6 relative overflow-hidden cursor-pointer transition-all duration-500 transform hover:scale-110 hover:bg-blue-700"
               href="{{ route('coordinator.pre_users') }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-semibold">Pre-Users</h1>
                        <p class="text-sm mt-2">Manage and view Pre-Users.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl font-bold count" data-count="{{ $preUserCount }}">0</p>
                        <i class="fas fa-user-plus text-3xl opacity-75"></i>
                    </div>
                </div>
            </a>

            <!-- Trainees Box -->
            <a class="dashboard-card bg-green-500 text-white rounded-lg shadow-lg p-6 relative overflow-hidden cursor-pointer transition-all duration-500 transform hover:scale-110 hover:bg-green-700"
               href="{{ route('coordinator.trainees') }}">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-xl font-semibold">Trainees</h1>
                        <p class="text-sm mt-2">Manage and view Trainees.</p>
                    </div>
                    <div class="flex flex-col items-center">
                        <p class="text-5xl font-bold count" data-count="{{ $traineeCount }}">0</p>
                        <i class="fas fa-users text-3xl opacity-75"></i>
                    </div>
                </div>
            </a>
        </div>

    </div>
</div>

<!-- Number Animation -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const counters = document.querySelectorAll('.count');
        
        counters.forEach(counter => {
            let target = +counter.getAttribute('data-count');
            let count = 0;
            let increment = Math.ceil(target / 50);

            let updateCount = () => {
                if (count < target) {
                    count += increment;
                    if (count > target) count = target;
                    counter.innerText = count;
                    setTimeout(updateCount, 20);
                }
            };

            updateCount();
        });
    });
</script>

@endsection
