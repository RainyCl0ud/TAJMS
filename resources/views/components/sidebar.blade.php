<!-- Sidebar -->
<aside id="sidebar"
    class="fixed left-0 top-0 z-40 w-64 h-screen bg-blue-50 text-black flex flex-col pt-0 text-left shadow-lg border-r-2 border-gray-100 transition-transform transform -translate-x-full md:translate-x-0 md:relative">
    
    <!-- Close Button (Only for Small Screens) -->
    <button id="closeSidebar" class="absolute top-4 right-4 text-gray-700 md:hidden">
        ✖
    </button>
    
    <div class="flex items-center justify-center py-0">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-40 h-40 rounded-full">
    </div>
    <nav class="flex-1">
        <ul class="space-y-8 px-6">
            <li class="flex items-center space-x-3">
                <a href="{{ route(auth()->user()->role . '.dashboard') }}" class="sideB block px-4 py-2 rounded {{ request()->routeIs(auth()->user()->role . '.dashboard') ? 'bg-blue-100' : '' }}">
                    <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard Icon" class="w-5 h-5 inline-block" />
                    Dashboard
                </a>
            </li>

            @if(auth()->user()->role === 'trainee')
                <!-- Attendance for Trainee -->
                <li class="flex items-center space-x-3">
                    <a href="{{ route('attendance.create') }}" class="sideB block px-4 py-2 rounded {{ request()->routeIs('attendance.*') ? 'bg-blue-100' : '' }}">
                        <img src="{{ asset('images/attendance.png') }}" alt="Attendance Icon" class="w-5 h-5 inline-block" />
                        Attendance
                    </a>
                </li>
            @endif

            @if(auth()->user()->role === 'coordinator')
                <!-- Attendance for Coordinator -->
                <li class="flex items-center space-x-3">
                    <a href="{{ route('coordinator.trainee-attendance-all-records') }}" class="sideB block px-4 py-2 rounded {{ request()->routeIs('coordinator.trainee-attendance-all-records') ? 'bg-blue-100' : '' }}">
                        <img src="{{ asset('images/attendance.png') }}" alt="Attendance Icon" class="w-5 h-5 inline-block" />
                        Attendance
                    </a>
                </li>
            @endif

            @if(auth()->user()->role === 'trainee')
                <li class="flex items-center space-x-3">
                    <a href="{{ route('journal.index') }}" class="sideB block px-4 py-2 rounded {{ request()->routeIs('journal.*') ? 'bg-blue-100' : '' }}">
                        <img src="{{ asset('images/journal.png') }}" alt="Journal Icon" class="w-5 h-5 inline-block" />
                        Journal
                    </a>
                </li>
            @endif

            <li class="flex flex-col space-y-1 relative">
                @if(auth()->user()->role === 'coordinator' || auth()->user()->role === 'pre_user')
                    <!-- Coordinator and pre_user see only a direct Request link -->
                    @if(auth()->user()->role === 'coordinator')
                    <a href="{{ route('coordinator.requests') }}" class="sideB block px-4 py-2 rounded {{ request()->routeIs('coordinator.requests') ? 'bg-blue-100' : '' }}">
                        <img src="{{ asset('images/journal.png') }}" alt="Request Icon" class="w-5 h-5 inline-block" />
                        Request
                    </a>
                    @endif
                @else
                    <!-- Trainee sees Request with dropdown -->
                    <a href="javascript:void(0);" onclick="toggleRequest();" class="sideB block px-4 py-2 rounded flex items-center space-x-3 transition-all duration-300 ease-in-out hover:bg-blue-100">
                        <img src="{{ asset('images/message.png') }}" alt="Message Icon" class="w-5 h-5 inline-block" />
                        <span>Request</span>
                        <span id="arrowIndicator" class="ml-2 transition-all duration-300 ease-in-out transform rotate-0">&#x25BC;</span>
                    </a>
                    <div id="requestDropdown" class="overflow-hidden max-h-0 transition-all duration-500 ease-in-out bg-gray-300 rounded-md mt-2 opacity-0 p-2" style="display: none;">
                        <button onclick="openForgotModal()" class="block pl-5 text-sm text-blue-500 hover:text-black mb-3 w-full text-left transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg hover:opacity-80">
                            Forgot time in/out request
                        </button>
                        <button onclick="openManualAttendanceModal()" class="block pl-5 text-sm text-blue-500 hover:text-black mb-3 w-full text-left transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg hover:opacity-80">
                            Manual attendance request
                        </button>
                    </div>
                @endif
            </li>
        </ul>
    </nav>
</aside>

<!-- Mobile Menu Button -->
<button id="mobileMenuButton" class="fixed top-4 left-4 z-50 p-2 rounded-md text-gray-700 bg-white shadow-lg md:hidden">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<!-- Overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 hidden md:hidden"></div>

<!-- Forgot Time In/Out Request Modal -->
<div id="forgotTimeModal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999;">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative opacity-0 transition-opacity duration-500 ease-in-out sm:max-w-lg md:max-w-xl lg:max-w-2xl" id="modalContentForgotTime">
            <h3 class="text-xl font-bold mb-4 text-center">Forgot time in/out Request</h3>
            
            <form method="POST" action="{{ route('request.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label for="type" class="text-sm font-semibold mb-1">Type</label>
                        <select name="type" id="type" class="w-full border p-2 rounded-lg" required>
                            <option value="time_in">Time In</option>
                            <option value="time_out">Time Out</option>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="date" class="text-sm font-semibold mb-1">Date</label>
                        <input type="date" name="date" id="date" class="w-full border p-2 rounded-lg" required max="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="flex flex-col mt-4">
                    <label for="time" class="text-sm font-semibold mb-1">Time</label>
                    <input type="time" name="time" id="time" class="w-full border p-2 rounded-lg" required>
                </div>

                <div class="mt-4">
                    <label for="reason" class="text-sm font-semibold mb-1">Reason</label>
                    <textarea name="reason" id="reason" class="w-full border p-2 rounded-lg" rows="4" placeholder="Reason..." required></textarea>
                </div>

                <!-- Drag and Drop Upload -->
                <div class="mt-4">
                    <label for="image" class="block text-sm font-semibold mb-1">Upload Image</label>
                    <div id="drag-drop-container-forgot" class="border-2 border-dashed p-4 rounded-lg flex justify-center items-center cursor-pointer hover:border-blue-400">
                        <p class="text-gray-600 text-center" id="drag-drop-text-forgot">Drag & Drop your file here or Click to select</p>
                        <input type="file" name="image" id="fileInputForgot" class="hidden" accept="image/*">
                    </div>
                </div>

                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4 w-full hover:bg-blue-600 transition-colors">Submit Request</button>
            </form>
            <button onclick="closeForgotModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-lg">✖</button>
        </div>
    </div>
</div>

<!-- Manual Attendance Request Modal -->
<div id="manualAttendanceModal" class="hidden fixed inset-0 overflow-y-auto" style="z-index: 9999;">
    <!-- Modal Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm"></div>
    
    <!-- Modal Content -->
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-2xl relative opacity-0 transition-opacity duration-500 ease-in-out sm:max-w-lg md:max-w-xl lg:max-w-2xl" id="modalContentManualAttendance">
            <h3 class="text-xl font-bold mb-4 text-center">Manual Attendance Request</h3>
            
            <form method="POST" action="{{ route('request.store') }}" enctype="multipart/form-data" onsubmit="return validateManualAttendance(event)">
                @csrf
                <input type="hidden" name="type" value="manual_attendance">
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex flex-col">
                        <label for="manual_date" class="text-sm font-semibold mb-1">Date</label>
                        <input type="date" name="date" id="manual_date" class="w-full border p-2 rounded-lg" required 
                               max="{{ date('Y-m-d') }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
                    <div class="flex flex-col">
                        <label for="time_in" class="text-sm font-semibold mb-1">Time In</label>
                        <input type="time" name="time" id="time_in" class="w-full border p-2 rounded-lg" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="time_out" class="text-sm font-semibold mb-1">Time Out</label>
                        <input type="time" name="time_out" id="time_out" class="w-full border p-2 rounded-lg" required>
                    </div>
                </div>

                <div class="mt-4">
                    <label for="manual_reason" class="text-sm font-semibold mb-1">Reason</label>
                    <textarea name="reason" id="manual_reason" class="w-full border p-2 rounded-lg" rows="4" 
                              placeholder="Please explain why you need to submit a manual attendance..." required></textarea>
                </div>

                <!-- Drag and Drop Upload -->
                <div class="mt-4">
                    <label for="manual_image" class="block text-sm font-semibold mb-1">Upload DTR/Logbook Image <span class="text-red-500">*</span></label>
                    <div id="drag-drop-container-manual" class="border-2 border-dashed p-4 rounded-lg flex justify-center items-center cursor-pointer hover:border-blue-400">
                        <p class="text-gray-600 text-center" id="drag-drop-text-manual">Drag & Drop your DTR/Logbook image or Click to select</p>
                        <input type="file" name="image" id="fileInputManual" class="hidden" accept="image/*" required>
                    </div>
                </div>

                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded-lg mt-4 w-full hover:bg-blue-600 transition-colors">Submit Request</button>
            </form>
            <button onclick="closeManualAttendanceModal()" class="absolute top-4 right-4 text-gray-600 hover:text-gray-900 text-lg">✖</button>
        </div>
    </div>
</div>

<style>
    #manualAttendanceModal {
        isolation: isolate;
    }
    #manualAttendanceModal .bg-black {
        z-index: -1;
    }
    #forgotTimeModal {
        isolation: isolate;
    }
    #forgotTimeModal .bg-black {
        z-index: -1;
    }
</style>

<script>
    function toggleRequest() {
        var requestDropdown = document.getElementById("requestDropdown");
        var arrowIndicator = document.getElementById("arrowIndicator");

        if (requestDropdown.style.display === "none" || requestDropdown.style.maxHeight === "0px") {
            requestDropdown.style.display = "block";
            setTimeout(() => {
                requestDropdown.style.maxHeight = "300px";
                requestDropdown.style.opacity = "1";
            }, 10);
            arrowIndicator.innerHTML = "&#x25B2;";
        } else {
            requestDropdown.style.maxHeight = "0px";
            requestDropdown.style.opacity = "0";
            setTimeout(() => {
                requestDropdown.style.display = "none";
            }, 500);
            arrowIndicator.innerHTML = "&#x25BC;";
        }
    }

    function openForgotModal() {
        var modal = document.getElementById('forgotTimeModal');
        var modalContent = document.getElementById('modalContentForgotTime');
        modal.classList.remove('hidden');
        setTimeout(function () {
            modalContent.classList.remove('opacity-0');
            modalContent.classList.add('opacity-100');
        }, 10);
    }

    function closeForgotModal() {
        var modal = document.getElementById('forgotTimeModal');
        var modalContent = document.getElementById('modalContentForgotTime');
        modalContent.classList.remove('opacity-100');
        modalContent.classList.add('opacity-0');
        setTimeout(function () {
            modal.classList.add('hidden');
        }, 200);
    }

    function openManualAttendanceModal() {
        var modal = document.getElementById('manualAttendanceModal');
        var modalContent = document.getElementById('modalContentManualAttendance');
        modal.classList.remove('hidden');
        setTimeout(function () {
            modalContent.classList.remove('opacity-0');
            modalContent.classList.add('opacity-100');
        }, 10);
    }

    function closeManualAttendanceModal() {
        var modal = document.getElementById('manualAttendanceModal');
        var modalContent = document.getElementById('modalContentManualAttendance');
        modalContent.classList.remove('opacity-100');
        modalContent.classList.add('opacity-0');
        setTimeout(function () {
            modal.classList.add('hidden');
        }, 200);
    }

    function validateManualAttendance(event) {
        const dateInput = document.getElementById('manual_date');
        const timeInInput = document.getElementById('time_in');
        const timeOutInput = document.getElementById('time_out');
        
        // Check if date is not in the future
        const selectedDate = new Date(dateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate > today) {
            alert('Cannot submit attendance for future dates.');
            event.preventDefault();
            return false;
        }
        
        // Check if time out is after time in
        const timeIn = timeInInput.value;
        const timeOut = timeOutInput.value;
        
        if (timeOut <= timeIn) {
            alert('Time out must be after time in.');
            event.preventDefault();
            return false;
        }
        
        return true;
    }

    function setupDragDrop(containerId, inputId, textId) {
        const dragDropContainer = document.getElementById(containerId);
        const fileInput = document.getElementById(inputId);
        const dragDropText = document.getElementById(textId);

        dragDropContainer.addEventListener('click', () => fileInput.click());

        dragDropContainer.addEventListener('dragover', (e) => {
            e.preventDefault();
            dragDropContainer.classList.add('border-blue-400');
            dragDropText.textContent = 'Release to upload file';
        });

        dragDropContainer.addEventListener('dragleave', () => {
            dragDropContainer.classList.remove('border-blue-400');
            dragDropText.textContent = 'Drag & Drop your file here or Click to select';
        });

        dragDropContainer.addEventListener('drop', (e) => {
            e.preventDefault();
            fileInput.files = e.dataTransfer.files;
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                dragDropText.textContent = `File Selected: ${file.name}`;
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                const file = fileInput.files[0];
                dragDropText.textContent = `File Selected: ${file.name}`;
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        setupDragDrop('drag-drop-container-forgot', 'fileInputForgot', 'drag-drop-text-forgot');
        setupDragDrop('drag-drop-container-manual', 'fileInputManual', 'drag-drop-text-manual');
    });

    // Mobile sidebar toggle functionality
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('sidebar');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const closeSidebarButton = document.getElementById('closeSidebar');
        const overlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.style.overflow = '';
        }

        mobileMenuButton.addEventListener('click', openSidebar);
        closeSidebarButton.addEventListener('click', closeSidebar);
        overlay.addEventListener('click', closeSidebar);

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            if (window.innerWidth < 768) { // md breakpoint
                if (!sidebar.contains(event.target) && 
                    !mobileMenuButton.contains(event.target) && 
                    !sidebar.classList.contains('-translate-x-full')) {
                    closeSidebar();
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) { // md breakpoint
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });
    });
</script>
