<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Kelola Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= base_url('admin/dashboard') ?>" class="hover:text-blue-600">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-900 font-medium">Kelola Ujian</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-8 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                <?= $title ?>
            </h1>
            <p class="text-gray-600 mt-2">Kelola semua ujian dengan integrasi bank soal dan PDF scraping</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <a href="<?= base_url('admin/exams/create') ?>"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Buat Ujian Baru
            </a>
            <button type="button" id="btn-import"
                class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors">
                <i class="fas fa-file-import mr-2"></i>
                Import
            </button>
            <button type="button" id="btn-export"
                class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg font-medium hover:bg-gray-700 transition-colors">
                <i class="fas fa-file-export mr-2"></i>
                Export
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Ujian</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2"><?= number_format($stats['total']) ?></p>
                </div>
                <div class="p-3 bg-blue-500 rounded-full">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Sedang Aktif</p>
                    <p class="text-3xl font-bold text-green-900 mt-2"><?= number_format($stats['active']) ?></p>
                </div>
                <div class="p-3 bg-green-500 rounded-full">
                    <i class="fas fa-play-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-sm p-6 border border-yellow-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wide">Terjadwal</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2"><?= number_format($stats['scheduled']) ?></p>
                </div>
                <div class="p-3 bg-yellow-500 rounded-full">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-sm p-6 border border-red-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600 uppercase tracking-wide">Selesai</p>
                    <p class="text-3xl font-bold text-red-900 mt-2"><?= number_format($stats['completed']) ?></p>
                </div>
                <div class="p-3 bg-red-500 rounded-full">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl shadow-sm p-6 border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600 uppercase tracking-wide">Draft</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2"><?= number_format($stats['draft']) ?></p>
                </div>
                <div class="p-3 bg-gray-500 rounded-full">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Data Ujian
            </h3>
        </div>
        <div class="p-6">
            <form id="filter-form">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label for="subject_filter" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            id="subject_filter" name="subject_filter">
                            <option value="">Semua Mata Pelajaran</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="exam_type_filter" class="block text-sm font-medium text-gray-700 mb-2">Jenis Ujian</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            id="exam_type_filter" name="exam_type_filter">
                            <option value="">Semua Jenis Ujian</option>
                            <?php foreach ($examTypes as $examType): ?>
                                <option value="<?= $examType['id'] ?>"><?= esc($examType['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            id="status_filter" name="status_filter">
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="scheduled">Terjadwal</option>
                            <option value="active">Aktif</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>
                    <div>
                        <label for="teacher_filter" class="block text-sm font-medium text-gray-700 mb-2">Guru</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                            id="teacher_filter" name="teacher_filter">
                            <option value="">Semua Guru</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= esc($teacher['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="md:col-span-2 flex items-end space-x-2">
                        <button type="button" id="btn-reset-filter"
                            class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium text-sm transition-colors">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                        <button type="button" id="btn-refresh"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-colors">
                            <i class="fas fa-sync mr-2"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-table mr-2"></i>
                Data Ujian
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="examsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-hashtag text-blue-500 mr-2"></i>
                                ID
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-clipboard-list text-green-500 mr-2"></i>
                                Judul Ujian
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-book text-cyan-500 mr-2"></i>
                                Mata Pelajaran
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-tags text-yellow-500 mr-2"></i>
                                Jenis
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-database text-purple-500 mr-2"></i>
                                Bank Soal
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <i class="fas fa-user text-indigo-500 mr-2"></i>
                                Guru
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-question-circle text-gray-500 mr-2"></i>
                                Soal
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-clock text-orange-500 mr-2"></i>
                                Durasi
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-calendar text-red-500 mr-2"></i>
                                Jadwal
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-toggle-on text-green-500 mr-2"></i>
                                Status
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-cogs text-gray-500 mr-2"></i>
                                Aksi
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Data will be loaded via DataTables -->
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    Pilih item untuk melakukan aksi bulk
                </div>
                <div class="bulk-actions hidden">
                    <div class="flex space-x-2">
                        <button type="button" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" id="bulk-delete">
                            <i class="fas fa-trash mr-1"></i>
                            Hapus Terpilih
                        </button>
                        <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" id="bulk-activate">
                            <i class="fas fa-check mr-1"></i>
                            Aktifkan Terpilih
                        </button>
                        <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" id="bulk-draft">
                            <i class="fas fa-edit mr-1"></i>
                            Jadikan Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center mb-4">
            <div class="p-2 bg-red-100 rounded-full mr-3">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <div>
                <h3 class="text-lg font-medium text-gray-900">Konfirmasi Hapus</h3>
                <p class="text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan</p>
            </div>
        </div>
        <p class="text-gray-700 mb-6">Apakah Anda yakin ingin menghapus ujian ini? Semua data terkait akan ikut terhapus.</p>
        <div class="flex justify-end space-x-3">
            <button type="button" id="cancelDelete" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Batal
            </button>
            <button type="button" id="confirmDelete" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Hapus
            </button>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
            <div class="text-gray-900 font-medium">Memproses...</div>
        </div>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Import Data Ujian</h3>
            <button type="button" id="closeImportModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="importForm" enctype="multipart/form-data">
            <div class="mb-4">
                <label for="importFile" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih File Excel (.xlsx, .csv)
                </label>
                <input type="file" id="importFile" name="import_file"
                    accept=".xlsx,.xls,.csv" required
                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="mb-4">
                <div class="flex items-center">
                    <input type="checkbox" id="replaceExisting" name="replace_existing"
                        class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="replaceExisting" class="ml-2 text-sm text-gray-700">
                        Ganti data yang sudah ada
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" id="cancelImport"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-upload mr-2"></i>
                    Import
                </button>
            </div>
        </form>

        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Format file harus sesuai dengan template yang disediakan.
                <a href="<?= base_url('admin/exams/download-template') ?>"
                    class="text-blue-600 hover:underline ml-1">
                    Download Template
                </a>
            </p>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div id="exportModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Export Data Ujian</h3>
            <button type="button" id="closeExportModal" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <form id="exportForm">
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Format Export</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="export_format" value="excel" checked
                                class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Excel (.xlsx)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="export_format" value="csv"
                                class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">CSV (.csv)</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="export_format" value="pdf"
                                class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">PDF (.pdf)</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filter Data</label>
                    <div class="space-y-2">
                        <select name="export_subject" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Semua Mata Pelajaran</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                            <?php endforeach; ?>
                        </select>

                        <select name="export_status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm">
                            <option value="">Semua Status</option>
                            <option value="draft">Draft</option>
                            <option value="scheduled">Terjadwal</option>
                            <option value="active">Aktif</option>
                            <option value="completed">Selesai</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="include_questions" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Sertakan detail soal</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6">
                <button type="button" id="cancelExport"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit"
                    class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                    <i class="fas fa-download mr-2"></i>
                    Export
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let examsTable;
    let deleteExamId = null;

    document.addEventListener('DOMContentLoaded', function() {
        initializeDataTable();
        initializeEventHandlers();
    });

    function initializeDataTable() {
        examsTable = $('#examsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('admin/exams/data') ?>',
                type: 'POST',
                data: function(d) {
                    d.subject_filter = $('#subject_filter').val();
                    d.exam_type_filter = $('#exam_type_filter').val();
                    d.status_filter = $('#status_filter').val();
                    d.teacher_filter = $('#teacher_filter').val();
                },
                error: function(xhr, error, thrown) {
                    console.error('DataTables error:', error);
                    showNotification('Terjadi kesalahan saat memuat data', 'error');
                }
            },
            columns: [{
                    data: 'id',
                    width: '5%'
                },
                {
                    data: 'title',
                    width: '20%'
                },
                {
                    data: 'subject_name',
                    width: '12%'
                },
                {
                    data: 'exam_type_name',
                    width: '10%'
                },
                {
                    data: 'question_bank_name',
                    width: '15%'
                },
                {
                    data: 'teacher_name',
                    width: '12%'
                },
                {
                    data: 'question_count',
                    width: '8%',
                    className: 'text-center'
                },
                {
                    data: 'duration_minutes',
                    width: '8%',
                    className: 'text-center',
                    render: function(data) {
                        return data + ' menit';
                    }
                },
                {
                    data: 'start_time',
                    width: '10%',
                    className: 'text-center text-sm'
                },
                {
                    data: 'status_badge',
                    width: '8%',
                    className: 'text-center'
                },
                {
                    data: 'actions',
                    width: '15%',
                    orderable: false,
                    searchable: false,
                    className: 'text-center'
                }
            ],
            order: [
                [0, 'desc']
            ],
            pageLength: 25,
            language: {
                url: '<?= base_url('assets/datatables/i18n/id.json') ?>'
            },
            responsive: true,
            dom: '<"flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4"<"mb-2 sm:mb-0"l><"flex items-center space-x-2"f>>rtip',
            drawCallback: function() {
                // Reinitialize tooltips or other UI components if needed
            }
        });
    }

    function initializeEventHandlers() {
        // Filter handlers
        $('#subject_filter, #exam_type_filter, #status_filter, #teacher_filter').on('change', function() {
            examsTable.ajax.reload();
        });

        // Reset filter
        $('#btn-reset-filter').on('click', function() {
            $('#filter-form')[0].reset();
            examsTable.ajax.reload();
        }); // Refresh button
        $('#btn-refresh').on('click', function() {
            examsTable.ajax.reload();
            showNotification('Data berhasil diperbarui', 'success');
        });

        // Import button
        $('#btn-import').on('click', function() {
            showImportModal();
        });

        // Export button
        $('#btn-export').on('click', function() {
            showExportModal();
        });

        // Delete modal handlers
        $('#cancelDelete').on('click', function() {
            $('#deleteModal').addClass('hidden');
            deleteExamId = null;
        });

        $('#confirmDelete').on('click', function() {
            if (deleteExamId) {
                performDelete();
            }
        });

        // Close modal on outside click
        $('#deleteModal').on('click', function(e) {
            if (e.target.id === 'deleteModal') {
                $('#deleteModal').addClass('hidden');
                deleteExamId = null;
            }
        });
    }

    function deleteExam(examId) {
        deleteExamId = examId;
        $('#deleteModal').removeClass('hidden');
    }

    function performDelete() {
        showLoading(true);

        fetch(`<?= base_url('admin/exams/delete') ?>/${deleteExamId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                showLoading(false);
                $('#deleteModal').addClass('hidden');

                if (data.success) {
                    showNotification('Ujian berhasil dihapus', 'success');
                    examsTable.ajax.reload();
                } else {
                    showNotification(data.message || 'Gagal menghapus ujian', 'error');
                }

                deleteExamId = null;
            })
            .catch(error => {
                showLoading(false);
                $('#deleteModal').addClass('hidden');
                console.error('Error:', error);
                showNotification('Terjadi kesalahan saat menghapus ujian', 'error');
                deleteExamId = null;
            });
    }

    function showLoading(show) {
        if (show) {
            $('#loadingModal').removeClass('hidden');
        } else {
            $('#loadingModal').addClass('hidden');
        }
    }

    function showNotification(message, type = 'info') {
        const bgColor = {
            'success': 'bg-green-500',
            'error': 'bg-red-500',
            'warning': 'bg-yellow-500',
            'info': 'bg-blue-500'
        } [type] || 'bg-blue-500';

        const notification = $(`
        <div class="notification ${bgColor} text-white px-6 py-4 rounded-lg shadow-lg mb-4 transform transition-transform duration-300 translate-x-full">
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `);

        $('#notification-container').append(notification);

        setTimeout(() => {
            notification.removeClass('translate-x-full');
        }, 100);
        setTimeout(() => {
            notification.addClass('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Import/Export Functions
    function showImportModal() {
        $('#importModal').removeClass('hidden');
        $('#importForm')[0].reset();
    }

    function showExportModal() {
        $('#exportModal').removeClass('hidden');
        $('#exportForm')[0].reset();
    }

    // Additional Event Handlers for Import/Export
    $(document).ready(function() {
        // Import modal handlers
        $('#closeImportModal, #cancelImport').on('click', function() {
            $('#importModal').addClass('hidden');
        });

        $('#importModal').on('click', function(e) {
            if (e.target.id === 'importModal') {
                $('#importModal').addClass('hidden');
            }
        });

        $('#importForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = document.getElementById('importFile');

            if (!fileInput.files.length) {
                showNotification('Silakan pilih file untuk diimport', 'warning');
                return;
            }

            showLoading(true);

            fetch('<?= base_url('admin/exams/import') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showLoading(false);
                    $('#importModal').addClass('hidden');

                    if (data.success) {
                        showNotification(`Import berhasil! ${data.imported_count || 0} data berhasil diimport`, 'success');
                        examsTable.ajax.reload();
                    } else {
                        showNotification(data.message || 'Gagal mengimport data', 'error');
                    }
                })
                .catch(error => {
                    showLoading(false);
                    $('#importModal').addClass('hidden');
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat mengimport data', 'error');
                });
        });

        // Export modal handlers
        $('#closeExportModal, #cancelExport').on('click', function() {
            $('#exportModal').addClass('hidden');
        });

        $('#exportModal').on('click', function(e) {
            if (e.target.id === 'exportModal') {
                $('#exportModal').addClass('hidden');
            }
        });

        $('#exportForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Add current filter values
            formData.append('subject_filter', $('#subject_filter').val());
            formData.append('exam_type_filter', $('#exam_type_filter').val());
            formData.append('status_filter', $('#status_filter').val());
            formData.append('teacher_filter', $('#teacher_filter').val());

            showLoading(true);

            fetch('<?= base_url('admin/exams/export') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => {
                    showLoading(false);

                    if (response.ok) {
                        // Get filename from response headers
                        const disposition = response.headers.get('Content-Disposition');
                        let filename = 'export_ujian.xlsx';
                        if (disposition && disposition.includes('filename=')) {
                            filename = disposition.split('filename=')[1].replace(/"/g, '');
                        }

                        return response.blob().then(blob => {
                            // Create download link
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            document.body.removeChild(a);

                            $('#exportModal').addClass('hidden');
                            showNotification('Export berhasil! File sedang didownload', 'success');
                        });
                    } else {
                        return response.json().then(data => {
                            $('#exportModal').addClass('hidden');
                            showNotification(data.message || 'Gagal mengexport data', 'error');
                        });
                    }
                })
                .catch(error => {
                    showLoading(false);
                    $('#exportModal').addClass('hidden');
                    console.error('Error:', error);
                    showNotification('Terjadi kesalahan saat mengexport data', 'error');
                });
        });
    });
</script>
<?= $this->endSection() ?>