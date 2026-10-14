<?php
/**
 * Document Upload Module for MedicalVoice v1
 * Adds optional document support without disrupting main flow
 */

class DocumentModule {
    private $uploadDir;
    private $maxFileSize = 10485760; // 10MB
    private $allowedTypes = ['pdf', 'jpg', 'jpeg', 'png', 'docx', 'doc', 'txt'];

    public function __construct() {
        $this->uploadDir = __DIR__ . '/storage/uploads/documents/';
        $this->ensureDirectoryExists();
    }

    private function ensureDirectoryExists() {
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function renderUploadSection() {
        return <<<HTML
        <!-- Optional Document Upload Section -->
        <div id="documentSection" style="display: none; margin-top: 20px; padding: 20px; background: #f8f9fa; border-radius: 10px;">
            <h4 style="margin-bottom: 15px;">
                <span class="material-icons-round" style="vertical-align: middle;">attach_file</span>
                Add Supporting Documents (Optional)
            </h4>
            <p style="color: #666; margin-bottom: 15px;">
                Upload medical records, lab results, or prescriptions to enhance AI analysis
            </p>

            <div id="documentDropZone" style="border: 2px dashed #ddd; border-radius: 8px; padding: 30px; text-align: center; cursor: pointer; transition: all 0.3s;">
                <span class="material-icons-round" style="font-size: 48px; color: #999;">cloud_upload</span>
                <p style="margin: 10px 0;">Drop files here or click to browse</p>
                <p style="font-size: 12px; color: #999;">Supports: PDF, Images, Word docs (Max 10MB each)</p>
                <input type="file" id="documentFiles" multiple accept=".pdf,.jpg,.jpeg,.png,.docx,.doc,.txt" style="display: none;">
            </div>

            <div id="selectedDocuments" style="margin-top: 15px; display: none;">
                <h5>Selected Files:</h5>
                <ul id="documentList" style="list-style: none; padding: 0;"></ul>
            </div>

            <div style="margin-top: 15px; display: flex; gap: 10px;">
                <button id="uploadDocsBtn" class="btn btn-primary btn-lg" style="display: none;">
                    <span class="material-icons-round">upload</span>
                    Upload Documents
                </button>
                <button id="skipDocsBtn" class="btn btn-outline-primary btn-sm" onclick="hideDocumentSection()">
                    <span class="material-icons-round">close</span>
                    Cancel
                </button>
            </div>
        </div>

        <script>
        let selectedFiles = [];

        // Define showNotification function if not already defined
        if (typeof showNotification === 'undefined') {
            window.showNotification = function(message, type) {
                // Create notification element
                const notification = document.createElement('div');
                const bgColor = type === 'success' ? '#4CAF50' : type === 'error' ? '#f44336' : '#2196F3';
                notification.style.cssText =
                    'position: fixed;' +
                    'top: 20px;' +
                    'right: 20px;' +
                    'padding: 15px 20px;' +
                    'background: ' + bgColor + ';' +
                    'color: white;' +
                    'border-radius: 5px;' +
                    'box-shadow: 0 2px 5px rgba(0,0,0,0.2);' +
                    'z-index: 10000;' +
                    'display: flex;' +
                    'align-items: center;' +
                    'gap: 10px;' +
                    'animation: slideIn 0.3s ease-out;';

                notification.innerHTML =
                    '<span class="material-icons-round">' + (type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info') + '</span>' +
                    '<span>' + message + '</span>';

                document.body.appendChild(notification);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    notification.style.animation = 'slideOut 0.3s ease-out';
                    setTimeout(() => notification.remove(), 300);
                }, 5000);
            };
        }

        // Add CSS animation for notifications
        if (!document.getElementById('notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.innerHTML = `
                @keyframes slideIn {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                @keyframes slideOut {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        function toggleDocumentSection() {
            const section = document.getElementById('documentSection');
            section.style.display = section.style.display === 'none' ? 'block' : 'none';

            if (section.style.display === 'block') {
                initializeDocumentUpload();
            }
        }

        function hideDocumentSection() {
            document.getElementById('documentSection').style.display = 'none';
            selectedFiles = [];
            document.getElementById('documentList').innerHTML = '';
            document.getElementById('selectedDocuments').style.display = 'none';
            document.getElementById('uploadDocsBtn').style.display = 'none';
        }

        function initializeDocumentUpload() {
            const dropZone = document.getElementById('documentDropZone');
            const fileInput = document.getElementById('documentFiles');

            dropZone.onclick = () => fileInput.click();

            dropZone.ondragover = (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#4CAF50';
                dropZone.style.background = '#f0f8f0';
            };

            dropZone.ondragleave = () => {
                dropZone.style.borderColor = '#ddd';
                dropZone.style.background = 'transparent';
            };

            dropZone.ondrop = (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#ddd';
                dropZone.style.background = 'transparent';
                handleDocumentSelection(e.dataTransfer.files);
            };

            fileInput.onchange = (e) => {
                handleDocumentSelection(e.target.files);
            };

            document.getElementById('uploadDocsBtn').onclick = uploadDocuments;
        }

        function handleDocumentSelection(files) {
            selectedFiles = Array.from(files);
            const list = document.getElementById('documentList');
            list.innerHTML = '';

            selectedFiles.forEach(file => {
                const li = document.createElement('li');
                li.style.padding = '5px 0';
                li.innerHTML = `
                    <span class="material-icons-round" style="vertical-align: middle; color: #4CAF50;">description</span>
                    \${file.name} (\${(file.size / 1024 / 1024).toFixed(2)} MB)
                `;
                list.appendChild(li);
            });

            if (selectedFiles.length > 0) {
                document.getElementById('selectedDocuments').style.display = 'block';
                document.getElementById('uploadDocsBtn').style.display = 'inline-flex';
            }
        }

        function uploadDocuments() {
            if (selectedFiles.length === 0) {
                window.showNotification('Please select files to upload', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('action', 'upload_documents');
            formData.append('transcription_id', sessionStorage.getItem('transcriptionId') || '');

            selectedFiles.forEach(file => {
                formData.append('documents[]', file);
            });

            const uploadBtn = document.getElementById('uploadDocsBtn');
            if (uploadBtn) {
                uploadBtn.disabled = true;
                uploadBtn.innerHTML = '<span class="material-icons-round spinning">sync</span> Uploading...';
            }

            console.log('Uploading documents...', selectedFiles);

            fetch('document_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response received:', response);
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    window.showNotification('Documents uploaded successfully!', 'success');
                    hideDocumentSection();

                    // Store document IDs for combined processing
                    if (data.document_ids) {
                        sessionStorage.setItem('documentIds', JSON.stringify(data.document_ids));
                    }

                    // Update UI to show documents were added
                    if (window.updateProcessButtonText) {
                        window.updateProcessButtonText();
                    }
                } else {
                    window.showNotification(data.message || 'Upload failed', 'error');
                    if (uploadBtn) {
                        uploadBtn.disabled = false;
                        uploadBtn.innerHTML = '<span class="material-icons-round">upload</span> Upload Documents';
                    }
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                window.showNotification('Upload failed: ' + error.message, 'error');
                if (uploadBtn) {
                    uploadBtn.disabled = false;
                    uploadBtn.innerHTML = '<span class="material-icons-round">upload</span> Upload Documents';
                }
            });
        }

        function updateProcessButtonText() {
            const btn = document.getElementById('startTranscriptionBtn');
            if (btn && sessionStorage.getItem('documentIds')) {
                btn.innerHTML = '<span class="material-icons-round">psychology</span> Process Audio + Documents';
            }
        }

        // Make updateProcessButtonText globally accessible
        window.updateProcessButtonText = updateProcessButtonText;
        </script>
HTML;
    }

    public function processUploadedFiles($files, $transcriptionId) {
        $uploadedFiles = [];
        $errors = [];

        foreach ($files['documents']['name'] as $key => $filename) {
            if ($files['documents']['error'][$key] !== UPLOAD_ERR_OK) {
                $errors[] = "Failed to upload $filename";
                continue;
            }

            $fileSize = $files['documents']['size'][$key];
            if ($fileSize > $this->maxFileSize) {
                $errors[] = "$filename exceeds 10MB limit";
                continue;
            }

            $fileExt = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (!in_array($fileExt, $this->allowedTypes)) {
                $errors[] = "$filename has invalid type";
                continue;
            }

            $safeFilename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
            $targetPath = $this->uploadDir . $safeFilename;

            if (move_uploaded_file($files['documents']['tmp_name'][$key], $targetPath)) {
                $uploadedFiles[] = [
                    'original_name' => $filename,
                    'stored_name' => $safeFilename,
                    'path' => $targetPath,
                    'type' => $fileExt,
                    'size' => $fileSize
                ];
            } else {
                $errors[] = "Failed to save $filename";
            }
        }

        return [
            'success' => count($uploadedFiles) > 0,
            'files' => $uploadedFiles,
            'errors' => $errors
        ];
    }

    public function extractTextFromDocument($filePath, $fileType) {
        $text = '';

        switch ($fileType) {
            case 'txt':
                $text = file_get_contents($filePath);
                break;

            case 'pdf':
                $pythonScript = __DIR__ . '/extract_pdf_text.py';
                if (file_exists($pythonScript)) {
                    $command = escapeshellcmd("python3 $pythonScript " . escapeshellarg($filePath));
                    $text = shell_exec($command);
                }
                break;

            case 'docx':
            case 'doc':
                $pythonScript = __DIR__ . '/extract_word_text.py';
                if (file_exists($pythonScript)) {
                    $command = escapeshellcmd("python3 $pythonScript " . escapeshellarg($filePath));
                    $text = shell_exec($command);
                }
                break;

            case 'jpg':
            case 'jpeg':
            case 'png':
                // Basic OCR placeholder - you can integrate with existing OCR
                $text = "[Image file - OCR processing needed]";
                break;
        }

        return $text;
    }
}
?>