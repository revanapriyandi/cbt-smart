<?php

/**
 * Admin Routes Verification Script
 * 
 * This script helps verify that the restructured admin routes are working correctly.
 * Run this from the command line: php spark app:test-admin-routes
 */

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class TestAdminRoutes extends BaseCommand
{
    protected $group = 'app';
    protected $name = 'app:test-admin-routes';
    protected $description = 'Test that restructured admin routes are accessible';

    public function run(array $params)
    {
        CLI::write('Testing Admin Controller Restructuring...', 'yellow');
        CLI::newLine();

        $routes = [
            'Dashboard' => '/admin/dashboard',
            'Users List' => '/admin/users',
            'Subjects List' => '/admin/subjects',
            'Exams List' => '/admin/exams',
            'Create User' => '/admin/create-user',
            'Create Subject' => '/admin/create-subject',
            'Create Exam' => '/admin/exams/create',
        ];

        $baseUrl = 'http://127.0.0.1:8080';

        foreach ($routes as $name => $route) {
            $url = $baseUrl . $route;
            CLI::write("Testing {$name}: {$route}", 'white');

            // Basic route existence test using cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only

            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                CLI::write("  ✅ OK (HTTP {$httpCode})", 'green');
            } elseif ($httpCode === 302 || $httpCode === 301) {
                CLI::write("  🔄 Redirect (HTTP {$httpCode}) - Expected for auth routes", 'yellow');
            } elseif ($httpCode === 404) {
                CLI::write("  ❌ Not Found (HTTP {$httpCode})", 'red');
            } else {
                CLI::write("  ⚠️  Unexpected response (HTTP {$httpCode})", 'yellow');
            }
        }

        CLI::newLine();
        CLI::write('Controller Class Tests:', 'yellow');

        // Test that controller classes exist and can be instantiated
        $controllers = [
            'AdminDashboardController' => '\\App\\Controllers\\Admin\\AdminDashboardController',
            'AdminUserController' => '\\App\\Controllers\\Admin\\AdminUserController',
            'AdminSubjectController' => '\\App\\Controllers\\Admin\\AdminSubjectController',
            'AdminExamController' => '\\App\\Controllers\\Admin\\AdminExamController',
            'BaseAdminController' => '\\App\\Controllers\\Admin\\BaseAdminController',
        ];

        foreach ($controllers as $name => $class) {
            if (class_exists($class)) {
                CLI::write("  ✅ {$name} class exists", 'green');

                // Test method existence for key methods
                $reflection = new \ReflectionClass($class);
                if ($name !== 'BaseAdminController') {
                    if ($reflection->hasMethod('index')) {
                        CLI::write("    ✅ index() method exists", 'green');
                    } else {
                        CLI::write("    ❌ index() method missing", 'red');
                    }
                }
            } else {
                CLI::write("  ❌ {$name} class not found", 'red');
            }
        }

        CLI::newLine();
        CLI::write('Security Filter Tests:', 'yellow');

        // Test security filter exists
        if (class_exists('\\App\\Filters\\ExamResultAccessFilter')) {
            CLI::write("  ✅ ExamResultAccessFilter exists", 'green');
        } else {
            CLI::write("  ❌ ExamResultAccessFilter not found", 'red');
        }

        CLI::newLine();
        CLI::write('✅ Admin restructuring verification complete!', 'green');
        CLI::write('🔍 Check the server logs at http://127.0.0.1:8080 for any runtime errors.', 'cyan');
    }
}
