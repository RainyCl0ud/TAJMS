@extends('layouts.app')
@include('components.header')
@section('content')

<div class="container mx-auto p-6 h-screen bg-blue-100">


  <!-- Flash Messages -->
@if(session('success') || session('error'))
<div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
     style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
     {{ session('success') ?? session('error') }}
</div>
@endif

    <div class="mb-6 flex">
        <input type="text" id="search" placeholder="Search..." class="px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-400 w-1/2"/>
        <a href="{{route('coordinator.dashboard')}}" class="back flex justify-between ml-auto bg-red-500 text-white p-2 rounded-lg hover:bg-blue-600 hover:shadow-black">←GO BACK</a>
    </div>
    <div class="overflow-hidden rounded-lg shadow-lg border border-black bg-white">
        <div class="cont overflow-y-auto">
            <table class="min-w-full">
                <thead class="bg-gray-800 text-white sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Student Name</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody id="table-body">
                    @foreach ($preUsers as $user)
                    @php
                    // Define the required document types
                    $requiredDocumentTypes = ['cor', 'medical_records', 'birth_certificate', 'assurance', 'company_paper'];
                
                    // Get the uploaded documents' types for the user
                    $uploadedDocumentTypes = $user->documents->pluck('document_type')->toArray();
                
                    // Check if all required documents are uploaded and approved
                    $allApproved = collect($requiredDocumentTypes)->every(function ($type) use ($user) {
                        $document = $user->documents->where('document_type', $type)->first();
                        return $document && $document->status === 'approved';
                    }); 
                
                    $hasDocuments = $user->documents->isNotEmpty();
                @endphp
                <tr 
                    class="border-b cursor-pointer hover:bg-gray-200"
                    onclick="window.location='{{ route('coordinator.user.documents', ['user' => $user->id]) }}'"
                    data-name="{{ strtolower($user->first_name . ' ' .$user->last_name ) }}">
                    <td class="px-6 py-4 whitespace-nowrap truncate max-w-xs" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">{{ $user->first_name}} {{$user->last_name}}</td>
                    <td class="px-6 py-4 whitespace-nowrap truncate max-w-xs" style="max-width: 50px; overflow: hidden; text-overflow: ellipsis;">
                        <span class="px-3 py-1 rounded-lg text-white {{ $allApproved ? 'bg-green-500' : ($hasDocuments ? 'bg-gray-400' : 'bg-gray-300') }}">
                            {{ $allApproved ? 'Completed' : ($hasDocuments ? 'Pending' : 'No Documents') }}
                        </span>
                    </td>
                    
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        @if ($allApproved)
                            <button  onclick="openModal({{ $user->id }})" class="flex justify-center items-center">
                                <span class="hidden sm:inline px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                                    Mark as Trainee ✅
                                </span>
                                <span class="sm:hidden text-blue-500">
                                    ✅
                                </span>
                            </button>
                    
                          
                        @elseif ($hasDocuments)
                            <div class="flex justify-center items-center">
                                <span class="hidden sm:inline">
                                    <button class="px-4 py-2 bg-orange-400 text-white rounded cursor-default max-w-full sm:max-w-[120px] overflow-hidden text-ellipsis">
                                        Pending ⏳
                                    </button>
                                </span>
                                <span class="sm:hidden text-orange-400">
                                    ⏳
                                </span>
                            </div>
                        @else
                            <button class="px-4 py-2 text-white rounded cursor-not-allowed" disabled>
                                ❌
                            </button>
                        @endif
                    </td>
                    
                    
                    
                </tr>
                               
                    @endforeach
                    @if ($preUsers->isEmpty())
                        <tr>
                            <td colspan="3" class="text-center py-4 text-gray-500">
                                No Pre-Users found.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
  <!-- Modal -->
  <div id="confirmModal-{{ $user->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
        <h2 class="text-xl font-bold mb-4">Confirm Action</h2>
        <p>Are you sure you want to mark <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> as a Trainee?</p>
        <div class="flex justify-end mt-4">
            <button onclick="closeModal({{ $user->id }})" class="mr-2 px-4 py-2 bg-gray-400 text-white rounded hover:bg-gray-500 cursor-pointer">Cancel</button>
            <form action="{{ route('coordinator.promote', $user) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 cursor-pointer">Confirm</button>
            </form>
        </div>
    </div>
</div>
    <script>
        document.getElementById('search').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#table-body tr');
            
            rows.forEach(row => {
                const studentName = row.getAttribute('data-name');
                
                if (studentName.includes(searchTerm)) {
                    row.style.display = ''; // Show the row
                } else {
                    row.style.display = 'none'; // Hide the row
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500); // Remove after fade out
            }, 3000); // Show for 3 seconds
        }
    });

    function openModal(userId) {
    event.stopPropagation();
    document.getElementById('confirmModal-' + userId).classList.remove('hidden');
}


    function closeModal(userId) {
        document.getElementById('confirmModal-' + userId).classList.add('hidden');
    }
    </script>
@endsection
