<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class TestDatabase extends BaseCommand
{
    protected $group       = 'Test';
    protected $name        = 'test:database';
    protected $description = 'Test database connection and user counts';

    public function run(array $params)
    {
        $userModel = new UserModel();

        try {
            // Test basic connection
            $totalUsers = $userModel->countAllResults();
            CLI::write("Total users: " . $totalUsers);

            // Test role-based counts
            $totalAdmins = $userModel->where('role', 'admin')->countAllResults();
            CLI::write("Total admins: " . $totalAdmins);

            $totalTeachers = $userModel->where('role', 'teacher')->countAllResults();
            CLI::write("Total teachers: " . $totalTeachers);

            $totalStudents = $userModel->where('role', 'student')->countAllResults();
            CLI::write("Total students: " . $totalStudents);

            // Test if there are any users at all
            $users = $userModel->findAll();
            CLI::write("Found users:");
            foreach ($users as $user) {
                CLI::write("- ID: {$user['id']}, Username: {$user['username']}, Role: {$user['role']}");
            }
        } catch (\Exception $e) {
            CLI::error("Database error: " . $e->getMessage());
            CLI::error("Stack trace: " . $e->getTraceAsString());
        }
    }
}
