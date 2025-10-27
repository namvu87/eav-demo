<!-- File Upload Component -->
<div class="file-upload-container">
    <div class="file-upload-area" id="fileUploadArea">
        <div class="file-upload-content">
            <svg class="file-upload-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
            <div class="file-upload-text">
                <p class="file-upload-title">Kéo thả file vào đây hoặc click để chọn</p>
                <p class="file-upload-subtitle">
                    Tối đa <span id="maxFileCountDisplay">3</span> files, 
                    kích thước <span id="maxFileSizeDisplay">1 MB</span> mỗi file
                </p>
                <p class="file-upload-extensions">
                    Loại file: <span id="allowedExtensionsDisplay">JPG, PNG</span>
                </p>
            </div>
        </div>
        <input type="file" id="fileInput" multiple accept="" style="display: none;">
    </div>
    
    <!-- File Preview List -->
    <div id="filePreviewList" class="file-preview-list hidden">
        <div class="file-preview-header">
            <h4 class="file-preview-title">Files đã chọn</h4>
            <button type="button" id="clearAllFiles" class="clear-all-btn">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Xóa tất cả
            </button>
        </div>
        <div id="fileList" class="file-list">
            <!-- Files will be added here dynamically -->
        </div>
    </div>
    
    <!-- Upload Progress -->
    <div id="uploadProgress" class="upload-progress hidden">
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill"></div>
        </div>
        <div class="progress-text">
            <span id="progressText">Đang upload...</span>
            <span id="progressPercent">0%</span>
        </div>
    </div>
</div>

<style>
.file-upload-container {
    @apply w-full;
}

.file-upload-area {
    @apply border-2 border-dashed border-gray-300 rounded-lg p-8 text-center cursor-pointer transition-colors duration-200 hover:border-indigo-400 hover:bg-indigo-50;
}

.file-upload-area.dragover {
    @apply border-indigo-500 bg-indigo-100;
}

.file-upload-content {
    @apply flex flex-col items-center space-y-4;
}

.file-upload-icon {
    @apply h-12 w-12 text-gray-400;
}

.file-upload-text {
    @apply space-y-2;
}

.file-upload-title {
    @apply text-lg font-medium text-gray-900;
}

.file-upload-subtitle {
    @apply text-sm text-gray-500;
}

.file-upload-extensions {
    @apply text-xs text-gray-400;
}

.file-preview-list {
    @apply mt-4 bg-white border border-gray-200 rounded-lg;
}

.file-preview-header {
    @apply flex justify-between items-center p-4 border-b border-gray-200;
}

.file-preview-title {
    @apply text-sm font-medium text-gray-900;
}

.clear-all-btn {
    @apply flex items-center space-x-1 text-sm text-red-600 hover:text-red-500;
}

.file-list {
    @apply divide-y divide-gray-200;
}

.file-item {
    @apply flex items-center justify-between p-4 hover:bg-gray-50;
}

.file-info {
    @apply flex items-center space-x-3;
}

.file-icon {
    @apply h-8 w-8 text-gray-400;
}

.file-details {
    @apply flex-1;
}

.file-name {
    @apply text-sm font-medium text-gray-900 truncate;
}

.file-size {
    @apply text-xs text-gray-500;
}

.file-actions {
    @apply flex items-center space-x-2;
}

.remove-file-btn {
    @apply text-red-600 hover:text-red-500;
}

.upload-progress {
    @apply mt-4 bg-gray-50 rounded-lg p-4;
}

.progress-bar {
    @apply w-full bg-gray-200 rounded-full h-2 mb-2;
}

.progress-fill {
    @apply bg-indigo-600 h-2 rounded-full transition-all duration-300;
    width: 0%;
}

.progress-text {
    @apply flex justify-between text-sm text-gray-600;
}

.file-error {
    @apply text-red-600 text-xs mt-1;
}

.file-success {
    @apply text-green-600 text-xs mt-1;
}
</style>

<script>
class FileUploadManager {
    constructor(attributeConfig) {
        this.config = attributeConfig;
        this.files = [];
        this.maxFiles = parseInt(attributeConfig.max_file_count || 3);
        this.maxSize = parseInt(attributeConfig.max_file_size_kb || 1024);
        this.allowedExtensions = (attributeConfig.allowed_extensions || 'jpg,png').split(',').map(ext => ext.trim().toLowerCase());
        
        this.init();
    }
    
    init() {
        this.setupEventListeners();
        this.updateDisplay();
    }
    
    setupEventListeners() {
        const uploadArea = document.getElementById('fileUploadArea');
        const fileInput = document.getElementById('fileInput');
        const clearAllBtn = document.getElementById('clearAllFiles');
        
        // Click to select files
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // File input change
        fileInput.addEventListener('change', (e) => {
            this.handleFiles(e.target.files);
        });
        
        // Drag and drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            this.handleFiles(e.dataTransfer.files);
        });
        
        // Clear all files
        clearAllBtn.addEventListener('click', () => {
            this.clearAllFiles();
        });
    }
    
    handleFiles(fileList) {
        const newFiles = Array.from(fileList);
        const validFiles = [];
        
        newFiles.forEach(file => {
            if (this.validateFile(file)) {
                validFiles.push(file);
            }
        });
        
        // Check if adding these files would exceed max count
        if (this.files.length + validFiles.length > this.maxFiles) {
            alert(`Chỉ được upload tối đa ${this.maxFiles} files`);
            return;
        }
        
        this.files.push(...validFiles);
        this.updateFileList();
        this.updateDisplay();
    }
    
    validateFile(file) {
        // Check file extension
        const extension = file.name.split('.').pop().toLowerCase();
        if (!this.allowedExtensions.includes(extension)) {
            alert(`File ${file.name} có extension không được phép. Chỉ chấp nhận: ${this.allowedExtensions.join(', ')}`);
            return false;
        }
        
        // Check file size
        const fileSizeKB = file.size / 1024;
        if (fileSizeKB > this.maxSize) {
            alert(`File ${file.name} quá lớn. Kích thước tối đa: ${this.maxSize} KB`);
            return false;
        }
        
        return true;
    }
    
    removeFile(index) {
        this.files.splice(index, 1);
        this.updateFileList();
        this.updateDisplay();
    }
    
    clearAllFiles() {
        this.files = [];
        this.updateFileList();
        this.updateDisplay();
    }
    
    updateFileList() {
        const fileList = document.getElementById('fileList');
        const previewList = document.getElementById('filePreviewList');
        
        if (this.files.length === 0) {
            previewList.classList.add('hidden');
            return;
        }
        
        previewList.classList.remove('hidden');
        
        fileList.innerHTML = this.files.map((file, index) => `
            <div class="file-item">
                <div class="file-info">
                    <svg class="file-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <div class="file-details">
                        <div class="file-name">${file.name}</div>
                        <div class="file-size">${this.formatFileSize(file.size)}</div>
                    </div>
                </div>
                <div class="file-actions">
                    <button type="button" onclick="fileUploadManager.removeFile(${index})" class="remove-file-btn">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        `).join('');
    }
    
    updateDisplay() {
        // Update max file count display
        document.getElementById('maxFileCountDisplay').textContent = this.maxFiles;
        
        // Update max file size display
        const sizeInMB = Math.round(this.maxSize / 1024 * 100) / 100;
        document.getElementById('maxFileSizeDisplay').textContent = `${sizeInMB} MB`;
        
        // Update allowed extensions display
        document.getElementById('allowedExtensionsDisplay').textContent = this.allowedExtensions.map(ext => ext.toUpperCase()).join(', ');
        
        // Update file input accept attribute
        const fileInput = document.getElementById('fileInput');
        fileInput.accept = this.allowedExtensions.map(ext => `.${ext}`).join(',');
    }
    
    formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    getFiles() {
        return this.files;
    }
    
    getFileData() {
        return this.files.map(file => ({
            name: file.name,
            size: file.size,
            type: file.type,
            lastModified: file.lastModified
        }));
    }
}

// Global variable to store file upload manager
let fileUploadManager = null;

// Initialize file upload when attribute type is file
function initializeFileUpload(attributeConfig) {
    if (fileUploadManager) {
        fileUploadManager.clearAllFiles();
    }
    
    fileUploadManager = new FileUploadManager(attributeConfig);
}

// Get file data for form submission
function getFileUploadData() {
    if (!fileUploadManager) return null;
    
    return {
        files: fileUploadManager.getFileData(),
        count: fileUploadManager.getFiles().length,
        config: fileUploadManager.config
    };
}
</script>
