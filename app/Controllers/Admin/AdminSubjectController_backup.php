<?php

namespace App\Controllers\Admin;

class AdminSubjectController extends BaseAdminController
{
    public function index()
    {
        return $this->subjects();
    }    public function subjects()
    {
        $search = $this->request->getGet('search') ?? '';
        $teacherFilter = $this->request->getGet('teacher_id') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';        $perPage = 10;

        $builder = $this->subjectModel
            ->select('subjects.*, users.full_name as teacher_name, users.email as teacher_email, 
                COUNT(DISTINCT exams.id) as exam_count,
                COUNT(DISTINCT exam_results.student_id) as student_count,
                COALESCE(AVG(exam_results.total_score), 0) as average_score')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->join('exams', 'exams.subject_id = subjects.id', 'left')
            ->join('exam_results', 'exam_results.exam_id = exams.id', 'left')
            ->groupBy('subjects.id');

        // Apply search filter
        if ($search) {
            $builder->groupStart()
                ->like('subjects.name', $search)
                ->orLike('subjects.code', $search)
                ->orLike('subjects.description', $search)
                ->orLike('users.full_name', $search)
                ->groupEnd();
        }

        // Apply teacher filter
        if ($teacherFilter) {
            $builder->where('subjects.teacher_id', $teacherFilter);
        }

        // Apply sorting
        if (in_array($sortBy, ['name', 'code', 'created_at', 'exam_count', 'teacher_name'])) {
            if ($sortBy === 'exam_count') {
                $builder->orderBy('COUNT(DISTINCT exams.id)', $sortOrder);
            } elseif ($sortBy === 'teacher_name') {
                $builder->orderBy('users.full_name', $sortOrder);
            } else {
                $builder->orderBy('subjects.' . $sortBy, $sortOrder);
            }
        }

        // Get paginated results
        $subjects = $builder->paginate($perPage);
        $pager = $this->subjectModel->pager;

        // Get statistics
        $totalSubjects = $this->subjectModel->countAllResults(false);
        $subjectsWithTeacher = $this->subjectModel
            ->select('subjects.id')
            ->join('users', 'users.id = subjects.teacher_id', 'inner')
            ->countAllResults(false);
        $subjectsWithoutTeacher = $totalSubjects - $subjectsWithTeacher;

        $data = [
            'subjects' => $subjects,
            'pager' => $pager,
            'teachers' => $this->userModel->getTeachers(),
            'search' => $search,
            'teacherFilter' => $teacherFilter,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'statistics' => [
                'total' => $totalSubjects,
                'with_teacher' => $subjectsWithTeacher,
                'without_teacher' => $subjectsWithoutTeacher,
                'total_exams' => $this->db->table('exams')->countAllResults(),
                'active_exams' => $this->db->table('exams')->where('is_active', 1)->countAllResults()
            ]
        ];

        return view('admin/subjects', $data);
    }    public function getSubjectsData()
    {
        $search = $this->request->getGet('search') ?? '';        $teacherFilter = $this->request->getGet('teacher_id') ?? '';
        $builder = $this->subjectModel
            ->select('subjects.*, users.full_name as teacher_name, users.email as teacher_email, 
                COUNT(DISTINCT exams.id) as exam_count,
                COUNT(DISTINCT exam_results.student_id) as student_count,
                COALESCE(AVG(exam_results.total_score), 0) as average_score')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->join('exams', 'exams.subject_id = subjects.id', 'left')
            ->join('exam_results', 'exam_results.exam_id = exams.id', 'left')
            ->groupBy('subjects.id');

        if ($search) {
            $builder->groupStart()
                ->like('subjects.name', $search)
                ->orLike('subjects.code', $search)
                ->orLike('subjects.description', $search)
                ->orLike('users.full_name', $search)
                ->groupEnd();
        }

        if ($teacherFilter) {
            $builder->where('subjects.teacher_id', $teacherFilter);
        }

        $subjects = $builder->orderBy('subjects.created_at', 'DESC')->findAll();

        return $this->response->setJSON([
            'data' => $subjects,
            'recordsTotal' => count($subjects),
            'recordsFiltered' => count($subjects)
        ]);
    }

    public function view($id)
    {
        $subject = $this->subjectModel->getSubjectStatistics($id);

        if (!$subject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Subject not found');
        }

        // Get recent exams for this subject
        $recentExams = $this->examModel
            ->select('exams.*, COUNT(exam_results.id) as participant_count')
            ->join('exam_results', 'exam_results.exam_id = exams.id', 'left')
            ->where('exams.subject_id', $id)
            ->groupBy('exams.id')
            ->orderBy('exams.created_at', 'DESC')
            ->limit(10)
            ->findAll();

        // Get top performing students
        $topStudents = $this->db->table('exam_results er')
            ->select('u.full_name, u.email, AVG(er.percentage) as avg_score, COUNT(er.id) as exam_count')
            ->join('users u', 'u.id = er.student_id')
            ->join('exams e', 'e.id = er.exam_id')
            ->where('e.subject_id', $id)
            ->where('er.status', 'graded')
            ->groupBy('er.student_id')
            ->orderBy('avg_score', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $data = [
            'subject' => $subject,
            'recentExams' => $recentExams,
            'topStudents' => $topStudents
        ];

        return view('admin/view_subject', $data);
    }

    public function viewSubject($id)
    {
        return $this->view($id);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null,
                'is_active' => 1
            ];

            if ($this->subjectModel->insert($data)) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Subject created successfully!']);
                }
                session()->setFlashdata('success', 'Mata pelajaran berhasil ditambahkan!');
                return redirect()->to('/admin/subjects');
            } else {
                $errors = $this->subjectModel->errors();
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Failed to create subject!', 'errors' => $errors]);
                }
                session()->setFlashdata('error', 'Gagal menambahkan mata pelajaran! ' . implode(', ', $errors));
                return redirect()->back()->withInput();
            }
        }

        $data = ['teachers' => $this->userModel->getTeachers()];
        return view('admin/create_subject', $data);
    }

    public function store()
    {
        return $this->create();
    }

    public function update($id)
    {
        return $this->edit($id);
    }

    public function createSubject()
    {
        return $this->create();
    }

    public function editSubject($id)
    {
        return $this->edit($id);
    }

    public function getSubject($id)
    {
        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            return $this->response->setJSON(['error' => 'Subject not found'], 404);
        }

        return $this->response->setJSON($subject);
    }

    public function delete($id)
    {
        if ($this->subjectModel->delete($id)) {
            session()->setFlashdata('success', 'Mata pelajaran berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus mata pelajaran!');
        }        return redirect()->to('/admin/subjects');
    }

    // Method aliases for route compatibility
    public function createSubject()
    {
        return $this->create();
    }

    public function editSubject($id)
    {
        return $this->edit($id);
    }

    public function deleteSubject($id)
    {
        return $this->delete($id);
    }    public function viewSubject($id)
    {
        $subject = $this->subjectModel->find($id);
        if (!$subject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Subject not found');
        }
        return view('admin/view_subject', ['subject' => $subject]);
    }
}
