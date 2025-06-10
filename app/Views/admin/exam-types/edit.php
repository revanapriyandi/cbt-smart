<?php
// admin/exam-types/edit.php
$this->extend('layout/main');
$this->section('title');
echo $title;
$this->endSection();
$this->section('content');
?>
<div class="p-4 sm:p-6">
    <h1 class="text-2xl font-bold mb-4">Edit Jenis Ujian</h1>
    <form id="editExamTypeForm" action="<?= base_url('admin/exam-types/update/' . $examType['id']) ?>" method="POST">
        <?= csrf_field() ?>
        <div class="mb-4">
            <label for="name" class="block mb-1">Nama Jenis Ujian</label>
            <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" value="<?= esc($examType['name']) ?>" required>
        </div>
        <div class="mb-4">
            <label for="category" class="block mb-1">Kategori</label>
            <select name="category" id="category" class="w-full border rounded px-3 py-2" required>
                <option value="">Pilih Kategori</option>
                <option value="daily" <?= $examType['category'] === 'daily' ? 'selected' : '' ?>>Harian</option>
                <option value="mid_semester" <?= $examType['category'] === 'mid_semester' ? 'selected' : '' ?>>UTS</option>
                <option value="final_semester" <?= $examType['category'] === 'final_semester' ? 'selected' : '' ?>>UAS</option>
                <option value="national" <?= $examType['category'] === 'national' ? 'selected' : '' ?>>Ujian Nasional</option>
                <option value="practice" <?= $examType['category'] === 'practice' ? 'selected' : '' ?>>Latihan</option>
                <option value="simulation" <?= $examType['category'] === 'simulation' ? 'selected' : '' ?>>Simulasi</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="duration_minutes" class="block mb-1">Durasi (menit)</label>
            <input type="number" name="duration_minutes" id="duration_minutes" class="w-full border rounded px-3 py-2" min="1" max="480" value="<?= esc($examType['duration_minutes']) ?>" required>
        </div>
        <div class="mb-4">
            <label for="status" class="block mb-1">Status</label>
            <select name="status" id="status" class="w-full border rounded px-3 py-2" required>
                <option value="active" <?= $examType['status'] === 'active' ? 'selected' : '' ?>>Aktif</option>
                <option value="inactive" <?= $examType['status'] === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Update</button>
        <a href="<?= base_url('admin/exam-types') ?>" class="ml-2 text-gray-600">Batal</a>
    </form>
</div>
<?php $this->endSection(); ?>