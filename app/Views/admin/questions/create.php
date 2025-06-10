<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Tambah Soal<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Soal</h1>
            <p class="text-gray-600 mt-1">Buat soal baru untuk bank soal</p>
        </div>
        <a href="/admin/questions" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form method="POST" enctype="multipart/form-data" class="p-6">
            <?= csrf_field() ?>

            <!-- Basic Information -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bank Soal *</label>
                    <select name="question_bank_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Bank Soal</option>
                        <?php foreach ($questionBanks as $bank): ?>
                            <option value="<?= $bank['id'] ?>" <?= old('question_bank_id') == $bank['id'] ? 'selected' : '' ?>>
                                <?= esc($bank['name']) ?> (<?= esc($bank['subject_name']) ?> - <?= esc($bank['exam_type_name']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['question_bank_id'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['question_bank_id'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Soal *</label>
                    <select name="question_type" id="question-type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Jenis Soal</option>
                        <option value="multiple_choice" <?= old('question_type') == 'multiple_choice' ? 'selected' : '' ?>>Pilihan Ganda</option>
                        <option value="essay" <?= old('question_type') == 'essay' ? 'selected' : '' ?>>Essay</option>
                        <option value="true_false" <?= old('question_type') == 'true_false' ? 'selected' : '' ?>>Benar/Salah</option>
                        <option value="fill_blank" <?= old('question_type') == 'fill_blank' ? 'selected' : '' ?>>Isian</option>
                    </select>
                    <?php if (isset($errors['question_type'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['question_type'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tingkat Kesulitan *</label>
                    <select name="difficulty_level" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Tingkat Kesulitan</option>
                        <option value="easy" <?= old('difficulty_level') == 'easy' ? 'selected' : '' ?>>Mudah</option>
                        <option value="medium" <?= old('difficulty_level') == 'medium' ? 'selected' : '' ?>>Sedang</option>
                        <option value="hard" <?= old('difficulty_level') == 'hard' ? 'selected' : '' ?>>Sulit</option>
                    </select>
                    <?php if (isset($errors['difficulty_level'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['difficulty_level'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Poin *</label>
                    <input type="number" name="points" value="<?= old('points', 10) ?>" min="1" step="0.01" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($errors['points'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['points'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu (detik)</label>
                    <input type="number" name="time_limit" value="<?= old('time_limit') ?>" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Kosongkan untuk tanpa batas waktu">
                    <?php if (isset($errors['time_limit'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['time_limit'] ?></p>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="active" <?= old('status', 'active') == 'active' ? 'selected' : '' ?>>Aktif</option>
                        <option value="inactive" <?= old('status') == 'inactive' ? 'selected' : '' ?>>Tidak Aktif</option>
                    </select>
                    <?php if (isset($errors['status'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $errors['status'] ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Question Text -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Teks Soal *</label>
                <textarea name="question_text" rows="6" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Masukkan teks soal..."><?= old('question_text') ?></textarea>
                <?php if (isset($errors['question_text'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['question_text'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Question Image -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Soal (Opsional)</label>
                <input type="file" name="image" accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 5MB</p>
            </div>

            <!-- Multiple Choice Options -->
            <div id="multiple-choice-options" class="mb-6 hidden">
                <label class="block text-sm font-medium text-gray-700 mb-4">Pilihan Jawaban</label>
                <div class="space-y-3">
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="correct_option" value="0" class="text-blue-600">
                        <label class="font-medium text-gray-700">A.</label>
                        <input type="text" name="options[]" placeholder="Pilihan A"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="correct_option" value="1" class="text-blue-600">
                        <label class="font-medium text-gray-700">B.</label>
                        <input type="text" name="options[]" placeholder="Pilihan B"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="correct_option" value="2" class="text-blue-600">
                        <label class="font-medium text-gray-700">C.</label>
                        <input type="text" name="options[]" placeholder="Pilihan C"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="correct_option" value="3" class="text-blue-600">
                        <label class="font-medium text-gray-700">D.</label>
                        <input type="text" name="options[]" placeholder="Pilihan D"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex items-center space-x-3">
                        <input type="radio" name="correct_option" value="4" class="text-blue-600">
                        <label class="font-medium text-gray-700">E.</label>
                        <input type="text" name="options[]" placeholder="Pilihan E (Opsional)"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <p class="text-sm text-gray-500 mt-2">Pilih radio button untuk menandai jawaban yang benar</p>
            </div>

            <!-- Explanation -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Penjelasan (Opsional)</label>
                <textarea name="explanation" rows="4"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Penjelasan jawaban atau pembahasan soal..."><?= old('explanation') ?></textarea>
                <?php if (isset($errors['explanation'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['explanation'] ?></p>
                <?php endif; ?>
            </div>

            <!-- Form Actions -->
            <div class="flex gap-4">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Soal
                </button>
                <a href="/admin/questions" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const questionTypeSelect = document.getElementById('question-type');
        const multipleChoiceOptions = document.getElementById('multiple-choice-options');

        questionTypeSelect.addEventListener('change', function() {
            if (this.value === 'multiple_choice') {
                multipleChoiceOptions.classList.remove('hidden');
            } else {
                multipleChoiceOptions.classList.add('hidden');
            }
        });

        // Trigger change event on page load to handle old values
        if (questionTypeSelect.value === 'multiple_choice') {
            multipleChoiceOptions.classList.remove('hidden');
        }
    });
</script>

<?= $this->endSection() ?>