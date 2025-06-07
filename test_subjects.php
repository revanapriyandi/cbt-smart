<?php

// Simple test script to check subjects functionality
require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$app = require_once APPPATH . '../app/Config/Paths.php';
$app = new \CodeIgniter\CodeIgniter($app);
$app->initialize();

use App\Models\SubjectModel;
use App\Models\UserModel;

try {
    echo "Testing Subject Model...\n";

    $subjectModel = new SubjectModel();
    $userModel = new UserModel();

    // Test basic connection
    echo "1. Testing database connection...\n";
    $count = $subjectModel->countAllResults(false);
    echo "   - Total subjects: $count\n";

    // Test getSubjectsWithDetails method
    echo "2. Testing getSubjectsWithDetails method...\n";
    $subjects = $subjectModel->getSubjectsWithDetails();
    echo "   - Subjects with details count: " . count($subjects) . "\n";

    // Test getTeachers method
    echo "3. Testing getTeachers method...\n";
    $teachers = $userModel->getTeachers();
    echo "   - Total teachers: " . count($teachers) . "\n";

    // Test getSubjectStatistics method
    echo "4. Testing getSubjectStatistics method...\n";
    if ($count > 0) {
        $firstSubject = $subjectModel->first();
        if ($firstSubject) {
            $stats = $subjectModel->getSubjectStatistics($firstSubject['id']);
            echo "   - Statistics for subject '{$firstSubject['name']}': " . ($stats ? 'OK' : 'ERROR') . "\n";
        }
    }

    echo "\nAll tests completed successfully!\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
