document.addEventListener('DOMContentLoaded', function() {
    
    const form = document.getElementById('uploadForm');
    const fileInput = document.getElementById('fileInput');
    const uploadButton = document.getElementById('uploadButton');
    const uploadText = document.getElementById('uploadText');
    const uploadSpinner = document.getElementById('uploadSpinner');
    const uploadMessage = document.getElementById('uploadMessage');
    const filesTableBody = document.getElementById('files-table-body');

    if (!form) {
        console.error('No se encontró el formulario');
        return;
    }

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!fileInput.files || fileInput.files.length === 0) {
            alert('Por favor selecciona un archivo');
            return;
        }

        uploadText.classList.add('hidden');
        uploadSpinner.classList.remove('hidden');
        uploadButton.disabled = true;

        const formData = new FormData();
        formData.append('file', fileInput.files[0]);

        fetch('/files/upload', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })

        .then(response => {            
            return response.json().then(data => {
                if (!response.ok) {
                    showMessage(data.message, 'error');
                    return null;
                }
                return data;
            });
        })

        .then(data => {
            if (data) {
                console.log('Datos recibidos:', data);
                showMessage(data.message, 'success');
                fileInput.value = '';
                if (data.file) {
                    addFileToTable(data.file);
                }
            }
        })

        .catch(error => {
            console.error('Error:', error);
            showMessage('Error de conexión', 'error');
        })

        .finally(() => {
            uploadText.classList.remove('hidden');
            uploadSpinner.classList.add('hidden');
            uploadButton.disabled = false;
        });
    });

    function showMessage(message, type) {
        const bgColor = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 
                           'bg-green-100 border-green-400 text-green-700';
        
        uploadMessage.innerHTML = `
            <div class="border-l-4 p-4 ${bgColor} rounded">
                <p class="text-sm">${message}</p>
            </div>
        `;

        setTimeout(() => {
            uploadMessage.innerHTML = '';
        }, 5000);
    }

    function addFileToTable(fileData) {        
        const emptyRow = filesTableBody.querySelector('tr:only-child');
        if (emptyRow && emptyRow.textContent.includes('No hay archivos')) {
            emptyRow.remove();
        }

        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-gray-50';
        newRow.innerHTML = `
            <td class="py-2 px-3 text-blue-600 underline">${fileData.original_name}</td>
            <td class="py-2 px-3">${fileData.mime_type.toUpperCase()}</td>
            <td class="py-2 px-3">${fileData.formatted_size}</td>
        `;

        filesTableBody.insertBefore(newRow, filesTableBody.firstChild);
    }
});