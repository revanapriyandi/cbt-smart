<div class="row">
    <!-- Basic Information -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-info-circle me-2"></i>
                    Informasi Bank Soal
                </h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr>
                        <td width="30%" class="fw-bold">Nama Bank Soal:</td>
                        <td><?= esc($questionBank['name']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Mata Pelajaran:</td>
                        <td>
                            <span class="badge bg-info rounded-pill px-3 py-2">
                                <i class="fas fa-book me-1"></i>
                                <?= esc($questionBank['subject_name']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Jenis Ujian:</td>
                        <td>
                            <span class="badge bg-warning rounded-pill px-3 py-2">
                                <i class="fas fa-clipboard-list me-1"></i>
                                <?= esc($questionBank['exam_type_name']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Tingkat Kesulitan:</td>
                        <td>
                            <?php
                            $levels = [
                                'easy' => ['class' => 'success', 'icon' => 'smile', 'text' => 'Mudah'],
                                'medium' => ['class' => 'warning', 'icon' => 'meh', 'text' => 'Sedang'],
                                'hard' => ['class' => 'danger', 'icon' => 'frown', 'text' => 'Sulit']
                            ];
                            $level = $levels[$questionBank['difficulty_level']] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $questionBank['difficulty_level']];
                            ?>
                            <span class="badge bg-<?= $level['class'] ?> rounded-pill px-3 py-2">
                                <i class="fas fa-<?= $level['icon'] ?> me-1"></i>
                                <?= $level['text'] ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Status:</td>
                        <td>
                            <?php
                            $statuses = [
                                'active' => ['class' => 'success', 'icon' => 'check-circle', 'text' => 'Aktif'],
                                'draft' => ['class' => 'warning', 'icon' => 'edit', 'text' => 'Draft'],
                                'archived' => ['class' => 'secondary', 'icon' => 'archive', 'text' => 'Arsip']
                            ];
                            $status = $statuses[$questionBank['status']] ?? ['class' => 'secondary', 'icon' => 'question', 'text' => $questionBank['status']];
                            ?>
                            <span class="badge bg-<?= $status['class'] ?> rounded-pill px-3 py-2">
                                <i class="fas fa-<?= $status['icon'] ?> me-1"></i>
                                <?= $status['text'] ?>
                            </span>
                        </td>
                    </tr>
                    <?php if (!empty($questionBank['description'])): ?>
                        <tr>
                            <td class="fw-bold">Deskripsi:</td>
                            <td><?= nl2br(esc($questionBank['description'])) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($questionBank['instructions'])): ?>
                        <tr>
                            <td class="fw-bold">Instruksi:</td>
                            <td><?= nl2br(esc($questionBank['instructions'])) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="fw-bold">Dibuat Oleh:</td>
                        <td><?= esc($questionBank['created_by_name']) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Dibuat Pada:</td>
                        <td><?= date('d/m/Y H:i', strtotime($questionBank['created_at'])) ?></td>
                    </tr>
                    <tr>
                        <td class="fw-bold">Terakhir Diupdate:</td>
                        <td><?= date('d/m/Y H:i', strtotime($questionBank['updated_at'])) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistik
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="h4 text-primary"><?= $questionBank['question_count'] ?? 0 ?></div>
                        <div class="small text-muted">Total Soal</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="h4 text-success"><?= $questionBank['used_count'] ?? 0 ?></div>
                        <div class="small text-muted">Digunakan</div>
                    </div>
                </div>
                <div class="progress mb-3" style="height: 6px;">
                    <?php
                    $usagePercentage = ($questionBank['question_count'] > 0) ?
                        round(($questionBank['used_count'] / $questionBank['question_count']) * 100, 1) : 0;
                    ?>
                    <div class="progress-bar bg-success" role="progressbar"
                        style="width: <?= $usagePercentage ?>%"
                        aria-valuenow="<?= $usagePercentage ?>"
                        aria-valuemin="0"
                        aria-valuemax="100">
                    </div>
                </div>
                <div class="small text-center text-muted">
                    Tingkat Penggunaan: <?= $usagePercentage ?>%
                </div>
            </div>
        </div>

        <!-- Configuration -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-cog me-2"></i>
                    Konfigurasi
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-clock text-primary me-2"></i>
                        <strong>Waktu per Soal:</strong>
                        <?= $questionBank['time_per_question'] ? $questionBank['time_per_question'] . ' detik' : 'Tidak dibatasi' ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-minus-circle text-danger me-2"></i>
                        <strong>Penilaian Negatif:</strong>
                        <?= $questionBank['negative_marking'] ? 'Ya' : 'Tidak' ?>
                        <?php if ($questionBank['negative_marking'] && $questionBank['negative_marks']): ?>
                            (<?= $questionBank['negative_marks'] ?> poin)
                        <?php endif; ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-random text-warning me-2"></i>
                        <strong>Acak Soal:</strong>
                        <?= $questionBank['randomize_questions'] ? 'Ya' : 'Tidak' ?>
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-eye text-info me-2"></i>
                        <strong>Tampilkan Jawaban:</strong>
                        <?= $questionBank['show_correct_answer'] ? 'Ya' : 'Tidak' ?>
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-calculator text-success me-2"></i>
                        <strong>Kalkulator:</strong>
                        <?= $questionBank['allow_calculator'] ? 'Diizinkan' : 'Tidak diizinkan' ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Questions Preview -->
<?php if (!empty($questions)): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-question-circle me-2"></i>
                        Pratinjau Soal (<?= count($questions) ?> dari <?= $questionBank['question_count'] ?>)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Soal</th>
                                    <th width="15%">Tipe</th>
                                    <th width="10%">Poin</th>
                                    <th width="15%">Digunakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($questions as $index => $question): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <div class="question-preview">
                                                <?= mb_substr(strip_tags($question['question_text']), 0, 100) . (mb_strlen(strip_tags($question['question_text'])) > 100 ? '...' : '') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                <?= ucfirst($question['question_type']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary rounded-pill">
                                                <?= $question['points'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $question['usage_count'] > 0 ? 'success' : 'light text-dark' ?> rounded-pill">
                                                <?= $question['usage_count'] ?> kali
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($questionBank['question_count'] > count($questions)): ?>
                        <div class="text-center mt-3">
                            <a href="<?= base_url('admin/questions?bank_id=' . $questionBank['id']) ?>"
                                class="btn btn-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Lihat Semua Soal
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>
                Bank soal ini belum memiliki soal.
                <a href="<?= base_url('admin/questions?bank_id=' . $questionBank['id']) ?>" class="alert-link">
                    Tambahkan soal sekarang
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($questionBank['tags'])): ?>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tags me-2"></i>
                        Tags
                    </h6>
                </div>
                <div class="card-body">
                    <?php
                    $tags = explode(',', $questionBank['tags']);
                    foreach ($tags as $tag):
                        $tag = trim($tag);
                        if (!empty($tag)):
                    ?>
                            <span class="badge bg-light text-dark me-2 mb-2"><?= esc($tag) ?></span>
                    <?php
                        endif;
                    endforeach;
                    ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<style>
    .question-preview {
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .table-sm td {
        padding: 0.5rem;
    }

    .progress {
        background-color: #e9ecef;
    }

    .card-header {
        border-bottom: none;
    }

    .badge {
        font-size: 0.8rem;
    }
</style>