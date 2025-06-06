<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Subjects<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Manage Subjects</h1>
            <p class="mt-2 text-gray-600">Kelola mata pelajaran dalam sistem CBT</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Subject
        </button>
    </div>

    <!-- Subjects Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (isset($subjects) && !empty($subjects)): ?>
            <?php foreach ($subjects as $subject): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between mb-4">
                        <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editSubject(<?= $subject['id'] ?>)" class="text-blue-600 hover:text-blue-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </button>
                            <button onclick="deleteSubject(<?= $subject['id'] ?>)" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2"><?= esc($subject['name']) ?></h3>
                        <p class="text-sm text-gray-600 mb-4"><?= esc($subject['description']) ?></p>

                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span>Code: <?= esc($subject['code']) ?></span>
                            <span><?= isset($subject['exam_count']) ? $subject['exam_count'] : 0 ?> exams</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full text-center py-12">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No subjects found</h3>
                <p class="text-gray-500">Get started by creating your first subject.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create/Edit Subject Modal -->
<div id="subjectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full mx-4">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New Subject</h3>
        </div>
        <form id="subjectForm" method="POST">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code</label>
                    <input type="text" name="code" id="subjectCode" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., MTK001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name</label>
                    <input type="text" name="name" id="subjectName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., Matematika Dasar">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="subjectDescription" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Brief description of the subject"></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    let editingSubjectId = null;

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New Subject';
        document.getElementById('subjectForm').action = '<?= base_url('admin/subjects/store') ?>';
        document.getElementById('subjectForm').reset();
        editingSubjectId = null;
        document.getElementById('subjectModal').classList.remove('hidden');
    }

    function editSubject(subjectId) {
        editingSubjectId = subjectId;
        document.getElementById('modalTitle').textContent = 'Edit Subject';
        document.getElementById('subjectForm').action = '<?= base_url('admin/subjects/update') ?>/' + subjectId;

        // Fetch subject data
        fetch('<?= base_url('admin/subjects/get') ?>/' + subjectId)
            .then(response => response.json())
            .then(subject => {
                document.getElementById('subjectCode').value = subject.code;
                document.getElementById('subjectName').value = subject.name;
                document.getElementById('subjectDescription').value = subject.description;
            });

        document.getElementById('subjectModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('subjectModal').classList.add('hidden');
    }

    function deleteSubject(subjectId) {
        if (confirm('Are you sure you want to delete this subject? This will also delete all related exams.')) {
            window.location.href = '<?= base_url('admin/subjects/delete') ?>/' + subjectId;
        }
    }

    // Close modal when clicking outside
    document.getElementById('subjectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?= $this->endSection() ?>