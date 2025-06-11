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