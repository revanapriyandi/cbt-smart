<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-6 space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Admin Dashboard</h1>
            <p class="mt-2 text-gray-600">Selamat datang di panel administrasi CBT Smart</p>
            <p class="text-sm text-gray-500"><?= date('l, d F Y') ?></p>
        </div>
        <div class="mt-4 md:mt-0 flex space-x-3">
            <a href="<?= base_url('admin/users/create') ?>" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add User
            </a>
            <a href="<?= base_url('admin/exams/create') ?>" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Create Exam
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Users Card -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-sm p-6 border border-blue-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600 uppercase tracking-wide">Total Users</p>
                    <p class="text-3xl font-bold text-blue-900 mt-2"><?= $totalUsers ?? 0 ?></p>
                    <p class="text-xs text-blue-600 mt-1">All registered users</p>
                </div>
                <div class="p-3 rounded-full bg-blue-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('admin/users') ?>" class="text-blue-700 hover:text-blue-800 text-sm font-medium flex items-center">
                    Manage Users
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Active Exams Card -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-sm p-6 border border-green-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600 uppercase tracking-wide">Active Exams</p>
                    <p class="text-3xl font-bold text-green-900 mt-2"><?= $activeExams ?? 0 ?></p>
                    <p class="text-xs text-green-600 mt-1">Currently running</p>
                </div>
                <div class="p-3 rounded-full bg-green-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('admin/exams?status=active') ?>" class="text-green-700 hover:text-green-800 text-sm font-medium flex items-center">
                    View Active
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Total Subjects Card -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl shadow-sm p-6 border border-purple-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-600 uppercase tracking-wide">Subjects</p>
                    <p class="text-3xl font-bold text-purple-900 mt-2"><?= $totalSubjects ?? 0 ?></p>
                    <p class="text-xs text-purple-600 mt-1">Available subjects</p>
                </div>
                <div class="p-3 rounded-full bg-purple-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('admin/subjects') ?>" class="text-purple-700 hover:text-purple-800 text-sm font-medium flex items-center">
                    Manage Subjects
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <!-- Total Exams Card -->
        <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl shadow-sm p-6 border border-orange-200 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600 uppercase tracking-wide">Total Exams</p>
                    <p class="text-3xl font-bold text-orange-900 mt-2"><?= $totalExams ?? 0 ?></p>
                    <p class="text-xs text-orange-600 mt-1">All exams created</p>
                </div>
                <div class="p-3 rounded-full bg-orange-500">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <a href="<?= base_url('admin/exams') ?>" class="text-orange-700 hover:text-orange-800 text-sm font-medium flex items-center">
                    View All Exams
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div> <!-- Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Recent Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                        <span class="text-sm text-gray-500"><?= count($recentUsers ?? []) ?> users</span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (isset($recentUsers) && !empty($recentUsers)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentUsers as $user): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex items-center">
                                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center shadow-md">
                                            <span class="text-white font-semibold text-lg"><?= strtoupper(substr($user['full_name'], 0, 1)) ?></span>
                                        </div>
                                        <div class="ml-4">
                                            <p class="text-sm font-semibold text-gray-900"><?= esc($user['full_name']) ?></p>
                                            <p class="text-xs text-gray-500"><?= esc($user['email']) ?></p>
                                            <p class="text-xs text-gray-400">Joined <?= date('M d, Y', strtotime($user['created_at'])) ?></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-3 py-1 text-xs font-medium rounded-full <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                                            <?= ucfirst($user['role']) ?>
                                        </span>
                                        <a href="<?= base_url('admin/users/view/' . $user['id']) ?>" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">No users found</p>
                        </div>
                    <?php endif; ?>
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <a href="<?= base_url('admin/users') ?>" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium py-2 px-4 rounded-lg text-center block transition-colors">
                            View All Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Exams -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recent Exams</h2>
                        <span class="text-sm text-gray-500"><?= count($recentExams ?? []) ?> exams</span>
                    </div>
                </div>
                <div class="p-6">
                    <?php if (isset($recentExams) && !empty($recentExams)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentExams as $exam): ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-600 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3 flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-900 truncate"><?= esc($exam['title']) ?></p>
                                                <p class="text-xs text-gray-500 truncate"><?= esc($exam['subject_name'] ?? 'No Subject') ?></p>
                                                <p class="text-xs text-gray-400"><?= date('M d, Y \a\t H:i', strtotime($exam['start_time'])) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 ml-4">
                                        <?php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 text-gray-800',
                                            'scheduled' => 'bg-yellow-100 text-yellow-800',
                                            'active' => 'bg-green-100 text-green-800',
                                            'completed' => 'bg-blue-100 text-blue-800'
                                        ];
                                        $status = $exam['status'] ?? 'draft';
                                        ?>
                                        <span class="px-2 py-1 text-xs font-medium rounded-full <?= $statusColors[$status] ?? 'bg-gray-100 text-gray-800' ?>">
                                            <?= ucfirst($status) ?>
                                        </span>
                                        <a href="<?= base_url('admin/exams/view/' . $exam['id']) ?>" class="text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 text-sm">No exams found</p>
                        </div>
                    <?php endif; ?>
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <a href="<?= base_url('admin/exams') ?>" class="w-full bg-green-50 hover:bg-green-100 text-green-700 font-medium py-2 px-4 rounded-lg text-center block transition-colors">
                            View All Exams
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column - Quick Actions & Info -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6 space-y-4">
                    <a href="<?= base_url('admin/users') ?>" class="flex items-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center group-hover:bg-blue-600 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">Manage Users</p>
                            <p class="text-xs text-gray-500">Add, edit, or remove users</p>
                        </div>
                    </a>

                    <a href="<?= base_url('admin/exams') ?>" class="flex items-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center group-hover:bg-green-600 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">Manage Exams</p>
                            <p class="text-xs text-gray-500">Create and monitor exams</p>
                        </div>
                    </a>

                    <a href="<?= base_url('admin/subjects') ?>" class="flex items-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors group">
                        <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center group-hover:bg-purple-600 transition-colors">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-semibold text-gray-900">Manage Subjects</p>
                            <p class="text-xs text-gray-500">Add or edit subjects</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">System Status</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Server Status</span>
                        </div>
                        <span class="text-sm font-medium text-green-600">Online</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Database</span>
                        </div>
                        <span class="text-sm font-medium text-green-600">Connected</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">CBT Version</span>
                        </div>
                        <span class="text-sm font-medium text-blue-600">v1.0.0</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-yellow-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700">Last Backup</span>
                        </div>
                        <span class="text-sm font-medium text-yellow-600"><?= date('M d, Y') ?></span>
                    </div>
                </div>
            </div>

            <!-- Today's Schedule -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Schedule</h2>
                </div>
                <div class="p-6">
                    <?php
                    $todaysExams = array_filter($recentExams ?? [], function ($exam) {
                        return date('Y-m-d', strtotime($exam['start_time'])) === date('Y-m-d');
                    });
                    ?>
                    <?php if (!empty($todaysExams)): ?>
                        <div class="space-y-3">
                            <?php foreach (array_slice($todaysExams, 0, 3) as $exam): ?>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-2 h-8 bg-blue-500 rounded-full mr-3"></div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($exam['title']) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('H:i', strtotime($exam['start_time'])) ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <svg class="w-8 h-8 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm text-gray-500">No exams scheduled for today</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>