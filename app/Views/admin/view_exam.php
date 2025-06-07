<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>View Exam - <?= esc($exam['title']) ?><?= $this->endSection() ?>

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
            <h1 class="text-3xl font-bold text-gray-900"><?= esc($exam['title']) ?></h1>
        </div>
        <p class="text-gray-600">Detail informasi ujian</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Exam Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Exam Information</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500">Subject</label>
                        <p class="text-gray-900"><?= esc($exam['subject_name']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Teacher</label>
                        <p class="text-gray-900"><?= esc($exam['teacher_name']) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Duration</label>
                        <p class="text-gray-900"><?= $exam['duration_minutes'] ?> minutes</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Question Count</label>
                        <p class="text-gray-900"><?= $exam['question_count'] ?> questions</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Start Time</label>
                        <p class="text-gray-900"><?= date('d M Y, H:i', strtotime($exam['start_time'])) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">End Time</label>
                        <p class="text-gray-900"><?= date('d M Y, H:i', strtotime($exam['end_time'])) ?></p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Status</label>
                        <div class="flex items-center">
                            <?php if ($exam['is_active']): ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Active
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    <svg class="w-2 h-2 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Inactive
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-500">Created</label>
                        <p class="text-gray-900"><?= date('d M Y, H:i', strtotime($exam['created_at'])) ?></p>
                    </div>
                </div>

                <?php if ($exam['description']): ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500">Description</label>
                        <p class="text-gray-900 mt-1"><?= esc($exam['description']) ?></p>
                    </div>
                <?php endif; ?>

                <?php if ($exam['pdf_url']): ?>
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-500">PDF Source</label>
                        <a href="<?= esc($exam['pdf_url']) ?>" target="_blank"
                            class="inline-flex items-center text-blue-600 hover:text-blue-800 mt-1">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            View PDF
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Actions</h2>

                <div class="flex flex-wrap gap-3">
                    <a href="<?= base_url('admin/exams/edit/' . $exam['id']) ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Exam
                    </a>

                    <?php if (!$exam['is_active']): ?>
                        <button onclick="publishExam(<?= $exam['id'] ?>)"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Publish Exam
                        </button>
                    <?php endif; ?>

                    <a href="<?= base_url('admin/exam-results/' . $exam['id']) ?>"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        View Results
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Stats -->
        <div class="space-y-6">
            <!-- Statistics -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Statistics</h2>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total Participants</span>
                        <span class="text-2xl font-bold text-gray-900"><?= $stats['total_participants'] ?></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Completed</span>
                        <span class="text-2xl font-bold text-green-600"><?= $stats['completed_participants'] ?></span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Graded</span>
                        <span class="text-2xl font-bold text-blue-600"><?= $stats['graded_participants'] ?></span>
                    </div>

                    <?php if ($stats['average_score'] !== null): ?>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Average Score</span>
                            <span class="text-2xl font-bold text-purple-600"><?= number_format($stats['average_score'], 1) ?>%</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Quick Info</h2>

                <div class="space-y-3">
                    <?php
                    $now = new DateTime();
                    $start = new DateTime($exam['start_time']);
                    $end = new DateTime($exam['end_time']);
                    ?>

                    <?php if ($now < $start): ?>
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <p class="text-sm font-medium text-yellow-800">Upcoming</p>
                            <p class="text-xs text-yellow-600">Starts in <?= $start->diff($now)->format('%d days, %h hours') ?></p>
                        </div>
                    <?php elseif ($now >= $start && $now <= $end): ?>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-sm font-medium text-green-800">Ongoing</p>
                            <p class="text-xs text-green-600">Ends in <?= $end->diff($now)->format('%d days, %h hours') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Ended</p>
                            <p class="text-xs text-gray-600">Ended <?= $now->diff($end)->format('%d days ago') ?></p>
                        </div>
                    <?php endif; ?>

                    <?php if ($exam['is_active']): ?>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm font-medium text-blue-800">Published</p>
                            <p class="text-xs text-blue-600">Students can access this exam</p>
                        </div>
                    <?php else: ?>
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <p class="text-sm font-medium text-gray-800">Draft</p>
                            <p class="text-xs text-gray-600">Not accessible to students</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function publishExam(examId) {
        if (confirm('Are you sure you want to publish this exam? Students will be able to take it once published.')) {
            window.location.href = '<?= base_url('admin/exams/publish') ?>/' + examId;
        }
    }
</script>
<?= $this->endSection() ?>