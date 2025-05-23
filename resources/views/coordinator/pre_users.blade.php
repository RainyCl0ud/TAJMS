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
  
  <div class="overflow-x-auto rounded-lg shadow-lg border border-black bg-white h-[450px] w-full table-container">
      <table class="min-w-full table-auto">
          <thead class="bg-gray-800 text-white sticky top-0 z-20">
              <tr>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider min-w-[80px]">Student Name</th>
                  <th class="px-6 py-3 text-center text-sm font-medium uppercase tracking-wider min-w-[80px]">Status</th>
                  <th class="px-6 py-3 text-center text-sm font-medium uppercase tracking-wider min-w-[80px]">Action</th>
              </tr>
          </thead>
          <tbody id="table-body">
              @foreach ($preUsers as $user)
              @php
                  $requiredDocumentTypes = ['cor', 'medical', 'psa', 'insurance', 'consent', 'waiver', 'mdr', 'resume', 'bio_data', 'letter', 'clearance', 'philhealth'];
                  $uploadedDocumentTypes = $user->documents->pluck('document_type')->toArray();
                  $allApproved = collect($requiredDocumentTypes)->every(function ($type) use ($user) {
                      $document = $user->documents->where('document_type', $type)->first();
                      return $document && $document->status === 'approved';
                  }); 
                  $hasDocuments = $user->documents->isNotEmpty();
              @endphp
              <tr class="border-b cursor-pointer hover:bg-gray-200" onclick="window.location='{{ route('coordinator.user.documents', ['user' => $user->id]) }}'" data-name="{{ strtolower($user->first_name . ' ' .$user->last_name ) }}">
                  <td class="px-6 py-4 whitespace-normal break-words max-w-xs">{{ $user->first_name}} {{$user->last_name}}</td>
                  <td class="px-6 py-4 text-center">
                      <span class="px-3 py-1 rounded-lg text-white {{ $allApproved ? 'bg-green-500' : ($hasDocuments ? 'bg-gray-400' : 'bg-gray-300') }}">
                          {{ $allApproved ? 'Completed' : ($hasDocuments ? 'Pending' : 'No Documents') }}
                      </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                      <div class="flex justify-center items-center space-x-2">
                          @if ($allApproved)
                              <!-- Button to open confirmation modal -->
                              <button type="button" onclick="event.stopPropagation(); openModal({{ $user->id }})" class="flex justify-center items-center">
                                  <span class="hidden sm:inline px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-200 cursor-pointer">
                                      Mark as Trainee ✅
                                  </span>
                                  <span class="sm:hidden text-blue-500 cursor-pointer">✅</span>
                              </button>

                              <!-- Confirmation Modal -->
                              <div id="confirmModal-{{ $user->id }}" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
                                  <div class="bg-white rounded-lg p-6 w-11/12 max-w-md shadow-lg relative">
                                      <h2 class="text-lg font-semibold mb-4">Confirm Promotion</h2>
                                      <p>Are you sure you want to promote <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> to Trainee?</p>
                                      <div class="mt-6 flex justify-end space-x-4">
                                          <button onclick="closeModal({{ $user->id }})" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">Cancel</button>

                                          <form method="POST" action="{{ route('coordinator.promote', ['user' => $user->id]) }}">
                                              @csrf
                                              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Yes, Promote</button>
                                          </form>
                                      </div>
                                      <button onclick="closeModal({{ $user->id }})" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">&times;</button>
                                  </div>
                              </div>
                          @elseif ($hasDocuments)
                              <button class="px-4 py-2 bg-orange-400 text-white rounded cursor-default">
                                  Pending ⏳
                              </button>
                          @else
                              <button class="px-4 py-2 text-white rounded cursor-not-allowed" disabled>❌</button>
                          @endif
                      </div>
                  </td>
              </tr>
              @endforeach
              @if ($preUsers->isEmpty())
                  <tr>
                      <td colspan="3" class="text-center py-4 text-gray-500">No Pre-Users found.</td>
                  </tr>
              @endif
          </tbody>
      </table>
  </div>
</div>

<script>
    document.getElementById('search').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('#table-body tr');
        rows.forEach(row => {
            const studentName = row.getAttribute('data-name');
            row.style.display = studentName.includes(searchTerm) ? '' : 'none';
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500);
            }, 3000);
        }
    });

    function openModal(userId) {
        document.getElementById('confirmModal-' + userId).classList.remove('hidden');
    }

    function closeModal(userId) {
        document.getElementById('confirmModal-' + userId).classList.add('hidden');
    }
</script>

@endsection
