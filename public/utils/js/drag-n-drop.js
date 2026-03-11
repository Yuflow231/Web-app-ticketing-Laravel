const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('drop-file');
const fileListDisplay = document.getElementById('file-list');
let selectedFiles = []; // Tab to stock selected files

// Open file explorer on click
dropZone.addEventListener('click', () => {
    fileInput.click();
});

// Update selected file list
fileInput.addEventListener('change', () => {
    handleFiles(fileInput.files);
});

// Handle the drag&drop
dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(e.dataTransfer.files);
});

// Logic to handle single vs multiple
function handleFiles(files) {
    const isMultiple = fileInput.hasAttribute('multiple');

    if (!isMultiple) {
        // Single file mode: Replace the list with only the first new file
        if (files.length > 0) {
            selectedFiles = [files[0]];
        }
    } else {
        // Multiple file mode: Append unique files
        Array.from(files).forEach(file => {
            if (!selectedFiles.some(f => f.name === file.name)) {
                selectedFiles.push(file);
            }
        });
    }
    renderFileList();
}
// Function to render file list
function renderFileList() {
    fileListDisplay.innerHTML = ''; // Reset list
    selectedFiles.forEach((file, index) => {
        const listItem = document.createElement('li');
        listItem.textContent = file.name;

        // Add a button to remove a file
        const removeButton = document.createElement('button');
        removeButton.classList.add('removeBtn');
        removeButton.innerHTML = '<i class="fa-solid fa-trash"></i>';

        removeButton.addEventListener('click', () => {
            selectedFiles.splice(index, 1); // Delete file from list
            renderFileList(); // Update render
        });

        listItem.appendChild(removeButton);
        fileListDisplay.appendChild(listItem);
    });
}

export function syncFilesToInput() {
    const dataTransfer = new DataTransfer();

    selectedFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    fileInput.files = dataTransfer.files;
}