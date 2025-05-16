@extends('layouts.app')
@include('components.header')

@section('content')
<div class="bg-blue-100 flex flex-col p-10 overflow-y-auto max-h-screen pb-20">

    <!-- Back Button -->
    <div class="mb-6 flex justify-end">
        <a href="{{ route('coordinator.pre_users') }}" 
            class="px-4 py-2 bg-red-600 text-white hover:bg-blue-600 hover:shadow-black rounded-lg shadow-md flex items-center">
            ← Go Back
        </a>
    </div>
 
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-20">

        @foreach ([
            'cor', 'medical', 'psa', 'insurance', 'consent', 
            'waiver', 'mdr', 'resume', 'bio_data', 'letter', 'clearance', 'philhealth',
        ] as $type)
            @php
                $document = $user->documents->where('document_type', $type)->first();
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
                $formattedType = $documentLabels[$type] ?? ucwords(str_replace('_', ' ', $type));

                $docPath = $document?->document_path;
                $isDrive = $docPath && str_contains($docPath, 'drive.google.com');
                $fileId = null;

                if ($isDrive) {
                    if (str_contains($docPath, 'id=')) {
                        parse_str(parse_url($docPath, PHP_URL_QUERY), $query);
                        $fileId = $query['id'] ?? null;
                    } elseif (preg_match('/\/d\/(.*?)\//', $docPath, $matches)) {
                        $fileId = $matches[1];
                    }
                }

                $viewerPath = $isDrive
                    ? ($fileId ? "https://drive.google.com/file/d/{$fileId}/preview" : '')
                    : ($docPath ? asset('storage/' . $docPath) : '');
            @endphp

            <div class="block rounded-lg shadow-lg p-6 border border-black text-center
                {{ $document && $document->status === 'approved' ? 'bg-green-400' : 
                   ($document && $document->status === 'rejected' ? 'bg-red-400' : 
                   ($document && $document->status === 'pending' ? 'bg-yellow-200' : 'bg-gray-200')) }}">

                <p class="text-sm text-black mb-2">
                    {{ $document ? ($document->status === 'approved' ? '✅ Approved' : ($document->status === 'rejected' ? '❌ Rejected' : '⏳ Pending...')) : 'No document uploaded' }}
                </p>

                <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ $formattedType }}</h3>

                <button 
                    @if ($document && $viewerPath)
                        onclick="openModal('{{ $viewerPath }}', '{{ $formattedType }}', '{{ $document->id }}', '{{ $document->status }}')"
                    @else
                        disabled
                    @endif
                    class="mt-3 px-4 py-2 bg-blue-500 text-white rounded-lg shadow-md hover:bg-blue-600 hover:shadow-black {{ !$document ? 'opacity-50 cursor-not-allowed' : '' }}">
                    View Document
                </button>
            </div>
        @endforeach
    </div>

    <!-- Document Preview Modal -->
    <div id="previewModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg shadow-lg max-w-lg sm:max-w-2xl lg:max-w-4xl w-full p-6 relative">
            <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-xl">
                ✖
            </button>
            <h2 id="modalTitle" class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4 text-center"></h2>
            <iframe id="documentViewer" class="w-full h-72 sm:h-80 bg-gray-100 rounded-lg overflow-auto" frameborder="0" allowfullscreen></iframe>
            
            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2">
                <button id="approveButton" onclick="updateDocumentStatus('approved')" class="px-4 py-2 bg-green-600 text-white rounded-lg shadow-md w-full sm:w-auto">
                    Approve
                </button>
                <button id="rejectButton" onclick="updateDocumentStatus('rejected')" class="px-4 py-2 bg-red-600 text-white rounded-lg shadow-md w-full sm:w-auto">
                    Reject
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentDocumentId;

    function openModal(documentPath, documentTitle, documentId, status = '') {
        if (!documentPath || !documentId) {
            alert("No document available.");
            return;
        }

        currentDocumentId = documentId;
        document.getElementById('modalTitle').textContent = documentTitle;
        document.getElementById('documentViewer').src = documentPath;

        document.getElementById('previewModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('previewModal').classList.add('hidden');
        document.getElementById('documentViewer').src = ''; // Reset iframe
    }

    function updateDocumentStatus(status) {
        fetch(`/documents/update-status/${currentDocumentId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
            body: JSON.stringify({ status }),
        }).then(response => {
            if (response.ok) {
                location.reload();
            } else {
                alert('Failed to update document.');
            }
        }).catch(error => {
            console.error("Error updating document:", error);
        });
    }
</script>
@endsection
