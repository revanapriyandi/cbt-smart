<script>
    function liveMonitoring() {
        return {
            autoRefresh: true,
            isLoading: false,
            lastUpdated: new Date().toLocaleString(),
            showSessionDetailModal: false,
            showMessageModal: false,
            quickActionsOpen: false,
            sessionDetailContent: '',
            statistics: <?= json_encode($statistics ?? [
                            'active_sessions' => 0,
                            'active_participants' => 0,
                            'completed_today' => 0,
                            'flagged_participants' => 0
                        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            activeSessions: <?= json_encode($activeSessions ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            systemAlerts: <?= json_encode($systemAlerts ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            recentActivities: <?= json_encode($recentActivities ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            systemHealth: {
                cpu_usage: 15,
                memory_usage: 45,
                disk_usage: 67,
                active_connections: 12,
                response_time: 120,
                database_status: 'connected'
            },
            messageForm: {
                session_id: null,
                type: 'info',
                target: 'all',
                target_user_id: null,
                message: ''
            },
            init() {
                this.loadSystemHealth();

                // Auto refresh every 10 seconds
                setInterval(() => {
                    if (this.autoRefresh) {
                        this.refreshData();
                    }
                }, 10000);

                // Update system health every 30 seconds
                setInterval(() => {
                    this.loadSystemHealth();
                }, 30000);

                // Close quick actions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.floating-action')) {
                        this.quickActionsOpen = false;
                    }
                });
            },

            async refreshData() {
                this.isLoading = true;
                try {
                    const response = await fetch('/admin/monitoring/data');
                    const result = await response.json();

                    if (result.success) {
                        this.statistics = result.data.statistics;
                        this.activeSessions = result.data.activeSessions;
                        this.systemAlerts = result.data.systemAlerts;
                        this.recentActivities = result.data.recentActivities;
                        this.lastUpdated = new Date().toLocaleString();
                    }
                } catch (error) {
                    console.error('Failed to refresh data:', error);
                    this.showNotification('Gagal memuat data terbaru', 'error');
                } finally {
                    this.isLoading = false;
                }
            },

            async loadSystemHealth() {
                try {
                    const response = await fetch('/admin/monitoring/system-health');
                    const result = await response.json();

                    if (result.success) {
                        this.systemHealth = result.data;
                    }
                } catch (error) {
                    console.error('Failed to load system health:', error);
                }
            },

            async viewSessionDetail(sessionId) {
                try {
                    const response = await fetch(`/admin/monitoring/session/${sessionId}`);
                    const result = await response.json();

                    if (result.success) {
                        this.sessionDetailContent = this.generateSessionDetailHTML(result.data);
                        this.showSessionDetailModal = true;
                    }
                } catch (error) {
                    console.error('Failed to load session detail:', error);
                    this.showNotification('Gagal memuat detail sesi', 'error');
                }
            },

            generateSessionDetailHTML(data) {
                return `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold text-lg mb-4">Informasi Sesi</h4>
                            <div class="space-y-2">
                                <div><strong>Nama:</strong> ${data.session.name}</div>
                                <div><strong>Ujian:</strong> ${data.session.exam_title}</div>
                                <div><strong>Waktu Mulai:</strong> ${this.formatDateTime(data.session.start_time)}</div>
                                <div><strong>Status:</strong> <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">${data.session.status}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-4">Statistik</h4>
                            <div class="space-y-2">
                                <div><strong>Total Peserta:</strong> ${data.participants.length}</div>
                                <div><strong>Aktif:</strong> ${data.participants.filter(p => p.status === 'active').length}</div>
                                <div><strong>Selesai:</strong> ${data.participants.filter(p => p.status === 'completed').length}</div>
                            </div>
                        </div>
                    </div>
                `;
            },

            monitorSession(sessionId) {
                window.open(`/admin/monitoring/session/${sessionId}`, '_blank');
            },

            sendMessage(sessionId) {
                this.messageForm.session_id = sessionId;
                this.showMessageModal = true;
            },

            async sendMessageSubmit() {
                try {
                    const response = await fetch('/admin/monitoring/send-message', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(this.messageForm)
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification('Pesan berhasil dikirim', 'success');
                        this.showMessageModal = false;
                        this.resetMessageForm();
                    } else {
                        this.showNotification(result.message || 'Gagal mengirim pesan', 'error');
                    }
                } catch (error) {
                    console.error('Failed to send message:', error);
                    this.showNotification('Gagal mengirim pesan', 'error');
                }
            },

            resetMessageForm() {
                this.messageForm = {
                    session_id: null,
                    type: 'info',
                    target: 'all',
                    target_user_id: null,
                    message: ''
                };
            },

            async endSession(sessionId) {
                if (!confirm('Apakah Anda yakin ingin mengakhiri sesi ini?')) {
                    return;
                }

                try {
                    const response = await fetch(`/admin/monitoring/end-session/${sessionId}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const result = await response.json();

                    if (result.success) {
                        this.showNotification('Sesi berhasil diakhiri', 'success');
                        this.refreshData();
                    } else {
                        this.showNotification(result.message || 'Gagal mengakhiri sesi', 'error');
                    }
                } catch (error) {
                    console.error('Failed to end session:', error);
                    this.showNotification('Gagal mengakhiri sesi', 'error');
                }
            },

            showBroadcastModal() {
                this.messageForm.session_id = null; // For broadcast to all sessions
                this.showMessageModal = true;
            },

            exportData() {
                const exportData = {
                    timestamp: new Date().toISOString(),
                    statistics: this.statistics,
                    activeSessions: this.activeSessions,
                    systemHealth: this.systemHealth,
                    recentActivities: this.recentActivities,
                    systemAlerts: this.systemAlerts
                };

                const blob = new Blob([JSON.stringify(exportData, null, 2)], {
                    type: 'application/json'
                });

                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `monitoring-data-${new Date().toISOString().split('T')[0]}.json`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(url);

                this.showNotification('Data berhasil diekspor', 'success');
            },

            formatDateTime(dateString) {
                return new Date(dateString).toLocaleString('id-ID');
            },
            formatTime(dateString) {
                return new Date(dateString).toLocaleTimeString('id-ID');
            },

            getTimeElapsed(startTime) {
                const start = new Date(startTime);
                const now = new Date();
                const diff = now - start;

                const hours = Math.floor(diff / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));

                if (hours > 0) {
                    return `${hours}h ${minutes}m ago`;
                } else if (minutes > 0) {
                    return `${minutes}m ago`;
                } else {
                    return 'Just started';
                }
            },

            getActivityIcon(activityType) {
                const icons = {
                    'login': 'fa-sign-in-alt text-green-600',
                    'logout': 'fa-sign-out-alt text-gray-600',
                    'exam_start': 'fa-play text-blue-600',
                    'exam_submit': 'fa-check text-green-600',
                    'question_answered': 'fa-edit text-indigo-600',
                    'browser_switch': 'fa-window-restore text-orange-600',
                    'tab_switch': 'fa-external-link-alt text-yellow-600',
                    'flagged': 'fa-flag text-red-600',
                    'warning': 'fa-exclamation-triangle text-yellow-600'
                };
                return icons[activityType] || 'fa-circle text-gray-600';
            },

            getActivityBackgroundColor(activityType) {
                const colors = {
                    'login': 'bg-green-100',
                    'logout': 'bg-gray-100',
                    'exam_start': 'bg-blue-100',
                    'exam_submit': 'bg-green-100',
                    'question_answered': 'bg-indigo-100',
                    'browser_switch': 'bg-orange-100',
                    'tab_switch': 'bg-yellow-100',
                    'flagged': 'bg-red-100',
                    'warning': 'bg-yellow-100'
                };
                return colors[activityType] || 'bg-gray-100';
            },

            getActivityStatusColor(activityType) {
                const colors = {
                    'login': 'bg-green-400',
                    'logout': 'bg-gray-400',
                    'exam_start': 'bg-blue-400',
                    'exam_submit': 'bg-green-400',
                    'question_answered': 'bg-indigo-400',
                    'browser_switch': 'bg-orange-400',
                    'tab_switch': 'bg-yellow-400',
                    'flagged': 'bg-red-400',
                    'warning': 'bg-yellow-400'
                };
                return colors[activityType] || 'bg-gray-400';
            },

            showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                const bgColors = {
                    'success': 'bg-green-500',
                    'error': 'bg-red-500',
                    'warning': 'bg-yellow-500',
                    'info': 'bg-blue-500'
                };

                notification.className = `fixed top-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transform transition-all duration-300`;
                notification.textContent = message;

                document.body.appendChild(notification);

                // Animate in
                setTimeout(() => {
                    notification.style.transform = 'translateX(0)';
                }, 100);

                // Remove after 3 seconds
                setTimeout(() => {
                    notification.style.transform = 'translateX(100%)';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }, 3000);
            }
        }
    }
</script>