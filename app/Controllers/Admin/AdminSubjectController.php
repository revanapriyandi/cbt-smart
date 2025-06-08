<?php

namespace App\Controllers\Admin;

class AdminSubjectController extends BaseAdminController
{
    public function index()
    {
        return $this->subjects();
    }

    public function subjects()
    {
        $search = $this->request->getGet('search') ?? '';
        $teacherFilter = $this->request->getGet('teacher_id') ?? '';
        $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
        $sortOrder = $this->request->getGet('sort_order') ?? 'DESC';

        $perPage = 10;

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

        $subjects = $builder->paginate($perPage);
        $pager = $this->subjectModel->pager;

        $data = [
            'subjects' => $subjects,
            'pager' => $pager,
            'teachers' => $this->userModel->getTeachers(),
            'search' => $search,
            'teacherFilter' => $teacherFilter,
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'totalSubjects' => $this->subjectModel->countAllResults(),
            'totalTeachers' => $this->userModel->where('role', 'teacher')->countAllResults(),
        ];

        return view('admin/subjects', $data);
    }

    public function getSubjectsData()
    {
        $search = $this->request->getGet('search') ?? '';
        $teacherFilter = $this->request->getGet('teacher_id') ?? '';
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

        $subjects = $builder->get()->getResultArray();

        return $this->response->setJSON($subjects);
    }

    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null
            ];

            $result = $this->subjectModel->createSubject($data);

            if ($result['success']) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Mata pelajaran berhasil ditambahkan!'
                    ]);
                }

                session()->setFlashdata('success', 'Mata pelajaran berhasil ditambahkan!');
                return redirect()->to('/admin/subjects');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal menambahkan mata pelajaran!',
                        'errors' => $result['errors']
                    ]);
                }

                session()->setFlashdata('error', 'Gagal menambahkan mata pelajaran! ' . implode(', ', $result['errors']));
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'teachers' => $this->userModel->getTeachers()
        ];

        return view('admin/create_subject', $data);
    }

    public function edit($id)
    {
        $subject = $this->subjectModel->find($id);

        if (!$subject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Subject not found');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'description' => $this->request->getPost('description'),
                'teacher_id' => $this->request->getPost('teacher_id') ?: null
            ];

            $result = $this->subjectModel->updateSubject($id, $data);

            if ($result['success']) {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => 'Mata pelajaran berhasil diupdate!'
                    ]);
                }

                session()->setFlashdata('success', 'Mata pelajaran berhasil diupdate!');
                return redirect()->to('/admin/subjects');
            } else {
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal mengupdate mata pelajaran!',
                        'errors' => $result['errors']
                    ]);
                }

                session()->setFlashdata('error', 'Gagal mengupdate mata pelajaran! ' . implode(', ', $result['errors']));
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
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Subject not found']);
        }

        return $this->response->setJSON($subject);
    }

    public function delete($id)
    {
        if ($this->subjectModel->delete($id)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Mata pelajaran berhasil dihapus!'
                ]);
            }
            session()->setFlashdata('success', 'Mata pelajaran berhasil dihapus!');
        } else {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menghapus mata pelajaran!'
                ]);
            }
            session()->setFlashdata('error', 'Gagal menghapus mata pelajaran!');
        }

        return redirect()->to('/admin/subjects');
    }

    // Method aliases for route compatibility
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

    public function deleteSubject($id)
    {
        return $this->delete($id);
    }
    public function viewSubject($id)
    {
        // Get subject with teacher information and statistics
        $subject = $this->subjectModel
            ->select('subjects.*, users.full_name as teacher_name, users.email as teacher_email,
                COUNT(DISTINCT exams.id) as total_exams,
                COUNT(DISTINCT CASE WHEN exams.is_active = 1 THEN exams.id END) as active_exams,
                COUNT(DISTINCT exam_results.student_id) as enrolled_students,
                COUNT(exam_results.id) as completed_attempts,
                COALESCE(AVG(exam_results.total_score), 0) as average_score')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->join('exams', 'exams.subject_id = subjects.id', 'left')
            ->join('exam_results', 'exam_results.exam_id = exams.id', 'left')
            ->where('subjects.id', $id)
            ->groupBy('subjects.id')
            ->first();

        if (!$subject) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Subject not found');
        }

        return view('admin/view_subject', ['subject' => $subject]);
    }
}
