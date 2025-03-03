// document.addEventListener('DOMContentLoaded', () => {
//     const editButton = document.getElementById('editButton');
//     const saveButton = document.getElementById('saveButton');
//     const contentText = document.getElementById('contentText');
//     const editTextarea = document.getElementById('editTextarea');

//     if (editButton && saveButton && contentText && editTextarea) {
//         // Event listener for the "Edit" button
//         editButton.addEventListener('click', () => {
//         // Toggle visibility of content and textarea
//             contentText.classList.add('hidden');
//             editTextarea.classList.remove('hidden');
//             saveButton.classList.remove('hidden');
//         });

//         // Event listener for the "Save" button
//         saveButton.addEventListener('click', () => {
//             const content = editTextarea.value.trim(); // Get and trim the content from the textarea

//             // Exit if content is empty (validation fails)
//             if (content === '') return;

//             // Send the updated content via AJAX (fetch request)
//             fetch(editButton.dataset.updateUrl, {
//                 method: 'POST',
//                 headers: {
//                     'Content-Type': 'application/json', // Specify content type
//                     'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Include CSRF token
//                 },
//                 body: JSON.stringify({ content: content }) // Send the content in the request body
//             })
//             .then(response => response.json()) // Parse the JSON response
//             .then(data => {
//                 if (data.success) {
//                     // Update the displayed content if successful
//                     contentText.textContent = content;
//                     contentText.classList.remove('hidden');
//                     editTextarea.classList.add('hidden');
//                     saveButton.classList.add('hidden');
//                 }
//             })
//             .catch(error => {
//                 console.error('Error:', error); // Log any errors
//             });
//         });
//     }
// });
