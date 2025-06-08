<?php

namespace App\Controllers\Admin;

class AdminExamController extends BaseAdminController
{
    public function index()
    {
        return $this->exams();
    }

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

    public function results($examId)
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

    public function create()
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

    public function edit($id)
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

    public function delete($id)
    {
        if ($this->examModel->delete($id)) {
            session()->setFlashdata('success', 'Ujian berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus ujian!');
        }

        return redirect()->to('/admin/exams');
    }

    public function view($id)
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

    public function publish($id)
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

    public function createExam()
    {
        return $this->create();
    }

    public function editExam($id)
    {
        return $this->edit($id);
    }

    public function deleteExam($id)
    {
        return $this->delete($id);
    }

    public function viewExam($id)
    {
        return $this->view($id);
    }

    public function publishExam($id)
    {
        return $this->publish($id);
    }

    public function examResults($examId)
    {
        return $this->results($examId);
    }
}
