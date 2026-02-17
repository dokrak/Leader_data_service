<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leader Data Service - File Manager</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .header-logo {
            width: 100px;
            height: 100px;
            background: white;
            border-radius: 50%;
            padding: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in;
        }

        .header-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header-content {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 0.9rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-card p {
            font-size: 1.8rem;
            font-weight: bold;
        }

        .upload-section {
            border: 3px dashed #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            background: #f8f9ff;
            cursor: pointer;
            transition: all 0.3s;
        }

        .upload-section:hover {
            background: #eef1ff;
            border-color: #764ba2;
        }

        .upload-section.dragover {
            background: #e0e7ff;
            border-color: #4f46e5;
        }

        .file-input {
            display: none;
        }

        .upload-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .form-group {
            margin: 20px 0;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 30px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .files-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .files-table th,
        .files-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .files-table th {
            background: #f8f9ff;
            font-weight: 600;
            color: #4a5568;
        }

        .files-table tr:hover {
            background: #f8f9ff;
        }

        .action-btn {
            padding: 8px 16px;
            margin-right: 8px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .download-btn {
            background: #48bb78;
            color: white;
        }

        .download-btn:hover {
            background: #38a169;
        }

        .delete-btn {
            background: #f56565;
            color: white;
        }

        .delete-btn:hover {
            background: #e53e3e;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
        }

        .alert.error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert.show {
            display: block;
        }

        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e2e8f0;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 15px;
            display: none;
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            width: 0%;
            transition: width 0.3s;
        }

        .file-size {
            color: #718096;
            font-size: 0.9rem;
        }

        .no-files {
            text-align: center;
            padding: 40px;
            color: #718096;
        }

        .search-filter {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-filter input {
            flex: 1;
            padding: 10px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
        }

        .selected-file {
            margin-top: 15px;
            padding: 12px;
            background: #e0e7ff;
            border-radius: 8px;
            color: #4c51bf;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-logo">
                <img src="/images/chomthong-hospital-logo.svg" alt="Chomthong Hospital Logo">
            </div>
            <div class="header-content">
                <h1>📁 Leader Data Service</h1>
                <p>File Management System</p>
                <p style="font-size: 0.9rem; margin-top: 5px;">Chomthong Hospital - Chiang Mai</p>
            </div>
        </div>

        <div id="alertBox" class="alert"></div>

        <!-- Storage Statistics -->
        <div class="card">
            <h2 style="margin-bottom: 20px;">💾 Storage Statistics</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Total Storage</h3>
                    <p id="totalStorage">10 GB</p>
                </div>
                <div class="stat-card">
                    <h3>Used Space</h3>
                    <p id="usedSpace">0 B</p>
                </div>
                <div class="stat-card">
                    <h3>Available Space</h3>
                    <p id="availableSpace">10 GB</p>
                </div>
                <div class="stat-card">
                    <h3>Total Files</h3>
                    <p id="totalFiles">0</p>
                </div>
            </div>
        </div>

        <!-- Upload Section -->
        <div class="card">
            <h2 style="margin-bottom: 20px;">⬆️ Upload File</h2>
            <form id="uploadForm">
                <div class="upload-section" id="dropZone">
                    <div class="upload-icon">📤</div>
                    <h3>Drag & Drop files here</h3>
                    <p>or click to select files</p>
                    <p style="margin-top: 10px; color: #718096; font-size: 0.9rem;">
                        Allowed: PDF, Word, Excel, PowerPoint, Images (Max: 100MB)
                    </p>
                    <input type="file" id="fileInput" class="file-input" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.jpg,.jpeg,.png,.gif,.webp,.svg">
                </div>

                <div id="selectedFileInfo" class="selected-file" style="display: none;"></div>

                <div class="form-group">
                    <label for="uploadedBy">Uploaded By</label>
                    <input type="text" id="uploadedBy" placeholder="Enter your name">
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category">
                        <option value="general">General</option>
                        <option value="documents">Documents</option>
                        <option value="reports">Reports</option>
                        <option value="presentations">Presentations</option>
                        <option value="images">Images</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description (Optional)</label>
                    <textarea id="description" rows="3" placeholder="Add a description for this file"></textarea>
                </div>

                <div class="progress-bar" id="progressBar">
                    <div class="progress-bar-fill" id="progressBarFill"></div>
                </div>

                <button type="submit" class="btn" id="uploadBtn">Upload File</button>
            </form>
        </div>

        <!-- Files List -->
        <div class="card">
            <h2 style="margin-bottom: 20px;">📋 Uploaded Files</h2>
            
            <div class="search-filter">
                <input type="text" id="searchInput" placeholder="🔍 Search files...">
                <select id="filterCategory" style="width: 200px; padding: 10px; border: 2px solid #e2e8f0; border-radius: 8px;">
                    <option value="">All Categories</option>
                    <option value="general">General</option>
                    <option value="documents">Documents</option>
                    <option value="reports">Reports</option>
                    <option value="presentations">Presentations</option>
                    <option value="images">Images</option>
                </select>
            </div>

            <div id="filesContainer">
                <table class="files-table">
                    <thead>
                        <tr>
                            <th>File Name</th>
                            <th>Size</th>
                            <th>Category</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="filesTableBody">
                        <tr>
                            <td colspan="6" class="no-files">No files uploaded yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '/api';
        let selectedFile = null;

        // Load initial data
        document.addEventListener('DOMContentLoaded', () => {
            loadStorageStats();
            loadFiles();
        });

        // Drag and drop functionality
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');

        dropZone.addEventListener('click', () => fileInput.click());

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
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                showSelectedFile(files[0]);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                showSelectedFile(e.target.files[0]);
            }
        });

        function showSelectedFile(file) {
            selectedFile = file;
            const info = document.getElementById('selectedFileInfo');
            info.textContent = `✅ Selected: ${file.name} (${formatFileSize(file.size)})`;
            info.style.display = 'block';
        }

        // Upload form submission
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!selectedFile) {
                showAlert('Please select a file to upload', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('file', selectedFile);
            formData.append('uploaded_by', document.getElementById('uploadedBy').value || 'Unknown');
            formData.append('category', document.getElementById('category').value);
            formData.append('description', document.getElementById('description').value);

            const uploadBtn = document.getElementById('uploadBtn');
            const progressBar = document.getElementById('progressBar');
            const progressBarFill = document.getElementById('progressBarFill');

            uploadBtn.disabled = true;
            uploadBtn.textContent = 'Uploading...';
            progressBar.style.display = 'block';

            try {
                const response = await fetch(`${API_BASE}/files`, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('File uploaded successfully!', 'success');
                    document.getElementById('uploadForm').reset();
                    document.getElementById('selectedFileInfo').style.display = 'none';
                    selectedFile = null;
                    loadStorageStats();
                    loadFiles();
                } else {
                    showAlert(data.message || 'Upload failed', 'error');
                }
            } catch (error) {
                showAlert('Error uploading file: ' + error.message, 'error');
            } finally {
                uploadBtn.disabled = false;
                uploadBtn.textContent = 'Upload File';
                progressBar.style.display = 'none';
                progressBarFill.style.width = '0%';
            }
        });

        // Load storage statistics
        async function loadStorageStats() {
            try {
                const response = await fetch(`${API_BASE}/storage/stats`);
                const data = await response.json();

                document.getElementById('totalStorage').textContent = formatFileSize(data.total_quota);
                document.getElementById('usedSpace').textContent = formatFileSize(data.used_space);
                document.getElementById('availableSpace').textContent = formatFileSize(data.available_space);
                document.getElementById('totalFiles').textContent = data.total_files;
            } catch (error) {
                console.error('Error loading storage stats:', error);
            }
        }

        // Load files list
        async function loadFiles() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('filterCategory').value;
            
            let url = `${API_BASE}/files?per_page=50`;
            if (search) url += `&search=${encodeURIComponent(search)}`;
            if (category) url += `&category=${encodeURIComponent(category)}`;

            try {
                const response = await fetch(url);
                const data = await response.json();

                const tbody = document.getElementById('filesTableBody');
                
                if (data.data && data.data.length > 0) {
                    tbody.innerHTML = data.data.map(file => `
                        <tr>
                            <td>
                                <strong>${escapeHtml(file.original_name)}</strong>
                                ${file.description ? `<br><small style="color: #718096;">${escapeHtml(file.description)}</small>` : ''}
                            </td>
                            <td class="file-size">${formatFileSize(file.file_size)}</td>
                            <td><span style="background: #e0e7ff; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">${file.category}</span></td>
                            <td>${escapeHtml(file.uploaded_by)}</td>
                            <td>${new Date(file.created_at).toLocaleDateString()}</td>
                            <td>
                                <button class="action-btn download-btn" onclick="downloadFile(${file.id}, '${escapeHtml(file.original_name)}')">⬇️ Download</button>
                                <button class="action-btn delete-btn" onclick="deleteFile(${file.id})">🗑️ Delete</button>
                            </td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="6" class="no-files">No files found</td></tr>';
                }
            } catch (error) {
                console.error('Error loading files:', error);
            }
        }

        // Download file
        async function downloadFile(id, filename) {
            window.location.href = `${API_BASE}/files/${id}/download`;
        }

        // Delete file
        async function deleteFile(id) {
            if (!confirm('Are you sure you want to delete this file?')) {
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/files/${id}`, {
                    method: 'DELETE'
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('File deleted successfully!', 'success');
                    loadStorageStats();
                    loadFiles();
                } else {
                    showAlert(data.message || 'Delete failed', 'error');
                }
            } catch (error) {
                showAlert('Error deleting file: ' + error.message, 'error');
            }
        }

        // Search and filter
        document.getElementById('searchInput').addEventListener('input', debounce(loadFiles, 500));
        document.getElementById('filterCategory').addEventListener('change', loadFiles);

        // Utility functions
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 B';
            const k = 1024;
            const sizes = ['B', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = `alert ${type} show`;
            setTimeout(() => {
                alertBox.classList.remove('show');
            }, 5000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    </script>
</body>
</html>
