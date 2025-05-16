<!-- Request Details Modal -->
<div id="request-details-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg w-full max-w-2xl mx-4 overflow-hidden">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Request Details</h2>
                <button onclick="document.getElementById('request-details-modal').classList.add('hidden')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="request-details-content"></div>

            <!-- Modal Actions -->
            <div id="request-actions" class="mt-6 flex justify-end space-x-4"></div>
        </div>
    </div>
</div>

<script>
function showRequestModal(request) {
    const modal = document.getElementById('request-details-modal');
    const content = document.getElementById('request-details-content');
    const actions = document.getElementById('request-actions');

    // Handle profile picture (Google Drive-compatible)
    let profileUrl = request.user.profile_picture || '';
    let fileId = null;

    if (profileUrl.includes('id=')) {
        const urlParams = new URLSearchParams(profileUrl.split('?')[1]);
        fileId = urlParams.get('id');
    } else {
        const match = profileUrl.match(/\/d\/(.*?)\//);
        fileId = match ? match[1] : null;
    }

    const imageUrl = fileId
        ? `https://drive.google.com/thumbnail?id=${fileId}`
        : '{{ asset('storage/profile_pictures/default.png') }}';

    // Populate modal content
    content.innerHTML = `
        <div class="flex items-center mb-6">
            <img src="${imageUrl}" 
                 alt="Profile Picture" 
                 class="w-16 h-16 rounded-full mr-4 object-cover border border-gray-300"
                 onerror="this.src='{{ asset('storage/profile_pictures/default.png') }}'">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">${request.user.first_name} ${request.user.last_name}</h2>
                <p class="text-gray-500">${request.time_elapsed}</p>
            </div>
        </div>
        <div class="space-y-4">
            <p class="text-gray-700"><span class="font-semibold">Type:</span> ${request.type}</p>
            <p class="text-gray-700"><span class="font-semibold">Date:</span> ${request.date}</p>
            <p class="text-gray-700"><span class="font-semibold">Time:</span> ${request.time}</p>
            <p class="text-gray-700"><span class="font-semibold">Reason:</span> ${request.reason}</p>
            ${request.image ? `
                <div class="mt-4">
                    <h3 class="font-semibold text-gray-700 mb-2">Attached Image:</h3>
                    <img src="{{ asset('storage') }}/${request.image}" alt="Request Image" class="max-w-full rounded-lg">
                </div>
            ` : ''}
        </div>
    `;

    // Show action buttons
    if (request.status === 'Pending') {
        actions.innerHTML = `
            <button onclick="rejectRequest(${request.id})" class="bg-red-500 text-white px-6 py-2 rounded hover:bg-red-600">
                Reject
            </button>
            <button onclick="approveRequest(${request.id})" class="bg-green-500 text-white px-6 py-2 rounded hover:bg-green-600">
                Approve
            </button>
        `;
    } else {
        actions.innerHTML = `
            <span class="px-6 py-2 rounded ${request.status === 'Approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                ${request.status}
            </span>
        `;
    }

    modal.classList.remove('hidden');
}
</script> 