<!-- Absent Modal -->
<div id="absent-modal" class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 hidden">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-4xl transform scale-95 transition-all duration-300 ease-out opacity-0" id="absent-modal-content">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-3xl font-bold text-gray-800" id="absent-modal-name"></h3>
            <div class="flex items-center space-x-4">
                <div id="absent-attachment-button"></div>
                <button onclick="closeAbsentModal()" class="text-gray-600 hover:text-gray-800">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg> 
                </button>
            </div>
        </div>

        <div class="mb-4 text-gray-700">
            <p><strong>Date:</strong> <span id="absent-modal-date" class="font-medium"></span></p>
            <p><strong>Status:</strong> <span id="absent-modal-status" class="font-medium"></span></p>
        </div>

        <!-- Reason Textarea -->
        <div class="mb-6">
            <label class="block text-gray-700 font-semibold">Reason:</label>
            <textarea id="absent-modal-reason" class="w-full bg-blue-100 p-3 border rounded-lg text-gray-800" rows="4" readonly></textarea>
        </div>

        <!-- Buttons Container -->
        <div class="flex justify-between mt-6">
            <form id="absent-approve-form" method="POST" action="">
                @csrf
                <button type="submit" class="bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-green-600 transition">Approve</button>
            </form>
            <form id="absent-reject-form" method="POST" action="">
                @csrf
                <button type="submit" class="bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg hover:bg-red-600 transition">Reject</button>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="absent-image-modal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 backdrop-blur-md z-50 hidden">
    <div class="relative max-w-3xl w-full p-4">
        <button onclick="closeAbsentImageModal()" class="absolute top-2 right-2 bg-white p-2 rounded-full shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="bg-white p-6 rounded-lg shadow-xl overflow-hidden">
            <div class="relative w-full h-96 flex justify-center items-center overflow-hidden">
                <img id="absent-modal-image" class="max-w-full max-h-full object-contain rounded-md shadow-lg cursor-grab transition-transform duration-300 ease-in-out" alt="Attachment">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const absentRows = document.querySelectorAll('tr.absent-request-row');
    const modal = document.getElementById('absent-modal');
    const modalContent = document.getElementById('absent-modal-content');
    const approveForm = document.getElementById('absent-approve-form');
    const rejectForm = document.getElementById('absent-reject-form');
    const absentAttachmentButton = document.getElementById('absent-attachment-button');
    const imageModal = document.getElementById('absent-image-modal');
    const modalImage = document.getElementById('absent-modal-image');

    absentRows.forEach(row => {
        row.addEventListener('click', (event) => {
            event.stopPropagation();
            const absentId = row.getAttribute('data-id');
            const name = row.getAttribute('data-name');
            const date = row.getAttribute('data-date');
            const reason = row.getAttribute('data-reason');
            const status = row.getAttribute('data-status');
            const attachment = row.getAttribute('data-attachment');

            document.getElementById('absent-modal-name').textContent = name;
            document.getElementById('absent-modal-date').textContent = date;
            document.getElementById('absent-modal-reason').value = reason || 'N/A';
            document.getElementById('absent-modal-status').textContent = status || '--';

            approveForm.action = `/absent/${absentId}/approve`;
            rejectForm.action = `/absent/${absentId}/reject`;

            if (attachment) {
                absentAttachmentButton.innerHTML = `<button onclick="openAbsentImageModal('${attachment}')" class="bg-blue-600 text-white px-5 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition">View Attachment</button>`;
            } else {
                absentAttachmentButton.innerHTML = '<p class="text-gray-500">No attachment available</p>';
            }

            modal.style.display = 'flex';
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 50);
        });
    });

    // Close modal function
    window.closeAbsentModal = function () {
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.style.display = 'none';
        }, 200);
    };

    // Close modal when clicking outside content
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeAbsentModal();
        }
    });

    window.openAbsentImageModal = function (imageSrc) {
        modalImage.src = imageSrc;
        imageModal.classList.remove('hidden');
    };

    window.closeAbsentImageModal = function () {
        imageModal.classList.add('hidden');
    };
});
</script>
