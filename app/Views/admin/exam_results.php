<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Exam Results - <?= esc($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center mb-4">
            <a href="<?= base_url('admin/exams') ?>" class="text-gray-600 hover:text-gray-800 mr-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Exam Results</h1>
                <p class="text-gray-600"><?= esc($exam['title']) ?></p>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <div class="text-sm text-gray-500">
                <?= count($results) ?> participant(s) • <?= date('d M Y', strtotime($exam['start_time'])) ?>
            </div>
            <a href="<?= base_url('admin/download-results/' . $exam['id']) ?>"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Download CSV
            </a>
        </div>
    </div>

    <!-- Exam Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Participants</p>
                    <p class="text-2xl font-bold text-gray-900"><?= count($results) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= count(array_filter($results, function ($r) {
                            return $r['status'] === 'submitted' || $r['status'] === 'graded';
                        })) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-purple-100 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Graded</p>
                    <p class="text-2xl font-bold text-gray-900">
                        <?= count(array_filter($results, function ($r) {
                            return $r['status'] === 'graded';
                        })) ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Score</p>
                    <?php
                    $gradedResults = array_filter($results, function ($r) {
                        return $r['status'] === 'graded' && $r['percentage'] !== null;
                    });
                    $avgScore = !empty($gradedResults) ? array_sum(array_column($gradedResults, 'percentage')) / count($gradedResults) : 0;
                    ?>
                    <p class="text-2xl font-bold text-gray-900"><?= number_format($avgScore, 1) ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Participant Results</h2>
        </div>

        <?php if (!empty($results)): ?>
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Started</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($results as $result): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                                <span class="text-blue-600 font-medium text-sm">
                                                    <?= strtoupper(substr($result['student_name'] ?? '', 0, 2)) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?= esc($result['student_name'] ?? 'Unknown') ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?= esc($result['username'] ?? '') ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($result['status'] === 'graded' && $result['total_score'] !== null): ?>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= $result['total_score'] ?> / <?= $result['max_total_score'] ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= number_format($result['percentage'], 1) ?>%
                                        </div>
                                    <?php else: ?>
                                        <span class="text-sm text-gray-400">Not graded</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $statusColors = [
                                        'ongoing' => 'bg-yellow-100 text-yellow-800',
                                        'submitted' => 'bg-blue-100 text-blue-800',
                                        'graded' => 'bg-green-100 text-green-800'
                                    ];
                                    $statusColor = $statusColors[$result['status']] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $statusColor ?>">
                                        <?= ucfirst($result['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $result['started_at'] ? date('d M Y, H:i', strtotime($result['started_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= $result['submitted_at'] ? date('d M Y, H:i', strtotime($result['submitted_at'])) : '-' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if ($result['status'] === 'submitted' || $result['status'] === 'graded'): ?>
                                        <a href="<?= base_url('admin/exam-answers/' . $exam['id'] . '/' . $result['student_id']) ?>"
                                            class="text-blue-600 hover:text-blue-900">
                                            View Answers
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

            <!-- Mobile Cards -->
            <div class="md:hidden">
                <div class="space-y-4 p-4">
                    <?php foreach ($results as $result): ?>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <span class="text-blue-600 font-medium text-xs">
                                            <?= strtoupper(substr($result['student_name'] ?? '', 0, 2)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= esc($result['student_name'] ?? 'Unknown') ?>
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?= esc($result['username'] ?? '') ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $statusColors = [
                                    'ongoing' => 'bg-yellow-100 text-yellow-800',
                                    'submitted' => 'bg-blue-100 text-blue-800',
                                    'graded' => 'bg-green-100 text-green-800'
                                ];
                                $statusColor = $statusColors[$result['status']] ?? 'bg-gray-100 text-gray-800';
                                ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $statusColor ?>">
                                    <?= ucfirst($result['status']) ?>
                                </span>
                            </div>

                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-500">Score:</span>
                                    <?php if ($result['status'] === 'graded' && $result['total_score'] !== null): ?>
                                        <span class="font-medium"><?= $result['total_score'] ?>/<?= $result['max_total_score'] ?> (<?= number_format($result['percentage'], 1) ?>%)</span>
                                    <?php else: ?>
                                        <span class="text-gray-400">Not graded</span>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="text-gray-500">Started:</span>
                                    <span class="font-medium"><?= $result['started_at'] ? date('d M, H:i', strtotime($result['started_at'])) : '-' ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Submitted:</span>
                                    <span class="font-medium"><?= $result['submitted_at'] ? date('d M, H:i', strtotime($result['submitted_at'])) : '-' ?></span>
                                </div>
                                <div>
                                    <?php if ($result['status'] === 'submitted' || $result['status'] === 'graded'): ?>
                                        <a href="<?= base_url('admin/exam-answers/' . $exam['id'] . '/' . $result['student_id']) ?>"
                                            class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View Answers →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Results Yet</h3>
                <p class="text-gray-500">No students have taken this exam yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>