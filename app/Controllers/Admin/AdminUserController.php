<?php

namespace App\Controllers\Admin;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AdminUserController extends BaseAdminController
{
    /**
     * Display users management page
     */    public function users($role = null)
    {
        // Use fresh query builders for each count to avoid chain interference
        $userModel = new \App\Models\UserModel();

        $data = [
            'role' => $role,
            'totalUsers' => $userModel->countAllResults(),
            'totalAdmins' => (new \App\Models\UserModel())->where('role', 'admin')->countAllResults(),
            'totalTeachers' => (new \App\Models\UserModel())->where('role', 'teacher')->countAllResults(),
            'totalStudents' => (new \App\Models\UserModel())->where('role', 'student')->countAllResults(),
            'activeUsers' => (new \App\Models\UserModel())->where('is_active', 1)->countAllResults(),
        ];

        return view('admin/users/index', $data);
    }
    /**
     * Get users data for DataTables AJAX
     */
    public function getUsersData()
    {
        try {
            // Get DataTables parameters
            $draw = intval($this->request->getGet('draw') ?? 1);
            $start = intval($this->request->getGet('start') ?? 0);
            $length = intval($this->request->getGet('length') ?? 25);

            // Get custom filters
            $role = $this->request->getGet('role');
            $customSearch = $this->request->getGet('search');
            $status = $this->request->getGet('status');

            // Get DataTables search value - handle both string and array formats
            $searchParam = $this->request->getGet('search');
            $searchValue = '';
            if (is_array($searchParam) && isset($searchParam['value'])) {
                $searchValue = $searchParam['value'];
            } elseif (is_string($searchParam)) {
                $searchValue = $searchParam;
            } elseif (is_string($customSearch)) {
                $searchValue = $customSearch;
            }

            // Debug logging for search parameters
            log_message('debug', "Search parameters - customSearch: " . var_export($customSearch, true) . ", searchParam: " . var_export($searchParam, true) . ", final searchValue: " . $searchValue);

            // Get total records count (without any filters) - use fresh query
            $totalRecords = $this->userModel->countAllResults(false);            // Build query with filters - start fresh
            $builder = $this->userModel->select('users.id, users.username, users.email, users.full_name, users.role, users.is_active, users.created_at, users.last_login');

            // Apply custom filters
            if ($role && in_array($role, ['admin', 'teacher', 'student'])) {
                $builder->where('role', $role);
            }

            if ($status !== '' && $status !== null) {
                if ($status === 'active') {
                    $builder->where('is_active', 1);
                } elseif ($status === 'inactive') {
                    $builder->where('is_active', 0);
                }
            }

            // Apply search filter
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('username', $searchValue)
                    ->orLike('email', $searchValue)
                    ->orLike('full_name', $searchValue)
                    ->groupEnd();
            }

            // Get filtered records count - clone the builder before counting
            $filteredRecords = $this->userModel->select('id, username, email, full_name, role, is_active, created_at');

            // Reapply all filters for counting
            if ($role && in_array($role, ['admin', 'teacher', 'student'])) {
                $filteredRecords->where('role', $role);
            }

            if ($status !== '' && $status !== null) {
                if ($status === 'active') {
                    $filteredRecords->where('is_active', 1);
                } elseif ($status === 'inactive') {
                    $filteredRecords->where('is_active', 0);
                }
            }

            if (!empty($searchValue)) {
                $filteredRecords->groupStart()
                    ->like('username', $searchValue)
                    ->orLike('email', $searchValue)
                    ->orLike('full_name', $searchValue)
                    ->groupEnd();
            }

            $filteredCount = $filteredRecords->countAllResults(false);

            // Apply pagination to main query
            if ($length != -1) {
                $builder->limit($length, $start);
            }            // Get actual data with ordering
            $users = $builder->orderBy('created_at', 'DESC')->findAll();

            // Add activity counts for each user
            $activityLogModel = new \App\Models\UserActivityLogModel();
            foreach ($users as &$user) {
                $user['activity_count'] = $activityLogModel->where('user_id', $user['id'])->countAllResults();
            }

            // Debug logging
            log_message('debug', "Users data - Draw: {$draw}, Total: {$totalRecords}, Filtered: {$filteredCount}, Data count: " . count($users) . ", Start: {$start}, Length: {$length}, Role: {$role}, Status: {$status}, Search: '{$searchValue}'");

            return $this->response->setJSON([
                'draw' => $draw,
                'data' => $users,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getUsersData: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw') ?? 1),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => 'Failed to fetch users data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Create new user (both GET and POST)
     */
    public function store()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'role' => $this->request->getPost('role'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];
            if ($this->userModel->insert($data)) {
                $newUserId = $this->userModel->getInsertID();

                // Log user creation activity
                try {
                    $currentUserId = session()->get('user_id');
                    if ($currentUserId) {
                        $this->userActivityLogModel->logActivity(
                            $currentUserId,
                            'user_create',
                            "Created new user: {$data['username']} with role {$data['role']}",
                            $this->request->getIPAddress(),
                            $this->request->getUserAgent()
                        );
                    }
                } catch (\Exception $e) {
                    // Log the error but don't fail the user creation
                    log_message('error', 'Failed to log user creation activity: ' . $e->getMessage());
                }

                // Check if it's an AJAX request
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil ditambahkan!']);
                } else {
                    // For regular form submissions, redirect with success message
                    session()->setFlashdata('success', 'User berhasil ditambahkan!');
                    return redirect()->to('/admin/users');
                }
            } else {
                // Check if it's an AJAX request
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan user!', 'errors' => $this->userModel->errors()]);
                } else {
                    // For regular form submissions, redirect with error message
                    session()->setFlashdata('error', 'Gagal menambahkan user! ' . implode(', ', $this->userModel->errors()));
                    return redirect()->back()->withInput();
                }
            }
        }

        return view('admin/users/create');
    }

    /**
     * Get single user data
     */
    public function getUser($id)
    {
        $user = $this->userModel->find($id);
        if ($user) {
            return $this->response->setJSON($user);
        }
        return $this->response->setJSON(['error' => 'User not found'], 404);
    }

    /**
     * Edit user (both GET and POST)
     */
    public function editUser($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User tidak ditemukan'], 404);
        }
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'full_name' => $this->request->getPost('full_name'),
                'role' => $this->request->getPost('role'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }            // Validate and update the user
            if ($this->userModel->updateUser($id, $data)) {
                // Log user update activity for the admin user
                try {
                    $currentUserId = session()->get('user_id');
                    if ($currentUserId) {
                        $this->userActivityLogModel->logActivity(
                            $currentUserId,
                            'user_update',
                            "Updated user: {$user['username']}",
                            $this->request->getIPAddress(),
                            $this->request->getUserAgent()
                        );
                    }

                    // Also log activity for the updated user if it's a profile change
                    // Only log if the user being updated is different from the current user
                    // and the user still exists (to avoid foreign key constraint errors)
                    if ($id != $currentUserId && $this->userModel->find($id)) {
                        $this->userActivityLogModel->logActivity(
                            $id,
                            'profile_update',
                            'Profile updated by administrator',
                            $this->request->getIPAddress(),
                            $this->request->getUserAgent()
                        );
                    }
                } catch (\Exception $e) {
                    // Log the error but don't fail the user update
                    log_message('error', 'Failed to log user activity: ' . $e->getMessage());
                }

                // Check if it's an AJAX request
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'User berhasil diupdate!']);
                } else {
                    // For regular form submissions, redirect with success message
                    session()->setFlashdata('success', 'User berhasil diupdate!');
                    return redirect()->to('/admin/users');
                }
            } else {
                // Check if it's an AJAX request
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate user!', 'errors' => $this->userModel->errors()]);
                } else {
                    // For regular form submissions, redirect with error message
                    session()->setFlashdata('error', 'Gagal mengupdate user! ' . implode(', ', $this->userModel->errors()));
                    return redirect()->back()->withInput();
                }
            }
        }

        return view('admin/users/edit', ['user' => $user]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        $user = $this->userModel->find($id);
        if ($this->userModel->delete($id)) {
            // Log user deletion activity
            try {
                $currentUserId = session()->get('user_id');
                if ($currentUserId && $user) {
                    $this->userActivityLogModel->logActivity(
                        $currentUserId,
                        'user_delete',
                        "Deleted user: {$user['username']}",
                        $this->request->getIPAddress(),
                        $this->request->getUserAgent()
                    );
                }
            } catch (\Exception $e) {
                // Log the error but don't fail the deletion
                log_message('error', 'Failed to log user deletion activity: ' . $e->getMessage());
            }

            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dihapus!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus user!']);
        }
    }
    /**
     * Export users to Excel
     */
    public function exportUsers()
    {
        try {
            $role = $this->request->getGet('role');
            $status = $this->request->getGet('status');

            $builder = $this->userModel->select('username, email, full_name, role, is_active, created_at');

            if ($role && in_array($role, ['admin', 'teacher', 'student'])) {
                $builder->where('role', $role);
            }

            if ($status !== '' && $status !== null) {
                if ($status === 'active') {
                    $builder->where('is_active', 1);
                } elseif ($status === 'inactive') {
                    $builder->where('is_active', 0);
                }
            }

            $users = $builder->orderBy('created_at', 'DESC')->findAll();

            // Load PhpSpreadsheet
            require_once ROOTPATH . 'vendor/autoload.php';

            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set headers
            $headers = ['Username', 'Email', 'Full Name', 'Role', 'Status', 'Created At'];
            $columnLetter = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue($columnLetter . '1', $header);
                $sheet->getStyle($columnLetter . '1')->getFont()->setBold(true);
                $sheet->getColumnDimension($columnLetter)->setAutoSize(true);
                $columnLetter++;
            }

            // Add data
            $row = 2;
            foreach ($users as $user) {
                $sheet->setCellValue('A' . $row, $user['username']);
                $sheet->setCellValue('B' . $row, $user['email']);
                $sheet->setCellValue('C' . $row, $user['full_name']);
                $sheet->setCellValue('D' . $row, ucfirst($user['role']));
                $sheet->setCellValue('E' . $row, $user['is_active'] ? 'Active' : 'Inactive');
                $sheet->setCellValue('F' . $row, $user['created_at']);
                $row++;
            }

            // Style the header row
            $sheet->getStyle('A1:F1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FF4F46E5');
            $sheet->getStyle('A1:F1')->getFont()->getColor()->setARGB('FFFFFFFF');

            // Set filename
            $filename = 'users_' . ($role ? $role . '_' : '') . ($status ? $status . '_' : '') . date('Y-m-d_H-i-s') . '.xlsx';

            // Clear any previous output
            if (ob_get_level()) {
                ob_end_clean();
            }

            // Set headers for download
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"');
            header('Cache-Control: max-age=0');
            header('Pragma: public');

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Export users error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Export failed: ' . $e->getMessage());
            return redirect()->to('/admin/users');
        }
    }

    /**
     * Import users from CSV
     */
    public function importUsers()
    {
        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');

            if (!$file->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid!']);
            }

            if ($file->getExtension() !== 'csv') {
                return $this->response->setJSON(['success' => false, 'message' => 'File harus berformat CSV!']);
            }

            $csvData = array_map('str_getcsv', file($file->getTempName()));
            $header = array_shift($csvData); // Remove header row

            $imported = 0;
            $errors = [];

            foreach ($csvData as $row) {
                if (count($row) < 4) continue; // Skip incomplete rows

                $data = [
                    'username' => $row[0],
                    'email' => $row[1],
                    'full_name' => $row[2],
                    'role' => strtolower($row[3]),
                    'password' => password_hash('default123', PASSWORD_DEFAULT), // Default password
                    'is_active' => 1
                ];

                if ($this->userModel->insert($data)) {
                    $imported++;
                } else {
                    $errors[] = "Row " . ($imported + count($errors) + 2) . ": " . implode(', ', $this->userModel->errors());
                }
            }

            $message = "Imported {$imported} users successfully.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
            }

            return $this->response->setJSON(['success' => true, 'message' => $message]);
        }

        return view('admin/users/import');
    }

    /**
     * Download sample CSV file for user import
     */
    public function sampleCsv()
    {
        $filename = 'sample_users_import.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, ['username', 'email', 'full_name', 'role']);

        // Sample data
        fputcsv($output, ['john_doe', 'john@example.com', 'John Doe', 'student']);
        fputcsv($output, ['jane_smith', 'jane@example.com', 'Jane Smith', 'teacher']);
        fputcsv($output, ['admin_user', 'admin@example.com', 'Admin User', 'admin']);

        fclose($output);
        exit;
    }

    /**
     * Bulk action for multiple users
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $userIds = $this->request->getPost('user_ids');

        // Handle JSON string
        if (is_string($userIds)) {
            $userIds = json_decode($userIds, true);
        }

        if (empty($userIds) || !is_array($userIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No users selected!']);
        }

        $affected = 0;

        switch ($action) {
            case 'activate':
                $affected = $this->userModel->whereIn('id', $userIds)->set(['is_active' => 1])->update();
                $message = "Activated {$affected} users";
                break;

            case 'deactivate':
                $affected = $this->userModel->whereIn('id', $userIds)->set(['is_active' => 0])->update();
                $message = "Deactivated {$affected} users";
                break;

            case 'delete':
                $affected = $this->userModel->whereIn('id', $userIds)->delete();
                $message = "Deleted {$affected} users";
                break;

            default:
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid action!']);
        }
        return $this->response->setJSON(['success' => true, 'message' => $message]);
    }
    /**
     * Get user activity statistics
     */
    public function getUserActivityStats($userId)
    {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            $stats = $this->userActivityLogModel->getUserActivityStats($userId);

            // If user is a student, get exam stats too
            if ($user['role'] === 'student') {
                $examStats = $this->examResultModel
                    ->where('student_id', $userId)
                    ->where('status', 'graded')
                    ->findAll();

                $totalExams = count($examStats);
                $averageScore = 0;

                if ($totalExams > 0) {
                    $totalScore = array_sum(array_column($examStats, 'percentage'));
                    $averageScore = round($totalScore / $totalExams, 1);
                }

                $stats['total_exams'] = $totalExams;
                $stats['average_score'] = $averageScore;
            } else {
                $stats['total_exams'] = 0;
                $stats['average_score'] = 0;
            }

            return $this->response->setJSON(['success' => true, 'data' => $stats]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting user activity stats: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error loading activity stats']);
        }
    }

    /**
     * Get user recent activities
     */
    public function getUserRecentActivities($userId)
    {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            $activities = $this->userActivityLogModel->getUserActivity($userId, 10);

            // Format activities for frontend
            foreach ($activities as &$activity) {
                $activity['icon'] = $this->getActivityIcon($activity['activity_type']);
                $activity['time_ago'] = $this->timeAgo($activity['created_at']);
                $activity['description'] = $activity['activity_description'];
            }

            return $this->response->setJSON(['success' => true, 'data' => $activities]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting user recent activities: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error loading recent activities']);
        }
    }

    /**
     * Get user exam performance (for students only)
     */
    public function getUserExamPerformance($userId)
    {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
            }

            if ($user['role'] !== 'student') {
                return $this->response->setJSON(['success' => true, 'data' => []]);
            }

            $examResults = $this->examResultModel
                ->select('exam_results.*, exams.title as exam_title, subjects.name as subject_name')
                ->join('exams', 'exams.id = exam_results.exam_id')
                ->join('subjects', 'subjects.id = exams.subject_id')
                ->where('exam_results.student_id', $userId)
                ->where('exam_results.status', 'graded')
                ->orderBy('exam_results.submitted_at', 'DESC')
                ->limit(10)
                ->findAll();

            return $this->response->setJSON(['success' => true, 'data' => $examResults]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting user exam performance: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error loading exam performance']);
        }
    }

    /**
     * View user details
     */
    public function viewUser($id)
    {
        try {
            // Get user data
            $user = $this->userModel->find($id);
            if (!$user) {
                throw new \Exception('User not found');
            }

            // Get user activity stats
            $userActivityLogModel = new \App\Models\UserActivityLogModel();
            $totalActivities = $userActivityLogModel->where('user_id', $id)->countAllResults();
            $recentActivities = $userActivityLogModel->where('user_id', $id)
                ->orderBy('created_at', 'DESC')
                ->limit(10)
                ->findAll();            // Get exam performance if user is student
            $examStats = [];
            $recentResults = [];
            if ($user['role'] === 'student') {
                $examResultModel = new \App\Models\ExamResultModel();
                $examModel = new \App\Models\ExamModel();

                // Get exam results with exam details
                $examResults = $examResultModel->select('exam_results.*, exams.title as exam_title, exams.description as exam_description, subjects.name as subject_name')
                    ->join('exams', 'exams.id = exam_results.exam_id', 'left')
                    ->join('subjects', 'subjects.id = exams.subject_id', 'left')
                    ->where('exam_results.student_id', $id)
                    ->orderBy('exam_results.created_at', 'DESC')
                    ->findAll();

                $totalExams = count($examResults);
                $completedExams = count(array_filter($examResults, function ($result) {
                    return $result['status'] === 'graded';
                }));
                $averageScore = 0;

                if ($completedExams > 0) {
                    $gradedResults = array_filter($examResults, function ($result) {
                        return $result['status'] === 'graded' && $result['percentage'] !== null;
                    });
                    if (count($gradedResults) > 0) {
                        $totalScore = array_sum(array_column($gradedResults, 'percentage'));
                        $averageScore = round($totalScore / count($gradedResults), 2);
                    }
                }

                $examStats = [
                    'total_exams' => $totalExams,
                    'completed_exams' => $completedExams,
                    'average_score' => $averageScore,
                    'pending_exams' => count(array_filter($examResults, function ($result) {
                        return $result['status'] === 'in_progress' || $result['status'] === 'started';
                    }))
                ];

                $recentResults = array_slice($examResults, 0, 5);
            } // Get created exams if user is teacher
            $createdExams = [];
            $subjectsData = [];
            if ($user['role'] === 'teacher') {
                $examModel = new \App\Models\ExamModel();
                $subjectModel = new \App\Models\SubjectModel();

                // Get exams created by teacher with subject details
                $createdExams = $examModel->select('exams.*, subjects.name as subject_name')
                    ->join('subjects', 'subjects.id = exams.subject_id', 'left')
                    ->where('exams.teacher_id', $id)
                    ->orderBy('exams.created_at', 'DESC')
                    ->limit(10)
                    ->findAll();

                // Get subjects taught by teacher (if there's a teacher_subjects table)
                // For now, we'll get unique subjects from their exams
                $subjectsData = $examModel->select('subjects.id, subjects.name, COUNT(exams.id) as exam_count')
                    ->join('subjects', 'subjects.id = exams.subject_id', 'left')
                    ->where('exams.teacher_id', $id)
                    ->groupBy('subjects.id')
                    ->findAll();
            }

            // Get admin activity stats if user is admin
            $adminStats = [];
            if ($user['role'] === 'admin') {
                $adminStats = [
                    'total_users_managed' => $this->userModel->countAllResults(),
                    'recent_user_actions' => $userActivityLogModel->where('user_id', $id)
                        ->whereIn('activity_type', ['user_create', 'user_update', 'user_delete'])
                        ->orderBy('created_at', 'DESC')
                        ->limit(10)
                        ->findAll()
                ];
            }
            $data = [
                'user' => $user,
                'totalActivities' => $totalActivities,
                'recentActivities' => $recentActivities,
                'examStats' => $examStats,
                'recentResults' => $recentResults,
                'createdExams' => $createdExams,
                'subjectsData' => $subjectsData,
                'adminStats' => $adminStats
            ];

            return view('admin/users/view', $data);
        } catch (\Exception $e) {
            log_message('error', 'Error viewing user: ' . $e->getMessage());
            return redirect()->to('/admin/users')->with('error', 'Failed to load user details: ' . $e->getMessage());
        }
    }
}
