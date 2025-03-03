function previewImage(event) {
    const file = event.target.files[0];

    if (!file) {
        console.log('No file selected!');
        document.getElementById('image-preview').classList.add('hidden');
        return;
    }

    // Create a FileReader to read the file
    const reader = new FileReader();
    
    // When the file is loaded, display the image preview
    reader.onload = function () {
        const imagePreview = document.getElementById('image-preview');
        const thumbnail = document.getElementById('thumbnail');
        
        if (imagePreview && thumbnail) {
            imagePreview.classList.remove('hidden');
            thumbnail.src = reader.result; // Set the preview image source
        } else {
            console.error("Image preview or thumbnail element not found!");
        }
    };

    // Read the file as a data URL
    reader.readAsDataURL(file); 
}