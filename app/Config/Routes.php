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
    $routes->get('dashboard', 'Admin\AdminDashboardController::index');    // User management
    $routes->get('users/export', 'Admin\AdminUserController::exportUsers');
    $routes->get('users/view/(:num)', 'Admin\AdminUserController::viewUser/$1');
    $routes->get('users/get/(:num)', 'Admin\AdminUserController::getUser/$1');
    $routes->get('users/check-deletion/(:num)', 'Admin\AdminUserController::checkUserDeletion/$1');
    $routes->get('users/activity-stats/(:num)', 'Admin\AdminUserController::getUserActivityStats/$1');
    $routes->get('users/recent-activities/(:num)', 'Admin\AdminUserController::getUserRecentActivities/$1');
    $routes->get('users/exam-performance/(:num)', 'Admin\AdminUserController::getUserExamPerformance/$1', ['filter' => 'examaccess']);
    $routes->get('users-data', 'Admin\AdminUserController::getUsersData');
    $routes->get('users/(:segment)', 'Admin\AdminUserController::users/$1');
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
    $routes->get('delete-subject/(:num)', 'Admin\AdminSubjectController::deleteSubject/$1');

    // Exam management
    $routes->get('exams', 'Admin\AdminExamController::exams');
    $routes->get('exams/create', 'Admin\AdminExamController::createExam');
    $routes->post('exams/create', 'Admin\AdminExamController::createExam');
    $routes->get('exams/view/(:num)', 'Admin\AdminExamController::viewExam/$1');
    $routes->get('exams/publish/(:num)', 'Admin\AdminExamController::publishExam/$1');
    $routes->get('exams/edit/(:num)', 'Admin\AdminExamController::editExam/$1');
    $routes->post('exams/edit/(:num)', 'Admin\AdminExamController::editExam/$1');
    $routes->get('exams/delete/(:num)', 'Admin\AdminExamController::deleteExam/$1');
    $routes->get('exam-results/(:num)', 'Admin\AdminExamController::examResults/$1', ['filter' => 'examaccess']);
    $routes->get('exams/results/(:num)', 'Admin\AdminExamController::examResults/$1', ['filter' => 'examaccess']);
    $routes->get('download-results/(:num)', 'Admin\AdminExamController::downloadResults/$1', ['filter' => 'examaccess']);
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
