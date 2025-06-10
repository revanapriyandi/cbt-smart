<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-3">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 bg-purple-100 rounded-lg">
                        <i class="fas fa-play-circle text-purple-600 text-lg"></i>
                    </div>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Sesi Ujian</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola dan monitor sesi ujian yang sedang berlangsung</p>
                </div>
            </div>
            <div class="flex space-x-3 mt-4 sm:mt-0">
                <button type="button" onclick="exportData()" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-download -ml-1 mr-2 h-4 w-4"></i>
                    Export
                </button>
                <a href="/admin/exam-sessions/create" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-plus -ml-1 mr-2 h-4 w-4"></i>
                    Buat Sesi Ujian
                </a>
            </div>
        </div>
    </div> <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg">
                        <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Sesi</div>
                    <div class="text-2xl font-bold text-gray-900"><?= number_format($statistics['total_sessions']) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg">
                        <i class="fas fa-play-circle text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Sesi Aktif</div>
                    <div class="text-2xl font-bold text-gray-900"><?= number_format($statistics['active']) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 rounded-lg">
                        <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Sesi Hari Ini</div>
                    <div class="text-2xl font-bold text-gray-900"><?= number_format($statistics['today_sessions']) ?></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="flex items-center justify-center w-12 h-12 bg-yellow-100 rounded-lg">
                        <i class="fas fa-calendar-week text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Sesi Minggu Ini</div>
                    <div class="text-2xl font-bold text-gray-900"><?= number_format($statistics['week_sessions']) ?></div>
                </div>
            </div>
        </div>
    </div> <!-- Filters Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <i class="fas fa-filter text-purple-600"></i>
                <h2 class="text-lg font-semibold text-gray-900">Filter & Pencarian</h2>
            </div>
            <button type="button" onclick="resetFilters()" class="inline-flex items-center px-3 py-1.5 bg-gray-100 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-undo w-4 h-4 mr-1"></i>
                Reset
            </button>
        </div>
        <div class="p-6">
            <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label> <select name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="">Semua Status</option>
                        <option value="scheduled" <?= ($filters['status'] ?? '') === 'scheduled' ? 'selected' : '' ?>>Dijadwalkan</option>
                        <option value="active" <?= ($filters['status'] ?? '') === 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="completed" <?= ($filters['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Selesai</option>
                        <option value="cancelled" <?= ($filters['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ujian</label>
                    <select name="exam_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="">Semua Ujian</option> <?php foreach ($exams as $exam): ?>
                            <option value="<?= $exam['id'] ?>" <?= ($filters['exam_id'] ?? '') == $exam['id'] ? 'selected' : '' ?>>
                                <?= esc($exam['title']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="class_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm">
                        <option value="">Semua Kelas</option> <?php foreach ($classes as $class): ?>
                            <option value="<?= $class['id'] ?>" <?= ($filters['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                <?= esc($class['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" placeholder="Cari sesi ujian..." value="<?= esc($filters['search'] ?? '') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                    <input type="date" name="date_from" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" value="<?= esc($filters['date_from'] ?? '') ?>">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
                    <input type="date" name="date_to" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 sm:text-sm" value="<?= esc($filters['date_to'] ?? '') ?>">
                </div>
            </form>
            <div class="flex items-center space-x-3 mt-6">
                <button type="submit" form="filterForm" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    <i class="fas fa-search -ml-1 mr-2 h-4 w-4"></i>
                    Filter
                </button>
                <button type="button" onclick="bulkDelete()" id="bulkDeleteBtn" style="display: none;" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-trash -ml-1 mr-2 h-4 w-4"></i>
                    Hapus Terpilih
                </button>
            </div>
        </div>
    </div> <!-- Exam Sessions Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center justify-between p-6 border-b border-gray-200">
            <div class="flex items-center space-x-2">
                <i class="fas fa-list text-purple-600"></i>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Sesi Ujian</h2>
            </div>
        </div>
        <div class="overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200" id="dataTable">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="w-10 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="selectAll" class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Sesi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ujian</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Peserta</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lokasi</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($examSessions as $session): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" class="h-4 w-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500 session-checkbox" value="<?= $session->id ?>">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?= esc($session->session_name) ?></div>
                                    <div class="text-sm text-gray-500">Dibuat: <?= date('d/m/Y H:i', strtotime($session->created_at)) ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?= esc($session->exam_title) ?></div>
                                    <div class="text-sm text-gray-500"><?= $session->exam_duration ?> menit</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= esc($session->class_name) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center">
                                        <i class="fas fa-play text-green-500 mr-2 h-3 w-3"></i>
                                        <?= date('d/m/Y H:i', strtotime($session->start_time)) ?>
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-stop text-red-500 mr-2 h-3 w-3"></i>
                                        <?= date('d/m/Y H:i', strtotime($session->end_time)) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="font-medium text-gray-900"><?= $session->completed_count ?>/<?= $session->participant_count ?></div>
                                    <div class="text-sm text-gray-500">Max: <?= $session->max_participants ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $session->room_location ? '<i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>' . esc($session->room_location) : '<span class="text-gray-400">-</span>' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = [
                                        'scheduled' => 'bg-yellow-100 text-yellow-800',
                                        'active' => 'bg-green-100 text-green-800',
                                        'completed' => 'bg-blue-100 text-blue-800',
                                        'cancelled' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusText = [
                                        'scheduled' => 'Dijadwalkan',
                                        'active' => 'Aktif',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan'
                                    ];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass[$session->status] ?>">
                                        <?= $statusText[$session->status] ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="/admin/exam-sessions/<?= $session->id ?>" class="inline-flex items-center p-1.5 bg-blue-100 text-blue-600 rounded-md hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" title="Detail">
                                            <i class="fas fa-eye h-4 w-4"></i>
                                        </a>

                                        <?php if ($session->status === 'scheduled'): ?>
                                            <button onclick="startSession(<?= $session->id ?>)" class="inline-flex items-center p-1.5 bg-green-100 text-green-600 rounded-md hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500" title="Mulai">
                                                <i class="fas fa-play h-4 w-4"></i>
                                            </button>
                                            <a href="/admin/exam-sessions/edit/<?= $session->id ?>" class="inline-flex items-center p-1.5 bg-yellow-100 text-yellow-600 rounded-md hover:bg-yellow-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500" title="Edit">
                                                <i class="fas fa-edit h-4 w-4"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($session->status === 'active'): ?>
                                            <button onclick="endSession(<?= $session->id ?>)" class="inline-flex items-center p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" title="Akhiri">
                                                <i class="fas fa-stop h-4 w-4"></i>
                                            </button>
                                            <a href="/admin/exam-sessions/monitor/<?= $session->id ?>" class="inline-flex items-center p-1.5 bg-purple-100 text-purple-600 rounded-md hover:bg-purple-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" title="Monitor">
                                                <i class="fas fa-desktop h-4 w-4"></i>
                                            </a>
                                        <?php endif; ?>

                                        <?php if (in_array($session->status, ['scheduled', 'cancelled'])): ?>
                                            <button onclick="deleteSession(<?= $session->id ?>)" class="inline-flex items-center p-1.5 bg-red-100 text-red-600 rounded-md hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" title="Hapus">
                                                <i class="fas fa-trash h-4 w-4"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Session Detail Modal -->
<div id="sessionDetailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
        <div class="flex items-center justify-between p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Detail Sesi Ujian</h3>
            <button onclick="closeSessionDetailModal()" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4" id="sessionDetailContent">
            <div class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-600"></div>
                <span class="ml-2 text-gray-600">Loading...</span>
            </div>
        </div>
    </div>
</div>

<script>
    // Custom notification functions
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }

    function confirmAction(message) {
        return confirm(message);
    }

    function closeSessionDetailModal() {
        document.getElementById('sessionDetailModal').classList.add('hidden');
    }

    $(document).ready(function() {
        // Initialize DataTable
        $('#dataTable').DataTable({
            "pageLength": 25,
            "order": [
                [4, "desc"]
            ],
            "columnDefs": [{
                "orderable": false,
                "targets": [0, 8]
            }]
        });

        // Handle select all checkbox
        $('#selectAll').change(function() {
            $('.session-checkbox').prop('checked', this.checked);
            toggleBulkActions();
        });

        // Handle individual checkboxes
        $('.session-checkbox').change(function() {
            toggleBulkActions();
        });

        // Auto-refresh for active sessions
        setInterval(function() {
            if ($('.bg-green-100').length > 0) {
                location.reload();
            }
        }, 30000); // Refresh every 30 seconds if there are active sessions
    });

    function toggleBulkActions() {
        const checkedCount = $('.session-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteBtn').show();
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }

    function startSession(id) {
        if (confirmAction('Apakah Anda yakin ingin memulai sesi ujian ini?')) {
            $.post('/admin/exam-sessions/start/' + id, function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            }).fail(function() {
                showNotification('Terjadi kesalahan saat memulai sesi ujian', 'error');
            });
        }
    }

    function endSession(id) {
        if (confirmAction('Apakah Anda yakin ingin mengakhiri sesi ujian ini?')) {
            $.post('/admin/exam-sessions/end/' + id, function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            }).fail(function() {
                showNotification('Terjadi kesalahan saat mengakhiri sesi ujian', 'error');
            });
        }
    }

    function deleteSession(id) {
        if (confirmAction('Apakah Anda yakin ingin menghapus sesi ujian ini?')) {
            $.ajax({
                url: '/admin/exam-sessions/delete/' + id,
                type: 'POST',
                success: function(response) {
                    if (response.success) {
                        showNotification(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    } else {
                        showNotification('Error: ' + response.message, 'error');
                    }
                },
                error: function() {
                    showNotification('Terjadi kesalahan saat menghapus sesi ujian', 'error');
                }
            });
        }
    }

    function bulkDelete() {
        const selectedIds = $('.session-checkbox:checked').map(function() {
            return this.value;
        }).get();

        if (selectedIds.length === 0) {
            showNotification('Pilih minimal satu sesi ujian', 'error');
            return;
        }

        if (confirmAction('Apakah Anda yakin ingin menghapus ' + selectedIds.length + ' sesi ujian?')) {
            $.post('/admin/exam-sessions/bulk-action', {
                action: 'delete',
                session_ids: selectedIds
            }, function(response) {
                if (response.success) {
                    showNotification(response.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification('Error: ' + response.message, 'error');
                }
            }).fail(function() {
                showNotification('Terjadi kesalahan saat menghapus sesi ujian', 'error');
            });
        }
    }

    function resetFilters() {
        window.location.href = '/admin/exam-sessions';
    }

    function exportData() {
        const params = new URLSearchParams(window.location.search);
        params.set('format', 'excel');
        window.open('/admin/exam-sessions/export?' + params.toString());
    }

    function showSessionDetail(id) {
        document.getElementById('sessionDetailModal').classList.remove('hidden');

        $.get('/admin/exam-sessions/data/' + id, function(response) {
            if (response.success) {
                const data = response.data;
                const session = data.session;
                const progress = data.progress;

                const content = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Informasi Sesi</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Nama Sesi:</span>
                                <span class="text-gray-900">${session.session_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Ujian:</span>
                                <span class="text-gray-900">${session.exam_title}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Kelas:</span>
                                <span class="text-gray-900">${session.class_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Waktu Mulai:</span>
                                <span class="text-gray-900">${session.start_time}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Waktu Selesai:</span>
                                <span class="text-gray-900">${session.end_time}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Lokasi:</span>
                                <span class="text-gray-900">${session.room_location || '-'}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Progress Sesi</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Belum Mulai:</span>
                                <span class="text-gray-900">${progress.not_started}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Sedang Berlangsung:</span>
                                <span class="text-gray-900">${progress.in_progress}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Selesai:</span>
                                <span class="text-gray-900">${progress.completed}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="font-medium text-gray-700">Rata-rata Skor:</span>
                                <span class="text-gray-900">${progress.avg_score}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;

                $('#sessionDetailContent').html(content);
            }
        }).fail(function() {
            $('#sessionDetailContent').html('<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">Gagal memuat data sesi</div>');
        });
    }
</script>

<?= $this->endSection() ?>