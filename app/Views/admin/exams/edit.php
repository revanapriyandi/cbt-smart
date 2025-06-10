<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit Exam<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Edit Exam</h1>
            <p class="mt-2 text-gray-600">Update exam details and manage questions</p>
        </div>
        <div class="flex space-x-3">
            <a href="<?= base_url('admin/exams/view/' . $exam['id']) ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View Exam
            </a>
            <a href="<?= base_url('admin/exams') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Exams
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 mb-6">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 p-6" aria-label="Tabs">
                <button class="tab-button active border-b-2 border-blue-500 py-2 px-1 text-sm font-medium text-blue-600" data-tab="basic-info">
                    Basic Information
                </button>
                <button class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="question-management">
                    Question Management
                </button>
                <button class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="settings">
                    Advanced Settings
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Contents -->
    <div id="tab-contents">
        <!-- Basic Information Tab -->
        <div id="basic-info-tab" class="tab-content">
            <form action="<?= base_url('admin/exams/update/' . $exam['id']) ?>" method="post" id="examUpdateForm">
                <?= csrf_field() ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-4">
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                                <input type="text" id="title" name="title" required value="<?= esc($exam['title']) ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>

                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="description" name="description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"><?= esc($exam['description']) ?></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                    <select id="subject_id" name="subject_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Subject</option>
                                        <?php if (isset($subjects)): ?>
                                            <?php foreach ($subjects as $subject): ?>
                                                <option value="<?= $subject['id'] ?>" <?= $exam['subject_id'] == $subject['id'] ? 'selected' : '' ?>>
                                                    <?= esc($subject['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="exam_type_id" class="block text-sm font-medium text-gray-700 mb-2">Exam Type</label>
                                    <select id="exam_type_id" name="exam_type_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Type</option>
                                        <?php if (isset($examTypes)): ?>
                                            <?php foreach ($examTypes as $type): ?>
                                                <option value="<?= $type['id'] ?>" <?= $exam['exam_type_id'] == $type['id'] ? 'selected' : '' ?>>
                                                    <?= esc($type['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="question_bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Linked Question Bank
                                    <span class="text-xs text-gray-500">(Optional)</span>
                                </label>
                                <select id="question_bank_id" name="question_bank_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="">No Question Bank</option>
                                    <?php if (isset($questionBanks)): ?>
                                        <?php foreach ($questionBanks as $bank): ?>
                                            <option value="<?= $bank['id'] ?>"
                                                <?= ($exam['question_bank_id'] ?? '') == $bank['id'] ? 'selected' : '' ?>
                                                data-question-count="<?= $bank['question_count'] ?>">
                                                <?= esc($bank['name']) ?> (<?= $bank['question_count'] ?> questions)
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Link this exam to a question bank for easier question management</p>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                    <input type="datetime-local" id="start_time" name="start_time" required
                                        value="<?= date('Y-m-d\TH:i', strtotime($exam['start_time'])) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input type="datetime-local" id="end_time" name="end_time" required
                                        value="<?= date('Y-m-d\TH:i', strtotime($exam['end_time'])) ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                                    <input type="number" id="duration_minutes" name="duration_minutes" required min="1"
                                        value="<?= $exam['duration_minutes'] ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                                    <input type="number" id="max_attempts" name="max_attempts" min="1"
                                        value="<?= $exam['max_attempts'] ?? 1 ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                                    <input type="number" id="passing_score" name="passing_score" min="0" max="100"
                                        value="<?= $exam['passing_score'] ?? 60 ?>"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select id="status" name="status"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="draft" <?= $exam['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                        <option value="scheduled" <?= $exam['status'] === 'scheduled' ? 'selected' : '' ?>>Scheduled</option>
                                        <option value="active" <?= $exam['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                        <option value="completed" <?= $exam['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="shuffle_questions" class="block text-sm font-medium text-gray-700 mb-2">Shuffle Questions</label>
                                    <select id="shuffle_questions" name="shuffle_questions"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="0" <?= ($exam['shuffle_questions'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                                        <option value="1" <?= ($exam['shuffle_questions'] ?? 0) == 1 ? 'selected' : '' ?>>Yes</option>
                                    </select>
                                </div>

                                <div>
                                    <label for="show_results" class="block text-sm font-medium text-gray-700 mb-2">Show Results</label>
                                    <select id="show_results" name="show_results"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="immediately" <?= ($exam['show_results'] ?? 'immediately') === 'immediately' ? 'selected' : '' ?>>Immediately</option>
                                        <option value="after_end" <?= ($exam['show_results'] ?? 'immediately') === 'after_end' ? 'selected' : '' ?>>After Exam Ends</option>
                                        <option value="manual" <?= ($exam['show_results'] ?? 'immediately') === 'manual' ? 'selected' : '' ?>>Manual Release</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Update Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Question Management Tab -->
        <div id="question-management-tab" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Question Management</h3>
                    <div class="flex space-x-3">
                        <button id="add-from-bank" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Add from Bank
                        </button>
                        <a href="<?= base_url('admin/questions/create?exam_id=' . $exam['id']) ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Question
                        </a>
                    </div>
                </div>

                <!-- Questions Table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="examQuestionsTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAllQuestions" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Question</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Difficulty</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Points</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="questionsTableBody">
                            <!-- Questions will be loaded via AJAX -->
                        </tbody>
                    </table>
                </div>

                <!-- Empty state -->
                <div id="no-questions" class="text-center py-12 hidden">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No questions yet</h3>
                    <p class="text-gray-500 mb-4">Start by adding questions to this exam.</p>
                </div>
            </div>
        </div>

        <!-- Advanced Settings Tab -->
        <div id="settings-tab" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Advanced Settings</h3>

                <div class="space-y-6">
                    <!-- Security Settings -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-3">Security Settings</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="prevent_copy_paste" <?= ($exam['prevent_copy_paste'] ?? 0) ? 'checked' : '' ?>
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Prevent Copy/Paste</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="fullscreen_mode" <?= ($exam['fullscreen_mode'] ?? 0) ? 'checked' : '' ?>
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Force Fullscreen Mode</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="disable_right_click" <?= ($exam['disable_right_click'] ?? 0) ? 'checked' : '' ?>
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Disable Right Click</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="randomize_options" <?= ($exam['randomize_options'] ?? 0) ? 'checked' : '' ?>
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Randomize Answer Options</span>
                            </label>
                        </div>
                    </div>

                    <!-- Time Settings -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-3">Time Settings</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="time_warning" class="block text-sm font-medium text-gray-700 mb-2">Time Warning (minutes before end)</label>
                                <input type="number" id="time_warning" name="time_warning" min="0"
                                    value="<?= $exam['time_warning'] ?? 5 ?>"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            </div>
                            <div>
                                <label for="auto_submit" class="block text-sm font-medium text-gray-700 mb-2">Auto Submit</label>
                                <select id="auto_submit" name="auto_submit"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="1" <?= ($exam['auto_submit'] ?? 1) == 1 ? 'selected' : '' ?>>Yes</option>
                                    <option value="0" <?= ($exam['auto_submit'] ?? 1) == 0 ? 'selected' : '' ?>>No</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Access Control -->
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-3">Access Control</h4>
                        <div class="space-y-4">
                            <div>
                                <label for="exam_password" class="block text-sm font-medium text-gray-700 mb-2">Exam Password (Optional)</label>
                                <input type="password" id="exam_password" name="exam_password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Leave empty for no password">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="allowed_ip_addresses" class="block text-sm font-medium text-gray-700 mb-2">Allowed IP Addresses</label>
                                    <textarea id="allowed_ip_addresses" name="allowed_ip_addresses" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="One IP per line (optional)"><?= $exam['allowed_ip_addresses'] ?? '' ?></textarea>
                                </div>
                                <div>
                                    <label for="browser_lockdown" class="block text-sm font-medium text-gray-700 mb-2">Browser Requirements</label>
                                    <select id="browser_lockdown" name="browser_lockdown"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="none" <?= ($exam['browser_lockdown'] ?? 'none') === 'none' ? 'selected' : '' ?>>No Restrictions</option>
                                        <option value="modern" <?= ($exam['browser_lockdown'] ?? 'none') === 'modern' ? 'selected' : '' ?>>Modern Browsers Only</option>
                                        <option value="lockdown" <?= ($exam['browser_lockdown'] ?? 'none') === 'lockdown' ? 'selected' : '' ?>>Lockdown Browser</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Questions from Bank Modal -->
<div id="addFromBankModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Add Questions from Bank</h3>
            <button id="closeAddFromBankModal" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="space-y-4">
            <div>
                <label for="modal_question_bank_id" class="block text-sm font-medium text-gray-700 mb-2">Select Question Bank</label>
                <select id="modal_question_bank_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                    <option value="">Select Question Bank</option>
                    <?php if (isset($questionBanks)): ?>
                        <?php foreach ($questionBanks as $bank): ?>
                            <option value="<?= $bank['id'] ?>"><?= esc($bank['name']) ?> (<?= $bank['question_count'] ?> questions)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div id="bank-questions-container" class="hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-sm font-medium text-gray-700">Available Questions</span>
                    <button id="selectAllBankQuestions" class="text-sm text-blue-600 hover:text-blue-800">Select All</button>
                </div>
                <div id="bank-questions-list" class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg">
                    <!-- Questions will be loaded here -->
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
            <button id="cancelAddFromBank" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-medium">
                Cancel
            </button>
            <button id="addSelectedQuestions" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                Add Selected Questions
            </button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab functionality
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const tabId = this.dataset.tab + '-tab';

                // Update button states
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-blue-500', 'text-blue-600');
                    btn.classList.add('border-transparent', 'text-gray-500');
                });
                this.classList.add('active', 'border-blue-500', 'text-blue-600');
                this.classList.remove('border-transparent', 'text-gray-500');

                // Show selected tab content
                tabContents.forEach(content => content.classList.add('hidden'));
                document.getElementById(tabId).classList.remove('hidden');

                // Load questions if question management tab is selected
                if (tabId === 'question-management-tab') {
                    loadExamQuestions();
                }
            });
        });

        // Load exam questions
        function loadExamQuestions() {
            const examId = <?= $exam['id'] ?>;
            fetch(`<?= base_url('admin/exams/get-questions') ?>/${examId}`)
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('questionsTableBody');
                    const noQuestions = document.getElementById('no-questions');

                    if (data.questions && data.questions.length > 0) {
                        tbody.innerHTML = '';
                        noQuestions.classList.add('hidden');

                        data.questions.forEach((question, index) => {
                            const row = createQuestionRow(question, index + 1);
                            tbody.appendChild(row);
                        });
                    } else {
                        tbody.innerHTML = '';
                        noQuestions.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading questions:', error);
                });
        }

        function createQuestionRow(question, order) {
            const row = document.createElement('tr');
            row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="checkbox" value="${question.id}" class="question-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500">
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-900">${question.question.substring(0, 100)}...</div>
                <div class="text-xs text-gray-500">${question.question_bank_name || 'No Bank'}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                    ${question.type}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-medium rounded-full ${getDifficultyColor(question.difficulty_level)}">
                    ${question.difficulty_level}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                ${question.points || 1}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <input type="number" value="${order}" min="1" class="w-16 px-2 py-1 text-sm border border-gray-300 rounded focus:ring-2 focus:ring-blue-500" 
                    onchange="updateQuestionOrder(${question.id}, this.value)">
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm">
                <div class="flex space-x-2">
                    <a href="<?= base_url('admin/questions/edit') ?>/${question.id}" class="text-blue-600 hover:text-blue-800">Edit</a>
                    <button onclick="removeQuestion(${question.id})" class="text-red-600 hover:text-red-800">Remove</button>
                </div>
            </td>
        `;
            return row;
        }

        function getDifficultyColor(difficulty) {
            const colors = {
                'easy': 'bg-green-100 text-green-800',
                'medium': 'bg-yellow-100 text-yellow-800',
                'hard': 'bg-red-100 text-red-800'
            };
            return colors[difficulty] || 'bg-gray-100 text-gray-800';
        }

        // Add from bank modal functionality
        const addFromBankBtn = document.getElementById('add-from-bank');
        const addFromBankModal = document.getElementById('addFromBankModal');
        const closeModalBtn = document.getElementById('closeAddFromBankModal');
        const cancelBtn = document.getElementById('cancelAddFromBank');
        const modalBankSelect = document.getElementById('modal_question_bank_id');

        addFromBankBtn.addEventListener('click', () => {
            addFromBankModal.classList.remove('hidden');
        });

        [closeModalBtn, cancelBtn].forEach(btn => {
            btn.addEventListener('click', () => {
                addFromBankModal.classList.add('hidden');
            });
        });

        modalBankSelect.addEventListener('change', function() {
            if (this.value) {
                loadBankQuestions(this.value);
            } else {
                document.getElementById('bank-questions-container').classList.add('hidden');
            }
        });

        function loadBankQuestions(bankId) {
            fetch(`<?= base_url('admin/question-banks/get-questions') ?>/${bankId}`)
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('bank-questions-container');
                    const list = document.getElementById('bank-questions-list');

                    if (data.questions && data.questions.length > 0) {
                        list.innerHTML = '';
                        data.questions.forEach(question => {
                            const item = document.createElement('div');
                            item.className = 'p-3 border-b border-gray-100 hover:bg-gray-50';
                            item.innerHTML = `
                            <label class="flex items-start space-x-3 cursor-pointer">
                                <input type="checkbox" value="${question.id}" class="bank-question-checkbox mt-1 rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <div class="flex-1">
                                    <div class="text-sm text-gray-900">${question.question}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        ${question.type} • ${question.difficulty_level} • ${question.points || 1} points
                                    </div>
                                </div>
                            </label>
                        `;
                            list.appendChild(item);
                        });
                        container.classList.remove('hidden');
                    } else {
                        container.classList.add('hidden');
                    }
                });
        }

        // Select all bank questions
        document.getElementById('selectAllBankQuestions').addEventListener('click', function() {
            const checkboxes = document.querySelectorAll('.bank-question-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            checkboxes.forEach(cb => cb.checked = !allChecked);
            this.textContent = allChecked ? 'Select All' : 'Deselect All';
        });

        // Add selected questions
        document.getElementById('addSelectedQuestions').addEventListener('click', function() {
            const selectedQuestions = Array.from(document.querySelectorAll('.bank-question-checkbox:checked')).map(cb => cb.value);

            if (selectedQuestions.length === 0) {
                alert('Please select at least one question.');
                return;
            }

            // Add questions to exam
            fetch(`<?= base_url('admin/exams/add-questions/' . $exam['id']) ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        question_ids: selectedQuestions
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        addFromBankModal.classList.add('hidden');
                        loadExamQuestions(); // Reload questions table
                        alert('Questions added successfully!');
                    } else {
                        alert('Error adding questions: ' + (data.message || 'Unknown error'));
                    }
                });
        });

        // Form validation
        document.getElementById('examUpdateForm').addEventListener('submit', function(e) {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;

            if (startTime && endTime && new Date(startTime) >= new Date(endTime)) {
                e.preventDefault();
                alert('End time must be after start time.');
                return false;
            }
        });
    });

    // Global functions
    function updateQuestionOrder(questionId, newOrder) {
        fetch(`<?= base_url('admin/exams/update-question-order') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    exam_id: <?= $exam['id'] ?>,
                    question_id: questionId,
                    order: newOrder
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Error updating question order');
                }
            });
    }

    function removeQuestion(questionId) {
        if (confirm('Are you sure you want to remove this question from the exam?')) {
            fetch(`<?= base_url('admin/exams/remove-question') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        exam_id: <?= $exam['id'] ?>,
                        question_id: questionId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reload questions table
                        const tabButton = document.querySelector('[data-tab="question-management"]');
                        if (tabButton.classList.contains('active')) {
                            loadExamQuestions();
                        }
                    } else {
                        alert('Error removing question');
                    }
                });
        }
    }
</script>
<?= $this->endSection() ?>