<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 flex items-center">
                <i class="fas fa-database text-blue-600 mr-3"></i>
                <?= $title ?>
            </h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Kelola bank soal dengan fitur lengkap</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm transition-colors" id="btn-create">
                <i class="fas fa-plus mr-2"></i>
                Tambah Bank Soal
            </button>
            <button type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm transition-colors" id="btn-import">
                <i class="fas fa-file-import mr-2"></i>
                Import
            </button>
            <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm transition-colors" id="btn-export">
                <i class="fas fa-file-export mr-2"></i>
                Export
            </button>
        </div>
    </div> <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Bank Soal</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2"><?= number_format($stats['total']) ?></p>
                </div>
                <div class="p-3 bg-blue-500 rounded-full">
                    <i class="fas fa-database text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Bank Soal Aktif</p>
                    <p class="text-3xl font-bold text-green-900 mt-2"><?= number_format($stats['active']) ?></p>
                </div>
                <div class="p-3 bg-green-500 rounded-full">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl shadow-sm p-6 border border-yellow-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600 uppercase tracking-wide">Draft</p>
                    <p class="text-3xl font-bold text-yellow-900 mt-2"><?= number_format($stats['draft']) ?></p>
                </div>
                <div class="p-3 bg-yellow-500 rounded-full">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-6 border border-purple-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Total Soal</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2"><?= number_format($stats['total_questions']) ?></p>
                </div>
                <div class="p-3 bg-purple-500 rounded-full">
                    <i class="fas fa-question-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div> <!-- Filters Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-filter mr-2"></i>
                Filter Data Bank Soal
            </h3>
        </div>
        <div class="p-6">
            <form id="filter-form">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    <div>
                        <label for="subject_filter" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="subject_filter" name="subject_filter">
                            <option value="">Semua Mata Pelajaran</option>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="exam_type_filter" class="block text-sm font-medium text-gray-700 mb-2">Jenis Ujian</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="exam_type_filter" name="exam_type_filter">
                            <option value="">Semua Jenis Ujian</option>
                            <?php foreach ($examTypes as $examType): ?>
                                <option value="<?= $examType['id'] ?>"><?= esc($examType['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="difficulty_filter" class="block text-sm font-medium text-gray-700 mb-2">Tingkat Kesulitan</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="difficulty_filter" name="difficulty_filter">
                            <option value="">Semua Tingkat</option>
                            <option value="easy">Mudah</option>
                            <option value="medium">Sedang</option>
                            <option value="hard">Sulit</option>
                        </select>
                    </div>
                    <div>
                        <label for="status_filter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="status_filter" name="status_filter">
                            <option value="">Semua Status</option>
                            <option value="active">Aktif</option>
                            <option value="draft">Draft</option>
                            <option value="archived">Arsip</option>
                        </select>
                    </div>
                    <div>
                        <label for="created_by_filter" class="block text-sm font-medium text-gray-700 mb-2">Dibuat Oleh</label>
                        <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm" id="created_by_filter" name="created_by_filter">
                            <option value="">Semua Pengguna</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= esc($teacher['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="button" class="w-full bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors" id="btn-reset-filter">
                            <i class="fas fa-undo mr-2"></i>
                            Reset
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div> <!-- Main Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h3 class="text-lg font-semibold text-white flex items-center">
                <i class="fas fa-table mr-2"></i>
                Data Bank Soal
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="questionBanksTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                            <div class="flex items-center">
                                <div class="mr-2 p-1 rounded bg-blue-500">
                                    <i class="fas fa-hashtag text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">ID</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <div class="mr-2 p-1 rounded bg-green-500">
                                    <i class="fas fa-database text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Nama Bank Soal</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <div class="mr-2 p-1 rounded bg-cyan-500">
                                    <i class="fas fa-book text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Mata Pelajaran</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <div class="mr-2 p-1 rounded bg-yellow-500">
                                    <i class="fas fa-clipboard-list text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Jenis Ujian</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center">
                                <div class="mr-2 p-1 rounded bg-red-500">
                                    <i class="fas fa-signal text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Tingkat</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <div class="mr-2 p-1 rounded bg-gray-500">
                                    <i class="fas fa-question text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Soal</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <div class="mr-2 p-1 rounded bg-indigo-500">
                                    <i class="fas fa-chart-line text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Digunakan</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <div class="mr-2 p-1 rounded bg-purple-500">
                                    <i class="fas fa-toggle-on text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Status</span>
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <div class="flex items-center justify-center">
                                <div class="mr-2 p-1 rounded bg-orange-500">
                                    <i class="fas fa-cogs text-white text-xs"></i>
                                </div>
                                <span class="font-semibold">Aksi</span>
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
                        <button type="button" class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" id="bulk-archive">
                            <i class="fas fa-archive mr-1"></i>
                            Arsipkan Terpilih
                        </button>
                        <button type="button" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors" id="bulk-activate">
                            <i class="fas fa-check mr-1"></i>
                            Aktifkan Terpilih
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->include('admin/question-banks/components/modals') ?>

<!-- Notification Container -->
<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?= $this->include('admin/question-banks/components/scripts') ?>
<?= $this->endSection() ?>