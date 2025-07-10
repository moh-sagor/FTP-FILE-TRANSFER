document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');
    const fileInput = document.getElementById('file-input');
    const fileInfo = document.getElementById('file-info');

    fileInput.addEventListener('change', () => {
        fileInfo.innerHTML = '';
        const files = fileInput.files;
        if (files.length > 0) {
            const ul = document.createElement('ul');
            for (let i = 0; i < files.length; i++) {
                const li = document.createElement('li');
                li.textContent = `${files[i].name} (${(files[i].size / 1024).toFixed(2)} KB)`;
                ul.appendChild(li);
            }
            fileInfo.appendChild(ul);
        }
    });
    const sendBtn = document.getElementById('send-btn');
    const receiveBtn = document.getElementById('receive-btn');
    const codeInput = document.getElementById('code-input');
    const generatedCode = document.getElementById('generated-code');
    const progressBar = document.querySelector('.progress-bar');
    const progressContainer = document.querySelector('.progress-container');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            console.log('Tab clicked:', tab.dataset.tab);
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            tabContents.forEach(c => c.classList.remove('active'));
            document.getElementById(tab.dataset.tab).classList.add('active');
            console.log('Active tab content:', document.getElementById(tab.dataset.tab));
        });
    });

    sendBtn.addEventListener('click', () => {
        const files = fileInput.files;
        if (files.length === 0) {
            alert('Please select one or more files to send.');
            return;
        }

        const formData = new FormData();
        for (let i = 0; i < files.length; i++) {
            formData.append('files[]', files[i]);
        }

        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'upload.php', true);

        xhr.upload.onprogress = e => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = percentComplete + '%';
                progressContainer.style.display = 'block';
            }
        };

        xhr.onload = () => {
            progressContainer.style.display = 'none'; // Hide progress bar after upload
            console.log('XHR Status:', xhr.status);
            console.log('XHR Response Text:', xhr.responseText);

            if (xhr.status === 200) {
                generatedCode.textContent = xhr.responseText;
                // Clear file input after successful upload
                fileInput.value = '';
            } else {
                // Display specific error message from the server
                alert('Upload failed: ' + xhr.responseText);
            }
        };

        xhr.onerror = () => {
            progressContainer.style.display = 'none'; // Hide progress bar on error
            console.error('XHR Network Error.');
            alert('Network error or server unreachable. Please try again.');
        };

        xhr.send(formData);
    });

    receiveBtn.addEventListener('click', () => {
        const code = codeInput.value.trim();
        if (!code) {
            alert('Please enter a code to receive the file.');
            return;
        }

        window.location.href = `download.php?code=${code}`;
    });
});