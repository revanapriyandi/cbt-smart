<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
                Backup Management
            </h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Create, manage and restore database backups</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="createBackupBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Backup
            </button>
        </div>
    </div>    <!-- Backup Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Backups</dt>
                            <dd class="text-lg font-medium text-gray-900" id="totalBackups">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Latest Backup</dt>
                            <dd class="text-lg font-medium text-gray-900" id="latestBackup">Never</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Size</dt>
                            <dd class="text-lg font-medium text-gray-900" id="totalSize">0 MB</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Old Backups</dt>
                            <dd class="text-lg font-medium text-gray-900" id="oldBackups">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Actions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Upload Backup</h3>
                </div>
                <div class="p-6">
                    <form id="uploadBackupForm" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="backupFile" class="block text-sm font-medium text-gray-700 mb-2">Select Backup File (.sql)</label>
                            <input type="file" id="backupFile" name="backup_file" accept=".sql"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Upload Backup
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Backup Maintenance</h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-600 mb-4">Remove old backup files to free up disk space.</p>
                    <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium flex items-center" id="cleanupBtn">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Cleanup Old Backups
                    </button>
                </div>
            </div>
        </div>

        <!-- Backup Files Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Backup Files</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="backupTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Filename</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Size</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Data will be loaded via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
            <p class="mt-2 text-gray-600" id="loadingText">Processing...</p>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Confirm Database Restore</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="closeRestoreModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="mb-4">
            <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-800">
                            <strong>Warning:</strong> This action will replace all current data with the backup data. This action cannot be undone.
                        </p>
                    </div>
                </div>
            </div>
            <p class="text-gray-700">Are you sure you want to restore from backup: <strong id="restoreFilename"></strong>?</p>
        </div>
        <div class="flex justify-end space-x-3">
            <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md" onclick="closeRestoreModal()">Cancel</button>
            <button type="button" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md flex items-center" id="confirmRestoreBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                </svg>
                Restore Database
            </button>
        </div>
    </div>
</div>
</div>

<script>
    $(document).ready(function() {
        let backupTable;
        let currentRestoreFile = '';

        // Initialize DataTable
        backupTable = $('#backupTable').DataTable({
            ajax: {
                url: '<?= base_url('admin/backup/data') ?>',
                type: 'GET',
                dataSrc: 'backups'
            },
            columns: [{
                    data: 'filename'
                },
                {
                    data: 'size',
                    render: function(data) {
                        return formatFileSize(data);
                    }
                },
                {
                    data: 'created',
                    render: function(data) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <div class="flex space-x-2">
                            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs download-btn" data-filename="${row.filename}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </button>
                            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-xs restore-btn" data-filename="${row.filename}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"></path>
                                </svg>
                            </button>
                            <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-xs delete-btn" data-filename="${row.filename}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    `;
                    }
                }
            ],
            order: [
                [2, 'desc']
            ],
            drawCallback: function() {
                bindTableActions();
            }
        });

        // Load statistics
        loadBackupStats();

        // Show/hide modals
        function showModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function hideModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        // Create backup
        $('#createBackupBtn').click(function() {
            $('#loadingText').text('Creating backup...');
            showModal('loadingModal');

            $.ajax({
                url: '<?= base_url('admin/backup/create') ?>',
                type: 'POST',
                success: function(response) {
                    hideModal('loadingModal');
                    if (response.success) {
                        showNotification(response.message, 'success');
                        backupTable.ajax.reload();
                        loadBackupStats();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    hideModal('loadingModal');
                    showNotification('Failed to create backup', 'error');
                }
            });
        });

        // Upload backup
        $('#uploadBackupForm').submit(function(e) {
            e.preventDefault();

            let formData = new FormData(this);
            $('#loadingText').text('Uploading backup...');
            showModal('loadingModal');

            $.ajax({
                url: '<?= base_url('admin/backup/upload') ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    hideModal('loadingModal');
                    if (response.success) {
                        showNotification(response.message, 'success');
                        $('#uploadBackupForm')[0].reset();
                        backupTable.ajax.reload();
                        loadBackupStats();
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    hideModal('loadingModal');
                    showNotification('Failed to upload backup', 'error');
                }
            });
        });

        // Cleanup old backups
        $('#cleanupBtn').click(function() {
            if (confirm('This will remove backup files older than 30 days. Continue?')) {
                $.ajax({
                    url: '<?= base_url('admin/backup/cleanup') ?>',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification(response.message, 'success');
                            backupTable.ajax.reload();
                            loadBackupStats();
                        } else {
                            showNotification(response.message, 'error');
                        }
                    },
                    error: function() {
                        showNotification('Failed to cleanup backups', 'error');
                    }
                });
            }
        });

        // Restore confirmation
        $('#confirmRestoreBtn').click(function() {
            hideModal('restoreModal');
            $('#loadingText').text('Restoring database...');
            showModal('loadingModal');

            $.ajax({
                url: '<?= base_url('admin/backup/restore/') ?>' + currentRestoreFile,
                type: 'POST',
                success: function(response) {
                    hideModal('loadingModal');
                    if (response.success) {
                        showNotification(response.message, 'success');
                    } else {
                        showNotification(response.message, 'error');
                    }
                },
                error: function() {
                    hideModal('loadingModal');
                    showNotification('Failed to restore database', 'error');
                }
            });
        });

        // Close modal functions
        window.closeRestoreModal = function() {
            hideModal('restoreModal');
        };

        function bindTableActions() {
            // Download backup
            $('.download-btn').off('click').on('click', function() {
                let filename = $(this).data('filename');
                window.location.href = '<?= base_url('admin/backup/download/') ?>' + filename;
            });

            // Restore backup
            $('.restore-btn').off('click').on('click', function() {
                let filename = $(this).data('filename');
                currentRestoreFile = filename;
                $('#restoreFilename').text(filename);
                showModal('restoreModal');
            });

            // Delete backup
            $('.delete-btn').off('click').on('click', function() {
                let filename = $(this).data('filename');

                if (confirm('Delete backup "' + filename + '"? This action cannot be undone.')) {
                    $.ajax({
                        url: '<?= base_url('admin/backup/delete/') ?>' + filename,
                        type: 'POST',
                        success: function(response) {
                            if (response.success) {
                                showNotification(response.message, 'success');
                                backupTable.ajax.reload();
                                loadBackupStats();
                            } else {
                                showNotification(response.message, 'error');
                            }
                        },
                        error: function() {
                            showNotification('Failed to delete backup', 'error');
                        }
                    });
                }
            });
        }

        function loadBackupStats() {
            $.ajax({
                url: '<?= base_url('admin/backup/stats') ?>',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let stats = response.stats;
                        $('#totalBackups').text(stats.total_backups);
                        $('#latestBackup').text(stats.latest_backup || 'Never');
                        $('#totalSize').text(formatFileSize(stats.total_size));
                        $('#oldBackups').text(stats.old_backups);
                    }
                }
            });
        }

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const notification = $(`
                <div class="fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded shadow-lg z-50 notification">
                    ${message}
                </div>
            `);
            $('body').append(notification);
            setTimeout(() => {
                notification.fadeOut(() => notification.remove());
            }, 3000);
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
    });
</script>
<?= $this->endSection() ?>