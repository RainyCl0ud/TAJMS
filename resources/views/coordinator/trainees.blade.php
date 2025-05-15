@extends('layouts.app')
@include('components.header')
@section('content')
<div class="h-screen bg-blue-100 p-8 overflow-auto">
    
    @if (session('success'))
        <div class="mb-4 p-4 bg-green-200 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @elseif (session('error'))
        <div class="mb-4 p-4 bg-red-200 text-red-800 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6 flex">
        <input 
            type="text" 
            id="search" 
            placeholder="Search..." 
            class="px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-1/2"/>
        <a href="{{ route('coordinator.dashboard') }}" class="back flex justify-between ml-auto bg-red-500 text-white p-2 rounded-lg">←GO Back</a>
    </div>
    
    <div class="rounded-lg shadow-lg bg-white border border-black max-h-[25rem] overflow-hidden relative z-10">
        <div class="overflow-x-auto max-w-full">
            <table class="min-w-full">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider whitespace-normal truncate">
                            Student ID
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider whitespace-normal truncate">
                            Student Name
                        </th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider whitespace-normal truncate">
                            Email
                        </th>
                    </tr>
                </thead>
            </table>
            <div class="overflow-y-auto" style="max-height: calc(25rem - 3rem);">
                <table class="min-w-full">
                    <tbody id="table-body">
                        @foreach ($trainees as $user)
                        <tr class="border-b cursor-pointer hover:bg-gray-100"
                            onclick="window.location.href='{{ route('coordinator.trainee-records', ['traineeId' => $user->id, 'from' => request()->fullUrl()]) }}'"
                            data-search="{{ strtolower($user->trainee->student_id . ' ' . $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name . ' ' . $user->email) }}">
                            <td class="px-4 py-2 whitespace-nowrap" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $user->trainee->student_id }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap truncate max-w-xs" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $user->first_name . ' ' . $user->middle_name . ' ' . $user->last_name }}
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap truncate max-w-xs" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $user->email }}
                            </td>
                        </tr>
                        @endforeach
                        @if ($trainees->isEmpty())
                            <tr>
                                <td colspan="3" class="text-center py-4 text-gray-500">
                                    No Trainees found.
                                </td>
                            </tr>
                        @endif
                    </tbody>                             
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#table-body tr');
        rows.forEach(row => {
            const dataSearch = row.getAttribute('data-search');
            if (dataSearch && dataSearch.includes(searchTerm)) {
                row.style.display = ''; 
            } else {
                row.style.display = 'none'; 
            }
        });
    });
</script>

<style>
    /* Ensure table container stays below dropdown */
    .table-container {
        z-index: 10;
    }
</style>
@endsection
