<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Analytics Dashboard Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Analytics Dashboard</h1>
            <p class="mt-2 text-sm text-gray-600">Comprehensive analytics and insights for your CBT system</p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <!-- Period Filter -->
            <select id="periodFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="7">Last 7 Days</option>
                <option value="30" selected>Last 30 Days</option>
                <option value="90">Last 3 Months</option>
                <option value="365">Last Year</option>
            </select>

            <!-- Export Button -->
            <button type="button" onclick="exportReport()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500">
                <i class="fas fa-download mr-2"></i>Export Report
            </button>

            <!-- Refresh Button -->
            <button type="button" onclick="refreshData()" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                <i class="fas fa-sync-alt mr-2"></i>Refresh
            </button>
        </div>
    </div>
</div>

<!-- Loading Indicator -->
<div id="loadingIndicator" class="hidden">
    <div class="flex items-center justify-center p-8">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
        <span class="ml-2 text-gray-600">Loading analytics data...</span>
    </div>
</div>

<!-- Analytics Content -->
<div id="analyticsContent" class="space-y-6">

    <!-- Overview Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                        <dd class="text-lg font-medium text-gray-900" id="totalUsers">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Exams Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Total Exams</dt>
                        <dd class="text-lg font-medium text-gray-900" id="totalExams">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Average Score Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Average Score</dt>
                        <dd class="text-lg font-medium text-gray-900" id="averageScore">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Completion Rate Card -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-white text-sm"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Completion Rate</dt>
                        <dd class="text-lg font-medium text-gray-900" id="completionRate">--</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Exam Trends Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Exam Performance Trends</h3>
                <p class="text-sm text-gray-500">Daily exam sessions and completion rates</p>
            </div>
            <div class="p-6">
                <canvas id="examTrendsChart" width="400" height="200"></canvas>
            </div>
        </div>

        <!-- User Activity Chart -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">User Activity</h3>
                <p class="text-sm text-gray-500">User engagement over time</p>
            </div>
            <div class="p-6">
                <canvas id="userActivityChart" width="400" height="200"></canvas>
            </div>
        </div>

    </div>

    <!-- Performance Analysis Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Subject Performance -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Subject Performance</h3>
                <p class="text-sm text-gray-500">Average scores by subject</p>
            </div>
            <div class="p-6">
                <canvas id="subjectPerformanceChart" width="400" height="300"></canvas>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Class Performance</h3>
                <p class="text-sm text-gray-500">Average scores by class</p>
            </div>
            <div class="p-6">
                <canvas id="classPerformanceChart" width="400" height="300"></canvas>
            </div>
        </div>

    </div>

    <!-- System Health Section -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">System Health</h3>
            <p class="text-sm text-gray-500">Real-time system metrics and performance indicators</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Database Status -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-database text-white"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Database</h4>
                    <p class="text-xs text-green-600" id="databaseStatus">Healthy</p>
                </div>

                <!-- Memory Usage -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-memory text-white"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Memory</h4>
                    <p class="text-xs text-gray-600" id="memoryUsage">--</p>
                </div>

                <!-- CPU Usage -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-microchip text-white"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">CPU</h4>
                    <p class="text-xs text-gray-600" id="cpuUsage">--</p>
                </div>

                <!-- Active Sessions -->
                <div class="text-center p-4 bg-gray-50 rounded-lg">
                    <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-2">
                        <i class="fas fa-users-cog text-white"></i>
                    </div>
                    <h4 class="text-sm font-medium text-gray-900">Active Sessions</h4>
                    <p class="text-xs text-gray-600" id="activeSessions">--</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activities</h3>
            <p class="text-sm text-gray-500">Latest exam sessions and activities</p>
        </div>
        <div class="p-6">
            <div id="recentActivities" class="space-y-4">
                <!-- Activities will be loaded here -->
            </div>
        </div>
    </div>

</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Export Analytics Report</h3>
            </div>
            <form id="exportForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Report Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="overview">Overview Report</option>
                            <option value="exams">Exam Analytics</option>
                            <option value="users">User Performance</option>
                            <option value="system">System Analytics</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Export Format</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="radio" name="format" value="excel" checked class="mr-2">
                                <span class="text-sm">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="pdf" class="mr-2">
                                <span class="text-sm">PDF Report</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="format" value="csv" class="mr-2">
                                <span class="text-sm">CSV Data</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time Period</label>
                        <select name="period" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 3 Months</option>
                            <option value="365">Last Year</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="closeExportModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 focus:ring-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Global variables for charts
    let examTrendsChart, userActivityChart, subjectPerformanceChart, classPerformanceChart;

    // Initialize analytics dashboard
    document.addEventListener('DOMContentLoaded', function() {
        loadAnalyticsData();

        // Period filter change handler
        document.getElementById('periodFilter').addEventListener('change', function() {
            loadAnalyticsData();
        });

        // Auto-refresh every 5 minutes
        setInterval(loadAnalyticsData, 300000);
    });

    // Load analytics data
    function loadAnalyticsData() {
        showLoading();

        const period = document.getElementById('periodFilter').value;

        fetch(`/admin/analytics/dashboard-data?period=${period}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateOverviewStats(data.data.overview);
                    updateCharts(data.data);
                    updateSystemHealth(data.data.system_health);
                    updateRecentActivities(data.data.recent_activities);
                } else {
                    showError('Failed to load analytics data');
                }
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
                showError('Error loading analytics data');
            })
            .finally(() => {
                hideLoading();
            });
    }

    // Update overview statistics
    function updateOverviewStats(stats) {
        document.getElementById('totalUsers').textContent = formatNumber(stats.total_users);
        document.getElementById('totalExams').textContent = formatNumber(stats.total_exams);
        document.getElementById('averageScore').textContent = stats.average_score + '%';
        document.getElementById('completionRate').textContent = stats.completion_rate + '%';
    }

    // Update all charts
    function updateCharts(data) {
        updateExamTrendsChart(data.exam_trends);
        updateUserActivityChart(data.user_activity);
        updateSubjectPerformanceChart(data.subject_performance);
        updateClassPerformanceChart(data.class_performance);
    }

    // Update exam trends chart
    function updateExamTrendsChart(data) {
        const ctx = document.getElementById('examTrendsChart').getContext('2d');

        if (examTrendsChart) {
            examTrendsChart.destroy();
        }

        examTrendsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.map(item => item.date),
                datasets: [{
                        label: 'Total Sessions',
                        data: data.map(item => item.total_sessions),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.1
                    },
                    {
                        label: 'Completion Rate (%)',
                        data: data.map(item => item.completion_rate),
                        borderColor: 'rgb(34, 197, 94)',
                        backgroundColor: 'rgba(34, 197, 94, 0.1)',
                        tension: 0.1,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    x: {
                        display: true,
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    },
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Sessions'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Completion Rate (%)'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });
    }

    // Update user activity chart
    function updateUserActivityChart(data) {
        const ctx = document.getElementById('userActivityChart').getContext('2d');

        if (userActivityChart) {
            userActivityChart.destroy();
        }

        // Group data by role
        const roles = [...new Set(data.map(item => item.role))];
        const dates = [...new Set(data.map(item => item.date))];

        const datasets = roles.map((role, index) => {
            const colors = ['rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(234, 88, 12)'];
            return {
                label: role.charAt(0).toUpperCase() + role.slice(1),
                data: dates.map(date => {
                    const found = data.find(item => item.role === role && item.date === date);
                    return found ? found.activity_count : 0;
                }),
                borderColor: colors[index % colors.length],
                backgroundColor: colors[index % colors.length] + '20',
                tension: 0.1
            };
        });

        userActivityChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: datasets
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'User Activity by Role'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Activity Count'
                        }
                    }
                }
            }
        });
    }

    // Update subject performance chart
    function updateSubjectPerformanceChart(data) {
        const ctx = document.getElementById('subjectPerformanceChart').getContext('2d');

        if (subjectPerformanceChart) {
            subjectPerformanceChart.destroy();
        }

        subjectPerformanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.subject_name),
                datasets: [{
                    label: 'Average Score',
                    data: data.map(item => parseFloat(item.average_score)),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Subject Performance Comparison'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Average Score (%)'
                        }
                    }
                }
            }
        });
    }

    // Update class performance chart
    function updateClassPerformanceChart(data) {
        const ctx = document.getElementById('classPerformanceChart').getContext('2d');

        if (classPerformanceChart) {
            classPerformanceChart.destroy();
        }

        classPerformanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.map(item => item.class_name),
                datasets: [{
                    label: 'Average Score',
                    data: data.map(item => parseFloat(item.average_score)),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderColor: 'rgb(34, 197, 94)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Class Performance Comparison'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        title: {
                            display: true,
                            text: 'Average Score (%)'
                        }
                    }
                }
            }
        });
    }

    // Update system health
    function updateSystemHealth(health) {
        document.getElementById('databaseStatus').textContent = health.database_status;
        document.getElementById('memoryUsage').textContent = health.memory_usage + '%';
        document.getElementById('cpuUsage').textContent = health.cpu_usage + '%';
        document.getElementById('activeSessions').textContent = health.active_connections;
    }

    // Update recent activities
    function updateRecentActivities(activities) {
        const container = document.getElementById('recentActivities');

        if (activities.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center">No recent activities</p>';
            return;
        }

        const activitiesHtml = activities.map(activity => `
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-file-alt text-blue-500"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">${activity.exam_title}</p>
                    <p class="text-xs text-gray-500">Created by ${activity.created_by_name}</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500">${formatDateTime(activity.created_at)}</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${getStatusColor(activity.status)}">
                    ${activity.status.charAt(0).toUpperCase() + activity.status.slice(1)}
                </span>
            </div>
        </div>
    `).join('');

        container.innerHTML = activitiesHtml;
    }

    // Export report
    function exportReport() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    // Close export modal
    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    // Handle export form submission
    document.getElementById('exportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('/admin/analytics/export-report', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    return response.blob();
                }
                throw new Error('Export failed');
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = `analytics_report_${Date.now()}.xlsx`;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                closeExportModal();
                showSuccess('Report exported successfully');
            })
            .catch(error => {
                console.error('Export error:', error);
                showError('Failed to export report');
            });
    });

    // Refresh data
    function refreshData() {
        loadAnalyticsData();
        showSuccess('Data refreshed successfully');
    }

    // Utility functions
    function showLoading() {
        document.getElementById('loadingIndicator').classList.remove('hidden');
        document.getElementById('analyticsContent').classList.add('opacity-50');
    }

    function hideLoading() {
        document.getElementById('loadingIndicator').classList.add('hidden');
        document.getElementById('analyticsContent').classList.remove('opacity-50');
    }

    function formatNumber(num) {
        return new Intl.NumberFormat().format(num);
    }

    function formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleDateString('id-ID', {
            year: 'numeric',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function getStatusColor(status) {
        const colors = {
            'active': 'bg-green-100 text-green-800',
            'completed': 'bg-blue-100 text-blue-800',
            'scheduled': 'bg-yellow-100 text-yellow-800',
            'cancelled': 'bg-red-100 text-red-800'
        };
        return colors[status] || 'bg-gray-100 text-gray-800';
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