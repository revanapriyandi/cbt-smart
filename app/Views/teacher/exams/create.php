<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Create Exam<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="mb-4 sm:mb-6">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Create New Exam</h1>
        <p class="mt-2 text-sm sm:text-base text-gray-600">Buat ujian baru dengan soal essay berbasis PDF</p>
    </div>

    <form id="examForm" method="POST" action="<?= base_url('teacher/exams/store') ?>" enctype="multipart/form-data">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-4 sm:space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h2>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
                            <input type="text" name="title" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                placeholder="e.g., Final Exam Matematika Semester 1">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                            <select name="subject_id" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                                <option value="">Select Subject</option>
                                <?php if (isset($subjects)): ?>
                                    <?php foreach ($subjects as $subject): ?>
                                        <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea name="description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                placeholder="Brief description of the exam"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Schedule & Settings -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Schedule & Settings</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date & Time *</label>
                            <input type="datetime-local" name="start_time" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                            <input type="number" name="duration" required min="1" max="480"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                                placeholder="e.g., 120">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Max Attempts</label>
                            <input type="number" name="max_attempts" min="1" max="5" value="1"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Passing Score (%)</label>
                            <input type="number" name="passing_score" min="0" max="100" value="60"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
                        <h2 class="text-lg font-semibold text-gray-900">Questions</h2>
                        <button type="button" onclick="addQuestion()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm sm:text-base">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Question
                        </button>
                    </div>

                    <div id="questionsContainer">
                        <!-- Questions will be added here dynamically -->
                    </div>

                    <div id="noQuestions" class="text-center py-6 sm:py-8 text-gray-500">
                        <svg class="w-10 sm:w-12 h-10 sm:h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm sm:text-base">No questions added yet. Click "Add Question" to get started.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 order-first lg:order-last">
                <!-- Quick Actions -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6 mb-4 sm:mb-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Quick Actions</h3>

                    <div class="space-y-3">
                        <button type="button" onclick="previewExam()"
                            class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm sm:text-base">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Preview
                        </button>

                        <button type="button" onclick="saveDraft()"
                            class="w-full flex items-center justify-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg text-sm sm:text-base">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                            </svg>
                            Save as Draft
                        </button>

                        <button type="submit"
                            class="w-full flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm sm:text-base">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Create & Publish
                        </button>
                    </div>
                </div>

                <!-- AI Generation Helper -->
                <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 sm:p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">AI Question Generator</h3>
                    <p class="text-xs sm:text-sm text-gray-600 mb-4">Generate questions automatically from PDF content using AI.</p>

                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">PDF File</label>
                            <input type="file" id="aiPdfFile" accept=".pdf"
                                class="w-full text-xs sm:text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:sm:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Number of Questions</label>
                            <input type="number" id="aiQuestionCount" min="1" max="10" value="5"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        </div>

                        <button type="button" onclick="generateQuestions()"
                            class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium text-sm sm:text-base">
                            Generate Questions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Question Template -->
<template id="questionTemplate">
    <div class="question-item border border-gray-200 rounded-lg p-4 mb-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 space-y-2 sm:space-y-0">
            <h4 class="font-medium text-gray-900">Question <span class="question-number">1</span></h4>
            <div class="flex items-center space-x-2">
                <input type="number" name="questions[{index}][points]" value="10" min="1" max="100"
                    class="w-16 sm:w-20 px-2 py-1 border border-gray-300 rounded text-sm" placeholder="Points">
                <button type="button" onclick="removeQuestion(this)"
                    class="text-red-600 hover:text-red-800 p-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Question Text *</label>
                <textarea name="questions[{index}][question_text]" rows="3" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base"
                    placeholder="Enter your essay question here..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">PDF Reference (Optional)</label>
                <input type="file" name="questions[{index}][pdf_file]" accept=".pdf"
                    class="w-full text-xs sm:text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:sm:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                <p class="text-xs text-gray-500 mt-1">Upload a PDF that students can reference while answering this question.</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Sample Answer / Rubric</label>
                <textarea name="questions[{index}][sample_answer]" rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Provide a sample answer or grading rubric for AI evaluation..."></textarea>
            </div>
        </div>
    </div>
</template>

<script>
    let questionCount = 0;

    function addQuestion() {
        const template = document.getElementById('questionTemplate');
        const clone = template.content.cloneNode(true);

        // Replace placeholders
        const html = clone.firstElementChild.outerHTML.replace(/{index}/g, questionCount);

        // Add to container
        const container = document.getElementById('questionsContainer');
        container.insertAdjacentHTML('beforeend', html);

        // Update question number
        updateQuestionNumbers();

        // Hide "no questions" message
        document.getElementById('noQuestions').style.display = 'none';

        questionCount++;
    }

    function removeQuestion(button) {
        const questionItem = button.closest('.question-item');
        questionItem.remove();

        updateQuestionNumbers();

        // Show "no questions" message if no questions remain
        const container = document.getElementById('questionsContainer');
        if (container.children.length === 0) {
            document.getElementById('noQuestions').style.display = 'block';
        }
    }

    function updateQuestionNumbers() {
        const questions = document.querySelectorAll('.question-item');
        questions.forEach((question, index) => {
            const numberSpan = question.querySelector('.question-number');
            numberSpan.textContent = index + 1;

            // Update input names
            const inputs = question.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
    }

    function generateQuestions() {
        const fileInput = document.getElementById('aiPdfFile');
        const countInput = document.getElementById('aiQuestionCount');

        if (!fileInput.files[0]) {
            alert('Please select a PDF file first.');
            return;
        }

        const formData = new FormData();
        formData.append('pdf_file', fileInput.files[0]);
        formData.append('question_count', countInput.value);

        // Show loading state
        const button = event.target;
        const originalText = button.textContent;
        button.textContent = 'Generating...';
        button.disabled = true;

        fetch('<?= base_url('teacher/exams/generate-questions') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.questions) {
                    data.questions.forEach(question => {
                        addQuestion();
                        const lastQuestion = document.querySelector('.question-item:last-child');
                        lastQuestion.querySelector('textarea[name*="question_text"]').value = question.text;
                        lastQuestion.querySelector('textarea[name*="sample_answer"]').value = question.sample_answer || '';
                    });
                } else {
                    alert('Failed to generate questions: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while generating questions.');
            })
            .finally(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
    }

    function previewExam() {
        // Validate form first
        const form = document.getElementById('examForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        // Open preview in new window/tab
        const formData = new FormData(form);

        fetch('<?= base_url('teacher/exams/preview') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                const previewWindow = window.open('', '_blank');
                previewWindow.document.write(html);
                previewWindow.document.close();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to generate preview.');
            });
    }

    function saveDraft() {
        const form = document.getElementById('examForm');
        const formData = new FormData(form);
        formData.append('status', 'draft');

        fetch('<?= base_url('teacher/exams/store') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Exam saved as draft successfully!');
                    window.location.href = '<?= base_url('teacher/exams') ?>';
                } else {
                    alert('Failed to save draft: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the draft.');
            });
    }

    // Initialize with one question
    document.addEventListener('DOMContentLoaded', function() {
        addQuestion();
    });
</script>
<?= $this->endSection() ?>