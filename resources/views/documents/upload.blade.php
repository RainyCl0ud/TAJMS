@extends('layouts.app')
@include('components.header')
@section('content')

<div class="relative h-screen flex m-12 justify-center items-center overflow-hidden">
    <div class="upload max-w-3xl w-full bg-white p-10 rounded-lg shadow-lg shadow-gray-500 relative">
        <button onclick="window.history.back();" class="back absolute top-4 right-4 mt-3 bg-red-500 text-white p-2 rounded-lg">
            <span class="hidden sm:inline">BACK</span> <!-- Show 'BACK' on medium and larger screens -->
            <span class="sm:hidden bg:">✖</span> <!-- Show '✖' on small screens -->
        </button>
        
        @php
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

            $formattedType = isset($type) && is_string($type) ? ($documentLabels[$type] ?? ucwords(str_replace('_', ' ', $type))) : 'Unknown Document';
        @endphp
        <h1 class="text-2xl font-semibold mb-6 text-center text-gray-800">Upload {{ $formattedType }}</h1>
        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="type" value="{{ $type }}">

            <div class="mb-4">
                <label for="document" class="block text-sm font-medium text-gray-700">Choose File</label>
                <input type="file" name="document" id="document" required accept="image/*,application/pdf"
                    class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    onchange="previewDocument(event)">
            </div>

            <!-- Image Preview -->
            <div id="image-preview-container" class="mt-4 hidden">
                <img id="image-preview" class="w-full h-48 object-cover rounded-lg border" alt="Selected image preview">
            </div>

            <!-- PDF Preview -->
            <div id="pdf-preview-container" class="mt-4 hidden">
                <iframe id="pdf-preview" class="w-full h-48 border" src="" frameborder="0"></iframe>
            </div>

            @if(isset($existingDocumentUrl))
    <div class="mt-4">
        <h2 class="text-lg font-semibold">Uploaded Document</h2>
        <img src="{{ $existingDocumentUrl }}" class="w-full h-48 object-cover rounded-lg border" alt="Uploaded document preview">
    </div>
@endif


            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors duration-200">
                Upload
            </button>
        </form>
    </div>
</div>

<script>
    function previewDocument(event) {
        const file = event.target.files[0];
        const fileReader = new FileReader();

        if (file) {
            // Check if the file is an image or PDF
            const fileType = file.type;

            // Reset previews
            document.getElementById('image-preview-container').classList.add('hidden');
            document.getElementById('pdf-preview-container').classList.add('hidden');

            // Image preview
            if (fileType.startsWith('image')) {
                const imagePreview = document.getElementById('image-preview');
                document.getElementById('image-preview-container').classList.remove('hidden');
                fileReader.onload = function(e) {
                    imagePreview.src = e.target.result;
                };
                fileReader.readAsDataURL(file);
            }
            // PDF preview
            else if (fileType === 'application/pdf') {
                const pdfPreview = document.getElementById('pdf-preview');
                document.getElementById('pdf-preview-container').classList.remove('hidden');
                pdfPreview.src = URL.createObjectURL(file);
            }
        }
    }
</script>

@endsection
