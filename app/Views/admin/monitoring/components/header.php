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