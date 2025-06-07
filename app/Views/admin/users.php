<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Include DataTables CSS and JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Manage Users</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Comprehensive user management with role separation</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <button onclick="openImportModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                </svg>
                Import CSV
            </button>
            <button onclick="exportUsers()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Export CSV
            </button>
            <button onclick="openCreateModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Total Users</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-users"><?= $totalUsers ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.031 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Admins</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-admins"><?= $totalAdmins ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Teachers</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-teachers"><?= $totalTeachers ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Students</div>
                    <div class="text-2xl font-bold text-gray-900" id="total-students"><?= $totalStudents ?? 0 ?></div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-sm font-medium text-gray-500">Active</div>
                    <div class="text-2xl font-bold text-gray-900" id="active-users"><?= $activeUsers ?? 0 ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Tabs and Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-6">
        <!-- Role Tabs -->
        <div class="border-b border-gray-200 mb-4">
            <nav class="-mb-px flex space-x-8">
                <button onclick="filterByRole('')" id="tab-all" class="role-tab border-b-2 border-indigo-500 py-2 px-1 text-sm font-medium text-indigo-600">
                    All Users
                </button>
                <button onclick="filterByRole('admin')" id="tab-admin" class="role-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Admins
                </button>
                <button onclick="filterByRole('teacher')" id="tab-teacher" class="role-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Teachers
                </button>
                <button onclick="filterByRole('student')" id="tab-student" class="role-tab border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                    Students
                </button>
            </nav>
        </div>

        <!-- Filters and Actions -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
            <div class="flex flex-col sm:flex-row gap-3 flex-1 max-w-md">
                <input type="text" id="search-input" placeholder="Search users..." 
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <div id="bulk-actions" class="hidden">
                    <select id="bulk-action-select" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                        <option value="">Bulk Actions</option>
                        <option value="activate">Activate Selected</option>
                        <option value="deactivate">Deactivate Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button onclick="performBulkAction()" class="ml-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-sm">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table id="users-table" class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New User</h3>
        </div>
        <form id="userForm">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                    <input type="text" name="username" id="userName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" name="full_name" id="userFullName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="userEmail" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="userRole" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="userPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (for edit)</p>
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="userActive" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <label class="ml-2 block text-sm text-gray-700">Active</label>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Import CSV Modal -->
<div id="importModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Import Users from CSV</h3>
        </div>
        <form id="importForm" enctype="multipart/form-data">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">CSV File</label>
                    <input type="file" name="csv_file" id="csvFile" accept=".csv" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <p class="text-xs text-gray-500 mt-1">CSV format: username, email, full_name, role</p>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">CSV Format Requirements:</h4>
                    <ul class="text-xs text-blue-700 space-y-1">
                        <li>• Header row: username, email, full_name, role</li>
                        <li>• Role must be: admin, teacher, or student</li>
                        <li>• Default password: 'default123' (users should change it)</li>
                        <li>• All users will be set as active by default</li>
                    </ul>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                    Import
                </button>
            </div>
        </form>
    </div>
</div>


<script>
let currentRole = '';
let usersTable;
let editingUserId = null;

$(document).ready(function() {
    // Check if DataTables is available
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded!');
        return;
    }

    // Initialize DataTable
    usersTable = $('#users-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= base_url('admin/users-data') ?>',
            data: function(d) {
                d.role = currentRole;
                d.search = $('#search-input').val();
            },
            error: function(xhr, error, code) {
                console.error('DataTables AJAX error:', error, code);
                console.log('Response:', xhr.responseText);
            }
        },        columns: [
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' + data + '">';
                }
            },
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    const initial = row.full_name ? row.full_name.charAt(0).toUpperCase() : 'U';
                    return '<div class="flex items-center">' +
                        '<div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">' +
                        '<span class="text-white font-medium text-sm">' + initial + '</span>' +
                        '</div>' +
                        '<div class="ml-4">' +
                        '<div class="text-sm font-medium text-gray-900">' + (row.full_name || '') + '</div>' +
                        '<div class="text-sm text-gray-500">' + (row.email || '') + '</div>' +
                        '<div class="text-xs text-gray-400">@' + (row.username || '') + '</div>' +
                        '</div>' +
                        '</div>';
                }
            },
            {
                data: 'role',
                render: function(data, type, row) {
                    const colors = {
                        'admin': 'bg-red-100 text-red-800',
                        'teacher': 'bg-blue-100 text-blue-800',
                        'student': 'bg-green-100 text-green-800'
                    };
                    return '<span class="px-2 py-1 text-xs font-medium rounded-full ' + (colors[data] || 'bg-gray-100 text-gray-800') + '">' +
                        (data ? data.charAt(0).toUpperCase() + data.slice(1) : '') +
                        '</span>';
                }
            },
            {
                data: 'is_active',
                render: function(data, type, row) {
                    const isActive = data == 1;
                    return '<span class="px-2 py-1 text-xs font-medium rounded-full ' +
                        (isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + '">' +
                        (isActive ? 'Active' : 'Inactive') +
                        '</span>';
                }
            },
            {
                data: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '';
                }
            },
            {
                data: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<div class="flex space-x-2">' +
                        '<button onclick="editUser(' + data + ')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</button>' +
                        '<button onclick="deleteUser(' + data + ')" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>' +
                        '</div>';
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        dom: 'rtip', // Remove default search and length controls since we have custom ones
        language: {
            processing: "Loading users...",
            emptyTable: "No users found",
            info: "Showing _START_ to _END_ of _TOTAL_ users",
            paginate: {
                first: "First",
                last: "Last",
                next: "Next",
                previous: "Previous"
            }
        }
    });

    // Search functionality
    $('#search-input').on('keyup', function() {
        usersTable.ajax.reload();
    });

    // Select all checkbox
    $('#select-all').on('change', function() {
        $('.user-checkbox').prop('checked', $(this).prop('checked'));
        toggleBulkActions();
    });

    // Individual checkbox change
    $(document).on('change', '.user-checkbox', function() {
        toggleBulkActions();
    });

    // User form submission
    $('#userForm').on('submit', function(e) {
        e.preventDefault();
        saveUser();
    });

    // Import form submission
    $('#importForm').on('submit', function(e) {
        e.preventDefault();
        importUsers();
    });
});

function filterByRole(role) {
    currentRole = role;
    
    // Update tab styles
    $('.role-tab').removeClass('border-indigo-500 text-indigo-600').addClass('border-transparent text-gray-500');
    $('#tab-' + (role || 'all')).removeClass('border-transparent text-gray-500').addClass('border-indigo-500 text-indigo-600');
    
    // Reload table
    usersTable.ajax.reload();
}

function toggleBulkActions() {
    const checkedBoxes = $('.user-checkbox:checked').length;
    if (checkedBoxes > 0) {
        $('#bulk-actions').removeClass('hidden');
    } else {
        $('#bulk-actions').addClass('hidden');
    }
}

function openCreateModal() {
    editingUserId = null;
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('userForm').reset();
    document.getElementById('userPassword').required = true;
    document.getElementById('userActive').checked = true;
    document.getElementById('userModal').classList.remove('hidden');
}

function editUser(userId) {
    editingUserId = userId;
    document.getElementById('modalTitle').textContent = 'Edit User';
    document.getElementById('userPassword').required = false;

    // Fetch user data
    fetch('<?= base_url('admin/users/get') ?>/' + userId)
        .then(response => response.json())
        .then(user => {
            document.getElementById('userName').value = user.username || '';
            document.getElementById('userFullName').value = user.full_name || '';
            document.getElementById('userEmail').value = user.email || '';
            document.getElementById('userRole').value = user.role || '';
            document.getElementById('userActive').checked = user.is_active == 1;
        })
        .catch(error => {
            console.error('Error fetching user:', error);
            alert('Error loading user data');
        });

    document.getElementById('userModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('userModal').classList.add('hidden');
}

function saveUser() {
    const formData = new FormData(document.getElementById('userForm'));
    const url = editingUserId ? 
        '<?= base_url('admin/users/update') ?>/' + editingUserId : 
        '<?= base_url('admin/users/store') ?>';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeModal();
            usersTable.ajax.reload();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error saving user', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error saving user', 'error');
    });
}

function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user?')) {
        fetch('<?= base_url('admin/users/delete') ?>/' + userId, {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                usersTable.ajax.reload();
                showNotification(data.message, 'success');
            } else {
                showNotification(data.message || 'Error deleting user', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error deleting user', 'error');
        });
    }
}

function performBulkAction() {
    const action = document.getElementById('bulk-action-select').value;
    const userIds = $('.user-checkbox:checked').map(function() {
        return $(this).val();
    }).get();

    if (!action) {
        alert('Please select an action');
        return;
    }

    if (userIds.length === 0) {
        alert('Please select users');
        return;
    }

    if (action === 'delete' && !confirm('Are you sure you want to delete the selected users?')) {
        return;
    }

    const formData = new FormData();
    formData.append('action', action);
    formData.append('user_ids', JSON.stringify(userIds));

    fetch('<?= base_url('admin/users/bulk-action') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            usersTable.ajax.reload();
            showNotification(data.message, 'success');
            $('#select-all').prop('checked', false);
            toggleBulkActions();
        } else {
            showNotification(data.message || 'Error performing bulk action', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error performing bulk action', 'error');
    });
}

    function exportUsers() {
        const role = currentRole;
        const url = '<?= base_url('admin/users/export') ?>' + (role ? '?role=' + role : '');
        window.location.href = url;
    }

    function openImportModal() {
        document.getElementById('importModal').classList.remove('hidden');
    }

    function closeImportModal() {
        document.getElementById('importModal').classList.add('hidden');
    }

    function importUsers() {
        const formData = new FormData(document.getElementById('importForm'));

        fetch('<?= base_url('admin/users/import') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeImportModal();
                    usersTable.ajax.reload();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error importing users', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error importing users', 'error');
            });
    }

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 shadow-lg rounded-lg p-4 ${
        type === 'success' ? 'border-green-400' : 'border-red-400'
    }`;

        notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 ${type === 'success' ? 'text-green-400' : 'text-red-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;

        document.body.appendChild(notification);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }

    // Close modals when clicking outside
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });

    document.getElementById('importModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeImportModal();
        }    });
</script>
<script>
    let currentRole = '';
    let usersTable;
    let editingUserId = null;

    $(document).ready(function() {
        // Check if DataTables is available
        if (typeof $.fn.DataTable === 'undefined') {
            console.error('DataTables library not loaded!');
            return;
        }

        // Initialize DataTable
        usersTable = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('admin/users-data') ?>',
                data: function(d) {
                    d.role = currentRole;
                    d.search = $('#search-input').val();
                },
                error: function(xhr, error, code) {
                    console.error('DataTables AJAX error:', error, code);
                    console.log('Response:', xhr.responseText);
                }
            },
            columns: [{
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<input type="checkbox" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" value="' + data + '">';
                    }
                },
                {
                    data: null,
                    orderable: false,
                    render: function(data, type, row) {
                        const initial = row.full_name ? row.full_name.charAt(0).toUpperCase() : 'U';
                        return '<div class="flex items-center">' +
                            '<div class="w-10 h-10 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center">' +
                            '<span class="text-white font-medium text-sm">' + initial + '</span>' +
                            '</div>' +
                            '<div class="ml-4">' +
                            '<div class="text-sm font-medium text-gray-900">' + (row.full_name || '') + '</div>' +
                            '<div class="text-sm text-gray-500">' + (row.email || '') + '</div>' +
                            '<div class="text-xs text-gray-400">@' + (row.username || '') + '</div>' +
                            '</div>' +
                            '</div>';
                    }
                },
                {
                    data: 'role',
                    render: function(data, type, row) {
                        const colors = {
                            'admin': 'bg-red-100 text-red-800',
                            'teacher': 'bg-blue-100 text-blue-800',
                            'student': 'bg-green-100 text-green-800'
                        };
                        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' + (colors[data] || 'bg-gray-100 text-gray-800') + '">' +
                            (data ? data.charAt(0).toUpperCase() + data.slice(1) : '') +
                            '</span>';
                    }
                },
                {
                    data: 'is_active',
                    render: function(data, type, row) {
                        const isActive = data == 1;
                        return '<span class="px-2 py-1 text-xs font-medium rounded-full ' +
                            (isActive ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') + '">' +
                            (isActive ? 'Active' : 'Inactive') +
                            '</span>';
                    }
                },
                {
                    data: 'created_at',
                    render: function(data, type, row) {
                        return data ? new Date(data).toLocaleDateString() : '';
                    }
                },
                {
                    data: 'id',
                    orderable: false,
                    searchable: false,
                    render: function(data, type, row) {
                        return '<div class="flex space-x-2">' +
                            '<button onclick="editUser(' + data + ')" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Edit</button>' +
                            '<button onclick="deleteUser(' + data + ')" class="text-red-600 hover:text-red-900 text-sm font-medium">Delete</button>' +
                            '</div>';
                    }
                }
            ],
            pageLength: 25,
            lengthMenu: [
                [10, 25, 50, 100],
                [10, 25, 50, 100]
            ],
            responsive: true,
            dom: 'rtip', // Remove default search and length controls since we have custom ones
            language: {
                processing: "Loading users...",
                emptyTable: "No users found",
                info: "Showing _START_ to _END_ of _TOTAL_ users",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        }); // Search functionality
        $('#search-input').on('keyup', function() {
            const searchValue = $(this).val();
            usersTable.search(searchValue).draw();
        });

        // Select all checkbox
        $('#select-all').on('change', function() {
            $('.user-checkbox').prop('checked', $(this).prop('checked'));
            toggleBulkActions();
        });

        // Individual checkbox change
        $(document).on('change', '.user-checkbox', function() {
            toggleBulkActions();
        });

        // User form submission
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            saveUser();
        });

        // Import form submission
        $('#importForm').on('submit', function(e) {
            e.preventDefault();
            importUsers();
        });
    });

    function filterByRole(role) {
        currentRole = role;

        // Update tab styles
        $('.role-tab').removeClass('border-indigo-500 text-indigo-600').addClass('border-transparent text-gray-500');
        $('#tab-' + (role || 'all')).removeClass('border-transparent text-gray-500').addClass('border-indigo-500 text-indigo-600');

        // Reload table
        usersTable.ajax.reload();
    }

    function toggleBulkActions() {
        const checkedBoxes = $('.user-checkbox:checked').length;
        if (checkedBoxes > 0) {
            $('#bulk-actions').removeClass('hidden');
        } else {
            $('#bulk-actions').addClass('hidden');
        }
    }

    function openCreateModal() {
        editingUserId = null;
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').reset();
        document.getElementById('userPassword').required = true;
        document.getElementById('userActive').checked = true;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(userId) {
        editingUserId = userId;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userPassword').required = false;

        // Fetch user data
        fetch('<?= base_url('admin/users/get') ?>/' + userId)
            .then(response => response.json())
            .then(user => {
                document.getElementById('userName').value = user.username || '';
                document.getElementById('userFullName').value = user.full_name || '';
                document.getElementById('userEmail').value = user.email || '';
                document.getElementById('userRole').value = user.role || '';
                document.getElementById('userActive').checked = user.is_active == 1;
            })
            .catch(error => {
                console.error('Error fetching user:', error);
                alert('Error loading user data');
            });

        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function saveUser() {
        const formData = new FormData(document.getElementById('userForm'));
        const url = editingUserId ?
            '<?= base_url('admin/users/update') ?>/' + editingUserId :
            '<?= base_url('admin/users/store') ?>';

        fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeModal();
                    usersTable.ajax.reload();
                    showNotification(data.message, 'success');
                } else {
                    showNotification(data.message || 'Error saving user', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error saving user', 'error');
            });
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            fetch('<?= base_url('admin/users/delete') ?>/' + userId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        usersTable.ajax.reload();
                        showNotification(data.message, 'success');
                    } else {
                        showNotification(data.message || 'Error deleting user', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Error deleting user', 'error');
                });
        }
    }

    function performBulkAction() {
        const action = document.getElementById('bulk-action-select').value;
        const userIds = $('.user-checkbox:checked').map(function() {
            return $(this).val();
        }).get();

        if (!action) {
            alert('Please select an action');
            return;
        }

        if (userIds.length === 0) {
            alert('Please select users');
            return;
        }

        if (action === 'delete' && !confirm('Are you sure you want to delete the selected users?')) {
            return;
        }

        const formData = new FormData();
        formData.append('action', action);
        formData.append('user_ids', JSON.stringify(userIds));

        fetch('<?= base_url('admin/users/bulk-action') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    usersTable.ajax.reload();
                    showNotification(data.message, 'success');
                    $('#select-all').prop('checked', false);
                    toggleBulkActions();
                } else {
                    showNotification(data.message || 'Error performing bulk action', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Error performing bulk action', 'error');
            });
    }

function exportUsers() {
    const role = currentRole;
    const url = '<?= base_url('admin/users/export') ?>' + (role ? '?role=' + role : '');
    window.location.href = url;
}

function openImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
}

function importUsers() {
    const formData = new FormData(document.getElementById('importForm'));

    fetch('<?= base_url('admin/users/import') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeImportModal();
            usersTable.ajax.reload();
            showNotification(data.message, 'success');
        } else {
            showNotification(data.message || 'Error importing users', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error importing users', 'error');
    });
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 max-w-sm w-full bg-white border-l-4 shadow-lg rounded-lg p-4 ${
        type === 'success' ? 'border-green-400' : 'border-red-400'
    }`;
    
    notification.innerHTML = `
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 ${type === 'success' ? 'text-green-400' : 'text-red-400'}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' ? 
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-gray-400 hover:text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Close modals when clicking outside
document.getElementById('userModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<?= $this->endSection() ?>