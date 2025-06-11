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
</div>