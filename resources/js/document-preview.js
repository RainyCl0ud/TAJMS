// function previewImage(event) {
//     const file = event.target.files[0];
//     const previewContainer = document.getElementById('preview-container');
//     const previewImage = document.getElementById('image-preview');

//     if (file) {
//         const reader = new FileReader();

//         reader.onload = function (e) {
//             previewImage.src = e.target.result;
//             previewContainer.classList.remove('hidden');
//         };

//         reader.readAsDataURL(file);
//     } else {
//         previewContainer.classList.add('hidden');
//     }
// }

// window.previewImage = previewImage;
