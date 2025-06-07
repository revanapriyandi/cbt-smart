<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit Exam<?= $this->endSection() ?>

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
            <h1 class="text-3xl font-bold text-gray-900">Edit Exam</h1>
        </div>
        <p class="text-gray-600">Edit ujian: <?= esc($exam['title']) ?></p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" class="p-6 space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Exam Title -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Exam Title *</label>
                    <input type="text" name="title" required value="<?= old('title', $exam['title']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Mid-Term Mathematics Exam">
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject *</label>
                    <select name="subject_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Subject</option>
                        <?php if (isset($subjects)): ?>
                            <?php foreach ($subjects as $subject): ?>
                                <option value="<?= $subject['id'] ?>" <?= old('subject_id', $exam['subject_id']) == $subject['id'] ? 'selected' : '' ?>>
                                    <?= esc($subject['name']) ?> (<?= esc($subject['code']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- Teacher -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Teacher *</label>
                    <select name="teacher_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Teacher</option>
                        <?php if (isset($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>" <?= old('teacher_id', $exam['teacher_id']) == $teacher['id'] ? 'selected' : '' ?>>
                                    <?= esc($teacher['full_name']) ?> (<?= esc($teacher['username']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div> <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes) *</label>
                    <input type="number" name="duration_minutes" required value="<?= old('duration_minutes', $exam['duration_minutes']) ?>" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 90">
                </div> <!-- Question Count -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Question Count *</label>
                    <input type="number" name="question_count" id="question_count" required value="<?= old('question_count', $exam['question_count']) ?>" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 20">
                </div><!-- PDF Source -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">PDF Source *</label>

                    <!-- PDF Input Method Selection -->
                    <div class="mb-4">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="radio" name="pdf_input_method" value="url" id="method_url" checked
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                    onchange="togglePdfInputMethod()">
                                <span class="ml-2 text-sm text-gray-700">URL</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="pdf_input_method" value="upload" id="method_upload"
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 focus:ring-2"
                                    onchange="togglePdfInputMethod()">
                                <span class="ml-2 text-sm text-gray-700">File Upload</span>
                            </label>
                        </div>
                    </div>

                    <!-- URL Input -->
                    <div id="url_input" class="space-y-3">
                        <input type="url" name="pdf_url" id="pdf_url" value="<?= old('pdf_url', $exam['pdf_url']) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., https://example.com/exam-questions.pdf">
                        <p class="text-sm text-gray-500">Enter the URL to the PDF file containing exam questions</p>
                    </div>

                    <!-- File Upload Input -->
                    <div id="file_input" class="space-y-3 hidden">
                        <input type="file" name="pdf_file" id="pdf_file" accept=".pdf"
                            class="w-full px-3 py-2 border-2 border-dashed border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        <p class="text-sm text-gray-500">Upload a PDF file containing exam questions (Max: 10MB)</p>
                    </div>

                    <!-- Parse PDF Button and Status -->
                    <div class="flex items-center space-x-4 mt-3">
                        <button type="button" onclick="parsePdfContent()" id="parse_btn"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Parse PDF
                        </button>
                        <div id="parse_status" class="text-sm"></div>
                    </div>

                    <!-- PDF Preview -->
                    <div id="pdf_preview" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">PDF Content Preview:</h4>
                        <div id="pdf_content" class="text-sm text-gray-700 max-h-32 overflow-y-auto"></div>
                        <div id="auto_question_count" class="mt-2 text-sm text-green-600 hidden"></div>
                    </div>

                    <!-- Hidden field to store final PDF URL -->
                    <input type="hidden" name="final_pdf_url" id="final_pdf_url" value="<?= old('pdf_url', $exam['pdf_url']) ?>">
                </div>

                <!-- Start Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Time *</label>
                    <input type="datetime-local" name="start_time" required
                        value="<?= old('start_time', date('Y-m-d\TH:i', strtotime($exam['start_time']))) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <!-- End Time -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Time *</label>
                    <input type="datetime-local" name="end_time" required
                        value="<?= old('end_time', date('Y-m-d\TH:i', strtotime($exam['end_time']))) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div> <!-- Active Status -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" value="1" <?= old('is_active', $exam['is_active']) ? 'checked' : '' ?>
                            class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                        <label for="is_active" class="ml-2 text-sm text-gray-700">Active (students can take this exam)</label>
                    </div>
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Brief description of the exam"><?= old('description', $exam['description']) ?></textarea>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?= base_url('admin/exams') ?>" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    Update Exam
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePdfInputMethod() {
        const methodUrl = document.getElementById('method_url');
        const methodUpload = document.getElementById('method_upload');
        const urlInput = document.getElementById('url_input');
        const fileInput = document.getElementById('file_input');
        const pdfUrl = document.getElementById('pdf_url');
        const pdfFile = document.getElementById('pdf_file');

        if (methodUrl.checked) {
            urlInput.classList.remove('hidden');
            fileInput.classList.add('hidden');
            pdfUrl.setAttribute('required', 'required');
            pdfFile.removeAttribute('required');
        } else {
            urlInput.classList.add('hidden');
            fileInput.classList.remove('hidden');
            pdfFile.setAttribute('required', 'required');
            pdfUrl.removeAttribute('required');
        }

        // Clear previous preview
        hidePdfPreview();
    }

    async function parsePdfContent() {
        const methodUrl = document.getElementById('method_url');
        const pdfUrl = document.getElementById('pdf_url');
        const pdfFile = document.getElementById('pdf_file');
        const parseBtn = document.getElementById('parse_btn');
        const status = document.getElementById('parse_status');
        const preview = document.getElementById('pdf_preview');
        const content = document.getElementById('pdf_content');
        const autoQuestionCount = document.getElementById('auto_question_count');
        const questionCountInput = document.getElementById('question_count');
        const finalPdfUrl = document.getElementById('final_pdf_url');

        let formData = new FormData();

        if (methodUrl.checked) {
            if (!pdfUrl.value) {
                status.innerHTML = '<span class="text-red-600">Please enter a PDF URL first</span>';
                return;
            }
            formData.append('pdf_url', pdfUrl.value);
            formData.append('input_method', 'url');
        } else {
            if (!pdfFile.files[0]) {
                status.innerHTML = '<span class="text-red-600">Please select a PDF file first</span>';
                return;
            }
            formData.append('pdf_file', pdfFile.files[0]);
            formData.append('input_method', 'upload');
        }

        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        // Update UI
        parseBtn.disabled = true;
        parseBtn.innerHTML = '<svg class="w-4 h-4 inline mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Processing...';
        status.innerHTML = '<span class="text-blue-600">Processing PDF...</span>';

        try {
            const response = await fetch('<?= base_url('api/parse-pdf-admin') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                status.innerHTML = '<span class="text-green-600"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>PDF parsed successfully</span>';
                content.textContent = result.text;
                preview.classList.remove('hidden');

                // Set the final PDF URL
                finalPdfUrl.value = result.pdf_url || pdfUrl.value;

                // Auto-fill question count if available
                if (result.estimated_questions && result.estimated_questions > 0) {
                    questionCountInput.value = result.estimated_questions;
                    autoQuestionCount.innerHTML = `<svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Auto-detected ${result.estimated_questions} questions`;
                    autoQuestionCount.classList.remove('hidden');
                } else {
                    autoQuestionCount.classList.add('hidden');
                }
            } else {
                status.innerHTML = `<span class="text-red-600"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>${result.message || 'Failed to parse PDF'}</span>`;
                hidePdfPreview();
            }
        } catch (error) {
            console.error('Error:', error);
            status.innerHTML = '<span class="text-red-600"><svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>An error occurred while parsing PDF</span>';
            hidePdfPreview();
        } finally {
            parseBtn.disabled = false;
            parseBtn.innerHTML = '<svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>Parse PDF';
        }
    }

    function hidePdfPreview() {
        const preview = document.getElementById('pdf_preview');
        const autoQuestionCount = document.getElementById('auto_question_count');

        preview.classList.add('hidden');
        autoQuestionCount.classList.add('hidden');
    }

    // Form submission handler
    document.querySelector('form').addEventListener('submit', function(e) {
        const finalPdfUrl = document.getElementById('final_pdf_url');
        const pdfUrl = document.getElementById('pdf_url');

        // Make sure we have a PDF URL set before submission
        if (!finalPdfUrl.value && !pdfUrl.value) {
            e.preventDefault();
            alert('Please parse a PDF first before updating the exam.');
            return false;
        }
    });
</script>
<?= $this->endSection() ?>