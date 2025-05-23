@extends('layouts.app')
@include('components.header')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')

@if(session('success') || session('error'))
    <div id="notification" class="fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold bg-black bg-opacity-80 transform transition-transform duration-300 ease-in-out">
        {{ session('success') ?? session('error') }}
    </div>

    <script>
        // Show notification
        const notification = document.getElementById('notification');
        
        // Fade out after 3 seconds
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    </script>
@endif

<!-- Confirmation Modal -->
<div id="confirmationModal" class="fixed inset-0 z-[9999] hidden" 
     role="dialog" 
     aria-labelledby="confirmationTitle"
     aria-modal="true"
     style="background: rgba(0, 0, 0, 0.5);">
    <div class="fixed inset-0 flex items-center justify-center">
        <div class="bg-white rounded-lg p-3 w-64 shadow-xl relative"
             role="document"
             tabindex="0">
            <h3 class="text-lg font-semibold text-gray-900 mb-2" id="confirmationTitle"></h3>
            <p class="text-sm text-gray-600 mb-2" id="confirmationMessage"></p>
            <p class="text-sm text-gray-800 font-medium mb-3" id="hoursMessage"></p>
            <div class="flex justify-end space-x-3">
                <form id="confirmationForm" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="w-20 h-8 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition duration-200"
                            id="confirmButton"
                            aria-label="Confirm action">
                        Approve
                    </button>
                </form>
                <button onclick="closeConfirmationModal()" 
                        class="w-20 h-8 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition duration-200"
                        aria-label="Cancel">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Attachment Modal -->
<div id="attachmentModal" class="fixed inset-0 z-[9998] hidden" style="background: rgba(0, 0, 0, 0.5);" onclick="handleModalClick(event)">
    <div class="fixed inset-0 flex items-center justify-center p-4" style="margin-top: 60px;">
        <div class="bg-white rounded-lg max-w-4xl w-full relative" onclick="event.stopPropagation()">
            <!-- Close button -->
            <button onclick="closeModal()" 
                    class="absolute right-4 top-4 bg-white rounded-full p-2 shadow-lg hover:bg-gray-100 transition-colors duration-200 z-50"
                    aria-label="Close modal">
                <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <!-- Modal content -->
            <div class="p-6">
                <h3 class="text-xl font-semibold mb-4" id="modalTitle">View Attachment</h3>
                
                <!-- Zoom controls -->
                <div class="flex justify-center gap-4 mb-4">
                    <button onclick="zoomIn()" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </button>
                    <button onclick="zoomOut()" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <button onclick="resetZoom()" class="bg-gray-200 p-2 rounded-full hover:bg-gray-300 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
                
                <!-- Image container with zoom functionality -->
                <div class="relative" style="height: 60vh;">
                    <div id="imageContainer" class="w-full h-full overflow-hidden">
                        <img id="modalImage" src="" alt="Attachment" 
                             class="w-full h-full object-contain transform transition-transform duration-200"
                             style="cursor: zoom-in;">
                    </div>
                </div>

                <!-- Action buttons container -->
                <div id="modalActions" class="flex justify-end space-x-4 mt-4">
                    <!-- Buttons will be inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<div class="p-6 max-w-6xl mx-auto">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 text-white py-4 px-6">
            <h2 class="text-xl font-semibold">Time In/Out Requests</h2>
        </div>

        <!-- Filter Buttons -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex space-x-4">
                <a href="{{ route('coordinator.requests', ['status' => 'pending']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $status === 'pending' ? 'bg-gray-300 text-black' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Pending Requests
                </a>
                <a href="{{ route('coordinator.requests', ['status' => 'all']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $status === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    All Requests
                </a>
                <a href="{{ route('coordinator.requests', ['status' => 'approved']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $status === 'approved' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Approved Requests
                </a>
                <a href="{{ route('coordinator.requests', ['status' => 'rejected']) }}" 
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 {{ $status === 'rejected' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    Rejected Requests
                </a>
            </div>
        </div>
        
        @if($status === 'pending')
            @php
                $filteredRequests = $requests->where('status', 'pending');
            @endphp
        @else
            @php
                $filteredRequests = $requests;
            @endphp
        @endif
        
        @if($filteredRequests->isNotEmpty())
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Trainee</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
            </table>
            <div class="overflow-y-auto pb-4" style="height: 300px;">
                <table class="w-full">
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($filteredRequests as $request)
                            @php
                                // Profile picture URL processing
                                $profileUrl = $request->user->profile_picture;
                                $profileFileId = null;

                                if (str_contains($profileUrl, 'id=')) {
                                    parse_str(parse_url($profileUrl, PHP_URL_QUERY), $query);
                                    $profileFileId = $query['id'] ?? null;
                                } elseif (preg_match('/\/d\/(.*?)\//', $profileUrl, $matches)) {
                                    $profileFileId = $matches[1];
                                }

                                $finalProfileUrl = $profileFileId ? "https://drive.google.com/thumbnail?id={$profileFileId}" : null;

                                // Attachment URL processing
                                $attachmentUrl = $request->image;
                                $attachmentFileId = null;

                                if ($attachmentUrl) {
                                    if (str_contains($attachmentUrl, 'id=')) {
                                        parse_str(parse_url($attachmentUrl, PHP_URL_QUERY), $query);
                                        $attachmentFileId = $query['id'] ?? null;
                                    } elseif (preg_match('/\/d\/(.*?)\//', $attachmentUrl, $matches)) {
                                        $attachmentFileId = $matches[1];
                                    }

                                    // Use thumbnail URL for Google Drive images
                                    $finalAttachmentUrl = $attachmentFileId 
                                        ? "https://drive.google.com/thumbnail?id={$attachmentFileId}&sz=w1000" 
                                        : $attachmentUrl;
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors" data-request-id="{{ $request->id }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($finalProfileUrl)
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ $finalProfileUrl }}" 
                                                     alt="{{ $request->user->first_name }}'s profile picture"
                                                     onerror="this.src='{{ asset('images/default-profile.png') }}'">
                                            @else
                                                <img class="h-10 w-10 rounded-full object-cover" 
                                                     src="{{ asset('images/default-profile.png') }}" 
                                                     alt="{{ $request->user->first_name }}'s profile picture">
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ Str::title($request->user->first_name . ' ' . $request->user->last_name) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $request->time_elapsed }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900" data-request-type>{{ ucfirst($request->type) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900" data-request-date>{{ $request->date }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900" data-request-time="{{ $request->time }}">{{ $request->time }}</span>
                                </td>
                                <td class="px-6 py-4" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $request->reason }}">
                                    <span class="text-sm text-gray-900">{{ $request->reason }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                           ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($request->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($request->image)
                                        <button onclick="openModal('{{ $finalAttachmentUrl }}', {{ $request->id }}, '{{ $request->status }}')" 
                                                class="text-blue-600 hover:text-blue-900">
                                            View Attachment
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
            <div class="text-center text-gray-500 py-8">
                No requests found.
            </div>
        @endif
    </div>
</div>

<script>
let currentZoom = 1;
const zoomStep = 0.2;
const maxZoom = 3;
const minZoom = 0.5;

function showNotification(message, type = 'success') {
    // Remove existing notification if any
    const existingNotification = document.getElementById('notification');
    if (existingNotification) {
        existingNotification.remove();
    }

    // Create new notification
    const notification = document.createElement('div');
    notification.id = 'notification';
    notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white text-lg font-semibold transform transition-all duration-300 z-[10000] ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    notification.style.opacity = '0';
    notification.textContent = message;

    // Add to document
    document.body.appendChild(notification);

    // Trigger animation
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 100);

    // Remove after delay
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function showConfirmationModal(action, requestId) {
    // First close the attachment modal
    closeModal();
    
    const modal = document.getElementById('confirmationModal');
    const title = document.getElementById('confirmationTitle');
    const message = document.getElementById('confirmationMessage');
    const hoursMessage = document.getElementById('hoursMessage');
    const form = document.getElementById('confirmationForm');
    const confirmButton = document.getElementById('confirmButton');
    
    // Get the request time from the table row
    const requestRow = document.querySelector(`tr[data-request-id="${requestId}"]`);
    const requestTime = requestRow.querySelector('[data-request-time]').getAttribute('data-request-time');
    const requestType = requestRow.querySelector('[data-request-type]').textContent.toLowerCase();
    const requestDate = requestRow.querySelector('[data-request-date]').textContent;
    const traineeName = requestRow.querySelector('.text-sm.font-medium.text-gray-900').textContent.trim();
    
    if (action === 'approve') {
        title.textContent = 'Approve Request';
        message.textContent = 'Are you sure you want to approve this request?';
        
        // Calculate time difference
        const requestDateTime = new Date(`${requestDate} ${requestTime}`);
        const regularTime = requestType === 'time_in' ? '08:00' : '17:00';
        const regularDateTime = new Date(`${requestDate} ${regularTime}`);
        const diffInSeconds = Math.abs(requestDateTime - regularDateTime) / 1000;
        
        // Convert to hours, minutes, seconds
        const hours = Math.floor(diffInSeconds / 3600);
        const minutes = Math.floor((diffInSeconds % 3600) / 60);
        const seconds = Math.floor(diffInSeconds % 60);
        
        // Build the time string
        let timeString = '';
        if (hours > 0) timeString += `${hours} hour${hours > 1 ? 's' : ''} `;
        if (minutes > 0) timeString += `${minutes} min${minutes > 1 ? 's' : ''} `;
        if (seconds > 0) timeString += `${seconds} sec${seconds > 1 ? 's' : ''}`;
        
        // Trim any extra spaces
        timeString = timeString.trim();
        
        hoursMessage.textContent = `Add ${timeString} to ${traineeName}'s total hours`;
        
        form.action = `/request/${requestId}/approve`;
        confirmButton.className = 'w-20 h-8 text-sm bg-green-500 text-white rounded hover:bg-green-600 transition duration-200';
        confirmButton.textContent = 'Approve';
        
        // Add form submit handler
        form.onsubmit = async (e) => {
            e.preventDefault();
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({})
                });

                if (response.ok) {
                    showNotification('Request approved successfully');
                    // Refresh the page after a short delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    showNotification('Failed to approve request', 'error');
                }
            } catch (error) {
                showNotification('An error occurred', 'error');
            }
            closeConfirmationModal();
        };
    } else {
        title.textContent = 'Reject Request';
        message.textContent = 'Are you sure you want to reject this request?';
        hoursMessage.textContent = '';
        form.action = `/request/${requestId}/reject`;
        confirmButton.className = 'w-20 h-8 text-sm bg-red-500 text-white rounded hover:bg-red-600 transition duration-200';
        confirmButton.textContent = 'Reject';
    }
    
    // Add a class to the body to prevent scrolling
    document.body.style.overflow = 'hidden';
    modal.classList.remove('hidden');
    
    const modalContent = document.querySelector('#confirmationModal [role="document"]');
    modalContent.focus();
    trapFocus(modalContent);
}

function closeConfirmationModal() {
    const modal = document.getElementById('confirmationModal');
    modal.classList.add('hidden');
    // Remove the body scroll lock
    document.body.style.overflow = '';
    // Reopen the attachment modal
    document.getElementById('attachmentModal').classList.remove('hidden');
}

function handleModalClick(event) {
    if (event.target.id === 'attachmentModal') {
        closeModal();
    }
}

function openModal(imageUrl, requestId, status) {
    const modal = document.getElementById('attachmentModal');
    const modalImage = document.getElementById('modalImage');
    const modalActions = document.getElementById('modalActions');
    
    // Set image source
    modalImage.src = imageUrl;
    
    // Reset zoom
    resetZoom();
    
    // Clear previous actions
    modalActions.innerHTML = '';
    
    // Add action buttons if status is pending
    if (status === 'pending') {
        modalActions.innerHTML = `
            <button onclick="showConfirmationModal('approve', ${requestId})" 
                    class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition duration-200">
                Approve
            </button>
            <button onclick="showConfirmationModal('reject', ${requestId})" 
                    class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition duration-200">
                Reject
            </button>
        `;
    }
    
    // Show modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevent scrolling when modal is open
}

function closeModal() {
    const modal = document.getElementById('attachmentModal');
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restore scrolling
    resetZoom(); // Reset zoom when closing
}

function zoomIn() {
    if (currentZoom < maxZoom) {
        currentZoom += zoomStep;
        updateZoom();
    }
}

function zoomOut() {
    if (currentZoom > minZoom) {
        currentZoom -= zoomStep;
        updateZoom();
    }
}

function resetZoom() {
    currentZoom = 1;
    updateZoom();
    // Reset position
    const modalImage = document.getElementById('modalImage');
    modalImage.style.transform = `scale(${currentZoom})`;
}

function updateZoom() {
    const modalImage = document.getElementById('modalImage');
    const imageContainer = document.getElementById('imageContainer');
    modalImage.style.transform = `scale(${currentZoom})`;
    
    // Add overflow control to container
    if (currentZoom > 1) {
        imageContainer.style.overflow = 'hidden';
    } else {
        imageContainer.style.overflow = 'visible';
    }
}

// Close confirmation modal when clicking outside
document.getElementById('confirmationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeConfirmationModal();
    }
});

// Close attachment modal when clicking outside
document.getElementById('attachmentModal').addEventListener('click', function(e) {
    // Check if the click is on the modal overlay (outside the modal content)
    if (e.target.id === 'attachmentModal') {
        closeModal();
    }
});

// Add keyboard navigation
document.addEventListener('keydown', function(e) {
    const attachmentModal = document.getElementById('attachmentModal');
    const confirmationModal = document.getElementById('confirmationModal');
    
    if (!attachmentModal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeModal();
        }
    } else if (!confirmationModal.classList.contains('hidden')) {
        if (e.key === 'Escape') {
            closeConfirmationModal();
        }
    }
});

// Trap focus within modal when open
function trapFocus(element) {
    const focusableElements = element.querySelectorAll('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
    const firstFocusableElement = focusableElements[0];
    const lastFocusableElement = focusableElements[focusableElements.length - 1];

    element.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstFocusableElement) {
                    lastFocusableElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastFocusableElement) {
                    firstFocusableElement.focus();
                    e.preventDefault();
                }
            }
        }
    });
}
</script>

@endsection
