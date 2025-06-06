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
    $routes->get('dashboard', 'AdminController::dashboard');

    // User management
    $routes->get('users/(:segment)', 'AdminController::users/$1');
    $routes->get('users', 'AdminController::users');
    $routes->get('create-user', 'AdminController::createUser');
    $routes->post('create-user', 'AdminController::createUser');
    $routes->get('edit-user/(:num)', 'AdminController::editUser/$1');
    $routes->post('edit-user/(:num)', 'AdminController::editUser/$1');
    $routes->get('delete-user/(:num)', 'AdminController::deleteUser/$1');

    // Subject management
    $routes->get('subjects', 'AdminController::subjects');
    $routes->get('create-subject', 'AdminController::createSubject');
    $routes->post('create-subject', 'AdminController::createSubject');
    $routes->get('edit-subject/(:num)', 'AdminController::editSubject/$1');
    $routes->post('edit-subject/(:num)', 'AdminController::editSubject/$1');
    $routes->get('delete-subject/(:num)', 'AdminController::deleteSubject/$1');

    // Exam management
    $routes->get('exams', 'AdminController::exams');
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
    $routes->post('grade-answer', 'ApiController::gradeAnswer');
    $routes->get('exam-time-remaining/(:num)', 'ApiController::getTimeRemaining/$1');
});
