@extends('layouts.app')
@include('components.header')
@section('content')

<div class="container mx-auto p-6 h-screen bg-blue-100">

  @if(session('success') || session('error'))
  <div id="flash-message" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-opacity-90 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transition-opacity duration-500"
       style="z-index: 9999; background-color: rgba(0, 0, 0, 0.8);">
       {{ session('success') ?? session('error') }}
  </div>
  @endif

  <div class="mb-6 flex">
      <h2 class="text-2xl font-bold">{{ $pageTitle }}</h2>
      <a href="{{route('coordinator.pre_users')}}" class="back flex justify-between ml-auto bg-red-500 text-white p-2 rounded-lg hover:bg-blue-600 hover:shadow-black">←GO BACK</a>
  </div>
  
  <div class="overflow-x-auto rounded-lg shadow-lg border border-black bg-white h-[450px] w-full">
      <table class="min-w-full table-auto">
          <thead class="bg-gray-800 text-white sticky top-0 z-10">
              <tr>
                  <th class="px-6 py-3 text-left text-sm font-medium uppercase tracking-wider min-w-[80px]">Document Type</th>
                  <th class="px-6 py-3 text-center text-sm font-medium uppercase tracking-wider min-w-[80px]">Status</th>
                  <th class="px-6 py-3 text-center text-sm font-medium uppercase tracking-wider min-w-[80px]">Action</th>
              </tr>
          </thead>
          <tbody id="table-body">
              @php
                  $requiredDocumentTypes = ['cor', 'medical', 'psa', 'insurance', 'consent', 'waiver', 'mdr', 'resume', 'bio_data', 'letter', 'clearance', 'philhealth'];
                  $documentLabels = [
                      'cor' => 'Certificate of Registration',
                      'medical' => 'Medical Certificate',
                      'psa' => 'Birth Certificate', 
                      'insurance' => 'Health Insurance',
                      'consent' => 'Parent Consent',
                      'waiver' => 'Waiver',
                      'mdr' => 'Member Data Record',
                      'resume' => 'Resume',
                      'bio_data' => 'Bio Data',
                      'letter' => 'Application Letter',
                      'clearance' => 'Police Clearance',
                      'philhealth' => 'PhilHealth ID',
                  ];
                  $allApproved = collect($requiredDocumentTypes)->every(function ($type) use ($user) {
                      $document = $user->documents->where('document_type', $type)->first();
                      return $document && $document->status === 'approved';
                  }); 
                  $hasDocuments = $user->documents->isNotEmpty();
              @endphp
              
              @foreach ($requiredDocumentTypes as $type)
              @php
                  $document = $user->documents->where('document_type', $type)->first();
                  $status = $document ? $document->status : 'not_uploaded';
              @endphp
              <tr class="border-b hover:bg-gray-200">
                  <td class="px-6 py-4 whitespace-normal break-words max-w-xs">{{ $documentLabels[$type] }}</td>
                  <td class="px-6 py-4 text-center">
                      <span class="px-3 py-1 rounded-lg text-white 
                          {{ $status === 'approved' ? 'bg-green-500' : 
                             ($status === 'pending' ? 'bg-orange-400' : 
                             ($status === 'rejected' ? 'bg-red-500' : 'bg-gray-300')) }}">
                          {{ ucfirst($status === 'not_uploaded' ? 'Not Uploaded' : $status) }}
                      </span>
                  </td>
                  <td class="px-6 py-4 text-center">
                      <div class="flex justify-center items-center">
                          @if ($document)
                              <a href="{{ $document->document_path }}" target="_blank" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                                  View Document
                              </a>
                              @if ($document->status !== 'approved')
                                  <button onclick="updateStatus('{{ $document->id }}', 'approved')" class="ml-2 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700">
                                      Approve
                                  </button>
                              @endif
                              @if ($document->status !== 'rejected')
                                  <button onclick="updateStatus('{{ $document->id }}', 'rejected')" class="ml-2 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">
                                      Reject
                                  </button>
                              @endif
                          @else
                              <span class="text-gray-500">No document uploaded</span>
                          @endif
                      </div>
                  </td>
              </tr>
              @endforeach
          </tbody>
      </table>
  </div>

  @if ($allApproved)
  <div class="mt-6 flex justify-center">
      <button onclick="openModal({{ $user->id }})" class="px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
          Mark as Trainee ✅
      </button>
  </div>

  <div id="confirmModal-{{ $user->id }}" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
      <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md relative">
          <h2 class="text-lg font-semibold text-gray-800 mb-4">Confirm Trainee Promotion</h2>
          <p class="text-gray-600 mb-6">Are you sure you want to mark <strong>{{ $user->first_name }} {{ $user->last_name }}</strong> as a trainee?</p>
          <form method="POST" action="{{ route('coordinator.promote', ['user' => $user->id]) }}">
              @csrf
              <div class="flex justify-end space-x-4">
                  <button type="button" onclick="closeModal({{ $user->id }})" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                  <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Confirm</button>
              </div>
          </form>
      </div>
  </div>
  @endif
</div>

<script>
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

    function updateStatus(documentId, status) {
        fetch(`/documents/update-status/${documentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ status: status })
        })
        .then(response => response.json())
        .then(data => {
            window.location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the document status.');
        });
    }
</script>

@endsection
