const dropZone = document.getElementById('drop_zone');
const addFileLabel = document.getElementById('add-file');
const fileInput = document.getElementById('file-input');
const dropBoxContent = document.getElementById('drop-box-base-content');
const droppedFileElement = document.getElementById('dropped-file');
const tooManyFilesElement = document.getElementById('too-many-content');
const invalidTypeContentElement = document.getElementById('invalid-type-content');
const alreadyExistContent = document.getElementById('already-exist-content');
const wrongFileContent = document.getElementById('wrong-file-content');
const pendingElement = document.getElementById('pending');
let intervalId;

const ticketId = document.getElementById("ticket_id");

dropZone.addEventListener('dragover', function(event) {
    event.preventDefault();
    dropZone.classList.add('dragover');
});

dropZone.addEventListener('dragleave', function(event) {
    dropZone.classList.remove('dragover');
});

dropZone.addEventListener('drop', function(event) {
    event.preventDefault();
    dropZone.classList.remove('dragover');
    handleFiles(event.dataTransfer.files);
});

fileInput.addEventListener('change', function(event) {
    handleFiles(event.target.files);
});

function handleFiles(files) {
    dropBoxContent.classList.add('hidden');
    droppedFileElement.classList.add('hidden');
    tooManyFilesElement.classList.add('hidden');
    invalidTypeContentElement.classList.add('hidden');
    alreadyExistContent.classList.add('hidden');
    wrongFileContent.classList.add('hidden');
    pending();
    if (files.length > 1) {
        tooManyFilesElement.classList.remove('hidden');
    } else {
        const file = files[0];

        sendFileToPHP(file)
    }
}

function sendFileToPHP(file) {
    const formData = new FormData();
    formData.append('uploadedFile', file);

    fetch('/tickets/create/upload.php', {
        method: 'POST',
        body: formData
    })
    
    .then(response => {
        stopPending()
        if (!response.ok) {
            throw new Error('Erreur lors de l\'envoi du fichier à PHP.');
        }
        return response.json();
    })
    .then(data => {
        stopPending()
        if (data.status == 'success') {
            ticketId.textContent = data.ticket_id;
            droppedFileElement.classList.remove('hidden');
        } else if (data.status == 'alredyExist') {
            alreadyExistContent.classList.remove('hidden');
        } else if (data.status == 'noMatch') {
            wrongFileContent.classList.remove('hidden');
        } else if (data.status == 'badType') {
            invalidTypeContentElement.classList.remove('hidden');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function pending() {
    pendingElement.classList.remove('hidden');
    let dot = '';
    intervalId = setInterval(() => {
        dot += '.';
        if (dot.length > 3) {
            dot = '.';
        }
        pendingElement.innerText = 'Chargement' + dot;
    }, 300);
}

function stopPending() {
    clearInterval(intervalId);
    pendingElement.classList.add('hidden');
}