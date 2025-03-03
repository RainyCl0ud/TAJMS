@extends('layouts.app')
@include('components.header')
@section('content')
<div class="overflow-y-auto max-h-[100vh] sm:max-h-screen px-2 sm:px-4 flex-wrap bg-blue-100 min-h-screen">
<div class="container mx-auto px-6 py-8 min-h-screen overflow-auto mb-10">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
        @foreach ([
            'cor', 'medical', 'psa', 'insurance', 'consent', 
            'waiver', 'mdr', 'resume', 'bio_data', 'letter', 'clearance', 'philhealth',
        ] as $type)
        
            @php
                $document = App\Models\Document::where('user_id', Auth::id())
                    ->where('document_type', $type)
                    ->first();
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
            @endphp
            
            <!-- Document Upload Box -->
            <a href="javascript:void(0);" onclick="openModal('{{ $type }}')"
               class="block rounded-lg mb-5 shadow-lg border border-black transition-all duration-300 hover:scale-105 p-6
               {{ $document && $document->status === 'approved' ? 'bg-green-400' :
                  ($document && $document->status === 'rejected' ? 'bg-red-400' :
                  ($document && $document->status === 'pending' ? 'bg-yellow-200' : 'bg-gray-100')) }}">
        
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ $formattedType }}</h3>
                    @if ($document)
                        <p class="text-sm text-black">
                            @if ($document->status === 'approved')
                                ✅ Approved
                            @elseif ($document->status === 'rejected')
                                ❌ Rejected
                            @else
                                ⏳ Pending Review
                            @endif
                        </p>
                    @else
                        <p class="text-sm text-gray-600">Click to upload document</p>
                    @endif
                </div>
            </a>

            <!-- Modal for uploaded document (Always Rendered) -->
            <div id="modal-{{ $type }}" class="modal fixed inset-0 bg-gray-900 bg-opacity-50 hidden flex justify-center items-center z-[999]">
                <div class="modal-content bg-white p-6 rounded-lg">
                    <h2 class="text-2xl font-semibold mb-4">{{ $formattedType }}</h2>
                    @if ($document && $document->document_path)
                        @if (str_ends_with($document->document_path, '.pdf'))
                            <iframe src="{{ asset('storage/' . $document->document_path) }}" class="w-full h-80" frameborder="0"></iframe>
                        @else
                            <img src="{{ asset('storage/' . $document->document_path) }}" class="w-full h-80 object-cover rounded-lg" alt="Uploaded document">
                        @endif
                    @else
                        <p class="text-gray-500">No document uploaded yet.</p>
                    @endif
                    <div class="flex justify-end space-x-4 mt-4">
                        <button onclick="closeModal('{{ $type }}')" class="px-4 py-2 bg-red-500 text-white rounded">Cancel</button>
                        <a href="{{ route('documents.upload', ['type' => $type]) }}" class="px-4 py-2 bg-blue-500 text-white rounded">Upload New File</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
   function openModal(type) {
    document.getElementById('modal-' + type).classList.remove('hidden');
    document.getElementById('sidebar').classList.add('z-0'); // Lower sidebar z-index
}

function closeModal(type) {
    document.getElementById('modal-' + type).classList.add('hidden');
    document.getElementById('sidebar').classList.remove('z-0'); // Restore sidebar z-index
}

</script>
</div>
@endsection
