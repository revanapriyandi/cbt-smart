<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Report Generation Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Generate Report</h1>
            <p class="mt-2 text-sm text-gray-600">Create customized reports for your CBT system</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="/admin/reports" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 focus:ring-2 focus:ring-gray-500">
                <i class="fas fa-arrow-left mr-2"></i>Back to Reports
            </a>
        </div>
    </div>
</div>

<!-- Report Generation Form -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-medium text-gray-900">Report Configuration</h3>
    </div>

    <form id="reportGenerationForm" class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Report Type Selection -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="relative">
                        <input type="radio" name="report_type" value="exam_results" id="exam_results" class="peer hidden">
                        <label for="exam_results" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-300 peer-checked:border-blue-500 peer-checked:bg-blue-50">
                            <i class="fas fa-chart-bar text-2xl text-blue-600 mb-2"></i>
                            <span class="font-medium text-gray-900">Exam Results</span>
                            <span class="text-sm text-gray-500 text-center">Comprehensive exam results and performance analysis</span>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" name="report_type" value="student_performance" id="student_performance" class="peer hidden">
                        <label for="student_performance" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 peer-checked:border-green-500 peer-checked:bg-green-50">
                            <i class="fas fa-user-graduate text-2xl text-green-600 mb-2"></i>
                            <span class="font-medium text-gray-900">Student Performance</span>
                            <span class="text-sm text-gray-500 text-center">Individual and class-wide student performance</span>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" name="report_type" value="exam_analytics" id="exam_analytics" class="peer hidden">
                        <label for="exam_analytics" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-300 peer-checked:border-purple-500 peer-checked:bg-purple-50">
                            <i class="fas fa-analytics text-2xl text-purple-600 mb-2"></i>
                            <span class="font-medium text-gray-900">Exam Analytics</span>
                            <span class="text-sm text-gray-500 text-center">Detailed analytics on exam difficulty</span>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" name="report_type" value="attendance" id="attendance" class="peer hidden">
                        <label for="attendance" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-300 peer-checked:border-yellow-500 peer-checked:bg-yellow-50">
                            <i class="fas fa-calendar-check text-2xl text-yellow-600 mb-2"></i>
                            <span class="font-medium text-gray-900">Attendance</span>
                            <span class="text-sm text-gray-500 text-center">Student attendance and participation</span>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" name="report_type" value="system_usage" id="system_usage" class="peer hidden">
                        <label for="system_usage" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 peer-checked:border-red-500 peer-checked:bg-red-50">
                            <i class="fas fa-server text-2xl text-red-600 mb-2"></i>
                            <span class="font-medium text-gray-900">System Usage</span>
                            <span class="text-sm text-gray-500 text-center">System utilization and performance</span>
                        </label>
                    </div>

                    <div class="relative">
                        <input type="radio" name="report_type" value="progress_tracking" id="progress_tracking" class="peer hidden">
                        <label for="progress_tracking" class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50">
                            <i class="fas fa-chart-line text-2xl text-indigo-600 mb-2"></i>
                            <span class="font-medium text-gray-900">Progress Tracking</span>
                            <span class="text-sm text-gray-500 text-center">Student progress over time</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="lg:col-span-2">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Filters</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="date" name="filters[start_date]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="date" name="filters[end_date]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Class Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Class</label>
                        <select name="filters[class_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">All Classes</option>
                            <?php if (isset($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class['id'] ?>"><?= esc($class['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Subject Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                        <select name="filters[subject_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">All Subjects</option>
                            <?php if (isset($subjects)): ?>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Exam Filter -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Specific Exam (Optional)</label>
                        <select name="filters[exam_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">All Exams</option>
                            <?php if (isset($exams)): ?>
                                <?php foreach ($exams as $exam): ?>
                                    <option value="<?= $exam['id'] ?>"><?= esc($exam['title']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Format and Options -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Report Format</label>
                <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                    <option value="pdf">PDF</option>
                    <option value="excel">Excel</option>
                    <option value="csv">CSV</option>
                </select>
            </div>

            <!-- Report Options -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="checkbox" name="options[include_summary]" value="1" checked class="mr-2">
                        <span class="text-sm">Include executive summary</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="options[include_charts]" value="1" class="mr-2">
                        <span class="text-sm">Include charts and graphs</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" name="options[include_detailed]" value="1" class="mr-2">
                        <span class="text-sm">Include detailed breakdown</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 mt-8 pt-6 border-t border-gray-200">
            <a href="/admin/reports" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring-2 focus:ring-gray-500">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-cog mr-2"></i>Generate Report
            </button>
        </div>
    </form>
</div>

<!-- Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Generating Report</h3>
            </div>
            <div class="p-6">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin text-4xl text-blue-600 mb-4"></i>
                    <p class="text-gray-600">Please wait while your report is being generated...</p>
                    <div class="mt-4 bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: 0%" id="progressBar"></div>
                    </div>
                    <p class="text-sm text-gray-500 mt-2" id="progressText">Initializing...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reportGenerationForm');
        const progressModal = document.getElementById('progressModal');
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('progressText');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            generateReport();
        });

        function generateReport() {
            // Validate report type selection
            const reportType = document.querySelector('input[name="report_type"]:checked');
            if (!reportType) {
                showError('Please select a report type');
                return;
            }

            // Show progress modal
            progressModal.classList.remove('hidden');
            updateProgress(10, 'Preparing report data...');

            // Prepare form data
            const formData = new FormData(form);

            // Send request
            fetch('/admin/reports/process-generation', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    updateProgress(50, 'Processing data...');

                    setTimeout(() => {
                        updateProgress(80, 'Generating file...');

                        setTimeout(() => {
                            updateProgress(100, 'Complete!');

                            setTimeout(() => {
                                progressModal.classList.add('hidden');

                                if (data.success) {
                                    showSuccess('Report generated successfully!');
                                    // Redirect to download or reports list
                                    if (data.download_url) {
                                        window.location.href = data.download_url;
                                    } else {
                                        window.location.href = '/admin/reports';
                                    }
                                } else {
                                    showError(data.message || 'Failed to generate report');
                                }
                            }, 1000);
                        }, 1000);
                    }, 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    progressModal.classList.add('hidden');
                    showError('Error generating report');
                });
        }

        function updateProgress(percent, text) {
            progressBar.style.width = percent + '%';
            progressText.textContent = text;
        }

        function showSuccess(message) {
            // You can integrate with your notification system here
            alert('Success: ' + message);
        }

        function showError(message) {
            // You can integrate with your notification system here
            alert('Error: ' + message);
        }
    });
</script>

<?= $this->endSection() ?>