<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Import Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Import Users</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Bulk import users from CSV file</p>
        </div>
        <div>
            <a href="<?= base_url('admin/users') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Import Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload CSV File</h3>
                <form id="importForm" action="<?= base_url('admin/users/import') ?>" method="POST" enctype="multipart/form-data">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">CSV File *</label>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Only CSV files are allowed (max 5MB)</p>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="update_existing" id="update_existing"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="ml-2 block text-sm text-gray-700">Update existing users</label>
                        </div>
                        <p class="text-xs text-gray-500">If checked, existing users will be updated. Otherwise, duplicates will be skipped.</p>

                        <div class="flex items-center">
                            <input type="checkbox" name="send_notification" id="send_notification"
                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            <label class="ml-2 block text-sm text-gray-700">Send welcome emails</label>
                        </div>
                        <p class="text-xs text-gray-500">Send welcome emails to new users with their login credentials.</p>
                    </div>

                    <div class="mt-6">
                        <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                            </svg>
                            Import Users
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- CSV Format Instructions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">CSV Format Requirements</h3>

                <div class="space-y-4">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-800 mb-2">Required Columns (in order):</h4>
                        <ul class="text-sm text-blue-700 space-y-1">
                            <li><strong>username</strong> - Unique username (letters, numbers, underscores only)</li>
                            <li><strong>email</strong> - Valid email address</li>
                            <li><strong>full_name</strong> - User's full name</li>
                            <li><strong>role</strong> - Must be: admin, teacher, or student</li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-gray-800 mb-2">Example CSV Content:</h4>
                        <pre class="text-xs text-gray-600 font-mono bg-white p-2 rounded border">username,email,full_name,role
john_doe,john@example.com,John Doe,student
jane_smith,jane@example.com,Jane Smith,teacher
admin_user,admin@example.com,Admin User,admin</pre>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-yellow-800 mb-2">Important Notes:</h4>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            <li>• First row must contain column headers</li>
                            <li>• Default password will be set to 'default123'</li>
                            <li>• All imported users will be active by default</li>
                            <li>• Users should change their password on first login</li>
                            <li>• Invalid rows will be skipped and reported</li>
                        </ul>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="<?= base_url('admin/users/sample-csv') ?>"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-center block">
                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Sample CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Section (hidden by default) -->
    <div id="progressSection" class="hidden mt-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Import Progress</h3>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div id="progressBar" class="bg-green-600 h-2 rounded-full" style="width: 0%"></div>
                </div>
                <div id="progressText" class="text-sm text-gray-600 mt-2">Preparing import...</div>
                <div id="progressResults" class="mt-4 space-y-2"></div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('importForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const fileInput = document.getElementById('csv_file');
        const file = fileInput.files[0];

        if (!file) {
            alert('Please select a CSV file');
            return;
        }

        if (!file.name.toLowerCase().endsWith('.csv')) {
            alert('Please select a valid CSV file');
            return;
        }

        if (file.size > 5 * 1024 * 1024) { // 5MB
            alert('File size must be less than 5MB');
            return;
        }

        // Show progress section
        document.getElementById('progressSection').classList.remove('hidden');

        // Create FormData
        const formData = new FormData(this);

        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');
        const progressResults = document.getElementById('progressResults');

        fetch('<?= base_url('admin/users/import') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                progressBar.style.width = '100%';

                if (data.success) {
                    progressText.textContent = 'Import completed successfully!';

                    progressResults.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-green-800">Result</h4>
                        <p class="text-sm text-green-700 mt-1">${data.message}</p>
                    </div>
                `;

                    setTimeout(() => {
                        window.location.href = '<?= base_url('admin/users') ?>';
                    }, 3000);
                } else {
                    progressBar.classList.remove('bg-green-600');
                    progressBar.classList.add('bg-red-600');
                    progressText.textContent = 'Import failed!';

                    progressResults.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-red-800">Error</h4>
                        <p class="text-sm text-red-700 mt-1">${data.message}</p>
                    </div>
                `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                progressBar.style.width = '100%';
                progressBar.classList.remove('bg-green-600');
                progressBar.classList.add('bg-red-600');
                progressText.textContent = 'Import failed!';

                progressResults.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-red-800">Error</h4>
                    <p class="text-sm text-red-700 mt-1">An error occurred while importing users</p>
                </div>
            `;
            });
    });
</script>
<?= $this->endSection() ?>