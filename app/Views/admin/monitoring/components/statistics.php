<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Active Sessions Card -->
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