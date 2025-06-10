<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Academic Years Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <div class="mb-6">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Academic Years Management</h1>
                <p class="mt-2 text-gray-600">Manage academic years for the system</p>
            </div>
            <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add Academic Year
            </button>
        </div>
    </div>

    <!-- Academic Years Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="academicYearsTableBody">
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?= esc($year['name']) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($year['start_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M j, Y', strtotime($year['end_date'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $year['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $year['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if ($year['is_current']): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Current
                                        </span>
                                    <?php else: ?>
                                        <button onclick="setCurrent(<?= $year['id'] ?>)" class="text-blue-600 hover:text-blue-900 text-sm">
                                            Set Current
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editAcademicYear(<?= $year['id'] ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                        Edit
                                    </button>
                                    <button onclick="deleteAcademicYear(<?= $year['id'] ?>)" class="text-red-600 hover:text-red-900">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                No academic years found. <button onclick="openCreateModal()" class="text-blue-600 hover:text-blue-900">Create the first one</button>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div id="academicYearModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4" id="modalTitle">Add Academic Year</h3>
            <form id="academicYearForm">
                <input type="hidden" id="yearId" name="id">

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" id="name" name="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g., 2023-2024">
                </div>

                <div class="mb-4">
                    <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="startDate" name="start_date" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" id="endDate" name="end_date" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" id="isActive" name="is_active" value="1"
                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submission
        document.getElementById('academicYearForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const yearId = document.getElementById('yearId').value;
            const url = yearId ? `/admin/academic-years/update/${yearId}` : '/admin/academic-years/store';

            fetch(url, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        closeModal();
                        location.reload();
                    } else {
                        showNotification(data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
        });
    });

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add Academic Year';
        document.getElementById('academicYearForm').reset();
        document.getElementById('yearId').value = '';
        document.getElementById('isActive').checked = true;
        document.getElementById('academicYearModal').classList.remove('hidden');
    }

    function editAcademicYear(id) {
        // Fetch academic year data
        fetch(`/admin/academic-years/edit/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const year = data.data;
                    document.getElementById('modalTitle').textContent = 'Edit Academic Year';
                    document.getElementById('yearId').value = year.id;
                    document.getElementById('name').value = year.name;
                    document.getElementById('startDate').value = year.start_date;
                    document.getElementById('endDate').value = year.end_date;
                    document.getElementById('isActive').checked = year.is_active == 1;
                    document.getElementById('academicYearModal').classList.remove('hidden');
                } else {
                    showNotification(data.message || 'Failed to load academic year data', 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred', 'error');
            });
    }

    function closeModal() {
        document.getElementById('academicYearModal').classList.add('hidden');
    }

    function setCurrent(id) {
        if (confirm('Are you sure you want to set this as the current academic year?')) {
            fetch(`/admin/academic-years/set-current/${id}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        location.reload();
                    } else {
                        showNotification(data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
        }
    }

    function deleteAcademicYear(id) {
        if (confirm('Are you sure you want to delete this academic year? This action cannot be undone.')) {
            fetch(`/admin/academic-years/delete/${id}`, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        location.reload();
                    } else {
                        showNotification(data.message || 'An error occurred', 'error');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred', 'error');
                });
        }
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    } text-white`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Close modal when clicking outside
    document.getElementById('academicYearModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?= $this->endSection() ?>