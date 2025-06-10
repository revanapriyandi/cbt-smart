<?= $this->extend('layout/main') ?>

<?= $this->section('styles') ?>
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .card-hover {
        transition: all 0.3s ease;
    }

    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .pulse-ring {
        position: relative;
    }

    .pulse-ring::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        border: 2px solid currentColor;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        animation: pulse-ring 1.5s ease-out infinite;
        opacity: 0.7;
    }

    @keyframes pulse-ring {
        0% {
            transform: translate(-50%, -50%) scale(0.8);
            opacity: 1;
        }

        80%,
        100% {
            transform: translate(-50%, -50%) scale(1.8);
            opacity: 0;
        }
    }

    .progress-bar-animated {
        background-size: 1rem 1rem;
        background-image: linear-gradient(45deg, rgba(255, 255, 255, .15) 25%, transparent 25%, transparent 50%, rgba(255, 255, 255, .15) 50%, rgba(255, 255, 255, .15) 75%, transparent 75%, transparent);
        animation: progress-bar-stripes 1s linear infinite;
    }

    @keyframes progress-bar-stripes {
        0% {
            background-position-x: 1rem;
        }
    }

    .status-indicator {
        position: relative;
        display: inline-block;
    }

    .status-indicator.online::after {
        content: '';
        position: absolute;
        top: -2px;
        right: -2px;
        width: 8px;
        height: 8px;
        background: #10b981;
        border: 2px solid white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .modal-backdrop {
        backdrop-filter: blur(4px);
    }

    .notification-enter {
        transform: translateX(100%);
        opacity: 0;
    }

    .notification-enter-active {
        transform: translateX(0);
        opacity: 1;
        transition: all 0.3s ease;
    }

    .notification-exit {
        transform: translateX(0);
        opacity: 1;
    }

    .notification-exit-active {
        transform: translateX(100%);
        opacity: 0;
        transition: all 0.3s ease;
    }

    .health-meter {
        background: linear-gradient(90deg, #10b981 0%, #f59e0b 50%, #ef4444 100%);
        height: 4px;
        border-radius: 2px;
        position: relative;
        overflow: hidden;
    }

    .health-meter::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.3) 50%, transparent 100%);
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .floating-action {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 40;
    }

    .floating-action button {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .floating-action button:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 25px rgba(0, 0, 0, 0.2);
    }

    .status-badge {
        animation: status-pulse 2s infinite;
    }

    @keyframes status-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .activity-timeline {
        position: relative;
    }

    .activity-timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6, #ec4899);
    }

    .activity-item {
        position: relative;
        z-index: 1;
    }

    @media (max-width: 768px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .lg\\:col-span-2 {
            grid-column: span 1;
        }

        .lg\\:col-span-1 {
            grid-column: span 1;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6 relative" x-data="liveMonitoring()">
    <!-- Loading Overlay -->
    <div x-show="isLoading"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        style="display: none;">
        <div class="bg-white rounded-2xl p-8 shadow-2xl">
            <div class="flex items-center gap-4">
                <div class="w-8 h-8 border-4 border-blue-600 border-t-transparent rounded-full animate-spin"></div>
                <div class="text-lg font-medium text-gray-700">Loading monitoring data...</div>
            </div>
        </div>
    </div>

    <!-- Page Header -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                        <i class="fas fa-desktop text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Live Monitoring</h1>
                        <p class="text-gray-600 mt-1">Monitor sesi ujian secara real-time</p>
                    </div>
                    <div class="ml-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                            <span x-text="statistics.active_participants || 0"></span> Online
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center bg-gray-50 rounded-lg p-2">
                    <label for="autoRefresh" class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" id="autoRefresh" x-model="autoRefresh" class="sr-only">
                        <div class="w-11 h-6 bg-gray-200 rounded-full peer relative transition-colors duration-200"
                            :class="autoRefresh ? 'bg-blue-600' : 'bg-gray-200'">
                            <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-200 transform"
                                :class="autoRefresh ? 'translate-x-5' : 'translate-x-0'"></div>
                        </div>
                        <span class="ml-3 text-sm font-medium text-gray-700">Auto Refresh</span>
                    </label>
                </div>
                <button type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 transform hover:scale-105"
                    @click="refreshData()" :disabled="isLoading">
                    <i class="fas fa-sync-alt" :class="{ 'animate-spin': isLoading }"></i>
                    <span x-text="isLoading ? 'Loading...' : 'Refresh'"></span>
                </button>
                <button type="button"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 transform hover:scale-105"
                    @click="exportData()">
                    <i class="fas fa-download"></i>
                    Export
                </button>
            </div>
        </div>
        <div class="mt-4 text-sm text-gray-500">
            Last updated: <span x-text="lastUpdated" class="font-medium"></span>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8"> <!-- Active Sessions Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-green-500 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-green-600 uppercase tracking-wide">Sesi Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2" x-text="statistics.active_sessions || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Sessions running</p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-green-600 font-medium">Live</span>
                    </div>
                </div>
                <div class="bg-green-100 p-4 rounded-full">
                    <i class="fas fa-play-circle text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 bg-green-50 rounded-lg p-2">
                <div class="flex justify-between text-xs">
                    <span class="text-green-600">Progress</span>
                    <span class="text-green-600 font-medium" x-text="(statistics.active_sessions || 0) + ' active'"></span>
                </div>
                <div class="mt-1 bg-green-200 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all duration-500"
                        :style="'width: ' + Math.min((statistics.active_sessions || 0) * 10, 100) + '%'"></div>
                </div>
            </div>
        </div>

        <!-- Active Participants Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-blue-500 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-blue-600 uppercase tracking-wide">Peserta Aktif</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2" x-text="statistics.active_participants || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Currently taking exams</p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="w-3 h-3 bg-blue-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-blue-600 font-medium">Online</span>
                    </div>
                </div>
                <div class="bg-blue-100 p-4 rounded-full">
                    <i class="fas fa-users text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 bg-blue-50 rounded-lg p-2">
                <div class="flex justify-between text-xs">
                    <span class="text-blue-600">Engagement</span>
                    <span class="text-blue-600 font-medium">High</span>
                </div>
                <div class="mt-1 bg-blue-200 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500"
                        :style="'width: ' + Math.min((statistics.active_participants || 0) * 2, 100) + '%'"></div>
                </div>
            </div>
        </div>

        <!-- Completed Today Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-indigo-500 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-indigo-600 uppercase tracking-wide">Selesai Hari Ini</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2" x-text="statistics.completed_today || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Completed today</p>
                    <div class="mt-2 flex items-center gap-2">
                        <div class="w-3 h-3 bg-indigo-400 rounded-full"></div>
                        <span class="text-xs text-indigo-600 font-medium">Done</span>
                    </div>
                </div>
                <div class="bg-indigo-100 p-4 rounded-full">
                    <i class="fas fa-check-circle text-indigo-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 bg-indigo-50 rounded-lg p-2">
                <div class="flex justify-between text-xs">
                    <span class="text-indigo-600">Completion Rate</span>
                    <span class="text-indigo-600 font-medium" x-text="Math.round((statistics.completed_today || 0) / Math.max((statistics.active_participants || 1), 1) * 100) + '%'"></span>
                </div>
                <div class="mt-1 bg-indigo-200 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full transition-all duration-500"
                        :style="'width: ' + Math.round((statistics.completed_today || 0) / Math.max((statistics.active_participants || 1), 1) * 100) + '%'"></div>
                </div>
            </div>
        </div>

        <!-- Flagged Participants Card -->
        <div class="bg-white rounded-2xl shadow-lg p-6 border-l-4 border-orange-500 transform hover:scale-105 transition-all duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-orange-600 uppercase tracking-wide">Ditandai</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2" x-text="statistics.flagged_participants || 0"></p>
                    <p class="text-xs text-gray-500 mt-1">Flagged for review</p>
                    <div class="mt-2 flex items-center gap-2" x-show="(statistics.flagged_participants || 0) > 0">
                        <div class="w-3 h-3 bg-orange-400 rounded-full animate-pulse"></div>
                        <span class="text-xs text-orange-600 font-medium">Attention</span>
                    </div>
                </div>
                <div class="bg-orange-100 p-4 rounded-full">
                    <i class="fas fa-flag text-orange-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 bg-orange-50 rounded-lg p-2">
                <div class="flex justify-between text-xs">
                    <span class="text-orange-600">Risk Level</span>
                    <span class="text-orange-600 font-medium"
                        x-text="(statistics.flagged_participants || 0) > 5 ? 'High' : ((statistics.flagged_participants || 0) > 2 ? 'Medium' : 'Low')"></span>
                </div>
                <div class="mt-1 bg-orange-200 rounded-full h-2">
                    <div class="bg-orange-500 h-2 rounded-full transition-all duration-500"
                        :style="'width: ' + Math.min((statistics.flagged_participants || 0) * 10, 100) + '%'"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <div class="bg-red-100 p-2 rounded-lg">
                    <i class="fas fa-heartbeat text-red-600"></i>
                </div>
                System Health
            </h2>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <span class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></span>
                    All Systems Operational
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-blue-600 uppercase tracking-wide mb-2">CPU</div>
                <div class="text-2xl font-bold mb-1"
                    :class="systemHealth.cpu_usage > 80 ? 'text-red-600' : (systemHealth.cpu_usage > 60 ? 'text-yellow-600' : 'text-green-600')"
                    x-text="(systemHealth.cpu_usage || 0) + '%'"></div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="'width: ' + (systemHealth.cpu_usage || 0) + '%'"></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-green-600 uppercase tracking-wide mb-2">Memory</div>
                <div class="text-2xl font-bold mb-1"
                    :class="systemHealth.memory_usage > 80 ? 'text-red-600' : (systemHealth.memory_usage > 60 ? 'text-yellow-600' : 'text-green-600')"
                    x-text="(systemHealth.memory_usage || 0) + 'MB'"></div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300"
                        :style="'width: ' + Math.min((systemHealth.memory_usage || 0), 100) + '%'"></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-purple-600 uppercase tracking-wide mb-2">Disk</div>
                <div class="text-2xl font-bold mb-1"
                    :class="systemHealth.disk_usage > 90 ? 'text-red-600' : (systemHealth.disk_usage > 70 ? 'text-yellow-600' : 'text-green-600')"
                    x-text="(systemHealth.disk_usage || 0) + '%'"></div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-purple-600 h-2 rounded-full transition-all duration-300"
                        :style="'width: ' + (systemHealth.disk_usage || 0) + '%'"></div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-indigo-600 uppercase tracking-wide mb-2">Connections</div>
                <div class="text-2xl font-bold text-indigo-600 mb-1" x-text="systemHealth.active_connections || 0"></div>
                <div class="text-xs text-gray-500">Active</div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-orange-600 uppercase tracking-wide mb-2">Response</div>
                <div class="text-2xl font-bold mb-1"
                    :class="systemHealth.response_time > 500 ? 'text-red-600' : (systemHealth.response_time > 200 ? 'text-yellow-600' : 'text-green-600')"
                    x-text="(systemHealth.response_time || 0) + 'ms'"></div>
                <div class="text-xs text-gray-500">Avg time</div>
            </div>

            <div class="bg-gradient-to-br from-teal-50 to-teal-100 rounded-xl p-4 text-center">
                <div class="text-xs font-semibold text-teal-600 uppercase tracking-wide mb-2">Database</div>
                <div class="text-lg font-bold mb-1"
                    :class="systemHealth.database_status === 'connected' ? 'text-green-600' : 'text-red-600'"
                    x-text="systemHealth.database_status || 'Unknown'"></div>
                <div class="text-xs text-gray-500">Status</div>
            </div>
        </div>
    </div>

    <!-- Active Sessions -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-bold flex items-center gap-3">
                    <i class="fas fa-laptop text-2xl"></i>
                    Sesi Aktif
                    <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full text-sm"
                        x-text="(activeSessions?.length || 0) + ' sessions'"></span>
                </h2>
                <button type="button"
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2"
                    @click="showBroadcastModal()">
                    <i class="fas fa-bullhorn"></i>
                    Broadcast Message
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Sesi</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Ujian</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Kelas</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Peserta</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Progress</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Waktu</th>
                            <th class="text-left py-3 px-4 font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="session in activeSessions" :key="session.id">
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="bg-blue-100 p-2 rounded-lg relative">
                                            <i class="fas fa-desktop text-blue-600"></i>
                                            <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-400 rounded-full border-2 border-white animate-pulse"></div>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-800" x-text="session.session_name"></div>
                                            <div class="text-sm text-gray-500 flex items-center gap-2">
                                                <span x-text="'ID: ' + session.id"></span>
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 bg-green-400 rounded-full mr-1 animate-pulse"></span>
                                                    Live
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-medium text-gray-800" x-text="session.exam_title"></div>
                                    <div class="text-sm text-gray-500" x-text="session.subject_name"></div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                                        x-text="session.class_name"></span>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-center">
                                        <div class="text-lg font-bold text-gray-800"
                                            x-text="session.active_participants + '/' + session.participant_count"></div>
                                        <div class="text-xs text-gray-500">peserta</div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-300"
                                                :style="'width: ' + (session.participant_count > 0 ? (session.active_participants / session.participant_count * 100) : 0) + '%'"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-600"
                                            x-text="(session.participant_count > 0 ? Math.round(session.active_participants / session.participant_count * 100) : 0) + '%'"></span>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex flex-col items-start gap-1">
                                        <div class="flex items-center gap-2">
                                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                            <span class="text-sm text-gray-600 font-medium">Started</span>
                                        </div>
                                        <span class="text-sm text-gray-800" x-text="formatDateTime(session.start_time)"></span>
                                        <div class="text-xs text-gray-500 flex items-center gap-1">
                                            <i class="fas fa-clock"></i>
                                            <span x-text="getTimeElapsed(session.start_time)"></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-2">
                                        <button class="bg-blue-100 hover:bg-blue-200 text-blue-600 p-2 rounded-lg transition-all duration-200 transform hover:scale-110"
                                            @click="viewSessionDetail(session.id)" title="Detail">
                                            <i class="fas fa-eye text-sm"></i>
                                        </button>
                                        <button class="bg-indigo-100 hover:bg-indigo-200 text-indigo-600 p-2 rounded-lg transition-all duration-200 transform hover:scale-110"
                                            @click="monitorSession(session.id)" title="Monitor">
                                            <i class="fas fa-desktop text-sm"></i>
                                        </button>
                                        <button class="bg-green-100 hover:bg-green-200 text-green-600 p-2 rounded-lg transition-all duration-200 transform hover:scale-110"
                                            @click="sendMessage(session.id)" title="Pesan">
                                            <i class="fas fa-comment text-sm"></i>
                                        </button>
                                        <button class="bg-red-100 hover:bg-red-200 text-red-600 p-2 rounded-lg transition-all duration-200 transform hover:scale-110"
                                            @click="endSession(session.id)" title="Akhiri">
                                            <i class="fas fa-stop text-sm"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="!activeSessions || activeSessions.length === 0">
                            <td colspan="7" class="py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="bg-gray-100 p-6 rounded-full">
                                        <i class="fas fa-desktop text-4xl text-gray-300"></i>
                                    </div>
                                    <div>
                                        <div class="text-lg font-medium text-gray-700 mb-1">Tidak ada sesi aktif</div>
                                        <div class="text-sm text-gray-500">Semua sesi ujian telah selesai atau belum dimulai</div>
                                    </div>
                                    <button @click="refreshData()"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                                        <i class="fas fa-sync-alt"></i>
                                        Refresh
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- System Alerts -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 p-4 text-white">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-exclamation-triangle"></i>
                        System Alerts
                        <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs"
                            x-text="(systemAlerts?.length || 0) + ' alerts'"></span>
                    </h3>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <template x-for="alert in systemAlerts" :key="alert.timestamp">
                        <div class="border rounded-xl p-4 mb-3 transition-all duration-200 hover:shadow-md"
                            :class="alert.type === 'danger' ? 'border-red-200 bg-red-50' : 
                                    (alert.type === 'warning' ? 'border-yellow-200 bg-yellow-50' : 'border-blue-200 bg-blue-50')">
                            <div class="flex items-start gap-3">
                                <div class="p-2 rounded-lg"
                                    :class="alert.type === 'danger' ? 'bg-red-100' : 
                                            (alert.type === 'warning' ? 'bg-yellow-100' : 'bg-blue-100')">
                                    <i class="fas"
                                        :class="alert.type === 'danger' ? 'fa-exclamation-circle text-red-600' : 
                                              (alert.type === 'warning' ? 'fa-exclamation-triangle text-yellow-600' : 'fa-info-circle text-blue-600')"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-800" x-text="alert.message"></div>
                                    <div class="text-sm text-gray-500 mt-1" x-text="formatDateTime(alert.timestamp)"></div>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-medium"
                                    :class="alert.type === 'danger' ? 'bg-red-100 text-red-800' : 
                                             (alert.type === 'warning' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800')"
                                    x-text="alert.type"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="!systemAlerts || systemAlerts.length === 0"
                        class="text-center py-8 text-gray-500">
                        <i class="fas fa-check-circle text-3xl text-green-300 mb-2"></i>
                        <div>Tidak ada alert sistem</div>
                    </div>
                </div>
            </div>
        </div> <!-- Recent Activities -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-4 text-white">
                    <h3 class="text-lg font-bold flex items-center gap-2">
                        <i class="fas fa-clock"></i>
                        Aktivitas Terkini
                        <span class="bg-white bg-opacity-20 px-2 py-1 rounded-full text-xs"
                            x-text="(recentActivities?.length || 0) + ' activities'"></span>
                    </h3>
                </div>
                <div class="p-4 max-h-96 overflow-y-auto">
                    <template x-for="activity in recentActivities" :key="activity.id">
                        <div class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors duration-200 mb-2">
                            <div class="p-2 rounded-lg" :class="getActivityBackgroundColor(activity.event_type)">
                                <i class="fas text-sm" :class="getActivityIcon(activity.event_type)"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-800 truncate" x-text="activity.student_name"></div>
                                <div class="text-sm text-gray-600 capitalize" x-text="activity.event_type.replace('_', ' ')"></div>
                                <div class="text-xs text-gray-500" x-text="formatTime(activity.created_at)"></div>
                            </div>
                            <div class="w-2 h-2 rounded-full mt-2" :class="getActivityStatusColor(activity.event_type)"></div>
                        </div>
                    </template>
                    <div x-show="!recentActivities || recentActivities.length === 0"
                        class="text-center py-8 text-gray-500">
                        <i class="fas fa-history text-3xl text-gray-300 mb-2"></i>
                        <div>Tidak ada aktivitas terkini</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <div class="relative">
            <button @click="quickActionsOpen = !quickActionsOpen"
                class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white">
                <i class="fas fa-cog" :class="{ 'animate-spin': quickActionsOpen }"></i>
            </button>

            <!-- Quick Actions Menu -->
            <div x-show="quickActionsOpen"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute bottom-16 right-0 bg-white rounded-2xl shadow-2xl p-4 min-w-max"
                style="display: none;">
                <div class="space-y-2">
                    <button @click="refreshData(); quickActionsOpen = false"
                        class="flex items-center gap-3 w-full p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="bg-blue-100 p-2 rounded-lg">
                            <i class="fas fa-sync-alt text-blue-600"></i>
                        </div>
                        <span class="font-medium text-gray-700">Refresh Data</span>
                    </button>
                    <button @click="showBroadcastModal(); quickActionsOpen = false"
                        class="flex items-center gap-3 w-full p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="bg-green-100 p-2 rounded-lg">
                            <i class="fas fa-bullhorn text-green-600"></i>
                        </div>
                        <span class="font-medium text-gray-700">Broadcast</span>
                    </button>
                    <button @click="exportData(); quickActionsOpen = false"
                        class="flex items-center gap-3 w-full p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="bg-purple-100 p-2 rounded-lg">
                            <i class="fas fa-download text-purple-600"></i>
                        </div>
                        <span class="font-medium text-gray-700">Export Data</span>
                    </button>
                    <hr class="my-2">
                    <button @click="window.location.reload(); quickActionsOpen = false"
                        class="flex items-center gap-3 w-full p-3 text-left hover:bg-gray-50 rounded-lg transition-colors">
                        <div class="bg-gray-100 p-2 rounded-lg">
                            <i class="fas fa-redo text-gray-600"></i>
                        </div>
                        <span class="font-medium text-gray-700">Reload Page</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
<!-- Session Detail Modal -->
<div x-show="showSessionDetailModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showSessionDetailModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold">Detail Sesi Monitor</h3>
                    <button @click="showSessionDetailModal = false"
                        class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6 overflow-y-auto max-h-[70vh]" x-html="sessionDetailContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>

<!-- Send Message Modal -->
<div x-show="showMessageModal"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-black bg-opacity-50" @click="showMessageModal = false"></div>

        <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full">
            <div class="bg-gradient-to-r from-green-500 to-teal-600 p-6 text-white">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold">Kirim Pesan</h3>
                    <button @click="showMessageModal = false"
                        class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <form @submit.prevent="sendMessageSubmit()">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Pesan</label>
                        <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-model="messageForm.type" required>
                            <option value="info">Informasi</option>
                            <option value="warning">Peringatan</option>
                            <option value="alert">Alert</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                        <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-model="messageForm.target" required>
                            <option value="all">Semua Peserta</option>
                            <option value="individual">Peserta Tertentu</option>
                        </select>
                    </div>
                    <div class="mb-4" x-show="messageForm.target === 'individual'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Peserta</label>
                        <select class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            x-model="messageForm.target_user_id">
                            <option value="">Pilih peserta...</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pesan</label>
                        <textarea class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            rows="4" x-model="messageForm.message" placeholder="Masukkan pesan..." required></textarea>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button"
                            class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors"
                            @click="showMessageModal = false">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function liveMonitoring() {
        return {
            autoRefresh: true,
            isLoading: false,
            lastUpdated: new Date().toLocaleString(),
            showSessionDetailModal: false,
            showMessageModal: false,
            quickActionsOpen: false,
            sessionDetailContent: '',
            statistics: <?= json_encode($statistics ?? [
                            'active_sessions' => 0,
                            'active_participants' => 0,
                            'completed_today' => 0,
                            'flagged_participants' => 0
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            activeSessions: <?= json_encode($activeSessions ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            systemAlerts: <?= json_encode($systemAlerts ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            recentActivities: <?= json_encode($recentActivities ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            systemHealth: {
                cpu_usage: 15,
                memory_usage: 45,
                disk_usage: 67,
                active_connections: 12,
                response_time: 120,
                database_status: 'connected'
            },
            messageForm: {
                session_id: null,
                type: 'info',
                target: 'all',
                target_user_id: null,
                message: ''
            },
            init() {
                this.loadSystemHealth();

                // Auto refresh every 10 seconds
                setInterval(() => {
                    if (this.autoRefresh) {
                        this.refreshData();
                    }
                }, 10000);

                // Update system health every 30 seconds
                setInterval(() => {
                    this.loadSystemHealth();
                }, 30000);

                // Close quick actions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.floating-action')) {
                        this.quickActionsOpen = false;
                    }
                });
            },

            async refreshData() {
                this.isLoading = true;
                try {
                    const response = await fetch('/admin/monitoring/data');
                    const result = await response.json();

                    if (result.success) {
                        this.statistics = result.data.statistics;
                        this.activeSessions = result.data.activeSessions;
                        this.systemAlerts = result.data.systemAlerts;
                        this.recentActivities = result.data.recentActivities;
                        this.lastUpdated = new Date().toLocaleString();
                    }
                } catch (error) {
                    console.error('Failed to refresh data:', error);
                    this.showNotification('Gagal memuat data terbaru', 'error');
                } finally {
                    this.isLoading = false;
                }
            },

            async loadSystemHealth() {
                try {
                    const response = await fetch('/admin/monitoring/system-health');
                    const result = await response.json();

                    if (result.success) {
                        this.systemHealth = result.data;
                    }
                } catch (error) {
                    console.error('Failed to load system health:', error);
                }
            },

            async viewSessionDetail(sessionId) {
                try {
                    const response = await fetch(`/admin/monitoring/session/${sessionId}`);
                    const result = await response.json();

                    if (result.success) {
                        this.sessionDetailContent = this.generateSessionDetailHTML(result.data);
                        this.showSessionDetailModal = true;
                    }
                } catch (error) {
                    console.error('Failed to load session detail:', error);
                    this.showNotification('Gagal memuat detail sesi', 'error');
                }
            },

            generateSessionDetailHTML(data) {
                return `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold text-lg mb-4">Informasi Sesi</h4>
                            <div class="space-y-2">
                                <div><strong>Nama:</strong> ${data.session.name}</div>
                                <div><strong>Ujian:</strong> ${data.session.exam_title}</div>
                                <div><strong>Waktu Mulai:</strong> ${this.formatDateTime(data.session.start_time)}</div>
                                <div><strong>Status:</strong> <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">${data.session.status}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-4">Statistik</h4>
                            <div class="space-y-2">
                                <div><strong>Total Peserta:</strong> ${data.participants.length}</div>
                                <div><strong>Aktif:</strong> ${data.participants.filter(p => p.status === 'active').length}</div>
                                <div><strong>Selesai:</strong> ${data.participants.filter(p => p.status === 'completed').length}</div>
                            </div>
                        </div>
                    </div>
                `;
            },

            monitorSession(sessionId) {
                window.open(`/admin/monitoring/session/${sessionId}`, '_blank');
            },

            sendMessage(sessionId) {
                this.messageForm.session_id = sessionId;
                this.showMessageModal = true;
            },

            async sendMessageSubmit() {
                try {
                    const response = await fetch('/admin/monitoring/send-message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.messageForm)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification('Pesan berhasil dikirim', 'success');
                        this.showMessageModal = false;
                        this.resetMessageForm();
                    } else {
                        this.showNotification(result.message || 'Gagal mengirim pesan', 'error');
                    }
                } catch (error) {
                    console.error('Failed to send message:', error);
                    this.showNotification('Gagal mengirim pesan', 'error');
                }
            },

            resetMessageForm() {
                this.messageForm = {
                    session_id: null,
                    type: 'info',
                    target: 'all',
                    target_user_id: null,
                    message: ''
                };
            },

            async endSession(sessionId) {
                if (!confirm('Apakah Anda yakin ingin mengakhiri sesi ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/admin/monitoring/end-session/${sessionId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification('Sesi berhasil diakhiri', 'success');
                        this.refreshData();
                    } else {
                        this.showNotification(result.message || 'Gagal mengakhiri sesi', 'error');
                    }
                } catch (error) {
                    console.error('Failed to end session:', error);
                    this.showNotification('Gagal mengakhiri sesi', 'error');
                }
            },

            showBroadcastModal() {
                this.messageForm.session_id = null; // For broadcast to all sessions
                this.showMessageModal = true;
            },

            exportData() {
                const exportData = {
                    timestamp: new Date().toISOString(),
                    statistics: this.statistics,
                    activeSessions: this.activeSessions,
                    systemHealth: this.systemHealth,
                    recentActivities: this.recentActivities,
                    systemAlerts: this.systemAlerts
                };

                const blob = new Blob([JSON.stringify(exportData, null, 2)], {
                    type: 'application/json'
                });

                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `monitoring-data-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                this.showNotification('Data berhasil diekspor', 'success');
            },

            formatDateTime(dateString) {
                return new Date(dateString).toLocaleString('id-ID');
            },
            formatTime(dateString) {
                return new Date(dateString).toLocaleTimeString('id-ID');
            },

            getTimeElapsed(startTime) {
                const start = new Date(startTime);
                const now = new Date();
                const diff = now - start;

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                if (hours > 0) {
                    return `${hours}h ${minutes}m ago`;
                } else if (minutes > 0) {
                    return `${minutes}m ago`;
                } else {
                    return 'Just started';
                }
            },

            getActivityIcon(activityType) {
                const icons = {
                    'login': 'fa-sign-in-alt text-green-600',
                    'logout': 'fa-sign-out-alt text-gray-600',
                    'exam_start': 'fa-play text-blue-600',
                    'exam_submit': 'fa-check text-green-600',
                    'question_answered': 'fa-edit text-indigo-600',
                    'browser_switch': 'fa-window-restore text-orange-600',
                    'tab_switch': 'fa-external-link-alt text-yellow-600',
                    'flagged': 'fa-flag text-red-600',
                    'warning': 'fa-exclamation-triangle text-yellow-600'
                };
                return icons[activityType] || 'fa-circle text-gray-600';
            },

            getActivityBackgroundColor(activityType) {
                const colors = {
                    'login': 'bg-green-100',
                    'logout': 'bg-gray-100',
                    'exam_start': 'bg-blue-100',
                    'exam_submit': 'bg-green-100',
                    'question_answered': 'bg-indigo-100',
                    'browser_switch': 'bg-orange-100',
                    'tab_switch': 'bg-yellow-100',
                    'flagged': 'bg-red-100',
                    'warning': 'bg-yellow-100'
                };
                return colors[activityType] || 'bg-gray-100';
            },

            getActivityStatusColor(activityType) {
                const colors = {
                    'login': 'bg-green-400',
                    'logout': 'bg-gray-400',
                    'exam_start': 'bg-blue-400',
                    'exam_submit': 'bg-green-400',
                    'question_answered': 'bg-indigo-400',
                    'browser_switch': 'bg-orange-400',
                    'tab_switch': 'bg-yellow-400',
                    'flagged': 'bg-red-400',
                    'warning': 'bg-yellow-400'
                };
                return colors[activityType] || 'bg-gray-400';
            },

            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                const bgColors = {
                    'success': 'bg-green-500',
                    'error': 'bg-red-500',
                    'warning': 'bg-yellow-500',
                    'info': 'bg-blue-500'
                };

                notification.className = `fixed top-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        }
    }
</script>

<?= $this->endSection() ?>