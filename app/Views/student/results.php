<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Exam Results<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Exam Results</h1>
        <p class="mt-2 text-gray-600">Lihat hasil ujian yang telah Anda selesaikan</p>
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
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium">
                Filter
            </button>
        </form>
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php if (isset($results) && !empty($results)): ?>
            <?php foreach ($results as $result): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <!-- Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-1"><?= esc($result['exam_title']) ?></h3>
                            <p class="text-sm text-gray-600"><?= esc($result['subject_name']) ?></p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold <?= $result['score'] >= 75 ? 'text-green-600' : ($result['score'] >= 60 ? 'text-yellow-600' : 'text-red-600') ?>">
                                <?= number_format($result['score'], 1) ?>
                            </div>
                            <div class="text-xs text-gray-500">Score</div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Completed:</span>
                            <span class="font-medium"><?= date('M d, Y H:i', strtotime($result['completed_at'])) ?></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Duration:</span>
                            <span class="font-medium"><?= $result['time_taken'] ?? 'N/A' ?></span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Grade:</span>
                            <span class="font-medium">
                                <?php
                                $grade = 'F';
                                if ($result['score'] >= 85) $grade = 'A';
                                elseif ($result['score'] >= 75) $grade = 'B';
                                elseif ($result['score'] >= 65) $grade = 'C';
                                elseif ($result['score'] >= 55) $grade = 'D';
                                ?>
                                <span class="px-2 py-1 text-xs rounded-full <?= $grade === 'A' ? 'bg-green-100 text-green-800' : ($grade === 'B' ? 'bg-blue-100 text-blue-800' : ($grade === 'C' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) ?>">
                                    <?= $grade ?>
                                </span>
                            </span>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mb-4">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Performance</span>
                            <span><?= number_format($result['score'], 1) ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300 <?= $result['score'] >= 75 ? 'bg-green-500' : ($result['score'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') ?>"
                                style="width: <?= min(100, $result['score']) ?>%"></div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <div class="text-xs text-gray-500">
                            <?= isset($result['questions_answered']) ? $result['questions_answered'] : 'N/A' ?> questions answered
                        </div>
                        <div class="flex space-x-2">
                            <a href="<?= base_url('student/results/view/' . $result['id']) ?>"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Details
                            </a>
                            <?php if (isset($result['feedback']) && !empty($result['feedback'])): ?>
                                <span class="text-gray-300">|</span>
                                <button onclick="showFeedback(<?= $result['id'] ?>)"
                                    class="text-green-600 hover:text-green-800 text-sm font-medium">
                                    Feedback
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No results found</h3>
                <p class="text-gray-500">You haven't completed any exams yet.</p>
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

<!-- Feedback Modal -->
<div id="feedbackModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 max-h-96 overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Exam Feedback</h3>
        </div>
        <div class="p-6">
            <div id="feedbackContent" class="prose max-w-none">
                <!-- Feedback content will be loaded here -->
            </div>
        </div>
        <div class="p-6 border-t border-gray-200 flex justify-end">
            <button onclick="closeFeedbackModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Close</button>
        </div>
    </div>
</div>

<script>
    function showFeedback(resultId) {
        // Show modal
        document.getElementById('feedbackModal').classList.remove('hidden');

        // Load feedback content
        fetch(`<?= base_url('student/results/feedback') ?>/${resultId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('feedbackContent').innerHTML = data.feedback || 'No feedback available.';
            })
            .catch(error => {
                document.getElementById('feedbackContent').innerHTML = 'Error loading feedback.';
            });
    }

    function closeFeedbackModal() {
        document.getElementById('feedbackModal').classList.add('hidden');
    }

    // Close modal when clicking outside
    document.getElementById('feedbackModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeFeedbackModal();
        }
    });
</script>
<?= $this->endSection() ?>