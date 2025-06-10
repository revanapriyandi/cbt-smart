<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                        <svg class="w-7 h-7 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Monitor Sesi Ujian
                    </h1>
                    <p class="mt-1 text-sm text-gray-600"><?= esc($examSession->session_name) ?></p>
                </div>
                <div class="flex items-center space-x-3">
                    <!-- Auto Refresh Toggle -->
                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="autoRefresh" class="sr-only" checked>
                            <div class="relative">
                                <div class="block bg-gray-600 w-14 h-8 rounded-full"></div>
                                <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition"></div>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-700">Auto Refresh</span>
                        </label>
                    </div>
                    
                    <!-- Status Indicator -->
                    <div class="flex items-center">
                        <div id="statusIndicator" class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="ml-2 text-sm text-gray-600">Live</span>
                    </div>
                    
                    <!-- Action Buttons -->
                    <?php if ($examSession->status === 'active'): ?>
                        <button onclick="endSession(<?= $examSession->id ?>)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
                            </svg>
                            Akhiri Sesi
                        </button>
                    <?php endif; ?>
                    
                    <a href="/admin/exam-sessions/<?= $examSession->id ?>" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Detail
                    </a>
                    
                    <a href="/admin/exam-sessions" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 010 7.75"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Peserta</p>
                        <p id="totalParticipants" class="text-2xl font-bold text-gray-900"><?= $progress->total_participants ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Sedang Mengerjakan</p>
                        <p id="inProgress" class="text-2xl font-bold text-yellow-600"><?= $progress->in_progress ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Selesai</p>
                        <p id="completed" class="text-2xl font-bold text-green-600"><?= $progress->completed ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Progress Rata-rata</p>
                        <p id="averageProgress" class="text-2xl font-bold text-purple-600"><?= $progress->overall_progress ?? 0 ?>%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Participants Monitoring -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                                </svg>
                                Monitoring Peserta
                            </h3>
                            <button onclick="refreshParticipants()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Refresh
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div id="participantsContainer">
                            <?php if (!empty($participants)): ?>
                            <div class="space-y-4">
                                <?php foreach ($participants as $participant): ?>
                                <div class="participant-card border border-gray-200 rounded-lg p-4 hover:bg-gray-50" data-participant-id="<?= $participant->user_id ?>">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-purple-800"><?= strtoupper(substr($participant->student_name, 0, 1)) ?></span>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900"><?= esc($participant->student_name) ?></p>
                                                <p class="text-xs text-gray-500">NIS: <?= esc($participant->student_nis) ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <!-- Progress -->
                                            <div class="text-center">
                                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                                    <div class="bg-purple-600 h-2 rounded-full progress-bar" style="width: <?= $participant->progress ?>%"></div>
                                                </div>
                                                <span class="text-xs text-gray-600 progress-text"><?= $participant->progress ?>%</span>
                                            </div>
                                            
                                            <!-- Status -->
                                            <span class="status-badge inline-flex px-2 py-1 text-xs font-medium rounded-full 
                                                <?php 
                                                switch($participant->status) {
                                                    case 'not_started': echo 'bg-gray-100 text-gray-800'; break;
                                                    case 'in_progress': echo 'bg-yellow-100 text-yellow-800'; break;
                                                    case 'completed': echo 'bg-green-100 text-green-800'; break;
                                                    case 'absent': echo 'bg-red-100 text-red-800'; break;
                                                    default: echo 'bg-gray-100 text-gray-800';
                                                }
                                                ?>">
                                                <?= ucfirst(str_replace('_', ' ', $participant->status)) ?>
                                            </span>
                                            
                                            <!-- Time Remaining -->
                                            <?php if ($participant->status === 'in_progress'): ?>
                                            <div class="text-center">
                                                <p class="text-xs font-medium text-gray-900 time-remaining" data-end-time="<?= $examSession->end_time ?>">
                                                    --:--
                                                </p>
                                                <p class="text-xs text-gray-500">Sisa waktu</p>
                                            </div>
                                            <?php endif; ?>
                                            
                                            <!-- Action -->
                                            <div class="relative">
                                                <button onclick="toggleParticipantActions(<?= $participant->user_id ?>)" class="text-gray-400 hover:text-gray-600">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                                    </svg>
                                                </button>
                                                <div id="participantActions<?= $participant->user_id ?>" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                                                    <div class="py-1">
                                                        <button onclick="viewParticipantDetails(<?= $participant->user_id ?>)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Lihat Detail
                                                        </button>
                                                        <?php if ($participant->status === 'in_progress'): ?>
                                                        <button onclick="sendWarning(<?= $participant->user_id ?>)" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                            Kirim Peringatan
                                                        </button>
                                                        <button onclick="forceSubmit(<?= $participant->user_id ?>)" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                                            Paksa Submit
                                                        </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                                    <circle cx="9" cy="7" r="4"></circle>
                                    <path d="M23 21v-2a4 4 0 00-3-3.87"></path>
                                    <path d="M16 3.13a4 4 0 010 7.75"></path>
                                </svg>
                                <p class="text-gray-500">Belum ada peserta yang terdaftar</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Session Info -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Info Sesi
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Status</label>
                            <span class="inline-flex px-2 py-1 text-sm font-medium rounded-full 
                                <?php 
                                switch($examSession->status) {
                                    case 'scheduled': echo 'bg-yellow-100 text-yellow-800'; break;
                                    case 'active': echo 'bg-green-100 text-green-800'; break;
                                    case 'completed': echo 'bg-blue-100 text-blue-800'; break;
                                    case 'cancelled': echo 'bg-red-100 text-red-800'; break;
                                    default: echo 'bg-gray-100 text-gray-800';
                                }
                                ?>">
                                <?= ucfirst($examSession->status) ?>
                            </span>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Waktu Mulai</label>
                            <p class="text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($examSession->start_time)) ?></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Waktu Selesai</label>
                            <p class="text-sm text-gray-900"><?= date('d/m/Y H:i', strtotime($examSession->end_time)) ?></p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 uppercase">Sisa Waktu Sesi</label>
                            <p id="sessionTimeRemaining" class="text-lg font-semibold text-red-600">--:--:--</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Aktivitas Terbaru
                        </h3>
                    </div>
                    <div class="p-6">
                        <div id="recentActivities" class="space-y-3">
                            <?php if (!empty($activities)): ?>
                                <?php foreach (array_slice($activities, 0, 10) as $activity): ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-900"><?= esc($activity->description) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('H:i', strtotime($activity->created_at)) ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                            <p class="text-sm text-gray-500 text-center">Belum ada aktivitas</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Notification -->
<div id="notification" class="fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform translate-x-full transition-transform duration-300 ease-in-out">
    <div class="flex items-center">
        <div id="notification-icon" class="flex-shrink-0 w-6 h-6 mr-3"></div>
        <div id="notification-message" class="text-sm font-medium"></div>
        <button onclick="hideNotification()" class="ml-4 text-gray-400 hover:text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>

<!-- Participant Detail Modal -->
<div id="participantModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center pb-3">
            <h3 class="text-lg font-semibold">Detail Peserta</h3>
            <button onclick="closeParticipantModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="participantModalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
let autoRefreshInterval;
let sessionId = <?= $examSession->id ?>;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initializeAutoRefresh();
    updateTimeRemaining();
    setInterval(updateTimeRemaining, 1000);
});

function initializeAutoRefresh() {
    const autoRefreshToggle = document.getElementById('autoRefresh');
    
    // Set up toggle functionality
    autoRefreshToggle.addEventListener('change', function() {
        const dot = this.parentElement.querySelector('.dot');
        const bg = this.parentElement.querySelector('.block');
        
        if (this.checked) {
            dot.style.transform = 'translateX(100%)';
            bg.classList.add('bg-blue-600');
            bg.classList.remove('bg-gray-600');
            startAutoRefresh();
        } else {
            dot.style.transform = 'translateX(0)';
            bg.classList.add('bg-gray-600');
            bg.classList.remove('bg-blue-600');
            stopAutoRefresh();
        }
    });
    
    // Start auto refresh by default
    if (autoRefreshToggle.checked) {
        startAutoRefresh();
    }
}

function startAutoRefresh() {
    autoRefreshInterval = setInterval(() => {
        refreshParticipants();
        refreshActivities();
    }, 10000); // Refresh every 10 seconds
}

function stopAutoRefresh() {
    if (autoRefreshInterval) {
        clearInterval(autoRefreshInterval);
    }
}

function refreshParticipants() {
    fetch(`/admin/exam-sessions/${sessionId}/monitor-data`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateParticipants(data.data.participants);
            updateStatistics(data.data.progress);
            updateStatusIndicator();
        }
    })
    .catch(error => {
        console.error('Error refreshing participants:', error);
        updateStatusIndicator(false);
    });
}

function refreshActivities() {
    fetch(`/admin/exam-sessions/${sessionId}/recent-activities`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateActivities(data.data);
        }
    })
    .catch(error => {
        console.error('Error refreshing activities:', error);
    });
}

function updateParticipants(participants) {
    const container = document.getElementById('participantsContainer');
    
    participants.forEach(participant => {
        const card = document.querySelector(`[data-participant-id="${participant.user_id}"]`);
        if (card) {
            // Update progress
            const progressBar = card.querySelector('.progress-bar');
            const progressText = card.querySelector('.progress-text');
            if (progressBar && progressText) {
                progressBar.style.width = participant.progress + '%';
                progressText.textContent = participant.progress + '%';
            }
            
            // Update status
            const statusBadge = card.querySelector('.status-badge');
            if (statusBadge) {
                statusBadge.className = `status-badge inline-flex px-2 py-1 text-xs font-medium rounded-full ${getStatusClass(participant.status)}`;
                statusBadge.textContent = participant.status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
        }
    });
}

function updateStatistics(progress) {
    document.getElementById('totalParticipants').textContent = progress.total_participants || 0;
    document.getElementById('inProgress').textContent = progress.in_progress || 0;
    document.getElementById('completed').textContent = progress.completed || 0;
    document.getElementById('averageProgress').textContent = (progress.overall_progress || 0) + '%';
}

function updateActivities(activities) {
    const container = document.getElementById('recentActivities');
    
    if (activities.length > 0) {
        container.innerHTML = activities.slice(0, 10).map(activity => `
            <div class="flex items-start">
                <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                <div class="ml-3">
                    <p class="text-sm text-gray-900">${activity.description}</p>
                    <p class="text-xs text-gray-500">${formatTime(activity.created_at)}</p>
                </div>
            </div>
        `).join('');
    }
}

function updateStatusIndicator(isOnline = true) {
    const indicator = document.getElementById('statusIndicator');
    if (isOnline) {
        indicator.className = 'w-3 h-3 bg-green-500 rounded-full animate-pulse';
    } else {
        indicator.className = 'w-3 h-3 bg-red-500 rounded-full';
    }
}

function updateTimeRemaining() {
    const sessionEndTime = new Date('<?= $examSession->end_time ?>').getTime();
    const now = new Date().getTime();
    const timeLeft = sessionEndTime - now;
    
    const sessionTimeElement = document.getElementById('sessionTimeRemaining');
    
    if (timeLeft > 0) {
        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        sessionTimeElement.textContent = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        sessionTimeElement.className = 'text-lg font-semibold text-red-600';
    } else {
        sessionTimeElement.textContent = 'Waktu Habis';
        sessionTimeElement.className = 'text-lg font-semibold text-red-800';
    }
    
    // Update individual participant time remaining
    document.querySelectorAll('.time-remaining').forEach(element => {
        const endTime = new Date(element.dataset.endTime).getTime();
        const timeLeft = endTime - now;
        
        if (timeLeft > 0) {
            const minutes = Math.floor(timeLeft / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
            element.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        } else {
            element.textContent = '00:00';
        }
    });
}

function getStatusClass(status) {
    switch(status) {
        case 'not_started': return 'bg-gray-100 text-gray-800';
        case 'in_progress': return 'bg-yellow-100 text-yellow-800';
        case 'completed': return 'bg-green-100 text-green-800';
        case 'absent': return 'bg-red-100 text-red-800';
        default: return 'bg-gray-100 text-gray-800';
    }
}

function formatTime(datetime) {
    return new Date(datetime).toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit'
    });
}

function toggleParticipantActions(participantId) {
    const menu = document.getElementById(`participantActions${participantId}`);
    // Close all other menus
    document.querySelectorAll('[id^="participantActions"]').forEach(el => {
        if (el.id !== `participantActions${participantId}`) {
            el.classList.add('hidden');
        }
    });
    menu.classList.toggle('hidden');
}

function viewParticipantDetails(participantId) {
    // Close dropdown
    document.getElementById(`participantActions${participantId}`).classList.add('hidden');
    
    // Show modal
    document.getElementById('participantModal').classList.remove('hidden');
    document.getElementById('participantModalContent').innerHTML = '<div class="text-center py-4">Loading...</div>';
    
    // Load participant details
    fetch(`/admin/exam-sessions/${sessionId}/participant/${participantId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('participantModalContent').innerHTML = data.html;
        } else {
            document.getElementById('participantModalContent').innerHTML = '<div class="text-center py-4 text-red-600">Gagal memuat data peserta</div>';
        }
    })
    .catch(error => {
        console.error('Error loading participant details:', error);
        document.getElementById('participantModalContent').innerHTML = '<div class="text-center py-4 text-red-600">Terjadi kesalahan</div>';
    });
}

function closeParticipantModal() {
    document.getElementById('participantModal').classList.add('hidden');
}

function sendWarning(participantId) {
    document.getElementById(`participantActions${participantId}`).classList.add('hidden');
    
    if (confirm('Kirim peringatan kepada peserta ini?')) {
        fetch(`/admin/exam-sessions/${sessionId}/send-warning`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                participant_id: participantId,
                message: 'Harap perhatikan waktu yang tersisa dan fokus pada ujian.'
            })
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'error');
        })
        .catch(error => {
            console.error('Error sending warning:', error);
            showNotification('Terjadi kesalahan saat mengirim peringatan', 'error');
        });
    }
}

function forceSubmit(participantId) {
    document.getElementById(`participantActions${participantId}`).classList.add('hidden');
    
    if (confirm('Paksa submit ujian peserta ini? Tindakan ini tidak dapat dibatalkan.')) {
        fetch(`/admin/exam-sessions/${sessionId}/force-submit`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                participant_id: participantId
            })
        })
        .then(response => response.json())
        .then(data => {
            showNotification(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                setTimeout(() => refreshParticipants(), 1000);
            }
        })
        .catch(error => {
            console.error('Error forcing submit:', error);
            showNotification('Terjadi kesalahan saat memaksa submit', 'error');
        });
    }
}

function endSession(sessionId) {
    if (confirm('Apakah Anda yakin ingin mengakhiri sesi ujian ini? Semua siswa yang sedang mengerjakan akan dipaksa menyelesaikan ujian.')) {
        fetch(`/admin/exam-sessions/${sessionId}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.href = `/admin/exam-sessions/${sessionId}`;
                }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Terjadi kesalahan saat mengakhiri sesi ujian', 'error');
        });
    }
}

function showNotification(message, type = 'info') {
    const notification = document.getElementById('notification');
    const icon = document.getElementById('notification-icon');
    const messageEl = document.getElementById('notification-message');
    
    messageEl.textContent = message;
    
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transform transition-transform duration-300 ease-in-out ${
        type === 'success' ? 'bg-green-50 border border-green-200' :
        type === 'error' ? 'bg-red-50 border border-red-200' :
        type === 'warning' ? 'bg-yellow-50 border border-yellow-200' :
        'bg-blue-50 border border-blue-200'
    }`;
    
    icon.innerHTML = type === 'success' ? 
        '<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' :
        type === 'error' ? 
        '<svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' :
        type === 'warning' ? 
        '<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path></svg>' :
        '<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
    
    messageEl.className = `text-sm font-medium ${
        type === 'success' ? 'text-green-800' :
        type === 'error' ? 'text-red-800' :
        type === 'warning' ? 'text-yellow-800' :
        'text-blue-800'
    }`;
    
    notification.style.transform = 'translateX(0)';
    
    setTimeout(() => {
        hideNotification();
    }, 5000);
}

function hideNotification() {
    const notification = document.getElementById('notification');
    notification.style.transform = 'translateX(100%)';
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="participantActions"]') && !event.target.closest('button[onclick*="toggleParticipantActions"]')) {
        document.querySelectorAll('[id^="participantActions"]').forEach(el => {
            el.classList.add('hidden');
        });
    }
});
</script>
<?= $this->endSection() ?>
