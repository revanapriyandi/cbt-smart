<!-- Recent Activities -->
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