<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                <svg class="w-8 h-8 text-purple-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                </svg>
                Activity Logs
            </h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Monitor and track system activities</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="exportBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Logs
            </button>
            <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm" id="cleanupBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Cleanup Old Logs
            </button>
        </div>
    </div>

    <!-- Activity Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Today's Activities</dt>
                            <dd class="text-lg font-medium text-gray-900" id="todayActivities">0</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Week</dt>
                            <dd class="text-lg font-medium text-gray-900" id="weekActivities">0</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">This Month</dt>
                            <dd class="text-lg font-medium text-gray-900" id="monthActivities">0</dd>
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                            <dd class="text-lg font-medium text-gray-900" id="activeUsers">0</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- Filters -->
    <div class="bg-white shadow rounded-lg mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                </svg>
                Filters
            </h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <label for="dateFrom" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" id="dateFrom" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" id="dateTo" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <label for="activityType" class="block text-sm font-medium text-gray-700 mb-2">Activity Type</label>
                    <select id="activityType" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Types</option>
                        <option value="login">Login</option>
                        <option value="logout">Logout</option>
                        <option value="exam">Exam</option>
                        <option value="user">User Management</option>
                        <option value="system">System</option>
                        <option value="security">Security</option>
                    </select>
                </div>
                <div>
                    <label for="userSearch" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                    <input type="text" id="userSearch" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-purple-500 focus:border-purple-500" placeholder="Search user...">
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 mt-6">
                <button type="button" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center" id="applyFiltersBtn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                    </svg>
                    Apply Filters
                </button>
                <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center" id="clearFiltersBtn">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Clear Filters
                </button>
            </div>
        </div>
    </div>    <!-- Activity Logs Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Activity Logs</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="activityTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Activity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Agent</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created At</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>
    </div>
</div>

<!-- Activity Details Modal -->
<div id="activityDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-2/3 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Activity Details</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="hideActivityModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="activityDetailContent" class="mb-6">
            <!-- Content will be loaded via JavaScript -->
        </div>
        <div class="flex justify-end">
            <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium" onclick="hideActivityModal()">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-1/2 lg:w-1/3 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between border-b pb-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Export Activity Logs</h3>
            <button type="button" class="text-gray-400 hover:text-gray-600" onclick="hideExportModal()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="space-y-4 mb-6">
            <div>
                <label for="exportFormat" class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                <select id="exportFormat" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                </select>
            </div>
            <div>
                <label for="exportDateFrom" class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                <input type="date" id="exportDateFrom" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
            </div>
            <div>
                <label for="exportDateTo" class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                <input type="date" id="exportDateTo" class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>
        <div class="flex justify-end space-x-3">
            <button type="button" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium" onclick="hideExportModal()">
                Cancel
            </button>
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center" id="confirmExportBtn">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export
            </button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        let activityTable;

        // Initialize DataTable
        activityTable = $('#activityTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('admin/activity-logs/data') ?>',
                type: 'GET',
                data: function(d) {
                    d.date_from = $('#dateFrom').val();
                    d.date_to = $('#dateTo').val();
                    d.activity_type = $('#activityType').val();
                    d.user_search = $('#userSearch').val();
                }
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'user_name',
                    render: function(data, type, row) {
                        return data || 'Guest';
                    }
                },
                {
                    data: 'activity',
                    render: function(data, type, row) {
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                },
                {
                    data: 'activity_type',
                    render: function(data) {
                        let badgeClass = 'bg-gray-500';
                        switch (data) {
                            case 'login':
                                badgeClass = 'bg-green-500';
                                break;
                            case 'logout':
                                badgeClass = 'bg-blue-500';
                                break;
                            case 'exam':
                                badgeClass = 'bg-purple-500';
                                break;
                            case 'user':
                                badgeClass = 'bg-yellow-500';
                                break;
                            case 'system':
                                badgeClass = 'bg-gray-800';
                                break;
                            case 'security':
                                badgeClass = 'bg-red-500';
                                break;
                        }
                        return `<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white ${badgeClass}">${data}</span>`;
                    }
                },
                {
                    data: 'ip_address'
                },
                {
                    data: 'user_agent',
                    render: function(data) {
                        return data ? (data.length > 30 ? data.substring(0, 30) + '...' : data) : 'N/A';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data) {
                        return moment(data).format('YYYY-MM-DD HH:mm:ss');
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        return `
                        <button type="button" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm view-detail-btn" data-id="${row.id}">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    `;
                    }
                }
            ],
            order: [
                [0, 'desc']
            ],
            drawCallback: function() {
                bindTableActions();
            }
        });

        // Load statistics
        loadActivityStats();

        // Apply filters
        $('#applyFiltersBtn').click(function() {
            activityTable.ajax.reload();
        });

        // Clear filters
        $('#clearFiltersBtn').click(function() {
            $('#dateFrom').val('');
            $('#dateTo').val('');
            $('#activityType').val('');
            $('#userSearch').val('');
            activityTable.ajax.reload();
        });

        // Export logs
        $('#exportBtn').click(function() {
            showExportModal();
        });

        $('#confirmExportBtn').click(function() {
            let format = $('#exportFormat').val();
            let dateFrom = $('#exportDateFrom').val();
            let dateTo = $('#exportDateTo').val();

            let url = '<?= base_url('admin/activity-logs/export') ?>?' +
                'format=' + format +
                (dateFrom ? '&date_from=' + dateFrom : '') +
                (dateTo ? '&date_to=' + dateTo : '');

            window.location.href = url;
            hideExportModal();
        });

        // Cleanup old logs
        $('#cleanupBtn').click(function() {
            if (confirm('This will remove activity logs older than 90 days. Are you sure?')) {
                $.ajax({
                    url: '<?= base_url('admin/activity-logs/cleanup') ?>',
                    type: 'POST',
                    success: function(response) {
                        if (response.success) {
                            showNotification('Success: ' + response.message, 'success');
                            activityTable.ajax.reload();
                            loadActivityStats();
                        } else {
                            showNotification('Error: ' + response.message, 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error: Failed to cleanup logs', 'error');
                    }
                });
            }
        });

        function bindTableActions() {
            // View activity details
            $('.view-detail-btn').off('click').on('click', function() {
                let activityId = $(this).data('id');

                $.ajax({
                    url: '<?= base_url('admin/activity-logs/view/') ?>' + activityId,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            let activity = response.activity;
                            let detailsHtml = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-3">
                                    <div><span class="font-medium text-gray-700">ID:</span> <span class="text-gray-900">${activity.id}</span></div>
                                    <div><span class="font-medium text-gray-700">User:</span> <span class="text-gray-900">${activity.user_name || 'Guest'}</span></div>
                                    <div><span class="font-medium text-gray-700">Activity Type:</span> <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium text-white bg-purple-500">${activity.activity_type}</span></div>
                                    <div><span class="font-medium text-gray-700">IP Address:</span> <span class="text-gray-900">${activity.ip_address}</span></div>
                                    <div><span class="font-medium text-gray-700">Created At:</span> <span class="text-gray-900">${moment(activity.created_at).format('YYYY-MM-DD HH:mm:ss')}</span></div>
                                </div>
                                <div>
                                    <div class="font-medium text-gray-700 mb-2">User Agent:</div>
                                    <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded">${activity.user_agent || 'N/A'}</div>
                                </div>
                            </div>
                            <hr class="my-6">
                            <div>
                                <div class="font-medium text-gray-700 mb-3">Activity Description:</div>
                                <div class="text-gray-900 bg-gray-50 p-4 rounded">${activity.activity}</div>
                            </div>
                        `;

                            if (activity.additional_data) {
                                detailsHtml += `
                                <hr class="my-6">
                                <div>
                                    <div class="font-medium text-gray-700 mb-3">Additional Data:</div>
                                    <pre class="text-sm bg-gray-100 p-4 rounded overflow-x-auto max-h-48 overflow-y-auto">${JSON.stringify(JSON.parse(activity.additional_data), null, 2)}</pre>
                                </div>
                            `;
                            }

                            $('#activityDetailContent').html(detailsHtml);
                            showActivityModal();
                        } else {
                            showNotification('Error: ' + response.message, 'error');
                        }
                    },
                    error: function() {
                        showNotification('Error: Failed to load activity details', 'error');
                    }
                });
            });
        }

        function loadActivityStats() {
            $.ajax({
                url: '<?= base_url('admin/activity-logs/stats') ?>',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        let stats = response.stats;
                        $('#todayActivities').text(stats.today_activities);
                        $('#weekActivities').text(stats.week_activities);
                        $('#monthActivities').text(stats.month_activities);
                        $('#activeUsers').text(stats.active_users);
                    }
                }
            });
        }

        // Auto-refresh stats every 30 seconds
        setInterval(loadActivityStats, 30000);
    });

    // Modal functions
    function showActivityModal() {
        document.getElementById('activityDetailModal').classList.remove('hidden');
    }

    function hideActivityModal() {
        document.getElementById('activityDetailModal').classList.add('hidden');
    }

    function showExportModal() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function hideExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    // Notification function
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
        
        notification.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }
</script>
<?= $this->endSection() ?>