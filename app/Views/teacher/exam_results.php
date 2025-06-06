<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Hasil Ujian: <?= esc($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="<?= base_url('teacher/exams') ?>"
                    class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Hasil Ujian</h1>
                    <p class="text-gray-600"><?= esc($exam['title']) ?></p>
                </div>
            </div>
            <div class="flex space-x-3">
                <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/grade') ?>"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-clipboard-check mr-2"></i>
                    Kelola Penilaian
                </a>
                <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/download') ?>"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                    <i class="fas fa-download mr-2"></i>
                    Download CSV
                </a>
            </div>
        </div>
    </div>

    <div class="p-6">
        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-users text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-blue-600">Total Peserta</p>
                        <p class="text-2xl font-bold text-blue-900"><?= count($results) ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-green-600">Selesai</p>
                        <p class="text-2xl font-bold text-green-900">
                            <?= count(array_filter($results, fn($r) => $r['status'] === 'completed')) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-yellow-600">Sedang Berlangsung</p>
                        <p class="text-2xl font-bold text-yellow-900">
                            <?= count(array_filter($results, fn($r) => $r['status'] === 'in_progress')) ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="p-3 bg-gray-100 rounded-full">
                        <i class="fas fa-chart-line text-gray-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rata-rata Nilai</p>
                        <p class="text-2xl font-bold text-gray-900">
                            <?php
                            $completedResults = array_filter($results, fn($r) => $r['status'] === 'completed' && $r['total_score'] !== null);
                            $average = 0;
                            if (count($completedResults) > 0) {
                                $average = array_sum(array_column($completedResults, 'percentage')) / count($completedResults);
                            }
                            echo number_format($average, 1);
                            ?>%
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Table -->
        <?php if (empty($results)): ?>
            <div class="text-center py-12">
                <i class="fas fa-chart-bar text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Ada Hasil</h3>
                <p class="text-gray-500">Belum ada siswa yang mengikuti ujian ini</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Siswa
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Skor
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Persentase
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Waktu
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($results as $result): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-gray-200 rounded-full flex items-center justify-center">
                                            <i class="fas fa-user text-gray-500"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($result['student_name']) ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= esc($result['username']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($result['total_score'] !== null): ?>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= number_format($result['total_score'], 1) ?> / <?= number_format($result['max_total_score'], 1) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($result['percentage'] !== null): ?>
                                        <div class="flex items-center">
                                            <div class="text-sm font-medium text-gray-900 mr-2">
                                                <?= number_format($result['percentage'], 1) ?>%
                                            </div>
                                            <div class="w-16 bg-gray-200 rounded-full h-2">
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                    style="width: <?= min(100, $result['percentage']) ?>%"></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    switch ($result['status']) {
                                        case 'completed':
                                            $statusClass = 'bg-green-100 text-green-800';
                                            $statusText = 'Selesai';
                                            break;
                                        case 'in_progress':
                                            $statusClass = 'bg-blue-100 text-blue-800';
                                            $statusText = 'Berlangsung';
                                            break;
                                        case 'not_started':
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = 'Belum Mulai';
                                            break;
                                        default:
                                            $statusClass = 'bg-gray-100 text-gray-800';
                                            $statusText = ucfirst($result['status']);
                                    }
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        <?php if ($result['started_at']): ?>
                                            <div><strong>Mulai:</strong> <?= date('H:i d/m/Y', strtotime($result['started_at'])) ?></div>
                                        <?php endif; ?>
                                        <?php if ($result['submitted_at']): ?>
                                            <div><strong>Selesai:</strong> <?= date('H:i d/m/Y', strtotime($result['submitted_at'])) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($result['status'] === 'completed'): ?>
                                        <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/grade?student=' . $result['student_id']) ?>"
                                            class="text-blue-600 hover:text-blue-900">
                                            <i class="fas fa-eye mr-1"></i>
                                            Lihat Jawaban
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>