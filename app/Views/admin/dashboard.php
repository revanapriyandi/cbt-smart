<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>Admin Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Admin Dashboard</h1>
        <p class="mt-2 text-gray-600">Selamat datang di panel administrasi CBT Smart</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $totalUsers ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Active Exams</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $activeExams ?? 0 ?></p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Subjects</p>
                    <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $totalSubjects ?? 0 ?></p>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm p-4 sm:p-6 border border-gray-100">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Exams</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900"><?= $totalExams ?? 0 ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6"> <!-- Recent Users -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Users</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <?php if (isset($recentUsers) && !empty($recentUsers)): ?> <div class="space-y-4">
                            <?php foreach ($recentUsers as $user): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 sm:w-10 sm:h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-medium text-xs sm:text-sm"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900"><?= esc($user['name']) ?></p>
                                            <p class="text-xs text-gray-500 hidden sm:block"><?= esc($user['email']) ?></p>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full <?= $user['role'] === 'admin' ? 'bg-red-100 text-red-800' : ($user['role'] === 'teacher' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800') ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No users found</p>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="<?= base_url('admin/users') ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View all users →</a>
                    </div>
                </div>
            </div> <!-- Recent Exams -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-4 sm:p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Exams</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <?php if (isset($recentExams) && !empty($recentExams)): ?>
                        <div class="space-y-4">
                            <?php foreach ($recentExams as $exam): ?>
                                <div class="flex items-center justify-between">
                                    <div class="flex-1 min-w-0 mr-4">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($exam['title']) ?></p>
                                        <p class="text-xs text-gray-500"><?= date('M d, Y', strtotime($exam['start_time'])) ?></p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded-full flex-shrink-0 <?= $exam['status'] === 'active' ? 'bg-green-100 text-green-800' : ($exam['status'] === 'upcoming' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') ?>">
                                        <?= ucfirst($exam['status'] ?? 'draft') ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-4">No exams found</p>
                    <?php endif; ?>
                    <div class="mt-4">
                        <a href="<?= base_url('admin/exams') ?>" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View all exams →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?= $this->endSection() ?>