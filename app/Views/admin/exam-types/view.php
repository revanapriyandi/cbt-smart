<?php
// admin/exam-types/view.php
$this->extend('layout/main');
$this->section('title');
echo $title;
$this->endSection();
$this->section('content');
?>
<div class="p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-4">Detail Jenis Ujian</h1>
    <div class="mb-4">
        <strong>Nama:</strong> <?= esc($examType['name']) ?><br>
        <strong>Kategori:</strong> <?= esc($examType['category']) ?><br>
        <strong>Durasi:</strong> <?= esc($examType['duration_minutes']) ?> menit<br>
        <strong>Status:</strong> <?= esc($examType['status']) ?><br>
        <strong>Dibuat:</strong> <?= date('d/m/Y H:i', strtotime($examType['created_at'])) ?><br>
    </div>
    <a href="<?= base_url('admin/exam-types/edit/' . $examType['id']) ?>" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">Edit</a>
    <a href="<?= base_url('admin/exam-types') ?>" class="ml-2 text-gray-600">Kembali</a>
</div>
<?php $this->endSection(); ?>