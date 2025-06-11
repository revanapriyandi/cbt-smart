<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Default route
$routes->get('/', 'Home::index');

// Authentication routes
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::login');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::register');

// Admin routes
$routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin\AdminDashboardController::index');

    // Security management
    $routes->get('security', 'Admin\AdminSecurityController::index');
    $routes->post('security/update-general-settings', 'Admin\AdminSecurityController::updateGeneralSettings');
    $routes->post('security/update-password-policies', 'Admin\AdminSecurityController::updatePasswordPolicies');
    $routes->post('security/update-session-settings', 'Admin\AdminSecurityController::updateSessionSettings');
    $routes->post('security/manage-ip-whitelist', 'Admin\AdminSecurityController::manageIPWhitelist');
    $routes->post('security/unblock-ip', 'Admin\AdminSecurityController::unblockIP');
    $routes->post('security/terminate-session', 'Admin\AdminSecurityController::terminateSession');
    $routes->get('security/get-security-dashboard', 'Admin\AdminSecurityController::getSecurityDashboard');
    $routes->get('security/export-security-report', 'Admin\AdminSecurityController::exportSecurityReport');

    // Backup management
    $routes->get('backup', 'Admin\AdminBackupController::index');
    $routes->get('backup/data', 'Admin\AdminBackupController::data');
    $routes->post('backup/create', 'Admin\AdminBackupController::createBackup');
    $routes->get('backup/download/(:segment)', 'Admin\AdminBackupController::downloadBackup/$1');
    $routes->post('backup/upload', 'Admin\AdminBackupController::uploadBackup');
    $routes->post('backup/delete/(:segment)', 'Admin\AdminBackupController::deleteBackup/$1');
    $routes->post('backup/restore/(:segment)', 'Admin\AdminBackupController::restoreBackup/$1');
    $routes->get('backup/stats', 'Admin\AdminBackupController::getBackupStats');
    $routes->post('backup/cleanup', 'Admin\AdminBackupController::cleanupOldBackups');

    // Activity logs
    $routes->get('activity-logs', 'Admin\AdminActivityLogController::index');
    $routes->get('activity-logs/data', 'Admin\AdminActivityLogController::data');
    $routes->get('activity-logs/stats', 'Admin\AdminActivityLogController::stats');
    $routes->get('activity-logs/export', 'Admin\AdminActivityLogController::export');
    $routes->get('activity-logs/user/(:num)', 'Admin\AdminActivityLogController::getUserActivity/$1');
    $routes->get('activity-logs/view/(:num)', 'Admin\AdminActivityLogController::view/$1');
    $routes->post('activity-logs/cleanup', 'Admin\AdminActivityLogController::cleanup');

    // System settings
    $routes->get('system-settings', 'Admin\AdminSystemSettingsController::index');
    $routes->post('system-settings/update-general', 'Admin\AdminSystemSettingsController::updateGeneral');
    $routes->post('system-settings/update-email', 'Admin\AdminSystemSettingsController::updateEmail');
    $routes->post('system-settings/update-exam', 'Admin\AdminSystemSettingsController::updateExam');
    $routes->post('system-settings/update-notification', 'Admin\AdminSystemSettingsController::updateNotification');
    $routes->post('system-settings/update-maintenance', 'Admin\AdminSystemSettingsController::updateMaintenance');
    $routes->post('system-settings/test-email', 'Admin\AdminSystemSettingsController::testEmail');
    $routes->get('system-settings/info', 'Admin\AdminSystemSettingsController::getSystemInfo');
    $routes->post('system-settings/upload-logo', 'Admin\AdminSystemSettingsController::uploadLogo');
    $routes->post('system-settings/clear-cache', 'Admin\AdminSystemSettingsController::clearCache');
    $routes->post('system-settings/reset-defaults', 'Admin\AdminSystemSettingsController::resetDefaults');    // User management
    $routes->get('users/export', 'Admin\AdminUserController::exportUsers');
    $routes->get('users/view/(:num)', 'Admin\AdminUserController::viewUser/$1');
    $routes->get('users/get/(:num)', 'Admin\AdminUserController::getUser/$1');
    $routes->get('users/check-deletion/(:num)', 'Admin\AdminUserController::checkUserDeletion/$1');
    $routes->get('users/activity-stats/(:num)', 'Admin\AdminUserController::getUserActivityStats/$1');
    $routes->get('users/recent-activities/(:num)', 'Admin\AdminUserController::getUserRecentActivities/$1');
    $routes->get('users/exam-performance/(:num)', 'Admin\AdminUserController::getUserExamPerformance/$1', ['filter' => 'examaccess']);
    $routes->get('users-data', 'Admin\AdminUserController::getUsersData');

    // Role-specific user routes (must come before generic user routes)
    $routes->get('users/admins', 'Admin\AdminUserController::users/admin');
    $routes->get('users/teachers', 'Admin\AdminUserController::getTeachers');
    $routes->get('users/students', 'Admin\AdminUserController::users/student');

    // Generic user routes
    $routes->get('users/(:num)', 'Admin\AdminUserController::viewUser/$1');
    $routes->get('users', 'Admin\AdminUserController::users');
    $routes->get('create-user', 'Admin\AdminUserController::createUser');
    $routes->post('create-user', 'Admin\AdminUserController::createUser');
    $routes->post('users/store', 'Admin\AdminUserController::store');
    $routes->get('edit-user/(:num)', 'Admin\AdminUserController::editUser/$1');
    $routes->post('edit-user/(:num)', 'Admin\AdminUserController::editUser/$1');
    $routes->post('users/update/(:num)', 'Admin\AdminUserController::editUser/$1');
    $routes->get('delete-user/(:num)', 'Admin\AdminUserController::deleteUser/$1');
    $routes->post('users/delete/(:num)', 'Admin\AdminUserController::deleteUser/$1');
    $routes->post('users/import', 'Admin\AdminUserController::importUsers');
    $routes->post('users/bulk-action', 'Admin\AdminUserController::bulkAction');

    // Subject management
    $routes->get('subjects', 'Admin\AdminSubjectController::subjects');
    $routes->get('subjects-data', 'Admin\AdminSubjectController::getSubjectsData');
    $routes->get('create-subject', 'Admin\AdminSubjectController::createSubject');
    $routes->post('create-subject', 'Admin\AdminSubjectController::createSubject');
    $routes->post('subjects/store', 'Admin\AdminSubjectController::store');
    $routes->get('subjects/get/(:num)', 'Admin\AdminSubjectController::getSubject/$1');
    $routes->get('subjects/view/(:num)', 'Admin\AdminSubjectController::viewSubject/$1');
    $routes->post('subjects/update/(:num)', 'Admin\AdminSubjectController::editSubject/$1');
    $routes->get('subjects/delete/(:num)', 'Admin\AdminSubjectController::deleteSubject/$1');
    $routes->get('edit-subject/(:num)', 'Admin\AdminSubjectController::editSubject/$1');
    $routes->post('edit-subject/(:num)', 'Admin\AdminSubjectController::editSubject/$1');
    $routes->get('delete-subject/(:num)', 'Admin\AdminSubjectController::deleteSubject/$1');    // Exam management
    $routes->get('exams', 'Admin\AdminExamController::exams');
    $routes->match(['GET', 'POST'], 'exams/data', 'Admin\AdminExamController::getData');
    $routes->get('exams/create', 'Admin\AdminExamController::createExam');
    $routes->post('exams/create', 'Admin\AdminExamController::createExam');
    $routes->post('exams/create-manual', 'Admin\AdminExamController::createManualExam');
    $routes->post('exams/create-from-question-bank', 'Admin\AdminExamController::createFromQuestionBank');
    $routes->post('exams/create-from-pdf', 'Admin\AdminExamController::createFromPdf');
    $routes->get('exams/view/(:num)', 'Admin\AdminExamController::viewExam/$1');
    $routes->get('exams/publish/(:num)', 'Admin\AdminExamController::publishExam/$1');
    $routes->get('exams/edit/(:num)', 'Admin\AdminExamController::editExam/$1');
    $routes->post('exams/edit/(:num)', 'Admin\AdminExamController::editExam/$1');
    $routes->get('exams/delete/(:num)', 'Admin\AdminExamController::deleteExam/$1');
    $routes->post('exams/import', 'Admin\AdminExamController::importExams');
    $routes->post('exams/export', 'Admin\AdminExamController::exportExams');
    $routes->get('exams/download-template', 'Admin\AdminExamController::downloadTemplate');
    $routes->get('exam-results/(:num)', 'Admin\AdminExamController::examResults/$1', ['filter' => 'examaccess']);
    $routes->get('exams/results/(:num)', 'Admin\AdminExamController::examResults/$1', ['filter' => 'examaccess']);
    $routes->get('download-results/(:num)', 'Admin\AdminExamController::downloadResults/$1', ['filter' => 'examaccess']);

    // Exam sessions management
    $routes->get('exam-sessions', 'Admin\AdminExamSessionController::index');
    $routes->get('exam-sessions/create', 'Admin\AdminExamSessionController::create');
    $routes->post('exam-sessions/create', 'Admin\AdminExamSessionController::create');
    $routes->post('exam-sessions/store', 'Admin\AdminExamSessionController::store');
    $routes->get('exam-sessions/view/(:num)', 'Admin\AdminExamSessionController::show/$1');
    $routes->get('exam-sessions/(:num)', 'Admin\AdminExamSessionController::show/$1');
    $routes->get('exam-sessions/edit/(:num)', 'Admin\AdminExamSessionController::edit/$1');
    $routes->post('exam-sessions/edit/(:num)', 'Admin\AdminExamSessionController::edit/$1');
    $routes->post('exam-sessions/update/(:num)', 'Admin\AdminExamSessionController::update/$1');
    $routes->post('exam-sessions/delete/(:num)', 'Admin\AdminExamSessionController::delete/$1');
    $routes->get('exam-sessions/delete/(:num)', 'Admin\AdminExamSessionController::delete/$1');
    $routes->post('exam-sessions/start/(:num)', 'Admin\AdminExamSessionController::start/$1');
    $routes->post('exam-sessions/end/(:num)', 'Admin\AdminExamSessionController::end/$1');
    $routes->get('exam-sessions/monitor/(:num)', 'Admin\AdminExamSessionController::monitor/$1');
    $routes->get('exam-sessions/(:num)/monitor', 'Admin\AdminExamSessionController::monitor/$1');
    $routes->get('exam-sessions/(:num)/monitor-data', 'Admin\AdminExamSessionController::getMonitorData/$1');
    $routes->get('exam-sessions/(:num)/recent-activities', 'Admin\AdminExamSessionController::getRecentActivities/$1');
    $routes->get('exam-sessions/(:num)/participant/(:num)', 'Admin\AdminExamSessionController::getParticipantDetails/$1/$2');
    $routes->post('exam-sessions/(:num)/send-warning', 'Admin\AdminExamSessionController::sendWarning/$1');
    $routes->post('exam-sessions/(:num)/force-submit', 'Admin\AdminExamSessionController::forceSubmit/$1');
    $routes->get('exam-sessions/export', 'Admin\AdminExamSessionController::export');
    $routes->post('exam-sessions/bulk-action', 'Admin\AdminExamSessionController::bulkAction');
    $routes->get('exam-sessions/data/(:num)', 'Admin\AdminExamSessionController::getSessionData/$1');    // Classes management
    $routes->get('classes', 'Admin\AdminClassController::index');
    $routes->get('classes/create', 'Admin\AdminClassController::create');
    $routes->post('classes/store', 'Admin\AdminClassController::store');
    $routes->get('classes/edit/(:num)', 'Admin\AdminClassController::edit/$1');
    $routes->post('classes/update/(:num)', 'Admin\AdminClassController::update/$1');
    $routes->get('classes/view/(:num)', 'Admin\AdminClassController::show/$1');
    $routes->get('classes/(:num)', 'Admin\AdminClassController::show/$1');
    $routes->post('classes/delete/(:num)', 'Admin\AdminClassController::delete/$1');
    $routes->get('classes/delete/(:num)', 'Admin\AdminClassController::delete/$1');
    $routes->get('classes/statistics', 'Admin\AdminClassController::statistics');
    $routes->get('classes/statistics/(:num)', 'Admin\AdminClassController::classStatistics/$1');
    $routes->get('classes/students/(:num)', 'Admin\AdminClassController::classStudents/$1');
    $routes->get('classes/show/(:num)', 'Admin\AdminClassController::show/$1');
    $routes->match(['GET', 'POST'], 'classes/datatables', 'Admin\AdminClassController::datatables');
    $routes->get('classes/export', 'Admin\AdminClassController::export');
    $routes->get('classes/template', 'Admin\AdminClassController::downloadTemplate');
    $routes->get('classes/import', 'Admin\AdminClassController::import');
    $routes->post('classes/import', 'Admin\AdminClassController::import');
    $routes->post('classes/bulk-action', 'Admin\AdminClassController::bulkAction');
    $routes->post('classes/(:num)/toggle-status', 'Admin\AdminClassController::toggleStatus/$1');
    $routes->get('classes/(:num)/stats', 'Admin\AdminClassController::quickStats/$1');
    $routes->get('classes/(:num)/activity', 'Admin\AdminClassController::recentActivity/$1');    // Academic years management
    $routes->get('academic-years', 'Admin\AdminAcademicYearController::index');
    $routes->get('academic-years/create', 'Admin\AdminAcademicYearController::create');
    $routes->post('academic-years/store', 'Admin\AdminAcademicYearController::store');
    $routes->get('academic-years/edit/(:num)', 'Admin\AdminAcademicYearController::edit/$1');
    $routes->post('academic-years/update/(:num)', 'Admin\AdminAcademicYearController::update/$1');
    $routes->post('academic-years/set-current/(:num)', 'Admin\AdminAcademicYearController::setCurrent/$1');
    $routes->post('academic-years/delete/(:num)', 'Admin\AdminAcademicYearController::delete/$1');    // Schedules management
    $routes->get('schedules', 'Admin\AdminScheduleController::index');
    $routes->match(['GET', 'POST'], 'schedules/getData', 'Admin\AdminScheduleController::getData');
    $routes->get('schedules/create', 'Admin\AdminScheduleController::create');
    $routes->post('schedules/store', 'Admin\AdminScheduleController::store');
    $routes->get('schedules/edit/(:num)', 'Admin\AdminScheduleController::edit/$1');
    $routes->post('schedules/update/(:num)', 'Admin\AdminScheduleController::update/$1');
    $routes->get('schedules/view/(:num)', 'Admin\AdminScheduleController::show/$1');
    $routes->post('schedules/delete/(:num)', 'Admin\AdminScheduleController::delete/$1');
    $routes->get('schedules/weekly/(:num)', 'Admin\AdminScheduleController::getWeeklySchedule/$1');
    $routes->get('schedules/populate-test-data', 'Admin\AdminScheduleController::populateTestData'); // Temporary route
    $routes->get('schedules/debug-data', 'Admin\AdminScheduleController::debugData'); // Debug route    // Exam types management
    $routes->get('exam-types', 'Admin\AdminExamTypeController::index');
    $routes->post('exam-types/getData', 'Admin\AdminExamTypeController::getData');
    $routes->get('exam-types/create', 'Admin\AdminExamTypeController::create');
    $routes->post('exam-types/store', 'Admin\AdminExamTypeController::store');
    $routes->get('exam-types/edit/(:num)', 'Admin\AdminExamTypeController::edit/$1');
    $routes->get('exam-types/get/(:num)', 'Admin\AdminExamTypeController::getExamType/$1');
    $routes->post('exam-types/update/(:num)', 'Admin\AdminExamTypeController::update/$1');
    $routes->get('exam-types/view/(:num)', 'Admin\AdminExamTypeController::show/$1');
    $routes->post('exam-types/delete/(:num)', 'Admin\AdminExamTypeController::delete/$1');
    $routes->post('exam-types/bulk-action', 'Admin\AdminExamTypeController::bulkAction');    // Question banks management
    $routes->get('question-banks', 'Admin\AdminQuestionBankController::index');
    $routes->post('question-banks/data', 'Admin\AdminQuestionBankController::getData');
    $routes->get('question-banks/create', 'Admin\AdminQuestionBankController::create');
    $routes->post('question-banks/store', 'Admin\AdminQuestionBankController::store');
    $routes->get('question-banks/edit/(:num)', 'Admin\AdminQuestionBankController::edit/$1');
    $routes->post('question-banks/update/(:num)', 'Admin\AdminQuestionBankController::update/$1');
    $routes->get('question-banks/view/(:num)', 'Admin\AdminQuestionBankController::show/$1');
    $routes->post('question-banks/delete/(:num)', 'Admin\AdminQuestionBankController::delete/$1');
    $routes->post('question-banks/bulk-delete', 'Admin\AdminQuestionBankController::bulkDelete');
    $routes->post('question-banks/bulk-activate', 'Admin\AdminQuestionBankController::bulkActivate');
    $routes->post('question-banks/bulk-archive', 'Admin\AdminQuestionBankController::bulkArchive');
    $routes->get('question-banks/export', 'Admin\AdminQuestionBankController::export');
    $routes->get('question-banks/import', 'Admin\AdminQuestionBankController::import');
    $routes->post('question-banks/import', 'Admin\AdminQuestionBankController::processImport');    // Questions management
    $routes->get('questions', 'Admin\AdminQuestionController::index');
    $routes->post('questions/data', 'Admin\AdminQuestionController::getData');
    $routes->get('questions/get-data', 'Admin\AdminQuestionController::getData');
    $routes->post('questions/get-data', 'Admin\AdminQuestionController::getData');
    $routes->get('questions/create', 'Admin\AdminQuestionController::create');
    $routes->post('questions/store', 'Admin\AdminQuestionController::store');
    $routes->get('questions/edit/(:num)', 'Admin\AdminQuestionController::edit/$1');
    $routes->post('questions/update/(:num)', 'Admin\AdminQuestionController::update/$1');
    $routes->put('questions/(:num)', 'Admin\AdminQuestionController::update/$1');
    $routes->get('questions/view/(:num)', 'Admin\AdminQuestionController::show/$1');
    $routes->post('questions/delete/(:num)', 'Admin\AdminQuestionController::delete/$1');
    $routes->delete('questions/(:num)', 'Admin\AdminQuestionController::delete/$1');
    $routes->post('questions/bulk-action', 'Admin\AdminQuestionController::bulkAction');
    $routes->post('questions/upload-pdf', 'Admin\AdminQuestionController::uploadPdf');
    $routes->post('questions/process-pdf', 'Admin\AdminQuestionController::processPdf');
    $routes->get('questions/export', 'Admin\AdminQuestionController::export');
    $routes->get('questions/import', 'Admin\AdminQuestionController::import');
    $routes->post('questions/import', 'Admin\AdminQuestionController::processImport');
    $routes->get('questions/stats', 'Admin\AdminQuestionController::getStats');    // Monitoring management
    $routes->get('monitoring/live', 'Admin\AdminMonitoringController::live');
    $routes->get('monitoring', 'Admin\AdminMonitoringController::index');
    $routes->get('monitoring/session/(:num)', 'Admin\AdminMonitoringController::session/$1');
    $routes->get('monitoring/data', 'Admin\AdminMonitoringController::getData');
    $routes->get('monitoring/system-health', 'Admin\AdminMonitoringController::getSystemHealth');
    $routes->post('monitoring/send-message', 'Admin\AdminMonitoringController::sendMessage');
    $routes->post('monitoring/end-session/(:num)', 'Admin\AdminMonitoringController::endSession/$1');
    $routes->post('monitoring/terminate-participant', 'Admin\AdminMonitoringController::terminateParticipant');
    $routes->post('monitoring/flag-participant', 'Admin\AdminMonitoringController::flagParticipant');
    $routes->get('monitoring/participant/(:num)', 'Admin\AdminMonitoringController::getParticipantActivity/$1');

    // Results management
    $routes->get('results', 'Admin\AdminResultController::index');
    $routes->get('results/exam/(:num)', 'Admin\AdminResultController::examResult/$1');
    $routes->get('results/student/(:num)', 'Admin\AdminResultController::studentResult/$1');
    $routes->get('results/export', 'Admin\AdminResultController::export');    // Analytics management
    $routes->get('analytics', 'Admin\AdminAnalyticsController::index');
    $routes->get('analytics/dashboard-data', 'Admin\AdminAnalyticsController::getDashboardData');
    $routes->get('analytics/exams', 'Admin\AdminAnalyticsController::examAnalytics');
    $routes->get('analytics/exam-data', 'Admin\AdminAnalyticsController::getExamAnalyticsData');
    $routes->get('analytics/users', 'Admin\AdminAnalyticsController::userPerformance');
    $routes->get('analytics/user-data', 'Admin\AdminAnalyticsController::getUserPerformanceData');
    $routes->post('analytics/export-report', 'Admin\AdminAnalyticsController::exportReport');    // Reports management
    $routes->get('reports', 'Admin\AdminReportController::index');
    $routes->get('reports/generate', 'Admin\AdminReportController::generate');
    $routes->post('reports/create', 'Admin\AdminReportController::create');
    $routes->post('reports/process-generation', 'Admin\AdminReportController::processGeneration');
    $routes->get('reports/list', 'Admin\AdminReportController::getReportsList');
    $routes->get('reports/download/(:segment)', 'Admin\AdminReportController::download/$1');
    $routes->post('reports/delete/(:segment)', 'Admin\AdminReportController::delete/$1');
});

// Teacher routes
$routes->group('teacher', ['filter' => 'auth:teacher'], function ($routes) {
    $routes->get('dashboard', 'TeacherController::dashboard');

    // Exam management
    $routes->get('exams', 'TeacherController::exams');
    $routes->get('create-exam', 'TeacherController::createExam');
    $routes->post('create-exam', 'TeacherController::createExam');
    $routes->get('edit-exam/(:num)', 'TeacherController::editExam/$1');
    $routes->post('edit-exam/(:num)', 'TeacherController::editExam/$1');
    $routes->get('delete-exam/(:num)', 'TeacherController::deleteExam/$1');

    // Results and grading
    $routes->get('exam-results/(:num)', 'TeacherController::examResults/$1');
    $routes->get('grade-answers/(:num)', 'TeacherController::gradeAnswers/$1');
    $routes->post('save-manual-grade', 'TeacherController::saveManualGrade');
    $routes->get('download-results/(:num)', 'TeacherController::downloadResults/$1');
});

// Student routes
$routes->group('student', ['filter' => 'auth:student'], function ($routes) {
    $routes->get('dashboard', 'StudentController::dashboard');
    $routes->get('exam/(:num)', 'StudentController::takeExam/$1');
    $routes->post('save-answer', 'StudentController::saveAnswer');
    $routes->post('submit-exam/(:num)', 'StudentController::submitExam/$1');
    $routes->get('results', 'StudentController::results');
    $routes->get('result-detail/(:num)', 'StudentController::resultDetail/$1', ['filter' => 'examaccess']);
});

// API routes for AJAX calls
$routes->group('api', function ($routes) {
    $routes->post('parse-pdf', 'ApiController::parsePdf');
    $routes->post('parse-pdf-admin', 'ApiController::parsePdfAdmin');
    $routes->post('grade-answer', 'ApiController::gradeAnswer');
    $routes->get('exam-time-remaining/(:num)', 'ApiController::getTimeRemaining/$1');
    $routes->post('log-activity', 'ApiController::logActivity');
});
