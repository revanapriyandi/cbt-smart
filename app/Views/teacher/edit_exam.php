<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit Ujian<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center">
            <a href="<?= base_url('teacher/exams') ?>"
                class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Edit Ujian: <?= esc($exam['title']) ?></h1>
        </div>
    </div>

    <div class="p-6">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('teacher/exams/edit/' . $exam['id']) ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Ujian *
                    </label>
                    <input type="text"
                        id="title"
                        name="title"
                        required
                        value="<?= old('title', $exam['title']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Mata Pelajaran *
                    </label>
                    <select id="subject_id"
                        name="subject_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Mata Pelajaran</option>
                        <?php foreach ($subjects as $subject): ?>
                            <option value="<?= $subject['id'] ?>"
                                <?= (old('subject_id', $exam['subject_id']) == $subject['id']) ? 'selected' : '' ?>>
                                <?= esc($subject['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi
                </label>
                <textarea id="description"
                    name="description"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= old('description', $exam['description']) ?></textarea>
            </div>

            <div class="bg-blue-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Sumber Soal PDF</h3>

                <div class="space-y-4">
                    <div>
                        <label for="pdf_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL PDF *
                        </label>
                        <input type="url"
                            id="pdf_url"
                            name="pdf_url"
                            required
                            value="<?= old('pdf_url', $exam['pdf_url']) ?>"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <?php if ($exam['pdf_content']): ?>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Konten PDF Saat Ini:</h4>
                            <div class="text-sm text-gray-700 max-h-32 overflow-y-auto">
                                <?= esc(substr($exam['pdf_content'], 0, 500)) ?>...
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="question_count" class="block text-sm font-medium text-gray-700 mb-2">
                        Jumlah Soal *
                    </label>
                    <input type="number"
                        id="question_count"
                        name="question_count"
                        min="1"
                        max="50"
                        required
                        value="<?= old('question_count', $exam['question_count']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                        Durasi (menit) *
                    </label>
                    <input type="number"
                        id="duration_minutes"
                        name="duration_minutes"
                        min="5"
                        max="300"
                        required
                        value="<?= old('duration_minutes', $exam['duration_minutes']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox"
                        id="is_active"
                        name="is_active"
                        value="1"
                        <?= old('is_active', $exam['is_active']) ? 'checked' : '' ?>
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">
                        Ujian Aktif
                    </label>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Mulai *
                    </label>
                    <input type="datetime-local"
                        id="start_time"
                        name="start_time"
                        required
                        value="<?= old('start_time', date('Y-m-d\TH:i', strtotime($exam['start_time']))) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div>
                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                        Waktu Selesai *
                    </label>
                    <input type="datetime-local"
                        id="end_time"
                        name="end_time"
                        required
                        value="<?= old('end_time', date('Y-m-d\TH:i', strtotime($exam['end_time']))) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Questions Section -->
            <?php if (!empty($questions)): ?>
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Soal-soal Ujian</h3>
                    <div class="space-y-3">
                        <?php foreach ($questions as $question): ?>
                            <div class="bg-white p-3 rounded border">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="text-sm font-medium text-blue-600">Soal <?= $question['question_number'] ?></span>
                                        <p class="text-sm text-gray-700 mt-1"><?= esc($question['question_text']) ?></p>
                                        <span class="text-xs text-gray-500">Skor maksimal: <?= $question['max_score'] ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <p class="text-sm text-gray-600 mt-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Untuk mengedit soal, gunakan fitur "Generate ulang soal" atau edit manual setelah menyimpan.
                    </p>
                </div>
            <?php endif; ?>

            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="<?= base_url('teacher/exams') ?>"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Update Ujian
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>