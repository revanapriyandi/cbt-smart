<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Buat Ujian Baru<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center">
            <a href="<?= base_url('teacher/exams') ?>"
                class="text-gray-500 hover:text-gray-700 mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-gray-900">Buat Ujian Baru</h1>
        </div>
    </div>

    <div class="p-6">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('teacher/exams/create') ?>" method="POST" class="space-y-6">
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
                        value="<?= old('title') ?>"
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
                            <option value="<?= $subject['id'] ?>" <?= old('subject_id') == $subject['id'] ? 'selected' : '' ?>>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= old('description') ?></textarea>
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
                            value="<?= old('pdf_url') ?>"
                            placeholder="https://example.com/document.pdf"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-sm text-gray-500 mt-1">
                            Masukkan URL PDF yang berisi materi untuk soal ujian
                        </p>
                    </div>

                    <div class="flex items-center space-x-4">
                        <button type="button"
                            onclick="parsePdf()"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition duration-200">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Parse PDF
                        </button>
                        <div id="parse-status" class="text-sm"></div>
                    </div>

                    <div id="pdf-preview" class="hidden bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-900 mb-2">Preview Konten PDF:</h4>
                        <div id="pdf-content" class="text-sm text-gray-700 max-h-32 overflow-y-auto"></div>
                    </div>
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
                        value="<?= old('question_count', 5) ?>"
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
                        value="<?= old('duration_minutes', 90) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div class="flex items-center">
                    <input type="checkbox"
                        id="generate_questions"
                        name="generate_questions"
                        value="1"
                        <?= old('generate_questions') ? 'checked' : '' ?>
                        class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                    <label for="generate_questions" class="ml-2 text-sm text-gray-700">
                        Generate soal otomatis menggunakan AI
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
                        value="<?= old('start_time') ?>"
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
                        value="<?= old('end_time') ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="<?= base_url('teacher/exams') ?>"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition duration-200">
                    Batal
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Ujian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    async function parsePdf() {
        const pdfUrl = document.getElementById('pdf_url').value;
        const status = document.getElementById('parse-status');
        const preview = document.getElementById('pdf-preview');
        const content = document.getElementById('pdf-content');

        if (!pdfUrl) {
            status.innerHTML = '<span class="text-red-600">Masukkan URL PDF terlebih dahulu</span>';
            return;
        }

        status.innerHTML = '<span class="text-blue-600"><i class="fas fa-spinner fa-spin mr-1"></i>Memproses...</span>';

        try {
            const response = await fetch('<?= base_url('api/parse-pdf') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `pdf_url=${encodeURIComponent(pdfUrl)}&<?= csrf_token() ?>=<?= csrf_hash() ?>`
            });

            const result = await response.json();

            if (result.success) {
                status.innerHTML = '<span class="text-green-600"><i class="fas fa-check mr-1"></i>PDF berhasil diparse</span>';
                content.textContent = result.text;
                preview.classList.remove('hidden');
            } else {
                status.innerHTML = `<span class="text-red-600"><i class="fas fa-times mr-1"></i>${result.message}</span>`;
                preview.classList.add('hidden');
            }
        } catch (error) {
            status.innerHTML = '<span class="text-red-600"><i class="fas fa-times mr-1"></i>Terjadi kesalahan</span>';
            preview.classList.add('hidden');
        }
    }
</script>
<?= $this->endSection() ?>