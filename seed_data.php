<?php

define('ROOTPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('FCPATH', ROOTPATH . 'public' . DIRECTORY_SEPARATOR);
define('SYSTEMPATH', ROOTPATH . 'vendor/codeigniter4/framework/system/');
define('APPPATH', ROOTPATH . 'app' . DIRECTORY_SEPARATOR);
define('WRITEPATH', ROOTPATH . 'writable' . DIRECTORY_SEPARATOR);

require_once ROOTPATH . 'vendor/autoload.php';

$paths = new Config\Paths();
$bootstrap = \CodeIgniter\Boot::bootWeb($paths);
$app = \CodeIgniter\Config\Services::codeigniter();

use Config\Database;

// Initialize database connection
$db = Database::connect();

// Check if users exist
$users = $db->table('users')->get()->getResult();

if (empty($users)) {
    echo "Adding sample data...\n";

    // Insert users
    $userData = [
        [
            'username' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin',
            'full_name' => 'Administrator',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'username' => 'teacher1',
            'email' => 'teacher1@cbt.com',
            'password' => password_hash('teacher123', PASSWORD_DEFAULT),
            'role' => 'teacher',
            'full_name' => 'Teacher One',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'username' => 'student1',
            'email' => 'student1@cbt.com',
            'password' => password_hash('student123', PASSWORD_DEFAULT),
            'role' => 'student',
            'full_name' => 'Student One',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];

    $db->table('users')->insertBatch($userData);

    // Insert subjects
    $subjectData = [
        [
            'name' => 'Matematika',
            'description' => 'Mata pelajaran Matematika',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ],
        [
            'name' => 'Bahasa Indonesia',
            'description' => 'Mata pelajaran Bahasa Indonesia',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    ];

    $db->table('subjects')->insertBatch($subjectData);

    echo "Sample data added successfully!\n";
} else {
    echo "Data already exists. Users count: " . count($users) . "\n";
}

echo "Login credentials:\n";
echo "Admin: admin / admin123\n";
echo "Teacher: teacher1 / teacher123\n";
echo "Student: student1 / student123\n";
