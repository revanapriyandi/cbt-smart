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