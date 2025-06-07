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
        $data = [
            'totalUsers'    => $this->userModel->countAllResults(),
            'totalSubjects' => $this->subjectModel->countAllResults(),
            'totalExams'    => $this->examModel->countAllResults(),
            'activeExams'   => count($this->examModel->getActiveExams()),
            'recentUsers'   => $this->userModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->findAll(),
            'recentExams'   => $this->examModel
                ->orderBy('created_at', 'DESC')
                ->limit(5)
                ->getExamsWithDetails(),
        ];

        return view('admin/dashboard', $data);
    }

    // User Management
    public function users($role = null)
    {
        $search = $this->request->getGet('search');

        $builder = $this->userModel;

        if ($role && in_array($role, ['admin', 'teacher', 'student'])) {
            $builder = $builder->where('role', $role);
        }

        if ($search) {
            $builder = $builder
                ->groupStart()
                ->like('username', $search)
                ->orLike('email', $search)
                ->orLike('full_name', $search)
                ->groupEnd();
        }

        $data = [
            'users' => $builder->findAll(),
            'role'  => $role,
            'search' => $search,
        ];

        return view('admin/users', $data);
    }

    public function createUser()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'username' => $this->request->getPost('username'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'full_name' => $this->request->getPost('full_name'),
                'role' => $this->request->getPost('role')
            ];

            if ($this->userModel->insert($data)) {
                session()->setFlashdata('success', 'User berhasil ditambahkan!');
                return redirect()->to('/admin/users');
            } else {
                session()->setFlashdata('error', 'Gagal menambahkan user! ' . implode(', ', $this->userModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        return view('admin/create_user');
    }

    public function editUser($id)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User tidak ditemukan');
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
                session()->setFlashdata('success', 'User berhasil diupdate!');
                return redirect()->to('/admin/users');
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate user! ' . implode(', ', $this->userModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        return view('admin/edit_user', ['user' => $user]);
    }

    public function deleteUser($id)
    {
        if ($this->userModel->delete($id)) {
            session()->setFlashdata('success', 'User berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus user!');
        }

        return redirect()->to('/admin/users');
    }

    // Subject Management
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

    public function deleteSubject($id)
    {
        if ($this->subjectModel->delete($id)) {
            session()->setFlashdata('success', 'Mata pelajaran berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus mata pelajaran!');
        }

        return redirect()->to('/admin/subjects');
    }

    // Exam Management
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

        if ($statusFilter) {
            $builder->where('exams.status', $statusFilter);
        }

        if ($search) {
            $builder->like('exams.title', $search);
        }

        $data = [
            'exams'        => $builder->orderBy('exams.created_at', 'DESC')->findAll(),
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
}
