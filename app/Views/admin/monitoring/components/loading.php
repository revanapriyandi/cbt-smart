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