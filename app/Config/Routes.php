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
    $routes->get('dashboard', 'AdminController::dashboard');    // User management - organized routes
    $routes->get('users', 'AdminController::users');
    $routes->get('users/create', 'AdminController::createUser');
    $routes->get('users/edit/(:num)', 'AdminController::editUser/$1');
    $routes->get('users/import', 'AdminController::importUsers');
    $routes->get('users/(:segment)', 'AdminController::users/$1');
    $routes->get('users-data', 'AdminController::getUsersData');
    $routes->get('users/get/(:num)', 'AdminController::getUser/$1');
    $routes->post('users/store', 'AdminController::createUser');
    $routes->post('users/update/(:num)', 'AdminController::editUser/$1');
    $routes->post('users/delete/(:num)', 'AdminController::deleteUser/$1');
    $routes->get('users/export', 'AdminController::exportUsers');
    $routes->post('users/import', 'AdminController::importUsers');
    $routes->post('users/bulk-action', 'AdminController::bulkAction');
    $routes->get('users/sample-csv', 'AdminController::sampleCsv'); // Subject management
    $routes->get('subjects', 'AdminController::subjects');
    $routes->get('create-subject', 'AdminController::createSubject');
    $routes->post('create-subject', 'AdminController::createSubject');
    $routes->post('subjects/store', 'AdminController::createSubject');
    $routes->get('subjects/get/(:num)', 'AdminController::getSubject/$1');
    $routes->post('subjects/update/(:num)', 'AdminController::editSubject/$1');
    $routes->get('subjects/delete/(:num)', 'AdminController::deleteSubject/$1');
    $routes->get('edit-subject/(:num)', 'AdminController::editSubject/$1');
    $routes->post('edit-subject/(:num)', 'AdminController::editSubject/$1');
    $routes->get('delete-subject/(:num)', 'AdminController::deleteSubject/$1');    // Exam management
    $routes->get('exams', 'AdminController::exams');
    $routes->get('exams/create', 'AdminController::createExam');
    $routes->post('exams/create', 'AdminController::createExam');
    $routes->get('exams/view/(:num)', 'AdminController::viewExam/$1');
    $routes->get('exams/publish/(:num)', 'AdminController::publishExam/$1');
    $routes->get('exams/edit/(:num)', 'AdminController::editExam/$1');
    $routes->post('exams/edit/(:num)', 'AdminController::editExam/$1');
    $routes->get('exams/delete/(:num)', 'AdminController::deleteExam/$1');
    $routes->get('exam-results/(:num)', 'AdminController::examResults/$1');
    $routes->get('download-results/(:num)', 'AdminController::downloadResults/$1');
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
    $routes->get('result-detail/(:num)', 'StudentController::resultDetail/$1');
});

// API routes for AJAX calls
$routes->group('api', function ($routes) {
    $routes->post('parse-pdf', 'ApiController::parsePdf');
    $routes->post('parse-pdf-admin', 'ApiController::parsePdfAdmin');
    $routes->post('grade-answer', 'ApiController::gradeAnswer');
    $routes->get('exam-time-remaining/(:num)', 'ApiController::getTimeRemaining/$1');
    $routes->post('log-activity', 'ApiController::logActivity');
});
