const videoElement = document.getElementById('video');
const canvasElement = document.getElementById('canvas');
const startCameraButton = document.getElementById('startCameraButton');
const captureButton = document.getElementById('captureButton');
const attendanceForm = document.getElementById('attendanceForm');
const imageDataInput = document.getElementById('image_data');
let stream = null;

// Start the camera when the button is clicked
startCameraButton.addEventListener('click', () => {
    navigator.mediaDevices.getUserMedia({ video: true })
        .then((mediaStream) => {
            stream = mediaStream;
            videoElement.srcObject = stream;
            videoElement.style.display = 'block'; // Show video element
            attendanceForm.style.display = 'block'; // Show the form for taking attendance
            startCameraButton.style.display = 'none'; // Hide the start camera button
        })
        .catch((error) => {
            console.error("Error accessing webcam:", error);
        });
        
});

// Function to stop the webcam
function stopWebcam() {
    if (stream) {
        const tracks = stream.getTracks(); // Get all media tracks
        tracks.forEach((track) => track.stop()); // Stop each track
        stream = null; // Clear the stream
    }
}

// Capture the photo when the button is clicked
captureButton.addEventListener('click', function (event) {
    event.preventDefault();
    
    const context = canvasElement.getContext('2d');
    canvasElement.style.display = 'block';  // Show canvas for photo capture
    context.drawImage(videoElement, 0, 0, canvasElement.width, canvasElement.height);
    
    // Convert canvas image to data URL and set it in the hidden input
    const imageData = canvasElement.toDataURL('image/png');
    imageDataInput.value = imageData;  // Set the captured image as the form data

    // Stop the webcam
    stopWebcam();

    // Submit the form with the photo data
    attendanceForm.submit(); 
    
});

document.addEventListener('DOMContentLoaded', function () {
    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.style.opacity = '0';
            setTimeout(() => flashMessage.remove(), 500); // Remove after fade out
        }, 3000); // Show for 3 seconds
    }
});

// Optional: Stop the webcam when the user navigates away from the page
window.addEventListener('beforeunload', stopWebcam)
