<?php if ($subject): ?>
    <div class="space-y-6">
        <!-- Subject Header -->
        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl p-6 text-white">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2"><?= esc($subject['name']) ?></h2>
                    <p class="text-blue-100 mb-4">Code: <?= esc($subject['code']) ?></p>
                    <?php if ($subject['description']): ?>
                        <p class="text-blue-100"><?= esc($subject['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="text-right">
                    <p class="text-sm text-blue-100">Created</p>
                    <p class="font-semibold"><?= date('M d, Y', strtotime($subject['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <!-- Teacher Information -->
        <div class="bg-white rounded-xl border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                Assigned Teacher
            </h3>
            <?php if ($subject['teacher_name']): ?>
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="font-semibold text-gray-900"><?= esc($subject['teacher_name']) ?></p>
                        <p class="text-sm text-gray-600"><?= esc($subject['teacher_email']) ?></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-4">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <p class="text-gray-500">No teacher assigned yet</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Statistics Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <div class="p-3 rounded-full bg-green-100 w-fit mx-auto mb-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900"><?= $subject['total_exams'] ?? 0 ?></p>
                <p class="text-sm text-gray-600">Total Exams</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <div class="p-3 rounded-full bg-blue-100 w-fit mx-auto mb-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900"><?= $subject['active_exams'] ?? 0 ?></p>
                <p class="text-sm text-gray-600">Active Exams</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <div class="p-3 rounded-full bg-purple-100 w-fit mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900"><?= $subject['enrolled_students'] ?? 0 ?></p>
                <p class="text-sm text-gray-600">Students</p>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 p-6 text-center">
                <div class="p-3 rounded-full bg-yellow-100 w-fit mx-auto mb-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">
                    <?= $subject['average_score'] ? number_format($subject['average_score'], 1) . '%' : 'N/A' ?>
                </p>
                <p class="text-sm text-gray-600">Avg Score</p>
            </div>
        </div>

        <!-- Performance Statistics -->
        <?php if ($subject['completed_attempts'] > 0): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Performance Metrics
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <p class="text-2xl font-bold text-green-600"><?= number_format($subject['highest_score'], 1) ?>%</p>
                        <p class="text-sm text-gray-600">Highest Score</p>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <p class="text-2xl font-bold text-blue-600"><?= number_format($subject['average_score'], 1) ?>%</p>
                        <p class="text-sm text-gray-600">Average Score</p>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <p class="text-2xl font-bold text-orange-600"><?= number_format($subject['lowest_score'], 1) ?>%</p>
                        <p class="text-sm text-gray-600">Lowest Score</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Recent Exams -->
        <?php if (!empty($recentExams)): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Recent Exams
                </h3>
                <div class="space-y-3">
                    <?php foreach ($recentExams as $exam): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <p class="font-medium text-gray-900"><?= esc($exam['title']) ?></p>
                                <p class="text-sm text-gray-600">
                                    <?= date('M d, Y', strtotime($exam['start_time'])) ?> •
                                    <?= $exam['participant_count'] ?> participants
                                </p>
                            </div>
                            <div class="flex items-center">
                                <?php if ($exam['is_active']): ?>
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Active</span>
                                <?php else: ?>
                                    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">Draft</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Top Students -->
        <?php if (!empty($topStudents)): ?>
            <div class="bg-white rounded-xl border border-gray-100 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                    Top Performing Students
                </h3>
                <div class="space-y-3">
                    <?php foreach (array_slice($topStudents, 0, 5) as $index => $student): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm font-bold mr-3">
                                    <?= $index + 1 ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900"><?= esc($student['full_name']) ?></p>
                                    <p class="text-sm text-gray-600"><?= esc($student['email']) ?></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-blue-600"><?= number_format($student['avg_score'], 1) ?>%</p>
                                <p class="text-xs text-gray-600"><?= $student['exam_count'] ?> exam(s)</p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button onclick="editSubject(<?= $subject['id'] ?>); closeViewModal();" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Edit Subject
            </button>
            <button onclick="if(confirm('Are you sure you want to delete this subject?')) window.location.href='<?= base_url('admin/subjects/delete/' . $subject['id']) ?>'" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                Delete
            </button>
        </div>
    </div>
<?php else: ?>
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Subject Not Found</h3>
        <p class="text-gray-600">The requested subject could not be found.</p>
    </div>
<?php endif; ?>