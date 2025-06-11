<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Reports Management Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Reports Management</h1>
            <p class="mt-2 text-sm text-gray-600">Generate and manage comprehensive reports for your CBT system</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="/admin/reports/generate" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-plus mr-2"></i>Generate Report
            </a>
        </div>
    </div>
</div>

<!-- Report Types Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php foreach ($report_types as $type => $info): ?>
        <div class="bg-white rounded-lg shadow hover:shadow-md transition-shadow duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-<?= $info['color'] ?>-500 rounded-lg flex items-center justify-center">
                            <i class="<?= $info['icon'] ?> text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-medium text-gray-900"><?= $info['title'] ?></h3>
                        <p class="text-sm text-gray-500 mt-1"><?= $info['description'] ?></p>
                    </div>
                </div>
                <div class="mt-4">
                    <button onclick="generateReport('<?= $type ?>')" class="w-full bg-<?= $info['color'] ?>-50 text-<?= $info['color'] ?>-700 px-4 py-2 rounded-md hover:bg-<?= $info['color'] ?>-100 transition-colors duration-200">
                        Generate Report
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Recent Reports -->
<div class="bg-white rounded-lg shadow">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">Recent Reports</h3>
            <div class="flex space-x-2">
                <button onclick="refreshReportsList()" class="px-3 py-1 text-sm bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
                <button onclick="cleanupExpiredReports()" class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200">
                    <i class="fas fa-trash mr-1"></i>Cleanup
                </button>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Search and Filter -->
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0 sm:space-x-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="searchReports" placeholder="Search reports..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex space-x-2">
                <select id="statusFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="generating">Generating</option>
                    <option value="completed">Completed</option>
                    <option value="failed">Failed</option>
                    <option value="expired">Expired</option>
                </select>
                <select id="typeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <?php foreach ($report_types as $type => $info): ?>
                        <option value="<?= $type ?>"><?= $info['title'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Reports Table -->
        <div class="overflow-x-auto">
            <table id="reportsTable" class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Report</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Table rows will be populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div id="generateReportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Generate Report</h3>
            </div>
            <form id="generateReportForm" class="p-6">
                <div class="space-y-6">
                    <!-- Report Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select name="report_type" id="reportType" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <?php foreach ($report_types as $type => $info): ?>
                                <option value="<?= $type ?>"><?= $info['title'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="filters[start_date]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="filters[end_date]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Class</label> <select name="filters[class_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="">All Classes</option>
                                <?php if (isset($classes)): ?>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['id'] ?>"><?= esc($class['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label> <select name="filters[subject_id]" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                <option value="">All Subjects</option>
                                <?php if (isset($subjects)): ?>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Format -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" checked class="mr-2">
                                <span class="text-sm">PDF Report</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" class="mr-2">
                                <span class="text-sm">Excel Spreadsheet</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="csv" class="mr-2">
                                <span class="text-sm">CSV Data</span>
                            </label>
                        </div>
                    </div>

                    <!-- Options -->
                    <div id="reportOptions">
                        <!-- Dynamic options based on report type -->
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeGenerateModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-cog mr-2"></i>Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Details Modal -->
<div id="reportDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Report Details</h3>
            </div>
            <div id="reportDetailsContent" class="p-6">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end">
                <button onclick="closeDetailsModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.tailwindcss.min.css">

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.tailwindcss.min.js"></script>

<script>
    let reportsTable;

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        initializeReportsTable();
        initializeEventListeners();
    });

    // Initialize DataTables
    function initializeReportsTable() {
        reportsTable = $('#reportsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/admin/reports/list',
                type: 'GET',
                data: function(d) {
                    d.status = $('#statusFilter').val();
                    d.type = $('#typeFilter').val();
                    d.search = $('#searchReports').val();
                }
            },
            columns: [{
                    data: 'title',
                    render: function(data, type, row) {
                        return `
                        <div>
                            <div class="text-sm font-medium text-gray-900">${data}</div>
                            ${row.description ? `<div class="text-sm text-gray-500">${row.description}</div>` : ''}
                        </div>
                    `;
                    }
                },
                {
                    data: 'report_type',
                    render: function(data) {
                        const types = {
                            'exam_results': 'Exam Results',
                            'student_performance': 'Student Performance',
                            'exam_analytics': 'Exam Analytics',
                            'attendance': 'Attendance',
                            'system_usage': 'System Usage',
                            'progress_tracking': 'Progress Tracking'
                        };
                        return `<span class="text-sm text-gray-900">${types[data] || data}</span>`;
                    }
                },
                {
                    data: 'status',
                    render: function(data) {
                        const colors = {
                            'pending': 'bg-yellow-100 text-yellow-800',
                            'generating': 'bg-blue-100 text-blue-800',
                            'completed': 'bg-green-100 text-green-800',
                            'failed': 'bg-red-100 text-red-800',
                            'expired': 'bg-gray-100 text-gray-800'
                        };
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${colors[data] || 'bg-gray-100 text-gray-800'}">${data.charAt(0).toUpperCase() + data.slice(1)}</span>`;
                    }
                },
                {
                    data: 'generated_by_name',
                    render: function(data) {
                        return `<span class="text-sm text-gray-900">${data}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return `<span class="text-sm text-gray-500">${formatDateTime(data)}</span>`;
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        let actions = `
                        <div class="flex items-center space-x-2">
                            <button onclick="viewReportDetails(${row.id})" class="text-blue-600 hover:text-blue-900" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                    `;

                        if (row.status === 'completed') {
                            actions += `
                            <button onclick="downloadReport(${row.id})" class="text-green-600 hover:text-green-900" title="Download">
                                <i class="fas fa-download"></i>
                            </button>
                        `;
                        }

                        actions += `
                            <button onclick="deleteReport(${row.id})" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;

                        return actions;
                    }
                }
            ],
            pageLength: 25,
            order: [
                [4, 'desc']
            ],
            responsive: true,
            language: {
                processing: "Loading reports...",
                search: "",
                searchPlaceholder: "Search reports...",
                lengthMenu: "Show _MENU_ reports per page",
                info: "Showing _START_ to _END_ of _TOTAL_ reports",
                infoEmpty: "No reports found",
                infoFiltered: "(filtered from _MAX_ total reports)"
            }
        });
    }

    // Initialize event listeners
    function initializeEventListeners() {
        // Filter change handlers
        $('#statusFilter, #typeFilter').on('change', function() {
            reportsTable.ajax.reload();
        });

        // Search input handler
        $('#searchReports').on('keyup', function() {
            reportsTable.ajax.reload();
        });

        // Report type change handler
        $('#reportType').on('change', function() {
            updateReportOptions($(this).val());
        });

        // Generate report form submission
        $('#generateReportForm').on('submit', function(e) {
            e.preventDefault();
            processReportGeneration();
        });
    }

    // Generate report
    function generateReport(reportType = null) {
        if (reportType) {
            $('#reportType').val(reportType);
            updateReportOptions(reportType);
        }
        $('#generateReportModal').removeClass('hidden');
    }

    // Update report options based on type
    function updateReportOptions(reportType) {
        const optionsContainer = $('#reportOptions');
        let optionsHtml = '';

        switch (reportType) {
            case 'student_performance':
                optionsHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="options[include_detailed]" value="1" class="mr-2">
                            <span class="text-sm">Include detailed exam results</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="options[include_charts]" value="1" class="mr-2">
                            <span class="text-sm">Include performance charts</span>
                        </label>
                    </div>
                </div>
            `;
                break;
            case 'exam_analytics':
                optionsHtml = `
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Options</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="options[include_difficulty]" value="1" class="mr-2">
                            <span class="text-sm">Include difficulty analysis</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="options[include_questions]" value="1" class="mr-2">
                            <span class="text-sm">Include question-wise analysis</span>
                        </label>
                    </div>
                </div>
            `;
                break;
            default:
                optionsHtml = `
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
                    </div>
                </div>
            `;
        }

        optionsContainer.html(optionsHtml);
    }

    // Process report generation
    function processReportGeneration() {
        const formData = new FormData($('#generateReportForm')[0]);
        const submitButton = $('#generateReportForm button[type="submit"]');

        // Disable submit button and show loading
        submitButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i>Generating...');

        fetch('/admin/reports/process-generation', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Report generation started successfully');
                    closeGenerateModal();
                    refreshReportsList();

                    // Show progress notification
                    showProgressNotification(data.report_id);
                } else {
                    showError(data.message || 'Failed to generate report');
                }
            })
            .catch(error => {
                console.error('Error generating report:', error);
                showError('Error generating report');
            })
            .finally(() => {
                // Re-enable submit button
                submitButton.prop('disabled', false).html('<i class="fas fa-cog mr-2"></i>Generate Report');
            });
    }

    // Show progress notification
    function showProgressNotification(reportId) {
        // This would typically integrate with a real-time notification system
        // For now, we'll just show a simple notification
        const notification = $(`
        <div id="progress-${reportId}" class="fixed top-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50">
            <div class="flex items-center">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span>Generating report... This may take a few minutes.</span>
                <button onclick="$('#progress-${reportId}').remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `);

        $('body').append(notification);

        // Auto-remove after 10 seconds
        setTimeout(() => {
            $(`#progress-${reportId}`).fadeOut();
        }, 10000);
    }

    // View report details
    function viewReportDetails(reportId) {
        // Load report details via AJAX
        fetch(`/admin/reports/details/${reportId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayReportDetails(data.report);
                } else {
                    showError('Failed to load report details');
                }
            })
            .catch(error => {
                console.error('Error loading report details:', error);
                showError('Error loading report details');
            });
    }

    // Display report details
    function displayReportDetails(report) {
        const content = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Report Title</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.title}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Report Type</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.report_type}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Status</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.status}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Format</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.format.toUpperCase()}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Created</h4>
                    <p class="mt-1 text-sm text-gray-900">${formatDateTime(report.created_at)}</p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Generated By</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.generated_by_name}</p>
                </div>
            </div>
            
            ${report.description ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500">Description</h4>
                    <p class="mt-1 text-sm text-gray-900">${report.description}</p>
                </div>
            ` : ''}
            
            ${report.filters ? `
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-2">Filters</h4>
                    <div class="bg-gray-50 p-3 rounded">
                        <pre class="text-xs text-gray-600">${JSON.stringify(report.filters, null, 2)}</pre>
                    </div>
                </div>
            ` : ''}
            
            ${report.status === 'completed' ? `
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-2"></i>
                        <span class="text-green-800 font-medium">Report completed successfully</span>
                    </div>
                    <div class="mt-2">
                        <button onclick="downloadReport(${report.id})" class="inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>Download Report
                        </button>
                    </div>
                </div>
            ` : ''}
            
            ${report.status === 'failed' && report.error_message ? `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                        <span class="text-red-800 font-medium">Report generation failed</span>
                    </div>
                    <div class="mt-2 text-sm text-red-700">${report.error_message}</div>
                </div>
            ` : ''}
        </div>
    `;

        $('#reportDetailsContent').html(content);
        $('#reportDetailsModal').removeClass('hidden');
    }

    // Download report
    function downloadReport(reportId) {
        window.location.href = `/admin/reports/download/${reportId}`;
    }

    // Delete report
    function deleteReport(reportId) {
        if (!confirm('Are you sure you want to delete this report? This action cannot be undone.')) {
            return;
        }

        fetch(`/admin/reports/delete/${reportId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess('Report deleted successfully');
                    refreshReportsList();
                } else {
                    showError(data.message || 'Failed to delete report');
                }
            })
            .catch(error => {
                console.error('Error deleting report:', error);
                showError('Error deleting report');
            });
    }

    // Cleanup expired reports
    function cleanupExpiredReports() {
        if (!confirm('Are you sure you want to cleanup all expired reports? This action cannot be undone.')) {
            return;
        }

        fetch('/admin/reports/cleanup-expired', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccess(`${data.deleted_count} expired reports cleaned up successfully`);
                    refreshReportsList();
                } else {
                    showError(data.message || 'Failed to cleanup expired reports');
                }
            })
            .catch(error => {
                console.error('Error cleaning up reports:', error);
                showError('Error cleaning up expired reports');
            });
    }

    // Refresh reports list
    function refreshReportsList() {
        reportsTable.ajax.reload();
    }

    // Close modals
    function closeGenerateModal() {
        $('#generateReportModal').addClass('hidden');
        $('#generateReportForm')[0].reset();
    }

    function closeDetailsModal() {
        $('#reportDetailsModal').addClass('hidden');
    }

    // Utility functions
    function formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function showSuccess(message) {
        // Implement success notification
        console.log('Success:', message);
    }

    function showError(message) {
        // Implement error notification
        console.error('Error:', message);
    }
</script>

<?= $this->endSection() ?>