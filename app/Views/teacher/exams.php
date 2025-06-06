<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Daftar Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-4 sm:p-6 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-3 sm:space-y-0">
            <h1 class="text-xl sm:text-2xl font-bold text-gray-900">Daftar Ujian</h1>
            <a href="<?= base_url('teacher/exams/create') ?>"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 flex items-center justify-center text-sm sm:text-base">
                <i class="fas fa-plus mr-2"></i>
                Buat Ujian Baru
            </a>
        </div>
    </div>

    <div class="p-4 sm:p-6">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 sm:mb-6 text-sm">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 sm:mb-6 text-sm">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($exams)): ?>
            <div class="text-center py-8 sm:py-12">
                <i class="fas fa-clipboard-list text-4xl sm:text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-lg sm:text-xl font-medium text-gray-900 mb-2">Belum Ada Ujian</h3>
                <p class="text-sm sm:text-base text-gray-500 mb-4 sm:mb-6">Mulai buat ujian pertama Anda</p>
                <a href="<?= base_url('teacher/exams/create') ?>"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 sm:px-6 py-2 sm:py-3 rounded-lg transition duration-200 text-sm sm:text-base">
                    Buat Ujian Baru
                </a>
            </div>
        <?php else: ?>
            <!-- Desktop Table View -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ujian
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mata Pelajaran
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($exams as $exam): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($exam['title']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= esc($exam['description']) ?>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            <?= $exam['question_count'] ?> soal • <?= $exam['duration_minutes'] ?> menit
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <?= esc($exam['subject_name']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="text-sm">
                                        <div><strong>Mulai:</strong> <?= date('d/m/Y H:i', strtotime($exam['start_time'])) ?></div>
                                        <div><strong>Selesai:</strong> <?= date('d/m/Y H:i', strtotime($exam['end_time'])) ?></div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $now = new DateTime();
                                    $start = new DateTime($exam['start_time']);
                                    $end = new DateTime($exam['end_time']);

                                    if (!$exam['is_active']):
                                        $status = 'Nonaktif';
                                        $class = 'bg-gray-100 text-gray-800';
                                    elseif ($now < $start):
                                        $status = 'Terjadwal';
                                        $class = 'bg-yellow-100 text-yellow-800';
                                    elseif ($now >= $start && $now <= $end):
                                        $status = 'Berlangsung';
                                        $class = 'bg-green-100 text-green-800';
                                    else:
                                        $status = 'Selesai';
                                        $class = 'bg-red-100 text-red-800';
                                    endif;
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $class ?>">
                                        <?= $status ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="<?= base_url('teacher/exams/edit/' . $exam['id']) ?>"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/results') ?>"
                                            class="text-green-600 hover:text-green-900">
                                            <i class="fas fa-chart-bar"></i>
                                        </a>
                                        <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/grade') ?>"
                                            class="text-purple-600 hover:text-purple-900">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                        <button onclick="deleteExam(<?= $exam['id'] ?>)"
                                            class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="lg:hidden space-y-4">
                <?php foreach ($exams as $exam): ?>
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <div class="flex flex-col space-y-3">
                            <!-- Exam Title and Subject -->
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between space-y-2 sm:space-y-0">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900"><?= esc($exam['title']) ?></h3>
                                    <p class="text-sm text-gray-500 mt-1"><?= esc($exam['description']) ?></p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 self-start">
                                    <?= esc($exam['subject_name']) ?>
                                </span>
                            </div>

                            <!-- Exam Details -->
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">Soal:</span>
                                    <span class="font-medium"><?= $exam['question_count'] ?> soal</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Durasi:</span>
                                    <span class="font-medium"><?= $exam['duration_minutes'] ?> menit</span>
                                </div>
                            </div>

                            <!-- Time and Status -->
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-500">Mulai:</span>
                                    <span class="font-medium"><?= date('d/m/Y H:i', strtotime($exam['start_time'])) ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Selesai:</span>
                                    <span class="font-medium"><?= date('d/m/Y H:i', strtotime($exam['end_time'])) ?></span>
                                </div>
                            </div>

                            <!-- Status and Actions -->
                            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                                <div>
                                    <?php
                                    $now = new DateTime();
                                    $start = new DateTime($exam['start_time']);
                                    $end = new DateTime($exam['end_time']);

                                    if (!$exam['is_active']):
                                        $status = 'Nonaktif';
                                        $class = 'bg-gray-100 text-gray-800';
                                    elseif ($now < $start):
                                        $status = 'Terjadwal';
                                        $class = 'bg-yellow-100 text-yellow-800';
                                    elseif ($now >= $start && $now <= $end):
                                        $status = 'Berlangsung';
                                        $class = 'bg-green-100 text-green-800';
                                    else:
                                        $status = 'Selesai';
                                        $class = 'bg-red-100 text-red-800';
                                    endif;
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $class ?>">
                                        <?= $status ?>
                                    </span>
                                </div>
                                <div class="flex space-x-4">
                                    <a href="<?= base_url('teacher/exams/edit/' . $exam['id']) ?>"
                                        class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/results') ?>"
                                        class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-chart-bar"></i>
                                    </a>
                                    <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/grade') ?>"
                                        class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-clipboard-check"></i>
                                    </a>
                                    <button onclick="deleteExam(<?= $exam['id'] ?>)"
                                        class="text-red-600 hover:text-red-900">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteExam(examId) {
        if (confirm('Apakah Anda yakin ingin menghapus ujian ini?')) {
            window.location.href = '<?= base_url('teacher/exams/delete/') ?>' + examId;
        }
    }
</script>
<?= $this->endSection() ?>