<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Manage Users<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 sm:mb-6 space-y-3 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Manage Users</h1>
            <p class="mt-2 text-sm sm:text-base text-gray-600">Kelola pengguna sistem CBT Smart</p>
        </div>
        <button onclick="openCreateModal()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm sm:text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add User
        </button>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 p-4 mb-4 sm:mb-6">
        <form method="GET" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="<?= esc($search ?? '') ?>" placeholder="Search users..."
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
            </div>
            <div class="sm:w-40">
                <select name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                    <option value="">All Roles</option>
                    <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="teacher" <?= ($role ?? '') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                    <option value="student" <?= ($role ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                </select>
            </div>
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium text-sm sm:text-base">
                Filter
            </button>
        </form>
    </div>

    <!-- Users Table/Cards -->
    <div class="bg-white rounded-lg sm:rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($users) && !empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium text-sm"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($user['full_name']) ?></div>
                                            <div class="text-sm text-gray-500"><?= esc($user['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= ($user['is_active'] ?? true) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= ($user['is_active'] ?? true) ? 'Active' : 'Inactive' ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button onclick="editUser(<?= $user['id'] ?>)" class="text-blue-600 hover:text-blue-900 mr-3">Edit</button>
                                    <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:text-red-900">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                No users found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden">
            <?php if (isset($users) && !empty($users)): ?>
                <div class="divide-y divide-gray-200">
                    <?php foreach ($users as $user): ?>
                        <div class="p-4">
                            <div class="flex items-start space-x-3">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <span class="text-white font-medium"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-sm font-medium text-gray-900 truncate"><?= esc($user['full_name']) ?></h3>
                                            <p class="text-sm text-gray-500 truncate"><?= esc($user['email']) ?></p>
                                        </div>
                                        <div class="flex space-x-2 ml-2">
                                            <button onclick="editUser(<?= $user['id'] ?>)" class="text-blue-600 hover:text-blue-900 text-sm">Edit</button>
                                            <button onclick="deleteUser(<?= $user['id'] ?>)" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                                        </div>
                                    </div>
                                    <div class="mt-2 flex items-center space-x-3">
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= ($user['is_active'] ?? true) ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                            <?= ($user['is_active'] ?? true) ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        Created: <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    <p>No users found</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pagination -->
    <?php if (isset($pager)): ?>
        <div class="mt-4 sm:mt-6">
            <?= $pager->links() ?>
        </div>
    <?php endif; ?>
</div>

<!-- Create/Edit User Modal -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg sm:rounded-xl shadow-xl max-w-md w-full">
        <div class="p-4 sm:p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Add New User</h3>
        </div>
        <form id="userForm" method="POST">
            <div class="p-4 sm:p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" id="userName" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="userEmail" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="userRole" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                <div id="passwordField">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" id="userPassword"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm sm:text-base">
                    <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password (for edit)</p>
                </div>
            </div>
            <div class="p-4 sm:p-6 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">Cancel</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">Save</button>
            </div>
        </form>
    </div>
</div>

<script>
    let editingUserId = null;

    function openCreateModal() {
        document.getElementById('modalTitle').textContent = 'Add New User';
        document.getElementById('userForm').action = '<?= base_url('admin/users/store') ?>';
        document.getElementById('userForm').reset();
        document.getElementById('userPassword').required = true;
        editingUserId = null;
        document.getElementById('userModal').classList.remove('hidden');
    }

    function editUser(userId) {
        editingUserId = userId;
        document.getElementById('modalTitle').textContent = 'Edit User';
        document.getElementById('userForm').action = '<?= base_url('admin/users/update') ?>/' + userId;
        document.getElementById('userPassword').required = false;

        // Fetch user data (you'll need to implement this endpoint)
        fetch('<?= base_url('admin/users/get') ?>/' + userId)
            .then(response => response.json())
            .then(user => {
                document.getElementById('userName').value = user.name;
                document.getElementById('userEmail').value = user.email;
                document.getElementById('userRole').value = user.role;
            });

        document.getElementById('userModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('userModal').classList.add('hidden');
    }

    function deleteUser(userId) {
        if (confirm('Are you sure you want to delete this user?')) {
            window.location.href = '<?= base_url('admin/users/delete') ?>/' + userId;
        }
    }

    // Close modal when clicking outside
    document.getElementById('userModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
<?= $this->endSection() ?>