<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Exams<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Exams</h1>
            <p class="mt-2 text-gray-600">Kelola semua ujian dalam sistem</p>
        </div>
        <a href="<?= base_url('admin/exams/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Exam
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
        <form method="GET" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search exams..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <select name="subject_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Subjects</option>
                    <?php if (isset($subjects)): ?>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>" <?= ($subjectFilter ?? '') == $subject['id'] ? 'selected' : '' ?>>
                                <?= esc($subject['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Status</option>
                    <option value="draft" <?= ($statusFilter ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                    <option value="scheduled" <?= ($statusFilter ?? '') === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                    <option value="active" <?= ($statusFilter ?? '') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="completed" <?= ($statusFilter ?? '') === 'completed' ? 'selected' : '' ?>>Completed</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                Filter
            </button>
        </form>
    </div>

    <!-- Exams Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php if (isset($exams) && !empty($exams)): ?>
            <?php foreach ($exams as $exam): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1"><?= esc($exam['title']) ?></h3>
                            <p class="text-sm text-gray-600"><?= esc($exam['subject_name'] ?? 'No Subject') ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <?php
                            $statusColors = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'scheduled' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'completed' => 'bg-blue-100 text-blue-800'
                            ];
                            $status = $exam['status'] ?? 'draft';
                            ?>
                            <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                <?= ucfirst($status) ?>
                            </span>
                        </div>
                    </div>

                    <!-- Exam Details -->
                    <div class="space-y-3 mb-4">
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
                        <?php if (isset($exam['participant_count'])): ?>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <?= $exam['participant_count'] ?> participants
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="flex space-x-2">
                            <a href="<?= base_url('admin/exams/view/' . $exam['id']) ?>"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View
                            </a>
                            <a href="<?= base_url('admin/exams/edit/' . $exam['id']) ?>"
                                class="text-green-600 hover:text-green-800 text-sm font-medium">
                                Edit
                            </a>
                            <?php if ($status === 'draft'): ?>
                                <button onclick="deleteExam(<?= $exam['id'] ?>)"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                    Delete
                                </button>
                            <?php endif; ?>
                        </div>

                        <?php if ($status === 'completed'): ?>
                            <a href="<?= base_url('admin/exams/results/' . $exam['id']) ?>"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium">
                                Results
                            </a>
                        <?php elseif ($status === 'draft'): ?>
                            <button onclick="publishExam(<?= $exam['id'] ?>)"
                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium">
                                Publish
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No exams found</h3>
                <p class="text-gray-500 mb-4">Get started by creating your first exam.</p>
                <a href="<?= base_url('admin/exams/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    Create Exam
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager)): ?>
        <div class="mt-6">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<script>
    function deleteExam(examId) {
        if (confirm('Are you sure you want to delete this exam? This action cannot be undone.')) {
            window.location.href = '<?= base_url('admin/exams/delete') ?>/' + examId;
        }
    }

    function publishExam(examId) {
        if (confirm('Are you sure you want to publish this exam? Students will be able to take it once published.')) {
            window.location.href = '<?= base_url('admin/exams/publish') ?>/' + examId;
        }
    }
</script>
<?= $this->endSection() ?>