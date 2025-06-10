<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Create Exam<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Create New Exam</h1>
            <p class="mt-2 text-gray-600">Create exam using manual input, question bank, or PDF scraping</p>
        </div>
        <a href="<?= base_url('admin/exams') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Exams
        </a>
    </div>

    <!-- Creation Method Selection -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">Choose Creation Method</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Manual Creation -->
            <div class="creation-method-card p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-blue-500 transition-colors" data-method="manual">
                <div class="text-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">Manual Creation</h3>
                    <p class="text-sm text-gray-600">Create exam and add questions manually</p>
                </div>
            </div>

            <!-- From Question Bank -->
            <div class="creation-method-card p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-500 transition-colors" data-method="question-bank">
                <div class="text-center">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">From Question Bank</h3>
                    <p class="text-sm text-gray-600">Create exam from existing question bank</p>
                </div>
            </div>

            <!-- PDF Scraping -->
            <div class="creation-method-card p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-purple-500 transition-colors" data-method="pdf-scraping">
                <div class="text-center">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-2">PDF Scraping</h3>
                    <p class="text-sm text-gray-600">Extract questions from PDF file</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Forms Container -->
    <div id="forms-container">
        <!-- Manual Creation Form -->
        <div id="manual-form" class="creation-form hidden">
            <form action="<?= base_url('admin/exams/create-manual') ?>" method="post" id="manualExamForm">
                <?= csrf_field() ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Manual Exam Creation</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Info -->
                        <div class="space-y-4">
                            <div>
                                <label for="manual_title" class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                                <input type="text" id="manual_title" name="title" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Enter exam title">
                            </div>

                            <div>
                                <label for="manual_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea id="manual_description" name="description" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="Enter exam description"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="manual_subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                    <select id="manual_subject_id" name="subject_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Subject</option>
                                        <?php if (isset($subjects)): ?>
                                            <?php foreach ($subjects as $subject): ?>
                                                <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div>
                                    <label for="manual_exam_type_id" class="block text-sm font-medium text-gray-700 mb-2">Exam Type</label>
                                    <select id="manual_exam_type_id" name="exam_type_id" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="">Select Type</option>
                                        <?php if (isset($examTypes)): ?>
                                            <?php foreach ($examTypes as $type): ?>
                                                <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Schedule & Settings -->
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="manual_start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                    <input type="datetime-local" id="manual_start_time" name="start_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="manual_end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                    <input type="datetime-local" id="manual_end_time" name="end_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="manual_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                                    <input type="number" id="manual_duration_minutes" name="duration_minutes" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        placeholder="60">
                                </div>

                                <div>
                                    <label for="manual_max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                                    <input type="number" id="manual_max_attempts" name="max_attempts" min="1" value="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="manual_passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                                    <input type="number" id="manual_passing_score" name="passing_score" min="0" max="100" value="60"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                </div>

                                <div>
                                    <label for="manual_shuffle_questions" class="block text-sm font-medium text-gray-700 mb-2">Shuffle Questions</label>
                                    <select id="manual_shuffle_questions" name="shuffle_questions"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <option value="0">No</option>
                                        <option value="1" selected>Yes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Exam
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Question Bank Form -->
        <div id="question-bank-form" class="creation-form hidden">
            <form action="<?= base_url('admin/exams/create-from-question-bank') ?>" method="post" id="questionBankExamForm">
                <?= csrf_field() ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create Exam from Question Bank</h3>

                    <div class="space-y-6">
                        <!-- Question Bank Selection -->
                        <div>
                            <label for="question_bank_id" class="block text-sm font-medium text-gray-700 mb-2">Select Question Bank</label>
                            <select id="question_bank_id" name="question_bank_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select Question Bank</option>
                                <?php if (isset($questionBanks)): ?>
                                    <?php foreach ($questionBanks as $bank): ?>
                                        <option value="<?= $bank['id'] ?>"
                                            data-subject="<?= esc($bank['subject_name']) ?>"
                                            data-exam-type="<?= esc($bank['exam_type_name']) ?>"
                                            data-question-count="<?= $bank['question_count'] ?>">
                                            <?= esc($bank['name']) ?> (<?= $bank['question_count'] ?> questions)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Bank Info Display -->
                        <div id="bank-info" class="hidden p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Question Bank Information</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Subject:</span>
                                    <span id="bank-subject" class="font-medium ml-1"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Exam Type:</span>
                                    <span id="bank-exam-type" class="font-medium ml-1"></span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Total Questions:</span>
                                    <span id="bank-question-count" class="font-medium ml-1"></span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="qb_title" class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                                    <input type="text" id="qb_title" name="title" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter exam title">
                                </div>

                                <div>
                                    <label for="qb_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea id="qb_description" name="description" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter exam description"></textarea>
                                </div>

                                <div>
                                    <label for="qb_question_count" class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                                    <input type="number" id="qb_question_count" name="question_count" required min="1"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                        placeholder="Enter number of questions">
                                    <p class="text-xs text-gray-500 mt-1">Leave empty to use all questions from the bank</p>
                                </div>
                            </div>

                            <!-- Schedule & Settings -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="qb_start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                        <input type="datetime-local" id="qb_start_time" name="start_time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label for="qb_end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                        <input type="datetime-local" id="qb_end_time" name="end_time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="qb_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                                        <input type="number" id="qb_duration_minutes" name="duration_minutes" required min="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                            placeholder="60">
                                    </div>

                                    <div>
                                        <label for="qb_max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                                        <input type="number" id="qb_max_attempts" name="max_attempts" min="1" value="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="qb_passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                                        <input type="number" id="qb_passing_score" name="passing_score" min="0" max="100" value="60"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label for="qb_shuffle_questions" class="block text-sm font-medium text-gray-700 mb-2">Shuffle Questions</label>
                                        <select id="qb_shuffle_questions" name="shuffle_questions"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                            <option value="0">No</option>
                                            <option value="1" selected>Yes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                                Create from Question Bank
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- PDF Scraping Form -->
        <div id="pdf-scraping-form" class="creation-form hidden">
            <form action="<?= base_url('admin/exams/create-from-pdf') ?>" method="post" enctype="multipart/form-data" id="pdfScrapingExamForm">
                <?= csrf_field() ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Create Exam from PDF</h3>

                    <div class="space-y-6">
                        <!-- PDF Upload -->
                        <div>
                            <label for="pdf_file" class="block text-sm font-medium text-gray-700 mb-2">Upload PDF File</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-purple-500 transition-colors">
                                <input type="file" id="pdf_file" name="pdf_file" accept=".pdf" required class="hidden">
                                <label for="pdf_file" class="cursor-pointer">
                                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-900">Click to upload PDF</p>
                                    <p class="text-xs text-gray-500 mt-1">PDF files up to 10MB</p>
                                </label>
                            </div>
                            <div id="pdf-info" class="hidden mt-2 text-sm text-gray-600"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="pdf_title" class="block text-sm font-medium text-gray-700 mb-2">Exam Title</label>
                                    <input type="text" id="pdf_title" name="title" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="Enter exam title">
                                </div>

                                <div>
                                    <label for="pdf_description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                    <textarea id="pdf_description" name="description" rows="3"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                        placeholder="Enter exam description"></textarea>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="pdf_subject_id" class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                        <select id="pdf_subject_id" name="subject_id" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="">Select Subject</option>
                                            <?php if (isset($subjects)): ?>
                                                <?php foreach ($subjects as $subject): ?>
                                                    <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="pdf_exam_type_id" class="block text-sm font-medium text-gray-700 mb-2">Exam Type</label>
                                        <select id="pdf_exam_type_id" name="exam_type_id" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="">Select Type</option>
                                            <?php if (isset($examTypes)): ?>
                                                <?php foreach ($examTypes as $type): ?>
                                                    <option value="<?= $type['id'] ?>"><?= esc($type['name']) ?></option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Schedule & Settings -->
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="pdf_start_time" class="block text-sm font-medium text-gray-700 mb-2">Start Time</label>
                                        <input type="datetime-local" id="pdf_start_time" name="start_time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label for="pdf_end_time" class="block text-sm font-medium text-gray-700 mb-2">End Time</label>
                                        <input type="datetime-local" id="pdf_end_time" name="end_time" required
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="pdf_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                                        <input type="number" id="pdf_duration_minutes" name="duration_minutes" required min="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                            placeholder="60">
                                    </div>

                                    <div>
                                        <label for="pdf_max_attempts" class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                                        <input type="number" id="pdf_max_attempts" name="max_attempts" min="1" value="1"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="pdf_passing_score" class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                                        <input type="number" id="pdf_passing_score" name="passing_score" min="0" max="100" value="60"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>

                                    <div>
                                        <label for="pdf_shuffle_questions" class="block text-sm font-medium text-gray-700 mb-2">Shuffle Questions</label>
                                        <select id="pdf_shuffle_questions" name="shuffle_questions"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                            <option value="0">No</option>
                                            <option value="1" selected>Yes</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Question Bank Creation -->
                                <div class="p-4 bg-purple-50 rounded-lg">
                                    <h4 class="font-medium text-gray-900 mb-2">Question Bank Creation</h4>
                                    <div>
                                        <label for="create_question_bank" class="flex items-center">
                                            <input type="checkbox" id="create_question_bank" name="create_question_bank" value="1" checked
                                                class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                            <span class="ml-2 text-sm text-gray-700">Create question bank from PDF</span>
                                        </label>
                                        <p class="text-xs text-gray-500 mt-1">Questions extracted from PDF will be saved to a new question bank for future use</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Process PDF & Create Exam
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Creation method selection
        const methodCards = document.querySelectorAll('.creation-method-card');
        const forms = document.querySelectorAll('.creation-form');

        methodCards.forEach(card => {
            card.addEventListener('click', function() {
                const method = this.dataset.method;

                // Update card selection
                methodCards.forEach(c => {
                    c.classList.remove('border-blue-500', 'border-green-500', 'border-purple-500');
                    c.classList.add('border-gray-200');
                });

                if (method === 'manual') {
                    this.classList.remove('border-gray-200');
                    this.classList.add('border-blue-500');
                } else if (method === 'question-bank') {
                    this.classList.remove('border-gray-200');
                    this.classList.add('border-green-500');
                } else if (method === 'pdf-scraping') {
                    this.classList.remove('border-gray-200');
                    this.classList.add('border-purple-500');
                }

                // Show selected form
                forms.forEach(form => form.classList.add('hidden'));
                document.getElementById(method + '-form').classList.remove('hidden');
            });
        });

        // Question bank selection handler
        const questionBankSelect = document.getElementById('question_bank_id');
        const bankInfo = document.getElementById('bank-info');

        if (questionBankSelect) {
            questionBankSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];

                if (this.value) {
                    // Show bank info
                    document.getElementById('bank-subject').textContent = selectedOption.dataset.subject || '-';
                    document.getElementById('bank-exam-type').textContent = selectedOption.dataset.examType || '-';
                    document.getElementById('bank-question-count').textContent = selectedOption.dataset.questionCount || '0';
                    bankInfo.classList.remove('hidden');

                    // Set max question count
                    const questionCountInput = document.getElementById('qb_question_count');
                    const maxQuestions = parseInt(selectedOption.dataset.questionCount) || 0;
                    questionCountInput.max = maxQuestions;
                    questionCountInput.placeholder = `Max: ${maxQuestions} questions`;
                } else {
                    bankInfo.classList.add('hidden');
                }
            });
        }

        // PDF file handler
        const pdfFileInput = document.getElementById('pdf_file');
        const pdfInfo = document.getElementById('pdf-info');

        if (pdfFileInput) {
            pdfFileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const fileName = file.name;
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    pdfInfo.innerHTML = `Selected: <strong>${fileName}</strong> (${fileSize} MB)`;
                    pdfInfo.classList.remove('hidden');

                    // Auto-fill title from filename
                    const titleInput = document.getElementById('pdf_title');
                    if (!titleInput.value) {
                        const nameWithoutExt = fileName.replace(/\.[^/.]+$/, "");
                        titleInput.value = nameWithoutExt.replace(/[-_]/g, ' ');
                    }
                } else {
                    pdfInfo.classList.add('hidden');
                }
            });
        } // Form validation
        const examForms = document.querySelectorAll('form[id$="ExamForm"]');
        examForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const startTime = form.querySelector('[name="start_time"]').value;
                const endTime = form.querySelector('[name="end_time"]').value;

                if (startTime && endTime && new Date(startTime) >= new Date(endTime)) {
                    e.preventDefault();
                    alert('End time must be after start time.');
                    return false;
                }

                // Show loading state
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<svg class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="m4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...';

                // Reset on error (you might want to handle this differently)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 5000);
            });
        });
    });
</script>
<?= $this->endSection() ?>