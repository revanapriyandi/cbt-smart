<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Kelola Penilaian: <?= esc($exam['title']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-lg shadow-lg">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="<?= base_url('teacher/exams/' . $exam['id'] . '/results') ?>"
                    class="text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Kelola Penilaian</h1>
                    <p class="text-gray-600"><?= esc($exam['title']) ?></p>
                </div>
            </div>
            <form method="POST" action="<?= base_url('teacher/exams/' . $exam['id'] . '/grade') ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit"
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200"
                    onclick="return confirm('Nilai semua jawaban menggunakan AI? Ini akan menimpa nilai AI yang sudah ada.')">
                    <i class="fas fa-robot mr-2"></i>
                    Auto-Grade dengan AI
                </button>
            </form>
        </div>
    </div>

    <div class="p-6">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if (empty($answers)): ?>
            <div class="text-center py-12">
                <i class="fas fa-clipboard-list text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Belum Ada Jawaban</h3>
                <p class="text-gray-500">Belum ada siswa yang menjawab soal ujian ini</p>
            </div>
        <?php else: ?>
            <!-- Group answers by student -->
            <?php
            $answersByStudent = [];
            foreach ($answers as $answer) {
                $answersByStudent[$answer['student_id']][] = $answer;
            }

            // Create questions lookup
            $questionsMap = [];
            foreach ($questions as $question) {
                $questionsMap[$question['question_number']] = $question;
            }
            ?>

            <div class="space-y-8">
                <?php foreach ($answersByStudent as $studentId => $studentAnswers): ?>
                    <?php $student = $studentAnswers[0]; // Get student info from first answer 
                    ?>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">
                                        <?= esc($student['student_name']) ?>
                                    </h3>
                                    <p class="text-sm text-gray-600"><?= esc($student['username']) ?></p>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">
                                        Total Skor:
                                        <span class="font-medium">
                                            <?= array_sum(array_column($studentAnswers, 'final_score')) ?> /
                                            <?= array_sum(array_map(fn($a) => $questionsMap[$a['question_number']]['max_score'] ?? 0, $studentAnswers)) ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            <?php foreach ($studentAnswers as $answer): ?>
                                <?php $question = $questionsMap[$answer['question_number']] ?? null; ?>
                                <?php if ($question): ?>
                                    <div class="border border-gray-100 rounded-lg p-4" id="answer-<?= $answer['id'] ?>">
                                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            <!-- Question and Answer -->
                                            <div>
                                                <div class="mb-4">
                                                    <h4 class="font-medium text-gray-900 mb-2">
                                                        Soal <?= $answer['question_number'] ?>
                                                        <span class="text-sm text-gray-600">(Max: <?= $question['max_score'] ?> poin)</span>
                                                    </h4>
                                                    <p class="text-sm text-gray-700 bg-gray-50 p-3 rounded">
                                                        <?= esc($question['question_text']) ?>
                                                    </p>
                                                </div>

                                                <div>
                                                    <h5 class="font-medium text-gray-900 mb-2">Jawaban Siswa:</h5>
                                                    <div class="bg-blue-50 p-3 rounded text-sm text-gray-700 max-h-40 overflow-y-auto">
                                                        <?= nl2br(esc($answer['answer_text'])) ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Grading Section -->
                                            <div>
                                                <!-- AI Grading -->
                                                <?php if ($answer['ai_score'] !== null): ?>
                                                    <div class="mb-4 p-3 bg-purple-50 rounded-lg">
                                                        <h5 class="font-medium text-purple-900 mb-2">
                                                            <i class="fas fa-robot mr-1"></i>
                                                            Penilaian AI
                                                        </h5>
                                                        <div class="text-sm">
                                                            <div class="mb-2">
                                                                <span class="font-medium">Skor:</span>
                                                                <span class="text-purple-700"><?= number_format($answer['ai_score'], 1) ?></span>
                                                            </div>
                                                            <?php if ($answer['ai_feedback']): ?>
                                                                <div>
                                                                    <span class="font-medium">Feedback:</span>
                                                                    <p class="text-purple-700 mt-1"><?= esc($answer['ai_feedback']) ?></p>
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <!-- Manual Grading Form -->
                                                <div class="p-3 bg-yellow-50 rounded-lg">
                                                    <h5 class="font-medium text-yellow-900 mb-3">
                                                        <i class="fas fa-user-edit mr-1"></i>
                                                        Penilaian Manual
                                                    </h5>
                                                    <form class="manual-grade-form" data-answer-id="<?= $answer['id'] ?>">
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                Skor (0 - <?= $question['max_score'] ?>)
                                                            </label>
                                                            <input type="number"
                                                                name="manual_score"
                                                                min="0"
                                                                max="<?= $question['max_score'] ?>"
                                                                step="0.1"
                                                                value="<?= $answer['manual_score'] ?? $answer['ai_score'] ?? '' ?>"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                Feedback
                                                            </label>
                                                            <textarea name="manual_feedback"
                                                                rows="3"
                                                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                                                placeholder="Berikan feedback untuk siswa..."><?= esc($answer['manual_feedback'] ?? $answer['ai_feedback'] ?? '') ?></textarea>
                                                        </div>
                                                        <button type="submit"
                                                            class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded transition duration-200">
                                                            <i class="fas fa-save mr-2"></i>
                                                            Simpan Nilai
                                                        </button>
                                                    </form>
                                                </div>

                                                <!-- Current Final Score -->
                                                <div class="mt-3 p-3 bg-green-50 rounded-lg">
                                                    <div class="flex justify-between items-center">
                                                        <span class="font-medium text-green-900">Nilai Final:</span>
                                                        <span class="text-lg font-bold text-green-700">
                                                            <?= number_format($answer['final_score'] ?? 0, 1) ?> / <?= $question['max_score'] ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle manual grading forms
        document.querySelectorAll('.manual-grade-form').forEach(form => {
            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                const answerId = this.dataset.answerId;
                const formData = new FormData(this);
                formData.append('answer_id', answerId);
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Menyimpan...';
                submitBtn.disabled = true;

                try {
                    const response = await fetch('<?= base_url('teacher/exams/save-manual-grade') ?>', {
                        method: 'POST',
                        body: formData
                    });

                    const result = await response.json();

                    if (result.success) {
                        // Show success message
                        const answerDiv = document.getElementById(`answer-${answerId}`);
                        const successMsg = document.createElement('div');
                        successMsg.className = 'bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded text-sm mb-3';
                        successMsg.textContent = result.message;
                        answerDiv.insertBefore(successMsg, answerDiv.firstChild);

                        // Remove success message after 3 seconds
                        setTimeout(() => {
                            successMsg.remove();
                        }, 3000);

                        // Update final score display (simplified - you might want to reload or update dynamically)
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (error) {
                    alert('Terjadi kesalahan saat menyimpan nilai');
                } finally {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>