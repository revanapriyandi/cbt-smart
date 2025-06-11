<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Hasil Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Hasil Ujian</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Kelola dan analisis hasil ujian siswa</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="exportResults()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export Data
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <?php if (isset($statistics)): ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <!-- Total Results -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Results</p>
                        <p class="text-3xl font-bold text-blue-900 mt-2"><?= $statistics['total_results'] ?? 0 ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Average Score</p>
                        <p class="text-3xl font-bold text-green-900 mt-2"><?= number_format($statistics['average_score'] ?? 0, 1) ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-green-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pass Rate -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-sm p-6 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600 uppercase tracking-wide">Pass Rate</p>
                        <p class="text-3xl font-bold text-yellow-900 mt-2"><?= number_format($statistics['pass_rate'] ?? 0, 1) ?>%</p>
                    </div>
                    <div class="p-3 rounded-full bg-yellow-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending Grading -->
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-sm p-6 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600 uppercase tracking-wide">Pending Grading</p>
                        <p class="text-3xl font-bold text-red-900 mt-2"><?= $statistics['pending_grading'] ?? 0 ?></p>
                    </div>
                    <div class="p-3 rounded-full bg-red-500">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Hasil Ujian</h3>
        <form method="GET" action="<?= current_url() ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Exam Filter -->
                <div>
                    <label for="exam_id" class="block text-sm font-medium text-gray-700 mb-2">Ujian</label>
                    <select name="exam_id" id="exam_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Ujian</option>
                        <?php if (isset($exams)): ?>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?= $exam['id'] ?>" <?= ($filters['exam_id'] ?? '') == $exam['id'] ? 'selected' : '' ?>>
                                    <?= esc($exam['title']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Class Filter -->
                <div>
                    <label for="class_id" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                    <select name="class_id" id="class_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kelas</option>
                        <?php if (isset($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class['id'] ?>" <?= ($filters['class_id'] ?? '') == $class['id'] ? 'selected' : '' ?>>
                                    <?= esc($class['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Session Filter -->
                <div>
                    <label for="session_id" class="block text-sm font-medium text-gray-700 mb-2">Sesi</label>
                    <select name="session_id" id="session_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Sesi</option>
                        <?php if (isset($sessions)): ?>
                            <?php foreach ($sessions as $session): ?>
                                <option value="<?= $session['id'] ?>" <?= ($filters['session_id'] ?? '') == $session['id'] ? 'selected' : '' ?>>
                                    <?= esc($session['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Status</option>
                        <option value="completed" <?= ($filters['status'] ?? '') == 'completed' ? 'selected' : '' ?>>Selesai</option>
                        <option value="graded" <?= ($filters['status'] ?? '') == 'graded' ? 'selected' : '' ?>>Dinilai</option>
                        <option value="pending" <?= ($filters['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Menunggu</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Date From -->
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-2">Dari Tanggal</label>
                    <input type="date" name="date_from" id="date_from" value="<?= $filters['date_from'] ?? '' ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Date To -->
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-2">Sampai Tanggal</label>
                    <input type="date" name="date_to" id="date_to" value="<?= $filters['date_to'] ?? '' ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <input type="text" name="search" id="search" value="<?= $filters['search'] ?? '' ?>"
                        placeholder="Nama siswa, NIS..."
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="clearFilters()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm font-medium">
                    Reset Filter
                </button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-3 sm:space-y-0">
                <h3 class="text-lg font-semibold text-gray-900">Hasil Ujian</h3>
                <div class="flex items-center space-x-3">
                    <button onclick="toggleBulkActions()" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                        Bulk Actions
                    </button>
                    <div class="text-sm text-gray-600">
                        Total: <?= count($results ?? []) ?> hasil
                    </div>
                </div>
            </div>

            <!-- Bulk Actions -->
            <div id="bulkActions" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-4">
                    <select id="bulkAction" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">Pilih Aksi</option>
                        <option value="publish">Publikasi</option>
                        <option value="unpublish">Sembunyikan</option>
                        <option value="recalculate">Hitung Ulang Skor</option>
                        <option value="delete">Hapus</option>
                    </select>
                    <button onclick="executeBulkAction()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium">
                        Jalankan
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full" id="resultsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Siswa</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ujian</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kelas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Skor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($results) && count($results) > 0): ?>
                        <?php foreach ($results as $result): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" name="result_ids[]" value="<?= $result['id'] ?>" class="result-checkbox rounded border-gray-300">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900"><?= esc($result['student_name'] ?? 'N/A') ?></div>
                                            <div class="text-sm text-gray-500"><?= esc($result['student_username'] ?? 'N/A') ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900"><?= esc($result['exam_title'] ?? 'N/A') ?></div>
                                    <div class="text-sm text-gray-500"><?= esc($result['subject_name'] ?? 'N/A') ?></div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= esc($result['class_name'] ?? 'N/A') ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?= $result['total_score'] ?? 0 ?></div>
                                    <div class="text-xs text-gray-500"><?= number_format($result['percentage'] ?? 0, 1) ?>%</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if (isset($result['final_grade']) && $result['final_grade']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= ($result['final_grade'] >= 75) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= $result['final_grade'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-500">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($result['status'] ?? 'pending') {
                                        case 'completed':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            $statusText = 'Selesai';
                                            break;
                                        case 'graded':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            $statusText = 'Dinilai';
                                            break;
                                        case 'pending':
                                            $statusClass = 'bg-yellow-100 text-yellow-800';
                                            $statusText = 'Menunggu';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = ucfirst($result['status'] ?? 'Unknown');
                                    }
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if (isset($result['started_at'])): ?>
                                        <div><?= date('d/m/Y', strtotime($result['started_at'])) ?></div>
                                        <div><?= date('H:i', strtotime($result['started_at'])) ?></div>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="<?= base_url("admin/results/{$result['id']}") ?>"
                                            class="text-blue-600 hover:text-blue-900" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                        </a>
                                        <?php if (($result['status'] ?? '') === 'completed'): ?>
                                            <a href="<?= base_url("admin/results/{$result['id']}/grade") ?>"
                                                class="text-green-600 hover:text-green-900" title="Nilai">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                        <?php endif; ?>
                                        <button onclick="deleteResult(<?= $result['id'] ?>)"
                                            class="text-red-600 hover:text-red-900" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr class="no-data">
                            <td colspan="9" class="px-6 py-8 text-center">
                                <div class="text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-sm">Tidak ada hasil ujian ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian atau buat ujian baru</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Export Hasil Ujian</h3>
            <form id="exportForm" method="GET" action="<?= base_url('admin/results/export') ?>">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <select name="format" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="excel">Excel (.xlsx)</option>
                        <option value="csv">CSV (.csv)</option>
                        <option value="pdf">PDF (.pdf)</option>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeExportModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Report Modal -->
<div id="reportModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Generate Report</h3>
            <form id="reportForm">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Report</label>
                    <select name="report_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="summary">Ringkasan</option>
                        <option value="detailed">Detail</option>
                        <option value="analysis">Analisis</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ujian</label>
                    <select name="exam_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                        <option value="">Semua Ujian</option>
                        <?php if (isset($exams)): ?>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?= $exam['id'] ?>"><?= esc($exam['title']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeReportModal()" class="px-4 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-md text-sm">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm">
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Toggle bulk actions
    function toggleBulkActions() {
        const bulkActions = document.getElementById('bulkActions');
        bulkActions.classList.toggle('hidden');
    }

    // Toggle select all checkboxes
    function toggleSelectAll() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.result-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }

    // Execute bulk action
    function executeBulkAction() {
        const action = document.getElementById('bulkAction').value;
        const checkboxes = document.querySelectorAll('.result-checkbox:checked');

        if (!action) {
            alert('Pilih aksi yang akan dilakukan');
            return;
        }

        if (checkboxes.length === 0) {
            alert('Pilih minimal satu hasil ujian');
            return;
        }

        if (confirm('Apakah Anda yakin ingin melakukan aksi ini?')) {
            const resultIds = Array.from(checkboxes).map(cb => cb.value);

            fetch('<?= base_url("admin/results/bulk-action") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: action,
                        result_ids: resultIds
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }
    }

    // Delete single result
    function deleteResult(id) {
        if (confirm('Apakah Anda yakin ingin menghapus hasil ujian ini?')) {
            fetch(`<?= base_url("admin/results") ?>/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan');
                });
        }
    }

    // Clear all filters
    function clearFilters() {
        const form = document.querySelector('form');
        form.reset();
        window.location.href = '<?= base_url("admin/results") ?>';
    }

    // Export functions
    function exportResults() {
        document.getElementById('exportModal').classList.remove('hidden');
    }

    function closeExportModal() {
        document.getElementById('exportModal').classList.add('hidden');
    }

    // Report functions
    function generateReport() {
        document.getElementById('reportModal').classList.remove('hidden');
    }

    function closeReportModal() {
        document.getElementById('reportModal').classList.add('hidden');
    }

    // Handle report form submission
    document.getElementById('reportForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch('<?= base_url("admin/results/generate-report") ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    if (data.download_url) {
                        window.open(data.download_url, '_blank');
                    }
                    closeReportModal();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan');
            });
    });

    // Initialize DataTables if available
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure jQuery and DataTables are loaded
        if (typeof $ === 'undefined') {
            console.error('jQuery is not loaded');
            addBasicTableFunctionality();
            return;
        }

        if (!$.fn.DataTable) {
            console.error('DataTables is not loaded');
            addBasicTableFunctionality();
            return;
        }

        // Wait for DOM to be fully loaded
        setTimeout(function() {
            initializeDataTable();
        }, 300);
    });

    function initializeDataTable() {
        try {
            // Validate table structure before initializing DataTable
            const table = document.getElementById('resultsTable');
            if (!table) {
                console.warn('Results table not found');
                addBasicTableFunctionality();
                return;
            }

            // Clean and destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#resultsTable')) {
                try {
                    $('#resultsTable').DataTable().destroy();
                    console.log('Existing DataTable destroyed');
                } catch (e) {
                    console.warn('Error destroying existing DataTable:', e);
                }

                // Clean up DataTable artifacts
                $('#resultsTable').removeAttr('role aria-describedby');
                $('#resultsTable thead th, #resultsTable tbody td').removeAttr('style tabindex');
                $('#resultsTable thead th, #resultsTable tbody td').removeClass('sorting sorting_asc sorting_desc');

                // Restore original classes
                $('#resultsTable thead th').each(function() {
                    $(this).attr('class', 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider');
                });

                $('#resultsTable tbody td').each(function() {
                    if (!$(this).hasClass('px-6')) {
                        $(this).addClass('px-6 py-4 whitespace-nowrap');
                    }
                });

                // Remove any DataTable wrapper elements
                const wrapper = $('#resultsTable').closest('.dataTables_wrapper');
                if (wrapper.length) {
                    $('#resultsTable').unwrap();
                    wrapper.remove();
                }
            }

            // Validate table structure
            const thead = table.querySelector('thead');
            const tbody = table.querySelector('tbody');

            if (!thead || !tbody) {
                console.warn('Table structure invalid - missing thead or tbody');
                addBasicTableFunctionality();
                return;
            }

            const headerCells = thead.querySelectorAll('th').length;
            console.log('Header columns found:', headerCells);

            // Check if we have real data (not just the no-data row)
            const dataRows = tbody.querySelectorAll('tr:not(.no-data)');
            const hasRealData = dataRows.length > 0;

            console.log('Data rows found:', dataRows.length);
            console.log('Has real data:', hasRealData);

            // If no real data, create a simple table without DataTable functionality
            if (!hasRealData) {
                console.log('No data available, skipping DataTable initialization');
                addBasicTableFunctionality();
                return;
            }

            // Validate data row structure
            let isValidStructure = true;
            dataRows.forEach((row, index) => {
                const cellCount = row.querySelectorAll('td').length;
                if (cellCount !== headerCells) {
                    console.error(`Row ${index + 1} has ${cellCount} cells, expected ${headerCells}`);
                    isValidStructure = false;
                }
            });

            if (!isValidStructure) {
                console.error('Table has inconsistent structure');
                addBasicTableFunctionality();
                return;
            }

            // Set DataTable error mode to throw to catch errors
            $.fn.dataTable.ext.errMode = 'throw';

            // Initialize DataTable with minimal, safe configuration
            const dataTableConfig = {
                responsive: true,
                pageLength: 25,
                searching: true,
                paging: true,
                info: true,
                autoWidth: false,
                destroy: true,
                processing: false,
                deferRender: false,
                order: [
                    [7, 'desc']
                ], // Sort by time column
                columnDefs: [{
                    orderable: false,
                    targets: [0, 8], // Checkbox and action columns
                    searchable: false
                }],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data yang tersedia",
                    zeroRecords: "Tidak ada data yang sesuai dengan pencarian"
                },
                drawCallback: function() {
                    bindTableEvents();
                },
                initComplete: function() {
                    console.log('DataTable initialized successfully');
                }
            };

            console.log('Initializing DataTable with config:', dataTableConfig);
            $('#resultsTable').DataTable(dataTableConfig);

        } catch (error) {
            console.error('Error initializing DataTable:', error);
            // Reset DataTable error mode
            $.fn.dataTable.ext.errMode = 'none';
            addBasicTableFunctionality();
        }
    }

    // Global error handler for DataTable issues
    $(document).ready(function() {
        // Set up global DataTable error handler only if DataTables is available
        if ($.fn.DataTable) {
            $.fn.dataTable.ext.errMode = 'none';

            $(document).on('error.dt', '#resultsTable', function(e, settings, techNote, message) {
                console.error('DataTable error:', message, techNote);
                console.error('Settings:', settings);

                // Prevent the default error modal
                e.stopPropagation();

                // Clean up and switch to fallback
                setTimeout(function() {
                    try {
                        if ($.fn.DataTable.isDataTable('#resultsTable')) {
                            $('#resultsTable').DataTable().destroy();
                        }
                    } catch (destroyError) {
                        console.warn('Error during cleanup:', destroyError);
                    }
                    addBasicTableFunctionality();
                }, 100);

                return false; // Prevent default error handling
            });
        }
    });

    // Fallback function for basic table functionality
    function addBasicTableFunctionality() {
        console.log('Using basic table functionality as fallback');

        const table = document.getElementById('resultsTable');
        if (!table) {
            console.warn('Table not found for basic functionality');
            return;
        }

        // Check if search input already exists
        const existingSearch = table.parentNode.querySelector('.fallback-search');
        if (existingSearch) {
            console.log('Basic search already exists');
            return;
        }

        // Add basic search functionality
        const searchContainer = document.createElement('div');
        searchContainer.className = 'mb-4 flex justify-end';

        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Cari dalam tabel...';
        searchInput.className = 'fallback-search px-3 py-2 border border-gray-300 rounded-md w-64';

        searchContainer.appendChild(searchInput);

        if (table.parentNode) {
            table.parentNode.insertBefore(searchContainer, table);

            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const tbody = table.querySelector('tbody');
                if (!tbody) return;

                const rows = tbody.querySelectorAll('tr:not(.no-data)');
                let visibleCount = 0;

                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    const isVisible = text.includes(searchTerm);
                    row.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                });

                // Show/hide no-data row based on search results
                const noDataRow = tbody.querySelector('tr.no-data');
                if (noDataRow) {
                    noDataRow.style.display = visibleCount === 0 && searchTerm ? '' : 'none';
                }
            });

            // Add a simple results counter
            const counterDiv = document.createElement('div');
            counterDiv.className = 'mt-2 text-sm text-gray-600 text-right';
            counterDiv.textContent = `Total: ${table.querySelectorAll('tbody tr:not(.no-data)').length} hasil`;
            searchContainer.appendChild(counterDiv);
        }
    }

    // Function to bind table events
    function bindTableEvents() {
        // Re-bind checkbox events
        const selectAllCheckbox = document.getElementById('selectAll');
        if (selectAllCheckbox) {
            selectAllCheckbox.removeEventListener('change', toggleSelectAll);
            selectAllCheckbox.addEventListener('change', toggleSelectAll);
        }
    }
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<!-- DataTables CSS is already loaded in main layout -->
<style>
    .dataTables_wrapper {
        padding: 1rem;
    }

    .dataTables_filter input {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.5rem;
    }

    .dataTables_length select {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem;
    }

    /* Ensure table cell consistency */
    #resultsTable th,
    #resultsTable td {
        vertical-align: middle;
        min-width: 0;
    }

    /* Fix checkbox column width */
    #resultsTable th:first-child,
    #resultsTable td:first-child {
        width: 40px;
        text-align: center;
    }

    /* Fix action column width */
    #resultsTable th:last-child,
    #resultsTable td:last-child {
        width: 120px;
        text-align: center;
    }

    /* Responsive table improvements */
    @media (max-width: 768px) {

        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_length {
            margin-bottom: 0.5rem;
        }
    }

    /* Loading state */
    .dataTables_processing {
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 1rem;
        text-align: center;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- Scripts are already loaded in main layout -->
<?= $this->endSection() ?>