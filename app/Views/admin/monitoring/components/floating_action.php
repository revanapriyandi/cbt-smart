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