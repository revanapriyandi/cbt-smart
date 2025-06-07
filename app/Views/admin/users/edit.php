<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Edit User<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div>
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit User</h1>
            <p class="mt-2 text-sm lg:text-base text-gray-600">Update user information</p>
        </div>
        <div>
            <a href="<?= base_url('admin/users') ?>" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium flex items-center justify-center text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
            </a>
        </div>
    </div>

    <!-- Edit User Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6">
            <form id="editUserForm" action="<?= base_url('admin/users/update/' . ($user['id'] ?? '')) ?>" method="POST">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Username *</label>
                        <input type="text" name="username" id="username" value="<?= esc($user['username'] ?? '') ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Must be unique and contain only letters, numbers, and underscores</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="full_name" id="full_name" value="<?= esc($user['full_name'] ?? '') ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input type="email" name="email" id="email" value="<?= esc($user['email'] ?? '') ?>" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role *</label>
                        <select name="role" id="role" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            <option value="">Select Role</option>
                            <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="teacher" <?= ($user['role'] ?? '') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                            <option value="student" <?= ($user['role'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to keep current password</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                        <input type="password" name="password_confirm" id="password_confirm"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active"
                            <?= ($user['is_active'] ?? 1) ? 'checked' : '' ?>
                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <label class="ml-2 block text-sm text-gray-700">Active User</label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Inactive users cannot log in to the system</p>
                </div>

                <div class="mt-6 bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Account Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                        <div>
                            <span class="font-medium">Created:</span>
                            <?= isset($user['created_at']) ? date('M j, Y g:i A', strtotime($user['created_at'])) : 'N/A' ?>
                        </div>
                        <div>
                            <span class="font-medium">Last Updated:</span>
                            <?= isset($user['updated_at']) ? date('M j, Y g:i A', strtotime($user['updated_at'])) : 'N/A' ?>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="<?= base_url('admin/users') ?>" class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;

        if (password && password !== passwordConfirm) {
            e.preventDefault();
            alert('Passwords do not match');
            return false;
        }

        if (password && password.length < 8) {
            e.preventDefault();
            alert('Password must be at least 8 characters long');
            return false;
        }
    });
</script>
<?= $this->endSection() ?>