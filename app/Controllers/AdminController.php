<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\SubjectModel;
use App\Models\ExamModel;
use App\Models\ExamResultModel;

class AdminController extends BaseController
{
    protected $userModel;
    protected $subjectModel;
    protected $examModel;
    protected $examResultModel;
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->subjectModel = new SubjectModel();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
    }

    public function dashboard()
    {
        $recentExams = $this->examModel
            ->orderBy('created_at', 'DESC')
            ->limit(5)
            ->getExamsWithDetails();

        // Calculate status for recent exams
        $now = new \DateTime();
        foreach ($recentExams as &$exam) {
            $start = new \DateTime($exam['start_time']);
            $end = new \DateTime($exam['end_time']);

            if (!$exam['is_active']) {
                $exam['status'] = 'draft';
            } elseif ($now < $start) {
                $exam['status'] = 'upcoming';
            } elseif ($now >= $start && $now <= $end) {
                $exam['status'] = 'active';
            } else {
                $exam['status'] = 'completed';
            }
        }

        $data = [
            'totalUsers'    => $this->userModel->countAllResults(),
            'totalSubjects' => $this->subjectModel->countAllResults(),
            'totalExams'    => $this->examModel->countAllResults(),
            'activeExams'   => count($this->examModel->getActiveExams()),
            'recentUsers'   => $this->userModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll(),
            'recentExams'   => $recentExams,
        ];

        return view('admin/dashboard', $data);
    }

    // ========================================
    // USER MANAGEMENT SECTION
    // ========================================

    /**
     * Display users management page
     */
    public function users($role = null)
    {
        $data = [
            'role' => $role,
            'totalUsers' => $this->userModel->countAllResults(),
            'totalAdmins' => $this->userModel->where('role', 'admin')->countAllResults(),
            'totalTeachers' => $this->userModel->where('role', 'teacher')->countAllResults(),
            'totalStudents' => $this->userModel->where('role', 'student')->countAllResults(),
            'activeUsers' => $this->userModel->where('is_active', 1)->countAllResults(),
        ];

        return view('admin/users/index', $data);
    }
    /**
     * Get users data for DataTables AJAX
     */    public function getUsersData()
    {
        $role = $this->request->getGet('role');
        $search = $this->request->getGet('search');
        $status = $this->request->getGet('status');

        $builder = $this->userModel->select('id, username, email, full_name, role, is_active, created_at');

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

        if ($search) {
            $builder->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('full_name', $search)
                ->groupEnd();
        }

        // Get total count before applying filters
        $totalRecords = $this->userModel->countAllResults(false);
        
        // Get filtered count
        $filteredRecords = $builder->countAllResults(false);
        
        // Get actual data
        $users = $builder->orderBy('created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'data' => $users,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords
        ]);
    }

    /**
     * Create new user (both GET and POST)
     */
    public function createUser()
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
                return $this->response->setJSON(['success' => true, 'message' => 'User berhasil ditambahkan!']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal menambahkan user!', 'errors' => $this->userModel->errors()]);
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
            ];

            // Only update password if provided
            $password = $this->request->getPost('password');
            if (!empty($password)) {
                $data['password'] = $password;
            }

            if ($this->userModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'User berhasil diupdate!']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengupdate user!', 'errors' => $this->userModel->errors()]);
            }
        }

        return view('admin/users/edit', ['user' => $user]);
    }

    /**
     * Delete user
     */
    public function deleteUser($id)
    {
        if ($this->userModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'User berhasil dihapus!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus user!']);
        }
    }    /**
     * Export users to Excel
     */
    public function exportUsers()
    {
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
        require_once APPPATH . '../vendor/autoload.php';
        
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

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
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
     */    public function bulkAction()
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

    // ========================================
    // SUBJECT MANAGEMENT SECTION
    // ========================================
    public function subjects()
    {
        $data = [
            'subjects' => $this->subjectModel->getSubjectsWithTeacher(),
            'teachers' => $this->userModel->getTeachers()
        ];

        return view('admin/subjects', $data);
    }

    public function createSubject()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null
            ];

            if ($this->subjectModel->insert($data)) {
                session()->setFlashdata('success', 'Mata pelajaran berhasil ditambahkan!');
                return redirect()->to('/admin/subjects');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan mata pelajaran! ' . implode(', ', $this->subjectModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = ['teachers' => $this->userModel->getTeachers()];
        return view('admin/create_subject', $data);
    }

    public function editSubject($id)
    {
        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Mata pelajaran tidak ditemukan');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null
            ];

            if ($this->subjectModel->update($id, $data)) {
                session()->setFlashdata('success', 'Mata pelajaran berhasil diupdate!');
                return redirect()->to('/admin/subjects');
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate mata pelajaran! ' . implode(', ', $this->subjectModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'subject' => $subject,
            'teachers' => $this->userModel->getTeachers()
        ];
        return view('admin/edit_subject', $data);
    }

    public function getSubject($id)
    {
        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            return $this->response->setJSON(['error' => 'Subject not found'], 404);
        }

        return $this->response->setJSON($subject);
    }

    public function deleteSubject($id)
    {
        if ($this->subjectModel->delete($id)) {
            session()->setFlashdata('success', 'Mata pelajaran berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus mata pelajaran!');
        }

        return redirect()->to('/admin/subjects');
    }    // Exam Management
    public function exams()
    {
        $search = $this->request->getGet('search');
        $subjectFilter = $this->request->getGet('subject_id');
        $statusFilter = $this->request->getGet('status');

        $builder = $this->examModel
            ->select('exams.*, subjects.name as subject_name, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('users', 'users.id = exams.teacher_id');

        if ($subjectFilter) {
            $builder->where('exams.subject_id', $subjectFilter);
        }

        if ($search) {
            $builder->like('exams.title', $search);
        }

        $exams = $builder->orderBy('exams.created_at', 'DESC')->findAll();

        // Calculate status for each exam
        $now = new \DateTime();
        foreach ($exams as &$exam) {
            $start = new \DateTime($exam['start_time']);
            $end = new \DateTime($exam['end_time']);

            if (!$exam['is_active']) {
                $exam['status'] = 'draft';
            } elseif ($now < $start) {
                $exam['status'] = 'scheduled';
            } elseif ($now >= $start && $now <= $end) {
                $exam['status'] = 'active';
            } else {
                $exam['status'] = 'completed';
            }
        }

        // Filter by status if requested
        if ($statusFilter) {
            $exams = array_filter($exams, function ($exam) use ($statusFilter) {
                return $exam['status'] === $statusFilter;
            });
        }

        $data = [
            'exams'        => $exams,
            'subjects'     => $this->subjectModel->findAll(),
            'search'       => $search,
            'subjectFilter' => $subjectFilter,
            'statusFilter' => $statusFilter,
        ];

        return view('admin/exams', $data);
    }

    public function examResults($examId)
    {
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        $data = [
            'exam' => $exam,
            'results' => $this->examResultModel->getResultsByExam($examId)
        ];

        return view('admin/exam_results', $data);
    }

    public function downloadResults($examId)
    {
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        $results = $this->examResultModel->getResultsByExam($examId);

        // Generate CSV
        $filename = 'hasil_ujian_' . $exam['title'] . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, ['Nama Siswa', 'Username', 'Total Skor', 'Skor Maksimal', 'Persentase', 'Status', 'Waktu Mulai', 'Waktu Selesai']);

        // CSV Data
        foreach ($results as $result) {
            fputcsv($output, [
                $result['student_name'],
                $result['username'],
                $result['total_score'],
                $result['max_total_score'],
                $result['percentage'] . '%',
                ucfirst($result['status']),
                $result['started_at'],
                $result['submitted_at']
            ]);
        }
        fclose($output);
        exit;
    }
    public function createExam()
    {
        if ($this->request->getMethod() === 'POST') {
            // Get PDF URL from either final_pdf_url (uploaded) or pdf_url (direct URL)
            $pdfUrl = $this->request->getPost('final_pdf_url') ?: $this->request->getPost('pdf_url');

            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'teacher_id' => $this->request->getPost('teacher_id'),
                'pdf_url' => $pdfUrl,
                'question_count' => $this->request->getPost('question_count'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Validate that we have a PDF URL
            if (empty($data['pdf_url'])) {
                session()->setFlashdata('error', 'PDF URL is required. Please parse a PDF first.');
                return redirect()->back()->withInput();
            }

            if ($this->examModel->insert($data)) {
                session()->setFlashdata('success', 'Ujian berhasil ditambahkan!');
                return redirect()->to('/admin/exams');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan ujian! ' . implode(', ', $this->examModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'subjects' => $this->subjectModel->findAll(),
            'teachers' => $this->userModel->getTeachers()
        ];

        return view('admin/create_exam', $data);
    }

    public function editExam($id)
    {
        $exam = $this->examModel->find($id);

        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'teacher_id' => $this->request->getPost('teacher_id'),
                'pdf_url' => $this->request->getPost('pdf_url'),
                'question_count' => $this->request->getPost('question_count'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->examModel->update($id, $data)) {
                session()->setFlashdata('success', 'Ujian berhasil diupdate!');
                return redirect()->to('/admin/exams');
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate ujian! ' . implode(', ', $this->examModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'exam' => $exam,
            'subjects' => $this->subjectModel->findAll(),
            'teachers' => $this->userModel->getTeachers()
        ];

        return view('admin/edit_exam', $data);
    }

    public function deleteExam($id)
    {
        if ($this->examModel->delete($id)) {
            session()->setFlashdata('success', 'Ujian berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus ujian!');
        }

        return redirect()->to('/admin/exams');
    }

    public function viewExam($id)
    {
        $exam = $this->examModel->find($id);

        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        // Get exam details with related data
        $examDetails = $this->examModel
            ->select('exams.*, subjects.name as subject_name, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('users', 'users.id = exams.teacher_id')
            ->where('exams.id', $id)
            ->first();

        // Get exam statistics
        $stats = [
            'total_participants' => $this->examResultModel->where('exam_id', $id)->countAllResults(),
            'completed_participants' => $this->examResultModel->where('exam_id', $id)->where('status', 'submitted')->countAllResults(),
            'graded_participants' => $this->examResultModel->where('exam_id', $id)->where('status', 'graded')->countAllResults(),
            'average_score' => $this->examResultModel->getAverageScore($id)
        ];

        $data = [
            'exam' => $examDetails,
            'stats' => $stats
        ];

        return view('admin/view_exam', $data);
    }

    public function publishExam($id)
    {
        $exam = $this->examModel->find($id);

        if (!$exam) {
            session()->setFlashdata('error', 'Ujian tidak ditemukan!');
            return redirect()->to('/admin/exams');
        }

        // Update exam status to active
        if ($this->examModel->update($id, ['is_active' => 1])) {
            session()->setFlashdata('success', 'Ujian berhasil dipublikasikan!');
        } else {
            session()->setFlashdata('error', 'Gagal mempublikasikan ujian!');
        }

        return redirect()->to('/admin/exams');
    }
}
