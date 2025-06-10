<!-- Create/Edit Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" id="questionBankModal">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white flex items-center" id="questionBankModalLabel">
                    <i class="fas fa-database mr-2"></i>
                    <span id="modal-title">Tambah Bank Soal</span>
                </h3>
                <button type="button" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors" onclick="closeModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="questionBankForm">
                <div class="p-6">
                    <input type="hidden" id="questionbank_id" name="id">

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                        <div class="md:col-span-3">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Bank Soal <span class="text-red-500">*</span></label>
                            <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="name" name="name" required>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="active">Aktif</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Arsip</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="subject_id" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="subject_id" name="subject_id" required>
                                <option value="">Pilih Mata Pelajaran</option>
                                <?php foreach ($subjects as $subject): ?>
                                    <option value="<?= $subject['id'] ?>"><?= esc($subject['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                        <div>
                            <label for="exam_type_id" class="block text-sm font-medium text-gray-700 mb-2">Jenis Ujian <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="exam_type_id" name="exam_type_id" required>
                                <option value="">Pilih Jenis Ujian</option>
                                <?php foreach ($examTypes as $examType): ?>
                                    <option value="<?= $examType['id'] ?>"><?= esc($examType['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label for="difficulty_level" class="block text-sm font-medium text-gray-700 mb-2">Tingkat Kesulitan <span class="text-red-500">*</span></label>
                            <select class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="difficulty_level" name="difficulty_level" required>
                                <option value="">Pilih Tingkat</option>
                                <option value="easy">Mudah</option>
                                <option value="medium">Sedang</option>
                                <option value="hard">Sulit</option>
                            </select>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                        <div>
                            <label for="time_per_question" class="block text-sm font-medium text-gray-700 mb-2">Waktu Per Soal (detik)</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="time_per_question" name="time_per_question" min="1" max="300">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ada batasan waktu</p>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                        <div>
                            <label for="negative_marks" class="block text-sm font-medium text-gray-700 mb-2">Nilai Negatif</label>
                            <input type="number" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="negative_marks" name="negative_marks" step="0.1" max="0">
                            <p class="text-xs text-gray-500 mt-1">Masukkan nilai negatif (misal: -0.5)</p>
                            <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="description" name="description" rows="3" maxlength="1000"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Maksimal 1000 karakter</p>
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <div class="mb-4">
                        <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Instruksi</label>
                        <textarea class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="instructions" name="instructions" rows="4" maxlength="2000"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Instruksi khusus untuk pengerjaan soal. Maksimal 2000 karakter</p>
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <div class="mb-4">
                        <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">Tags</label>
                        <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" id="tags" name="tags" maxlength="500">
                        <p class="text-xs text-gray-500 mt-1">Pisahkan dengan koma (misal: materi1, konsep2, bab3)</p>
                        <div class="invalid-feedback text-red-500 text-sm mt-1 hidden"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" type="checkbox" id="negative_marking" name="negative_marking" value="1">
                            <label class="ml-2 text-sm font-medium text-gray-700" for="negative_marking">
                                Penilaian Negatif
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" type="checkbox" id="randomize_questions" name="randomize_questions" value="1">
                            <label class="ml-2 text-sm font-medium text-gray-700" for="randomize_questions">
                                Acak Urutan Soal
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" type="checkbox" id="show_correct_answer" name="show_correct_answer" value="1">
                            <label class="ml-2 text-sm font-medium text-gray-700" for="show_correct_answer">
                                Tampilkan Jawaban Benar
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center mb-4">
                        <input class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2" type="checkbox" id="allow_calculator" name="allow_calculator" value="1">
                        <label class="ml-2 text-sm font-medium text-gray-700" for="allow_calculator">
                            Izinkan Kalkulator
                        </label>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3 rounded-b-xl">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors" onclick="closeModal()">
                        <i class="fas fa-times mr-2"></i>
                        Batal
                    </button>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors" id="btn-save">
                        <i class="fas fa-save mr-2"></i>
                        <span id="btn-save-text">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden" id="viewModal">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-6xl w-full max-h-screen overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-cyan-500 to-blue-600 rounded-t-xl">
                <h3 class="text-lg font-semibold text-white flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    Detail Bank Soal
                </h3>
                <button type="button" class="absolute top-4 right-4 text-white hover:text-gray-200 transition-colors" onclick="closeViewModal()">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6" id="view-content">
                <!-- Content will be loaded dynamically -->
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-xl">
                <div class="flex justify-end">
                    <button type="button" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" onclick="closeViewModal()">
                        <i class="fas fa-times mr-2"></i>
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" class="fixed top-4 right-4 z-50 space-y-2"></div>