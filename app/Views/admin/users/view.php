<?= $this->extend('layout/main') ?>

<?= $this->section('title') ?>User Details - <?= esc($user['full_name']) ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="p-4 sm:p-6">
    <!-- Header with Back Button -->
    <div class="mb-6">
        <div class="flex items-center space-x-4 mb-4">
            <a href="<?= base_url('admin/users') ?>" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Users
            </a>
        </div>

        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-start space-y-4 lg:space-y-0">
            <div class="flex items-center space-x-4">
                <!-- User Avatar -->
                <div class="relative">
                    <div class="w-20 h-20 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-2xl">
                            <?= strtoupper(substr($user['full_name'], 0, 1)) ?>
                        </span>
                    </div>
                    <div class="absolute -bottom-2 -right-2">
                        <?php if ($user['is_active']): ?>
                            <div class="w-6 h-6 bg-green-400 rounded-full ring-4 ring-white"></div>
                        <?php else: ?>
                            <div class="w-6 h-6 bg-gray-400 rounded-full ring-4 ring-white"></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User Info -->
                <div>
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900"><?= esc($user['full_name']) ?></h1>
                    <p class="text-gray-600">@<?= esc($user['username']) ?></p>
                    <div class="flex items-center space-x-3 mt-2">
                        <!-- Role Badge -->
                        <?php
                        $roleConfig = [
                            'admin' => ['color' => 'bg-red-100 text-red-800 border border-red-200', 'icon' => 'M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z'],
                            'teacher' => ['color' => 'bg-blue-100 text-blue-800 border border-blue-200', 'icon' => 'M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3z'],
                            'student' => ['color' => 'bg-green-100 text-green-800 border border-green-200', 'icon' => 'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z']
                        ];
                        $config = $roleConfig[$user['role']] ?? $roleConfig['student'];
                        ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $config['color'] ?>">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="<?= $config['icon'] ?>" />
                            </svg>
                            <?= ucfirst($user['role']) ?>
                        </span>

                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $user['is_active'] ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200' ?>">
                            <div class="w-2 h-2 rounded-full mr-2 <?= $user['is_active'] ? 'bg-green-500' : 'bg-red-500' ?>"></div>
                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
                <a href="<?= base_url('admin/edit-user/' . $user['id']) ?>" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium flex items-center text-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit User
                </a>
                <button onclick="confirmDelete(<?= $user['id'] ?>)" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center text-sm transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                    Delete
                </button>
            </div>
        </div>
    </div>

    <!-- User Details Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Basic Information -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Email</label>
                    <p class="text-gray-900"><?= esc($user['email']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Username</label>
                    <p class="text-gray-900 font-mono">@<?= esc($user['username']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Role</label>
                    <p class="text-gray-900"><?= ucfirst($user['role']) ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Account Status</label>
                    <p class="text-gray-900"><?= $user['is_active'] ? 'Active' : 'Inactive' ?></p>
                </div>
            </div>
        </div>

        <!-- Account Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Timeline</h3>
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">Created</label>
                    <p class="text-gray-900"><?= date('M d, Y \a\t H:i', strtotime($user['created_at'])) ?></p>
                    <p class="text-xs text-gray-500">
                        <?php
                        $created = new DateTime($user['created_at']);
                        $now = new DateTime();
                        $diff = $now->diff($created);
                        if ($diff->days == 0) {
                            echo 'Today';
                        } elseif ($diff->days == 1) {
                            echo 'Yesterday';
                        } elseif ($diff->days < 30) {
                            echo $diff->days . ' days ago';
                        } else {
                            echo $diff->format('%m months, %d days ago');
                        }
                        ?>
                    </p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">Last Updated</label>
                    <p class="text-gray-900"><?= date('M d, Y \a\t H:i', strtotime($user['updated_at'])) ?></p>
                </div>
                <?php if ($user['last_login']): ?>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Login</label>
                        <p class="text-gray-900"><?= date('M d, Y \a\t H:i', strtotime($user['last_login'])) ?></p>
                        <p class="text-xs text-gray-500">
                            <?php
                            $lastLogin = new DateTime($user['last_login']);
                            $diff = $now->diff($lastLogin);
                            if ($diff->days == 0) {
                                echo 'Today';
                            } elseif ($diff->days == 1) {
                                echo 'Yesterday';
                            } else {
                                echo $diff->days . ' days ago';
                            }
                            ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div>
                        <label class="text-sm font-medium text-gray-500">Last Login</label>
                        <p class="text-gray-400 italic">Never logged in</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Activity Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Activity Summary</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-500">Total Activities</span>
                    <span class="text-2xl font-bold text-indigo-600"><?= $totalActivities ?></span>
                </div> <?php if ($user['role'] === 'student' && !empty($examStats)): ?>
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Exam Performance</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Total Exams</span>
                                <span class="text-sm font-medium"><?= $examStats['total_exams'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Completed</span>
                                <span class="text-sm font-medium"><?= $examStats['completed_exams'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Pending</span>
                                <span class="text-sm font-medium"><?= $examStats['pending_exams'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Average Score</span>
                                <span class="text-sm font-medium <?= $examStats['average_score'] >= 80 ? 'text-green-600' : ($examStats['average_score'] >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $examStats['average_score'] ?>%</span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($user['role'] === 'teacher' && !empty($createdExams)): ?>
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Teaching Activity</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Created Exams</span>
                                <span class="text-sm font-medium"><?= count($createdExams) ?></span>
                            </div>
                            <?php if (!empty($subjectsData)): ?>
                                <div class="flex justify-between">
                                    <span class="text-xs text-gray-500">Subjects</span>
                                    <span class="text-sm font-medium"><?= count($subjectsData) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($user['role'] === 'admin' && !empty($adminStats)): ?>
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-2">Admin Activity</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">Total Users</span>
                                <span class="text-sm font-medium"><?= $adminStats['total_users_managed'] ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-xs text-gray-500">User Actions</span>
                                <span class="text-sm font-medium"><?= count($adminStats['recent_user_actions']) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Detailed Information Tabs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <!-- Tab Navigation -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="switchTab('activities')" id="tab-activities" class="tab-button border-b-2 border-indigo-500 py-4 px-1 text-sm font-medium text-indigo-600">
                    Recent Activities
                </button>
                <?php if ($user['role'] === 'student' && !empty($examStats)): ?>
                    <button onclick="switchTab('exams')" id="tab-exams" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Exam History
                    </button>
                <?php endif; ?> <?php if ($user['role'] === 'teacher' && !empty($createdExams)): ?>
                    <button onclick="switchTab('created-exams')" id="tab-created-exams" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Created Exams
                    </button>
                <?php endif; ?>
                <?php if ($user['role'] === 'admin' && !empty($adminStats)): ?>
                    <button onclick="switchTab('admin-actions')" id="tab-admin-actions" class="tab-button border-b-2 border-transparent py-4 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Admin Actions
                    </button>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Tab Contents -->
        <div class="p-6">
            <!-- Activities Tab -->
            <div id="content-activities" class="tab-content">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h3>
                <?php if (!empty($recentActivities)): ?>
                    <div class="space-y-4">
                        <?php foreach ($recentActivities as $activity): ?>
                            <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900"><?= esc($activity['activity_type']) ?></p> <?php if (isset($activity['activity_description']) && $activity['activity_description']): ?>
                                        <p class="text-sm text-gray-600"><?= esc($activity['activity_description']) ?></p>
                                    <?php endif; ?>
                                    <p class="text-xs text-gray-500 mt-1"><?= date('M d, Y \a\t H:i', strtotime($activity['created_at'])) ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        <p class="text-gray-500">No activities found</p>
                    </div>
                <?php endif; ?>
            </div> <!-- Student Exams Tab -->
            <?php if ($user['role'] === 'student' && !empty($recentResults)): ?>
                <div id="content-exams" class="tab-content hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam History</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Exam</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($recentResults as $result): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?= esc($result['exam_title'] ?? 'Exam #' . $result['exam_id']) ?></div>
                                            <?php if (!empty($result['exam_description'])): ?>
                                                <div class="text-xs text-gray-500"><?= esc(substr($result['exam_description'], 0, 50)) ?>...</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <?= esc($result['subject_name'] ?? 'Unknown') ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($result['percentage'] !== null): ?>
                                                <div class="text-sm font-medium <?= $result['percentage'] >= 80 ? 'text-green-600' : ($result['percentage'] >= 60 ? 'text-yellow-600' : 'text-red-600') ?>">
                                                    <?= $result['percentage'] ?>%
                                                </div>
                                            <?php else: ?>
                                                <span class="text-sm text-gray-400">Not graded</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $result['status'] === 'graded' ? 'bg-green-100 text-green-800' : ($result['status'] === 'submitted' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') ?>">
                                                <?= ucfirst($result['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($result['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?> <!-- Teacher Created Exams Tab -->
            <?php if ($user['role'] === 'teacher' && !empty($createdExams)): ?>
                <div id="content-created-exams" class="tab-content hidden">
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Created Exams</h3>

                        <?php if (!empty($subjectsData)): ?>
                            <div class="mb-6">
                                <h4 class="text-md font-medium text-gray-700 mb-3">Subjects Taught</h4>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($subjectsData as $subject): ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                            <?= esc($subject['name']) ?>
                                            <span class="ml-1 text-xs">(<?= $subject['exam_count'] ?> exams)</span>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <?php foreach ($createdExams as $exam): ?>
                            <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition-colors">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900"><?= esc($exam['title']) ?></h4>
                                        <?php if (!empty($exam['description'])): ?>
                                            <p class="text-sm text-gray-600 mt-1"><?= esc(substr($exam['description'], 0, 100)) ?><?= strlen($exam['description']) > 100 ? '...' : '' ?></p>
                                        <?php endif; ?>

                                        <div class="mt-3 space-y-2">
                                            <?php if (!empty($exam['subject_name'])): ?>
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                    Subject: <?= esc($exam['subject_name']) ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <?= $exam['question_count'] ?> questions
                                            </div>

                                            <div class="flex items-center text-xs text-gray-500">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <?= $exam['duration_minutes'] ?> minutes
                                            </div>
                                        </div>
                                    </div>

                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $exam['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' ?>">
                                        <?= $exam['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>

                                <div class="flex items-center justify-between mt-4 pt-3 border-t border-gray-200">
                                    <span class="text-xs text-gray-500">Created: <?= date('M d, Y', strtotime($exam['created_at'])) ?></span>
                                    <?php if ($exam['start_time'] && $exam['end_time']): ?>
                                        <span class="text-xs text-gray-500">
                                            <?= date('M d', strtotime($exam['start_time'])) ?> - <?= date('M d, Y', strtotime($exam['end_time'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Admin Actions Tab -->
            <?php if ($user['role'] === 'admin' && !empty($adminStats)): ?>
                <div id="content-admin-actions" class="tab-content hidden">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Admin Actions</h3>

                    <div class="mb-6">
                        <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700">System Overview</h4>
                                    <p class="text-xs text-gray-500">Total users managed by this admin</p>
                                </div>
                                <div class="text-2xl font-bold text-indigo-600"><?= $adminStats['total_users_managed'] ?></div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($adminStats['recent_user_actions'])): ?>
                        <div class="space-y-3">
                            <?php foreach ($adminStats['recent_user_actions'] as $action): ?>
                                <div class="flex items-start space-x-3 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <?php
                                        $iconClass = 'w-5 h-5';
                                        $iconColor = 'text-blue-500';
                                        if ($action['activity_type'] === 'user_create') {
                                            $iconColor = 'text-green-500';
                                        } elseif ($action['activity_type'] === 'user_delete') {
                                            $iconColor = 'text-red-500';
                                        } elseif ($action['activity_type'] === 'user_update') {
                                            $iconColor = 'text-yellow-500';
                                        }
                                        ?>
                                        <svg class="<?= $iconClass ?> <?= $iconColor ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <?php if ($action['activity_type'] === 'user_create'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            <?php elseif ($action['activity_type'] === 'user_delete'): ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            <?php else: ?>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            <?php endif; ?>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                <?= ucfirst(str_replace('_', ' ', $action['activity_type'])) ?>
                                            </p>
                                            <p class="text-xs text-gray-500"><?= date('M d, Y H:i', strtotime($action['created_at'])) ?></p>
                                        </div>
                                        <?php if (isset($action['activity_description']) && $action['activity_description']): ?>
                                            <p class="text-sm text-gray-600"><?= esc($action['activity_description']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500">No admin actions found</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Remove active state from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-indigo-500', 'text-indigo-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });

        // Show selected tab content
        document.getElementById('content-' + tabName).classList.remove('hidden');

        // Add active state to selected tab button
        const activeButton = document.getElementById('tab-' + tabName);
        activeButton.classList.remove('border-transparent', 'text-gray-500');
        activeButton.classList.add('border-indigo-500', 'text-indigo-600');
    }

    function confirmDelete(userId) {
        if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
            window.location.href = '<?= base_url('admin/delete-user/') ?>' + userId;
        }
    }
</script>
<?= $this->endSection() ?>