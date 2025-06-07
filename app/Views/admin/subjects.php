<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Subjects<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header with Statistics -->
    <div class="mb-8">
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start mb-6">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Manage Subjects</h1>
                <p class="mt-2 text-sm lg:text-base text-gray-600">Comprehensive subject management with detailed analytics</p>
            </div>
            <button onclick="openCreateModal()" class="mt-4 lg:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Subject
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Subjects</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['total'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">With Teacher</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['with_teacher'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Exams</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['total_exams'] ?? 0 ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active Exams</p>
                        <p class="text-2xl font-bold text-gray-900"><?= $statistics['active_exams'] ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?= esc($search) ?>"
                    placeholder="Search subjects by name, code, description, or teacher..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div class="lg:w-48">
                <select name="teacher_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">All Teachers</option>
                    <?php if (isset($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= $teacher['id'] ?>" <?= $teacherFilter == $teacher['id'] ? 'selected' : '' ?>>
                                <?= esc($teacher['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="lg:w-40">
                <select name="sort_by" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="created_at" <?= $sortBy === 'created_at' ? 'selected' : '' ?>>Created Date</option>
                    <option value="name" <?= $sortBy === 'name' ? 'selected' : '' ?>>Name</option>
                    <option value="code" <?= $sortBy === 'code' ? 'selected' : '' ?>>Code</option>
                    <option value="exam_count" <?= $sortBy === 'exam_count' ? 'selected' : '' ?>>Exam Count</option>
                    <option value="teacher_name" <?= $sortBy === 'teacher_name' ? 'selected' : '' ?>>Teacher</option>
                </select>
            </div>
            <div class="lg:w-32">
                <select name="sort_order" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="DESC" <?= $sortOrder === 'DESC' ? 'selected' : '' ?>>DESC</option>
                    <option value="ASC" <?= $sortOrder === 'ASC' ? 'selected' : '' ?>>ASC</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center justify-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Search
            </button>
        </form>
    </div>

    <!-- Subjects Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        <?php if (isset($subjects) && !empty($subjects)): ?>
            <?php foreach ($subjects as $subject): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-200 overflow-hidden">
                    <!-- Header -->
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between mb-4">
                            <div class="p-3 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="viewSubject(<?= $subject['id'] ?>)" class="text-green-600 hover:text-green-800 p-1" title="View Details">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button onclick="editSubject(<?= $subject['id'] ?>)" class="text-blue-600 hover:text-blue-800 p-1" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </button>
                                <button onclick="deleteSubject(<?= $subject['id'] ?>)" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-1"><?= esc($subject['name']) ?></h3>
                            <p class="text-sm text-blue-600 font-medium mb-2">Code: <?= esc($subject['code']) ?></p>
                            <p class="text-sm text-gray-600 line-clamp-2"><?= esc($subject['description']) ?: 'No description available' ?></p>
                        </div>
                    </div>

                    <!-- Teacher Info -->
                    <div class="px-6 py-3 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center">
                            <div class="p-2 rounded-full bg-gray-200">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">
                                    <?= $subject['teacher_name'] ? esc($subject['teacher_name']) : 'No teacher assigned' ?>
                                </p>
                                <?php if ($subject['teacher_email']): ?>
                                    <p class="text-xs text-gray-500"><?= esc($subject['teacher_email']) ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-2">
                                    <div class="p-2 rounded-full bg-green-100">
                                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold text-gray-900"><?= $subject['exam_count'] ?? 0 ?></p>
                                <p class="text-xs text-gray-600">Total Exams</p>
                            </div>
                            <div class="text-center">
                                <div class="flex items-center justify-center mb-2">
                                    <div class="p-2 rounded-full bg-blue-100">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p class="text-2xl font-bold text-gray-900"><?= $subject['active_exam_count'] ?? 0 ?></p>
                                <p class="text-xs text-gray-600">Active Exams</p>
                            </div>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <p class="text-lg font-semibold text-gray-900"><?= $subject['student_count'] ?? 0 ?></p>
                                    <p class="text-xs text-gray-600">Students</p>
                                </div>
                                <div>
                                    <p class="text-lg font-semibold text-gray-900">
                                        <?= $subject['average_score'] ? number_format($subject['average_score'], 1) . '%' : 'N/A' ?>
                                    </p>
                                    <p class="text-xs text-gray-600">Avg Score</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            Created: <?= date('M d, Y', strtotime($subject['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full">
                <div class="text-center py-12 bg-white rounded-xl shadow-sm border border-gray-100">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No subjects found</h3>
                    <p class="text-gray-500 mb-4">Get started by creating your first subject or adjust your search filters.</p>
                    <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        Add Subject
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager) && $pager->getPageCount() > 1): ?>
        <div class="mt-8">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit Subject Modal -->
<div id="subjectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900" id="modalTitle">Add New Subject</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <form id="subjectForm" method="POST">
            <div class="p-6 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Code *</label>
                        <input type="text" name="code" id="subjectCode" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., MTK001">
                        <p class="mt-1 text-xs text-gray-500">Unique alphanumeric code for the subject</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject Name *</label>
                        <input type="text" name="name" id="subjectName" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            placeholder="e.g., Mathematics">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Assigned Teacher</label>
                    <select name="teacher_id" id="subjectTeacher"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select a teacher (optional)</option>
                        <?php if (isset($teachers)): ?>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= $teacher['id'] ?>"><?= esc($teacher['full_name']) ?> (<?= esc($teacher['email']) ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="mt-1 text-xs text-gray-500">You can assign a teacher later if needed</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="subjectDescription" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Brief description of the subject, learning objectives, or key topics covered..."></textarea>
                </div>
            </div>

            <div class="p-6 border-t border-gray-200 flex justify-between">
                <button type="button" onclick="closeModal()" class="px-6 py-2 text-gray-600 hover:text-gray-800 font-medium">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span id="submitButtonText">Save Subject</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- View Subject Modal -->
<div id="viewSubjectModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-xl font-semibold text-gray-900">Subject Details</h3>
                <button onclick="closeViewModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="viewSubjectContent" class="p-6">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<script>
    let editingSubjectId = null;

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New Subject';
        document.getElementById('submitButtonText').textContent = 'Save Subject';
        document.getElementById('subjectForm').action = '<?= base_url('admin/subjects/store') ?>';
        document.getElementById('subjectForm').reset();
        editingSubjectId = null;
        document.getElementById('subjectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function editSubject(subjectId) {
        editingSubjectId = subjectId;
        document.getElementById('modalTitle').textContent = 'Edit Subject';
        document.getElementById('submitButtonText').textContent = 'Update Subject';
        document.getElementById('subjectForm').action = '<?= base_url('admin/subjects/update') ?>/' + subjectId;

        // Show loading state
        const form = document.getElementById('subjectForm');
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => input.disabled = true);

        // Fetch subject data
        fetch('<?= base_url('admin/subjects/get') ?>/' + subjectId)
            .then(response => response.json())
            .then(subject => {
                document.getElementById('subjectCode').value = subject.code || '';
                document.getElementById('subjectName').value = subject.name || '';
                document.getElementById('subjectDescription').value = subject.description || '';
                document.getElementById('subjectTeacher').value = subject.teacher_id || '';

                // Re-enable inputs
                inputs.forEach(input => input.disabled = false);
            })
            .catch(error => {
                console.error('Error fetching subject:', error);
                alert('Error loading subject data. Please try again.');
                inputs.forEach(input => input.disabled = false);
            });

        document.getElementById('subjectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function viewSubject(subjectId) {
        document.getElementById('viewSubjectContent').innerHTML = `
            <div class="flex items-center justify-center py-12">
                <div class="text-center">
                    <svg class="animate-spin h-8 w-8 text-blue-600 mx-auto mb-4" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-gray-600">Loading subject details...</p>
                </div>
            </div>
        `;

        fetch('<?= base_url('admin/subjects/view') ?>/' + subjectId)
            .then(response => response.text())
            .then(html => {
                document.getElementById('viewSubjectContent').innerHTML = html;
            })
            .catch(error => {
                console.error('Error fetching subject details:', error);
                document.getElementById('viewSubjectContent').innerHTML = `
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 text-red-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Error Loading Details</h3>
                        <p class="text-gray-600">Unable to load subject details. Please try again.</p>
                    </div>
                `;
            });

        document.getElementById('viewSubjectModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        document.getElementById('subjectModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function closeViewModal() {
        document.getElementById('viewSubjectModal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function deleteSubject(subjectId) {
        // Show confirmation dialog
        if (confirm('Are you sure you want to delete this subject?\n\nThis action will also delete all related exams and cannot be undone.')) {
            // Show loading state
            const button = event.target.closest('button');
            const originalContent = button.innerHTML;
            button.innerHTML = '<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
            button.disabled = true;

            // Redirect to delete
            window.location.href = '<?= base_url('admin/subjects/delete') ?>/' + subjectId;
        }
    }

    // Close modals when clicking outside
    document.getElementById('subjectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('viewSubjectModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeViewModal();
        }
    });

    // Handle form submission
    document.getElementById('subjectForm').addEventListener('submit', function(e) {
        const submitButton = document.querySelector('#subjectForm button[type="submit"]');
        const originalText = submitButton.innerHTML;

        submitButton.innerHTML = `
            <svg class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            ${editingSubjectId ? 'Updating...' : 'Saving...'}
        `;
        submitButton.disabled = true;
    });

    // Auto-focus first input when modal opens
    document.getElementById('subjectModal').addEventListener('transitionend', function() {
        if (!this.classList.contains('hidden')) {
            document.getElementById('subjectCode').focus();
        }
    });
</script>
<?= $this->endSection() ?>