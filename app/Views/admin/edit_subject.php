<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit Subject<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Subject</h1>
            <p class="text-gray-600">Update subject information</p>
        </div>
        <a href="<?= base_url('admin/subjects') ?>"
            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium">
            Back to Subjects
        </a>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
        <form method="POST" action="<?= base_url('admin/edit-subject/' . $subject['id']) ?>" class="p-6 space-y-6">
            <?= csrf_field() ?>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Subject Name -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                    <input type="text" name="name" required value="<?= old('name', $subject['name']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Mathematics">
                </div>

                <!-- Subject Code -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                    <input type="text" name="code" required value="<?= old('code', $subject['code']) ?>"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., MATH101">
                </div>

                <!-- Teacher Assignment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assign Teacher</label>
                    <select name="teacher_id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">No teacher assigned</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>"
                                <?= old('teacher_id', $subject['teacher_id']) == $teacher['id'] ? 'selected' : '' ?>>
                                <?= esc($teacher['full_name']) ?> (<?= esc($teacher['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Description -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Brief description of the subject"><?= old('description', $subject['description']) ?></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                <a href="<?= base_url('admin/subjects') ?>"
                    class="px-6 py-2 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    Update Subject
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>