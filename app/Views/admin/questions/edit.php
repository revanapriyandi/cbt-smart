<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit Soal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            <li><a href="<?= base_url('admin/dashboard') ?>" class="hover:text-blue-600">Dashboard</a></li>
            <li><span class="mx-2">/</span></li>
            <li><a href="<?= base_url('admin/questions') ?>" class="hover:text-blue-600">Kelola Soal</a></li>
            <li><span class="mx-2">/</span></li>
            <li class="text-gray-900 font-medium">Edit Soal</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Soal</h1>
            <p class="text-gray-600 mt-1">Ubah data soal yang ada</p>
        </div>
        <a href="<?= base_url('admin/questions') ?>"
            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white shadow-sm border border-gray-200 rounded-lg">
        <form id="editQuestionForm" enctype="multipart/form-data">
            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Question Bank -->
                    <div>
                        <label for="question_bank_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Bank Soal <span class="text-red-500">*</span>
                        </label>
                        <select id="question_bank_id" name="question_bank_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Pilih Bank Soal</option>
                            <?php foreach ($questionBanks as $bank): ?>
                                <option value="<?= $bank['id'] ?>" <?= $question['question_bank_id'] == $bank['id'] ? 'selected' : '' ?>>
                                    <?= esc($bank['name']) ?> - <?= esc($bank['subject_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Question Type -->
                    <div>
                        <label for="question_type" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Soal <span class="text-red-500">*</span>
                        </label>
                        <select id="question_type" name="question_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="multiple_choice" <?= $question['question_type'] == 'multiple_choice' ? 'selected' : '' ?>>Pilihan Ganda</option>
                            <option value="essay" <?= $question['question_type'] == 'essay' ? 'selected' : '' ?>>Essay</option>
                            <option value="true_false" <?= $question['question_type'] == 'true_false' ? 'selected' : '' ?>>Benar/Salah</option>
                            <option value="fill_blank" <?= $question['question_type'] == 'fill_blank' ? 'selected' : '' ?>>Isian</option>
                        </select>
                    </div>

                    <!-- Difficulty Level -->
                    <div>
                        <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">
                            Tingkat Kesulitan <span class="text-red-500">*</span>
                        </label>
                        <select id="difficulty_level" name="difficulty_level" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="easy" <?= $question['difficulty_level'] == 'easy' ? 'selected' : '' ?>>Mudah</option>
                            <option value="medium" <?= $question['difficulty_level'] == 'medium' ? 'selected' : '' ?>>Sedang</option>
                            <option value="hard" <?= $question['difficulty_level'] == 'hard' ? 'selected' : '' ?>>Sulit</option>
                        </select>
                    </div>

                    <!-- Points -->
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-2">
                            Poin <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="points" name="points" min="1" step="0.5" required
                            value="<?= esc($question['points']) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="active" <?= $question['status'] == 'active' ? 'selected' : '' ?>>Aktif</option>
                            <option value="inactive" <?= $question['status'] == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                        </select>
                    </div>
                </div>

                <!-- Question Text -->
                <div>
                    <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">
                        Teks Soal <span class="text-red-500">*</span>
                    </label>
                    <textarea id="question_text" name="question_text" rows="5" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Masukkan teks soal..."><?= esc($question['question_text']) ?></textarea>
                </div>

                <!-- Image Upload -->
                <div>
                    <label for="question_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Gambar Soal
                    </label>
                    <?php if (!empty($question['image_url'])): ?>
                        <div class="mb-3">
                            <img src="<?= base_url($question['image_url']) ?>" alt="Question Image" class="max-w-xs h-auto border border-gray-300 rounded-lg">
                            <p class="text-sm text-gray-600 mt-1">Gambar saat ini</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="question_image" name="question_image" accept="image/*"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                </div>

                <!-- Multiple Choice Options -->
                <div id="optionsContainer" class="<?= $question['question_type'] === 'multiple_choice' ? '' : 'hidden' ?>">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Pilihan Jawaban <span class="text-red-500">*</span>
                        </label>
                        <button type="button" id="addOption"
                            class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Tambah Pilihan
                        </button>
                    </div>

                    <div id="optionsList" class="space-y-3">
                        <?php if (!empty($options)): ?>
                            <?php foreach ($options as $index => $option): ?>
                                <div class="option-item flex items-start space-x-3 p-3 border border-gray-200 rounded-lg">
                                    <div class="flex items-center">
                                        <input type="radio" name="correct_option" value="<?= $index ?>"
                                            <?= $option['is_correct'] ? 'checked' : '' ?>
                                            class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" name="options[]"
                                            value="<?= esc($option['option_text']) ?>"
                                            placeholder="Masukkan pilihan jawaban..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        <input type="hidden" name="option_ids[]" value="<?= $option['id'] ?>">
                                    </div>
                                    <button type="button" class="remove-option text-red-600 hover:text-red-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Explanation -->
                <div>
                    <label for="explanation" class="block text-sm font-medium text-gray-700 mb-2">
                        Penjelasan/Pembahasan
                    </label>
                    <textarea id="explanation" name="explanation" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Masukkan penjelasan atau pembahasan jawaban..."><?= esc($question['explanation']) ?></textarea>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-end space-x-3">
                <a href="<?= base_url('admin/questions') ?>"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Batal
                </a>
                <button type="submit" id="submitBtn"
                    class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Loading Modal -->
<div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mr-3"></div>
            <div class="text-gray-900 font-medium">Menyimpan perubahan...</div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('editQuestionForm');
        const questionTypeSelect = document.getElementById('question_type');
        const optionsContainer = document.getElementById('optionsContainer');
        const addOptionBtn = document.getElementById('addOption');
        const optionsList = document.getElementById('optionsList');
        const submitBtn = document.getElementById('submitBtn');
        const loadingModal = document.getElementById('loadingModal');

        let optionCounter = <?= count($options ?? []) ?>;

        // Toggle options container based on question type
        questionTypeSelect.addEventListener('change', function() {
            if (this.value === 'multiple_choice') {
                optionsContainer.classList.remove('hidden');
                ensureMinimumOptions();
            } else {
                optionsContainer.classList.add('hidden');
            }
        });

        // Add new option
        addOptionBtn.addEventListener('click', function() {
            addOption();
        });

        // Remove option handler
        optionsList.addEventListener('click', function(e) {
            if (e.target.closest('.remove-option')) {
                const optionItem = e.target.closest('.option-item');
                if (optionsList.children.length > 2) {
                    optionItem.remove();
                    updateOptionValues();
                } else {
                    alert('Minimal harus ada 2 pilihan jawaban');
                }
            }
        });

        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateForm()) {
                return;
            }

            showLoading(true);
            submitBtn.disabled = true;

            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            fetch('<?= base_url('admin/questions/' . $question['id']) ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    showLoading(false);
                    submitBtn.disabled = false;
                    console.log(data);
                    if (data.success) {
                        alert('Soal berhasil diperbarui!');
                        window.location.href = '<?= base_url('admin/questions') ?>';
                    } else {
                        alert(data.message || 'Terjadi kesalahan saat menyimpan data');
                    }
                })
                .catch(error => {
                    showLoading(false);
                    submitBtn.disabled = false;
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data');
                });
        });

        function addOption(text = '', isCorrect = false) {
            const optionDiv = document.createElement('div');
            optionDiv.className = 'option-item flex items-start space-x-3 p-3 border border-gray-200 rounded-lg';

            optionDiv.innerHTML = `
            <div class="flex items-center">
                <input type="radio" name="correct_option" value="${optionCounter}" ${isCorrect ? 'checked' : ''}
                       class="w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
            </div>
            <div class="flex-1">
                <input type="text" name="options[]" value="${text}"
                       placeholder="Masukkan pilihan jawaban..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <input type="hidden" name="option_ids[]" value="">
            </div>
            <button type="button" class="remove-option text-red-600 hover:text-red-800">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

            optionsList.appendChild(optionDiv);
            optionCounter++;
        }

        function ensureMinimumOptions() {
            while (optionsList.children.length < 2) {
                addOption();
            }
        }

        function updateOptionValues() {
            const options = optionsList.querySelectorAll('.option-item');
            options.forEach((option, index) => {
                const radio = option.querySelector('input[type="radio"]');
                radio.value = index;
            });
        }

        function validateForm() {
            const questionBankId = document.getElementById('question_bank_id').value;
            const questionText = document.getElementById('question_text').value.trim();
            const questionType = document.getElementById('question_type').value;
            const difficultyLevel = document.getElementById('difficulty_level').value;
            const points = document.getElementById('points').value;

            if (!questionBankId) {
                alert('Bank soal harus dipilih');
                return false;
            }

            if (!questionText || questionText.length < 10) {
                alert('Teks soal minimal 10 karakter');
                return false;
            }

            if (!questionType) {
                alert('Jenis soal harus dipilih');
                return false;
            }

            if (!difficultyLevel) {
                alert('Tingkat kesulitan harus dipilih');
                return false;
            }

            if (!points || parseFloat(points) <= 0) {
                alert('Poin harus lebih dari 0');
                return false;
            }

            // Validate multiple choice options
            if (questionType === 'multiple_choice') {
                const options = optionsList.querySelectorAll('input[name="options[]"]');
                const correctOption = optionsList.querySelector('input[name="correct_option"]:checked');

                if (options.length < 2) {
                    alert('Minimal harus ada 2 pilihan jawaban');
                    return false;
                }

                let hasEmptyOption = false;
                options.forEach(option => {
                    if (!option.value.trim()) {
                        hasEmptyOption = true;
                    }
                });

                if (hasEmptyOption) {
                    alert('Semua pilihan jawaban harus diisi');
                    return false;
                }

                if (!correctOption) {
                    alert('Pilih jawaban yang benar');
                    return false;
                }
            }

            return true;
        }

        function showLoading(show) {
            if (show) {
                loadingModal.classList.remove('hidden');
            } else {
                loadingModal.classList.add('hidden');
            }
        }

        // Initialize
        if (questionTypeSelect.value === 'multiple_choice') {
            ensureMinimumOptions();
        }
    });
</script>

<?= $this->endSection() ?>