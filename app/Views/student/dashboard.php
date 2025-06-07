<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Student Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Student Dashboard</h1>
        <p class="mt-2 text-gray-600">Selamat datang, <?= esc(session('user_name')) ?>!</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Available Exams</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $availableExams ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Completed</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $completedExams ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Average Score</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= number_format($averageScore ?? 0, 1) ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Pending</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $pendingExams ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Exams -->
    <?php if (isset($upcomingExams) && !empty($upcomingExams)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6 sm:mb-8">
            <div class="p-4 sm:p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Available Exams</h2>
                <p class="text-sm text-gray-600">Ujian yang dapat Anda ikuti</p>
            </div>
            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6"> <?php foreach ($upcomingExams as $exam): ?>
                        <div class="border border-gray-200 rounded-lg p-4 sm:p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1 min-w-0 mr-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-1 truncate"><?= esc($exam['title']) ?></h3>
                                    <p class="text-sm text-gray-600"><?= esc($exam['subject_name']) ?></p>
                                </div>
                                <?php if ($exam['status'] === 'active'): ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800 flex-shrink-0">
                                        Active
                                    </span>
                                <?php else: ?>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800 flex-shrink-0">
                                        Scheduled
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Start: <?= date('M d, Y H:i', strtotime($exam['start_time'])) ?>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Duration: <?= $exam['duration_minutes'] ?> minutes
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <?= $exam['question_count'] ?? 0 ?> questions
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                <?php if ($exam['status'] === 'active' && strtotime($exam['start_time']) <= time() && strtotime($exam['end_time']) >= time()): ?>
                                    <span class="text-sm text-green-600 font-medium">Exam is now available</span>
                                    <a href="<?= base_url('student/exam/take/' . $exam['id']) ?>"
                                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                                        Take Exam
                                    </a>
                                <?php elseif (strtotime($exam['start_time']) > time()): ?>
                                    <span class="text-sm text-yellow-600 font-medium">
                                        Starts in <?= time_difference(time(), strtotime($exam['start_time'])) ?>
                                    </span>
                                    <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-medium cursor-not-allowed">
                                        Not Started
                                    </button>
                                <?php else: ?>
                                    <span class="text-sm text-red-600 font-medium">Exam has ended</span>
                                    <button disabled class="bg-gray-300 text-gray-500 px-4 py-2 rounded-lg font-medium cursor-not-allowed">
                                        Ended
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Recent Results -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Results -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Recent Results</h2>
            </div>
            <div class="p-6">
                <?php if (isset($recentResults) && !empty($recentResults)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentResults as $result): ?>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($result['exam_title']) ?></p>
                                    <p class="text-xs text-gray-500"><?= date('M d, Y', strtotime($result['completed_at'])) ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold <?= $result['score'] >= 75 ? 'text-green-600' : ($result['score'] >= 60 ? 'text-yellow-600' : 'text-red-600') ?>">
                                        <?= number_format($result['score'], 1) ?>
                                    </p>
                                    <p class="text-xs text-gray-500">Score</p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">No results yet</p>
                    </div>
                <?php endif; ?>
                <div class="mt-4">
                    <a href="<?= base_url('student/results') ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View all results →</a>
                </div>
            </div>
        </div>

        <!-- Progress Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Performance Overview</h2>
            </div>
            <div class="p-6">
                <?php if (isset($subjectPerformance) && !empty($subjectPerformance)): ?>
                    <div class="space-y-4">
                        <?php foreach ($subjectPerformance as $subject): ?>
                            <div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-700"><?= esc($subject['subject_name']) ?></span>
                                    <span class="text-sm text-gray-600"><?= number_format($subject['average_score'], 1) ?>%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full" style="width: <?= min(100, $subject['average_score']) ?>%"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        <p class="text-gray-500">No performance data yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php if (!function_exists('time_difference')): ?>
    <?php
    function time_difference($from, $to)
    {
        $diff = $to - $from;
        if ($diff < 60) return $diff . ' seconds';
        if ($diff < 3600) return floor($diff / 60) . ' minutes';
        if ($diff < 86400) return floor($diff / 3600) . ' hours';
        return floor($diff / 86400) . ' days';
    }
    ?>
<?php endif; ?>
<?= $this->endSection() ?>