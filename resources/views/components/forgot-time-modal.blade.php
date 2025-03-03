<!-- Main Modal -->
<div id="details-modal" class="fixed inset-0 flex justify-center items-center bg-gray-900 bg-opacity-50 backdrop-blur-md z-50 hidden">
    <div class="bg-white p-8 md:p-12 rounded-2xl shadow-2xl w-full max-w-4xl min-h-[600px] relative transform scale-95 opacity-0 transition-all duration-300 ease-out" id="modal-content">
        <button onclick="closeModal()" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-3xl font-semibold text-gray-800" id="modal-name"></h3>
            <div id="modal-attachment-button"></div>
        </div>
        <div class="mt-6 bg-gray-100 p-6 rounded-lg border border-black">
            <div class="flex flex-wrap md:flex-nowrap justify-between gap-4">
                <div class="mb-6">
                    <p><strong>Type:</strong> <span id="modal-type-reason" class="font-medium text-gray-700"></span></p>
                </div>
                <div class="mb-6">
                    <p><strong>Date Happened:</strong> <span id="modal-date" class="font-medium text-gray-700"></span></p>
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-lg font-medium mb-2">Reason:</label>
                <div class="h-32 overflow-auto border border-gray-300 rounded-lg">
                    <textarea id="modal-additional-info" class="w-full h-full p-4 bg-blue-100 text-gray-700 resize-none break-words" readonly></textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row justify-between space-y-4 md:space-y-0 md:space-x-8 mt-8">
            <form id="approve-form" method="POST" action="" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-10 py-4 rounded-lg shadow-lg hover:bg-green-700 transition duration-300 w-full">
                    Approve
                </button>
            </form>
            <form id="reject-form" method="POST" action="" class="w-full md:w-auto">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-10 py-4 rounded-lg shadow-lg hover:bg-red-700 transition duration-300 w-full">
                    Reject
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="image-modal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-80 backdrop-blur-md z-50 hidden">
    <!-- Restrict the size of the modal to a specific width and height -->
    <div class="relative max-w-3xl w-full p-4 overflow-hidden bg-white rounded-lg">
        <button onclick="closeImageModal()" class="absolute top-2 right-2 bg-white p-1 rounded-full shadow-lg">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div class="bg-white p-4 rounded-lg shadow-xl overflow-hidden w-full">
            <div class="relative w-full max-h-[80vh] flex justify-center items-center overflow-hidden">
                <!-- Image adjusted to scale properly and be centered -->
                <img id="modal-image" class="max-w-full max-h-full object-contain rounded-md shadow-lg cursor-grab transition-transform duration-300 ease-in-out" alt="Attachment">
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const requestRows = document.querySelectorAll('tr.request-row');
    const modal = document.getElementById('details-modal');
    const modalContent = document.getElementById('modal-content');
    const approveForm = document.getElementById('approve-form');
    const rejectForm = document.getElementById('reject-form');
    const modalAttachmentButton = document.getElementById('modal-attachment-button');
    const imageModal = document.getElementById('image-modal');
    const modalImage = document.getElementById('modal-image');

    requestRows.forEach(row => {
        row.addEventListener('click', (event) => {
            event.stopPropagation();

            const requestId = row.getAttribute('data-id');
            const name = row.getAttribute('data-name');
            const type = row.getAttribute('data-type');
            const date = row.getAttribute('data-date');
            const reason = row.getAttribute('data-reason');
            const attachment = row.getAttribute('data-attachment');

            document.getElementById('modal-name').textContent = name;
            document.getElementById('modal-type-reason').textContent = type || reason;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-additional-info').textContent = reason || 'N/A';
            approveForm.action = `/request/${requestId}/approve`;
            rejectForm.action = `/request/${requestId}/reject`;

            if (attachment) {
                modalAttachmentButton.innerHTML = `<button onclick="openImageModal('${attachment}')" class="bg-blue-600 text-white px-4 py-2 rounded-lg shadow-lg hover:bg-blue-700 transition">View Attachment</button>`;
            } else {
                modalAttachmentButton.innerHTML = '<p class="text-gray-500">No attachment available</p>';
            }

            modal.style.display = 'flex';
            setTimeout(() => {
                modalContent.classList.remove('scale-95', 'opacity-0');
                modalContent.classList.add('scale-100', 'opacity-100');
            }, 50);
        });
    });

    window.closeModal = function() {
        modalContent.classList.add('scale-95', 'opacity-0');
        setTimeout(() => { modal.style.display = 'none'; }, 200);
    };

    window.openImageModal = function(imageSrc) {
        modalImage.src = imageSrc;
        modalImage.style.transform = 'scale(1) translate(0, 0)';
        imageModal.classList.remove('hidden');
    };

    window.closeImageModal = function() {
        imageModal.classList.add('hidden');
    };

    let scale = 1, posX = 0, posY = 0, isDragging = false, startX, startY;

    modalImage.addEventListener('wheel', (e) => {
        e.preventDefault();
        scale += e.deltaY * -0.01;
        scale = Math.min(Math.max(1, scale), 3);
        modalImage.style.transform = `scale(${scale}) translate(${posX}px, ${posY}px)`;
    });

    modalImage.addEventListener('mousedown', (e) => {
        if (scale > 1) {
            isDragging = true;
            startX = e.clientX - posX;
            startY = e.clientY - posY;
            modalImage.style.cursor = 'grabbing';
        }
    });

    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            posX = e.clientX - startX;
            posY = e.clientY - startY;
            modalImage.style.transform = `scale(${scale}) translate(${posX}px, ${posY}px)`;
        }
    });

    document.addEventListener('mouseup', () => {
        isDragging = false; 
        modalImage.style.cursor = 'grab';
    });

    modalImage.addEventListener('dblclick', () => {
        scale = 1; posX = 0; posY = 0;
        modalImage.style.transform = 'scale(1) translate(0, 0)';
    });
});
</script>
