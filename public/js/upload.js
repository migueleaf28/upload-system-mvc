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
    window.storageUpdater = new StorageUpdater();

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
                
                if (window.storageUpdater) {
                    window.storageUpdater.forceUpdate();
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
        newRow.id = `file-row-${fileData.id}`;
        
        newRow.innerHTML = `
            <td class="py-2 px-3 text-blue-600 underline">${fileData.original_name}</td>
            <td class="py-2 px-3">${fileData.mime_type.toUpperCase()}</td>
            <td class="py-2 px-3">${fileData.formatted_size}</td>
            <td class="py-2 px-3">
                <button 
                    onclick="deleteFile(${fileData.id})"
                    class="bg-red-500 hover:bg-red-600 text-white text-xs px-2 py-1 rounded transition">
                    Eliminar
                </button>
            </td>
        `;

        filesTableBody.insertBefore(newRow, filesTableBody.firstChild);
    }
});
class StorageUpdater {
    constructor() {
        this.storageUsed = document.getElementById('storage-used');
        this.storageLimit = document.getElementById('storage-limit');
        this.storageBar = document.getElementById('storage-bar');
        this.maxFileSize = document.getElementById('max-file-size');
        this.headerUsed = document.getElementById('header-used');
        this.init();
    }

    init() {
        this.updateStorageInfo();
        setInterval(() => this.updateStorageInfo(), 30000);
    }

    async updateStorageInfo() {
        try {
            const response = await fetch('/storage-info');
            const data = await response.json();
            
            if (data.success) {
                const info = data.storage_info;
                
                if (this.storageUsed) this.storageUsed.textContent = info.used;
                if (this.storageLimit) this.storageLimit.textContent = info.limit;
                if (this.maxFileSize) this.maxFileSize.textContent = info.max_file_size;
                if (this.headerUsed) this.headerUsed.textContent = info.used;
                
                if (this.storageBar) {
                    this.storageBar.style.width = info.percentage + '%';
                    
                    if (info.percentage > 90) {
                        this.storageBar.className = 'bg-red-600 h-2 rounded-full';
                    } else if (info.percentage > 70) {
                        this.storageBar.className = 'bg-yellow-600 h-2 rounded-full';
                    } else {
                        this.storageBar.className = 'bg-blue-600 h-2 rounded-full';
                    }
                }
            }
        } catch (error) {
            console.error('Error updating storage info:', error);
        }
    }

    forceUpdate() {
        this.updateStorageInfo();
    }
}

function deleteFile(fileId) {
    if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) {
        return;
    }

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(`/files/${fileId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('La respuesta del servidor no es JSON');
        }
        
        return response.json().then(data => {
            if (!response.ok) {
                throw new Error(data.message || 'Error al eliminar el archivo');
            }
            return data;
        });
    })
    .then(data => {
        const row = document.getElementById(`file-row-${fileId}`);
        if (row) {
            row.remove();
        }
        
        if (window.storageUpdater) {
            window.storageUpdater.forceUpdate();
        }
        
        showDeleteMessage('Archivo eliminado correctamente', 'success');
    })
    .catch(error => {
        console.error('Error:', error);
        showDeleteMessage(error.message, 'error');
    });
}

function showDeleteMessage(message, type) {
    const tempDiv = document.createElement('div');
    tempDiv.className = `mt-3 p-3 rounded ${
        type === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
    }`;
    tempDiv.textContent = message;
    
    document.querySelector('main').appendChild(tempDiv);
    
    setTimeout(() => {
        tempDiv.remove();
    }, 3000);
}