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