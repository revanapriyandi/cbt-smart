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
<div id="analyticsContent" class="space-y-6"> <!-- Overview Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="analytics-card bg-white rounded-xl shadow-lg p-6 stat-card-users text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-white/80 truncate">Total Users</dt>
                        <dd class="text-2xl font-bold text-white" id="totalUsers">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Total Exams Card -->
        <div class="analytics-card bg-white rounded-xl shadow-lg p-6 stat-card-exams text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-file-alt text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-white/80 truncate">Total Exams</dt>
                        <dd class="text-2xl font-bold text-white" id="totalExams">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Average Score Card -->
        <div class="analytics-card bg-white rounded-xl shadow-lg p-6 stat-card-score text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-line text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-white/80 truncate">Average Score</dt>
                        <dd class="text-2xl font-bold text-white" id="averageScore">--</dd>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Completion Rate Card -->
        <div class="analytics-card bg-white rounded-xl shadow-lg p-6 stat-card-completion text-white">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-percentage text-white text-lg"></i>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-white/80 truncate">Completion Rate</dt>
                        <dd class="text-2xl font-bold text-white" id="completionRate">--</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div> <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Exam Trends Chart -->
        <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="chart-title text-lg font-semibold text-gray-900">Exam Performance Trends</h3>
                <p class="text-sm text-gray-600">Daily exam sessions and completion rates</p>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="examTrendsChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- User Activity Chart -->
        <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-green-50 to-emerald-50">
                <h3 class="chart-title text-lg font-semibold text-gray-900">User Activity</h3>
                <p class="text-sm text-gray-600">User engagement over time</p>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="userActivityChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

    </div> <!-- Performance Analysis Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Subject Performance -->
        <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-pink-50">
                <h3 class="chart-title text-lg font-semibold text-gray-900">Subject Performance</h3>
                <p class="text-sm text-gray-600">Average scores by subject</p>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="subjectPerformanceChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-orange-50 to-yellow-50">
                <h3 class="chart-title text-lg font-semibold text-gray-900">Class Performance</h3>
                <p class="text-sm text-gray-600">Average scores by class</p>
            </div>
            <div class="p-6">
                <div class="chart-container">
                    <canvas id="classPerformanceChart" width="400" height="300"></canvas>
                </div>
            </div>
        </div>

    </div> <!-- System Health Section -->
    <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-cyan-50 to-blue-50">
            <h3 class="chart-title text-lg font-semibold text-gray-900">System Health</h3>
            <p class="text-sm text-gray-600">Real-time system metrics and performance indicators</p>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <!-- Database Status -->
                <div class="health-indicator text-center p-4 bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg border border-green-200">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-database text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Database</h4>
                    <p class="text-xs text-green-600 font-medium" id="databaseStatus">Healthy</p>
                </div>

                <!-- Memory Usage -->
                <div class="health-indicator text-center p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                    <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-memory text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Memory</h4>
                    <p class="text-xs text-gray-600 font-medium" id="memoryUsage">--</p>
                </div>

                <!-- CPU Usage -->
                <div class="health-indicator text-center p-4 bg-gradient-to-br from-yellow-50 to-orange-50 rounded-lg border border-yellow-200">
                    <div class="w-16 h-16 bg-yellow-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-microchip text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">CPU</h4>
                    <p class="text-xs text-gray-600 font-medium" id="cpuUsage">--</p>
                </div>

                <!-- Active Sessions -->
                <div class="health-indicator text-center p-4 bg-gradient-to-br from-purple-50 to-pink-50 rounded-lg border border-purple-200">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-3 shadow-lg">
                        <i class="fas fa-users-cog text-white text-xl"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-900 mb-1">Active Sessions</h4>
                    <p class="text-xs text-gray-600 font-medium" id="activeSessions">--</p>
                </div>

            </div>
        </div>
    </div> <!-- Recent Activities -->
    <div class="analytics-card bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-slate-50">
            <h3 class="chart-title text-lg font-semibold text-gray-900">Recent Activities</h3>
            <p class="text-sm text-gray-600">Latest exam sessions and activities</p>
        </div>
        <div class="p-6">
            <div id="recentActivities" class="space-y-3">
                <!-- Activities will be loaded here -->
            </div>
        </div>
    </div>

</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 modal-overlay hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full transform transition-all">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-lg font-semibold text-gray-900">Export Analytics Report</h3>
            </div>
            <form id="exportForm" class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Report Type</label>
                        <select name="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="overview">Overview Report</option>
                            <option value="exams">Exam Analytics</option>
                            <option value="users">User Performance</option>
                            <option value="system">System Analytics</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Export Format</label>
                        <div class="space-y-3">
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="format" value="excel" checked class="mr-3 text-blue-600 focus:ring-blue-500">
                                <i class="fas fa-file-excel text-green-600 mr-2"></i>
                                <span class="text-sm font-medium">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="format" value="pdf" class="mr-3 text-blue-600 focus:ring-blue-500">
                                <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                <span class="text-sm font-medium">PDF Report</span>
                            </label>
                            <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors">
                                <input type="radio" name="format" value="csv" class="mr-3 text-blue-600 focus:ring-blue-500">
                                <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                                <span class="text-sm font-medium">CSV Data</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Time Period</label>
                        <select name="period" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="90">Last 3 Months</option>
                            <option value="365">Last Year</option>
                        </select>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <button type="button" onclick="closeExportModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:ring-2 focus:ring-gray-500 transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors font-medium">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Enhanced CSS for Analytics Dashboard -->
<style>
    /* Loading animations */
    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }

    .pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Card hover effects */
    .analytics-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .analytics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-color: rgba(59, 130, 246, 0.3);
    }

    /* Chart containers */
    .chart-container {
        position: relative;
        height: 300px;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 8px;
        padding: 1rem;
    }

    /* System health indicators */
    .health-indicator {
        transition: all 0.3s ease;
    }

    .health-indicator:hover {
        transform: scale(1.05);
    }

    /* Recent activities */
    .activity-item {
        transition: all 0.2s ease;
        border-left: 3px solid transparent;
    }

    .activity-item:hover {
        background-color: #f8fafc;
        border-left-color: #3b82f6;
        transform: translateX(5px);
    }

    /* Statistics cards gradient backgrounds */
    .stat-card-users {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .stat-card-exams {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }

    .stat-card-score {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card-completion {
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    }

    /* Chart title styling */
    .chart-title {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }

    /* Export modal enhancements */
    .modal-overlay {
        backdrop-filter: blur(4px);
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Loading spinner */
    .spinner {
        border: 3px solid #f3f4f6;
        border-top: 3px solid #3b82f6;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    /* Notification styles */
    .notification {
        animation: slideInRight 0.3s ease-out;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Responsive improvements */
    @media (max-width: 768px) {
        .chart-container {
            height: 250px;
            padding: 0.5rem;
        }

        .analytics-card {
            margin-bottom: 1rem;
        }
    }
</style>

<script>
    // Global variables for charts
    let examTrendsChart, userActivityChart, subjectPerformanceChart, classPerformanceChart;

    // Global error handling for DataTables (defensive measure)
    if (typeof $ !== 'undefined' && $.fn.DataTable) {
        // Set global DataTable error handler as a preventive measure
        $.fn.dataTable.ext.errMode = function(settings, helpPage, message, exception) {
            console.warn('DataTable Error (Analytics):', message, exception);
            // Fallback: hide any potential table containers and show error message
            $(settings.nTable).closest('.dataTables_wrapper').hide();
            $(settings.nTable).after('<div class="alert alert-warning p-4 rounded-md bg-yellow-50 border border-yellow-200"><p class="text-yellow-800">Unable to load table data. Please refresh the page.</p></div>');
        };
    }

    // Enhanced error boundary for analytics
    window.addEventListener('error', function(event) {
        console.error('Analytics Page Error:', event.error);
        // Don't let errors break the entire page
        event.preventDefault();
        showError('An error occurred while loading analytics data');
    });

    window.addEventListener('unhandledrejection', function(event) {
        console.error('Analytics Promise Rejection:', event.reason);
        event.preventDefault();
        showError('Failed to load some analytics components');
    });

    // Initialize analytics dashboard
    document.addEventListener('DOMContentLoaded', function() {
        try {
            initializeAnalytics();
        } catch (error) {
            console.error('Failed to initialize analytics:', error);
            showError('Failed to initialize analytics dashboard');
        }
    });

    function initializeAnalytics() {
        loadAnalyticsData();

        // Period filter change handler
        const periodFilter = document.getElementById('periodFilter');
        if (periodFilter) {
            periodFilter.addEventListener('change', function() {
                try {
                    loadAnalyticsData();
                } catch (error) {
                    console.error('Error changing period filter:', error);
                    showError('Failed to update analytics data');
                }
            });
        }

        // Auto-refresh every 5 minutes with error handling
        setInterval(function() {
            try {
                loadAnalyticsData();
            } catch (error) {
                console.error('Auto-refresh error:', error);
                // Continue silently for auto-refresh errors
            }
        }, 300000);
    } // Load analytics data with comprehensive error handling
    function loadAnalyticsData() {
        showLoading();

        const period = document.getElementById('periodFilter')?.value || '30';

        // Enhanced fetch with timeout and error handling
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 30000); // 30 second timeout

        fetch(`/admin/analytics/dashboard-data?period=${period}`, {
                signal: controller.signal,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                clearTimeout(timeoutId);
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                return response.json();
            })
            .then(data => {
                if (data && data.success) {
                    try {
                        updateOverviewStats(data.data?.overview || {});
                        updateCharts(data.data || {});
                        updateSystemHealth(data.data?.system_health || {});
                        updateRecentActivities(data.data?.recent_activities || []);
                    } catch (error) {
                        console.error('Error updating analytics components:', error);
                        showError('Some analytics components failed to update');
                    }
                } else {
                    throw new Error(data?.message || 'Failed to load analytics data');
                }
            })
            .catch(error => {
                clearTimeout(timeoutId);
                console.error('Error loading analytics:', error);

                if (error.name === 'AbortError') {
                    showError('Request timed out. Please try again.');
                } else if (error.message.includes('Failed to fetch')) {
                    showError('Network error. Please check your connection.');
                } else {
                    showError(`Error loading analytics data: ${error.message}`);
                }

                // Set fallback data to prevent UI issues
                setFallbackData();
            })
            .finally(() => {
                hideLoading();
            });
    }

    // Set fallback data when main data loading fails
    function setFallbackData() {
        try {
            updateOverviewStats({
                total_users: 0,
                total_exams: 0,
                average_score: 0,
                completion_rate: 0
            });

            updateSystemHealth({
                database_status: 'Unknown',
                memory_usage: 0,
                cpu_usage: 0,
                active_connections: 0
            });

            updateRecentActivities([]);
        } catch (error) {
            console.error('Error setting fallback data:', error);
        }
    } // Update overview statistics with error handling
    function updateOverviewStats(stats) {
        try {
            const elements = {
                'totalUsers': stats.total_users || 0,
                'totalExams': stats.total_exams || 0,
                'averageScore': (stats.average_score || 0) + '%',
                'completionRate': (stats.completion_rate || 0) + '%'
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = formatNumber(value);
                } else {
                    console.warn(`Element with ID '${id}' not found`);
                }
            });
        } catch (error) {
            console.error('Error updating overview stats:', error);
        }
    }

    // Update all charts with comprehensive error handling
    function updateCharts(data) {
        try {
            updateExamTrendsChart(data.exam_trends || []);
            updateUserActivityChart(data.user_activity || []);
            updateSubjectPerformanceChart(data.subject_performance || []);
            updateClassPerformanceChart(data.class_performance || []);
        } catch (error) {
            console.error('Error updating charts:', error);
            showError('Some charts failed to update');
        }
    } // Update exam trends chart with error handling
    function updateExamTrendsChart(data) {
        try {
            const ctx = document.getElementById('examTrendsChart');
            if (!ctx) {
                console.warn('Exam trends chart canvas not found');
                return;
            }

            const chartContext = ctx.getContext('2d');
            if (!chartContext) {
                console.warn('Failed to get chart context for exam trends');
                return;
            }

            if (examTrendsChart) {
                examTrendsChart.destroy();
            }

            // Validate data structure
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Invalid or empty exam trends data');
                data = []; // Use empty array for empty chart
            }

            examTrendsChart = new Chart(chartContext, {
                type: 'line',
                data: {
                    labels: data.map(item => item.date || 'N/A'),
                    datasets: [{
                            label: 'Total Sessions',
                            data: data.map(item => item.total_sessions || 0),
                            borderColor: 'rgb(59, 130, 246)',
                            backgroundColor: 'rgba(59, 130, 246, 0.1)',
                            tension: 0.1
                        },
                        {
                            label: 'Completion Rate (%)',
                            data: data.map(item => item.completion_rate || 0),
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
                    },
                    plugins: {
                        legend: {
                            display: true
                        },
                        tooltip: {
                            enabled: true
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error updating exam trends chart:', error);
            showChartError('examTrendsChart', 'Failed to load exam trends chart');
        }
    } // Update user activity chart with error handling
    function updateUserActivityChart(data) {
        try {
            const ctx = document.getElementById('userActivityChart');
            if (!ctx) {
                console.warn('User activity chart canvas not found');
                return;
            }

            const chartContext = ctx.getContext('2d');
            if (!chartContext) {
                console.warn('Failed to get chart context for user activity');
                return;
            }

            if (userActivityChart) {
                userActivityChart.destroy();
            }

            // Validate data structure
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Invalid or empty user activity data');
                data = [];
            }

            // Group data by role with error handling
            const roles = [...new Set(data.map(item => item.role || 'Unknown'))];
            const dates = [...new Set(data.map(item => item.date || 'N/A'))];

            const datasets = roles.map((role, index) => {
                const colors = ['rgb(59, 130, 246)', 'rgb(34, 197, 94)', 'rgb(234, 88, 12)'];
                return {
                    label: role.charAt(0).toUpperCase() + role.slice(1),
                    data: dates.map(date => {
                        const found = data.find(item => item.role === role && item.date === date);
                        return found ? (found.activity_count || 0) : 0;
                    }),
                    borderColor: colors[index % colors.length],
                    backgroundColor: colors[index % colors.length] + '20',
                    tension: 0.1
                };
            });

            userActivityChart = new Chart(chartContext, {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'User Activity by Role'
                        },
                        legend: {
                            display: true
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
        } catch (error) {
            console.error('Error updating user activity chart:', error);
            showChartError('userActivityChart', 'Failed to load user activity chart');
        }
    }

    // Update subject performance chart with error handling
    function updateSubjectPerformanceChart(data) {
        try {
            const ctx = document.getElementById('subjectPerformanceChart');
            if (!ctx) {
                console.warn('Subject performance chart canvas not found');
                return;
            }

            const chartContext = ctx.getContext('2d');
            if (!chartContext) {
                console.warn('Failed to get chart context for subject performance');
                return;
            }

            if (subjectPerformanceChart) {
                subjectPerformanceChart.destroy();
            }

            // Validate data structure
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Invalid or empty subject performance data');
                data = [];
            }

            subjectPerformanceChart = new Chart(chartContext, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.subject_name || 'Unknown Subject'),
                    datasets: [{
                        label: 'Average Score',
                        data: data.map(item => parseFloat(item.average_score) || 0),
                        backgroundColor: 'rgba(59, 130, 246, 0.8)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Subject Performance Comparison'
                        },
                        legend: {
                            display: true
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
        } catch (error) {
            console.error('Error updating subject performance chart:', error);
            showChartError('subjectPerformanceChart', 'Failed to load subject performance chart');
        }
    }

    // Update class performance chart with error handling
    function updateClassPerformanceChart(data) {
        try {
            const ctx = document.getElementById('classPerformanceChart');
            if (!ctx) {
                console.warn('Class performance chart canvas not found');
                return;
            }

            const chartContext = ctx.getContext('2d');
            if (!chartContext) {
                console.warn('Failed to get chart context for class performance');
                return;
            }

            if (classPerformanceChart) {
                classPerformanceChart.destroy();
            }

            // Validate data structure
            if (!Array.isArray(data) || data.length === 0) {
                console.warn('Invalid or empty class performance data');
                data = [];
            }

            classPerformanceChart = new Chart(chartContext, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.class_name || 'Unknown Class'),
                    datasets: [{
                        label: 'Average Score',
                        data: data.map(item => parseFloat(item.average_score) || 0),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        title: {
                            display: true,
                            text: 'Class Performance Comparison'
                        },
                        legend: {
                            display: true
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
        } catch (error) {
            console.error('Error updating class performance chart:', error);
            showChartError('classPerformanceChart', 'Failed to load class performance chart');
        }
    } // Update system health with error handling
    function updateSystemHealth(health) {
        try {
            const healthElements = {
                'databaseStatus': health.database_status || 'Unknown',
                'memoryUsage': (health.memory_usage || 0) + '%',
                'cpuUsage': (health.cpu_usage || 0) + '%',
                'activeSessions': health.active_connections || 0
            };

            Object.entries(healthElements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                } else {
                    console.warn(`System health element with ID '${id}' not found`);
                }
            });
        } catch (error) {
            console.error('Error updating system health:', error);
        }
    }

    // Update recent activities with error handling
    function updateRecentActivities(activities) {
        try {
            const container = document.getElementById('recentActivities');
            if (!container) {
                console.warn('Recent activities container not found');
                return;
            }

            if (!Array.isArray(activities) || activities.length === 0) {
                container.innerHTML = '<div class="text-center py-8"><i class="fas fa-info-circle text-gray-400 text-2xl mb-2"></i><p class="text-gray-500">No recent activities</p></div>';
                return;
            }
            const activitiesHtml = activities.map(activity => `
            <div class="activity-item flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-slate-50 rounded-lg border border-gray-200 hover:shadow-md transition-all">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center shadow-md">
                            <i class="fas fa-file-alt text-white text-sm"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-gray-900">${activity.exam_title || 'Unknown Exam'}</p>
                        <p class="text-xs text-gray-500">Created by ${activity.created_by_name || 'Unknown User'}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">${formatDateTime(activity.created_at)}</p>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold ${getStatusColor(activity.status)}">
                        ${activity.status ? activity.status.charAt(0).toUpperCase() + activity.status.slice(1) : 'Unknown'}
                    </span>
                </div>
            </div>
        `).join('');

            container.innerHTML = activitiesHtml;
        } catch (error) {
            console.error('Error updating recent activities:', error);
            const container = document.getElementById('recentActivities');
            if (container) {
                container.innerHTML = '<div class="text-center py-4 text-red-500"><i class="fas fa-exclamation-triangle mb-2"></i><p>Failed to load recent activities</p></div>';
            }
        }
    }

    // Show chart error message
    function showChartError(chartId, message) {
        try {
            const canvas = document.getElementById(chartId);
            if (canvas) {
                const container = canvas.parentElement;
                if (container) {
                    container.innerHTML = `
                        <div class="flex items-center justify-center h-64">
                            <div class="text-center">
                                <i class="fas fa-chart-line text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-500 text-sm">${message}</p>
                                <button onclick="loadAnalyticsData()" class="mt-2 px-3 py-1 text-xs bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Retry
                                </button>
                            </div>
                        </div>
                    `;
                }
            }
        } catch (error) {
            console.error('Error showing chart error:', error);
        }
    } // Enhanced export report with better error handling
    function exportReport() {
        try {
            document.getElementById('exportModal').classList.remove('hidden');
        } catch (error) {
            console.error('Error opening export modal:', error);
            showError('Failed to open export dialog');
        }
    }

    // Close export modal
    function closeExportModal() {
        try {
            document.getElementById('exportModal').classList.add('hidden');
        } catch (error) {
            console.error('Error closing export modal:', error);
        }
    }

    // Enhanced export form submission with better error handling
    document.addEventListener('DOMContentLoaded', function() {
        const exportForm = document.getElementById('exportForm');
        if (exportForm) {
            exportForm.addEventListener('submit', function(e) {
                e.preventDefault();

                try {
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');

                    // Show loading state
                    if (submitButton) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
                    }

                    fetch('/admin/analytics/export-report', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                            return response.blob();
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
                            document.body.removeChild(a);
                            closeExportModal();
                            showSuccess('Report exported successfully');
                        })
                        .catch(error => {
                            console.error('Export error:', error);
                            showError(`Failed to export report: ${error.message}`);
                        })
                        .finally(() => {
                            // Reset button state
                            if (submitButton) {
                                submitButton.disabled = false;
                                submitButton.innerHTML = '<i class="fas fa-download mr-2"></i>Export';
                            }
                        });
                } catch (error) {
                    console.error('Form submission error:', error);
                    showError('Failed to process export request');
                }
            });
        }
    });

    // Enhanced refresh data function
    function refreshData() {
        try {
            const refreshButton = document.querySelector('button[onclick="refreshData()"]');
            if (refreshButton) {
                refreshButton.disabled = true;
                refreshButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
            }

            loadAnalyticsData();

            setTimeout(() => {
                if (refreshButton) {
                    refreshButton.disabled = false;
                    refreshButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Refresh';
                }
                showSuccess('Data refreshed successfully');
            }, 1000);
        } catch (error) {
            console.error('Error refreshing data:', error);
            showError('Failed to refresh data');
        }
    } // Enhanced utility functions with better error handling
    function showLoading() {
        try {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const analyticsContent = document.getElementById('analyticsContent');

            if (loadingIndicator) loadingIndicator.classList.remove('hidden');
            if (analyticsContent) analyticsContent.classList.add('opacity-50');
        } catch (error) {
            console.error('Error showing loading state:', error);
        }
    }

    function hideLoading() {
        try {
            const loadingIndicator = document.getElementById('loadingIndicator');
            const analyticsContent = document.getElementById('analyticsContent');

            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            if (analyticsContent) analyticsContent.classList.remove('opacity-50');
        } catch (error) {
            console.error('Error hiding loading state:', error);
        }
    }

    function formatNumber(num) {
        try {
            // Handle string inputs (like "0%" from stats)
            if (typeof num === 'string') {
                const numericPart = parseFloat(num.replace(/[^\d.-]/g, ''));
                const suffix = num.replace(/[\d.-]/g, '');
                return isNaN(numericPart) ? num : new Intl.NumberFormat().format(numericPart) + suffix;
            }
            return new Intl.NumberFormat().format(num || 0);
        } catch (error) {
            console.error('Error formatting number:', error);
            return num || '0';
        }
    }

    function formatDateTime(dateTime) {
        try {
            if (!dateTime) return 'N/A';
            return new Date(dateTime).toLocaleDateString('id-ID', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            console.error('Error formatting date:', error);
            return 'Invalid Date';
        }
    }

    function getStatusColor(status) {
        try {
            const colors = {
                'active': 'bg-green-100 text-green-800',
                'completed': 'bg-blue-100 text-blue-800',
                'scheduled': 'bg-yellow-100 text-yellow-800',
                'cancelled': 'bg-red-100 text-red-800',
                'draft': 'bg-gray-100 text-gray-800'
            };
            return colors[status?.toLowerCase()] || 'bg-gray-100 text-gray-800';
        } catch (error) {
            console.error('Error getting status color:', error);
            return 'bg-gray-100 text-gray-800';
        }
    }

    function showSuccess(message) {
        try {
            // Create and show a success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                notification.style.opacity = '0';
                setTimeout(() => {
                    if (notification.parentNode) {
                        document.body.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        } catch (error) {
            console.error('Error showing success message:', error);
            console.log('Success:', message);
        }
    }

    function showError(message) {
        try {
            // Create and show an error notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            document.body.removeChild(notification);
                        }
                    }, 300);
                }
            }, 5000);
        } catch (error) {
            console.error('Error showing error message:', error);
            console.error('Error:', message);
            alert(message); // Fallback to alert
        }
    }
</script>

<?= $this->endSection() ?>