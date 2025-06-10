<?php

namespace App\Controllers\Admin;

class AdminAcademicYearController extends BaseAdminController
{
    protected $academicYearModel;

    public function __construct()
    {
        parent::__construct();
        $this->academicYearModel = new \App\Models\AcademicYearModel();
    }

    public function index()
    {
        $academicYears = $this->academicYearModel->orderBy('created_at', 'DESC')->findAll();

        $data = [
            'academicYears' => $academicYears
        ];

        return view('admin/academic-years/index', $data);
    }
    /**
     * Create academic year
     */
    public function create()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'code' => $this->request->getPost('code'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'is_current' => $this->request->getPost('is_current') ? 1 : 0
            ];

            // If setting as current, update others to not current
            if ($data['is_current']) {
                $this->academicYearModel->where('is_current', 1)->set(['is_current' => 0])->update();
            }

            if ($this->academicYearModel->insert($data)) {
                session()->setFlashdata('success', 'Academic year successfully added!');
                return redirect()->to('/admin/academic-years');
            } else {
                session()->setFlashdata('error', 'Failed to add academic year!');
                return redirect()->back()->withInput();
            }
        }

        return view('admin/academic-years/create');
    }

    /**
     * Edit academic year
     */    public function edit($id)
    {
        $academicYear = $this->academicYearModel->find($id);

        if (!$academicYear) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Academic year not found!'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $academicYear
        ]);
    }
    /**
     * Delete academic year
     */
    public function delete($id)
    {
        $academicYear = $this->academicYearModel->find($id);

        if (!$academicYear) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Academic year not found!'
            ]);
        }

        if ($this->academicYearModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Academic year successfully deleted!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to delete academic year!'
            ]);
        }
    }

    /**
     * Set academic year as current
     */
    public function setCurrent($id)
    {
        $academicYear = $this->academicYearModel->find($id);

        if (!$academicYear) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Academic year not found!'
            ]);
        }

        // Set all academic years to not current
        $this->academicYearModel->set(['is_current' => 0])->update();

        // Set the selected one as current
        if ($this->academicYearModel->update($id, ['is_current' => 1])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Academic year set as current successfully!'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to set academic year as current!'
            ]);
        }
    }    // Route aliases for compatibility
    public function store()
    {
        if ($this->request->getMethod() === 'POST') {
            $data = [
                'name' => $this->request->getPost('name'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0,
                'is_current' => 0
            ];

            if ($this->academicYearModel->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Academic year successfully added!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to add academic year!'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method!'
        ]);
    }

    public function update($id)
    {
        if ($this->request->getMethod() === 'POST') {
            $academicYear = $this->academicYearModel->find($id);

            if (!$academicYear) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Academic year not found!'
                ]);
            }

            $data = [
                'name' => $this->request->getPost('name'),
                'start_date' => $this->request->getPost('start_date'),
                'end_date' => $this->request->getPost('end_date'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            if ($this->academicYearModel->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Academic year successfully updated!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update academic year!'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request method!'
        ]);
    }

    public function getData()
    {
        // Simple JSON response for any AJAX requests
        $academicYears = $this->academicYearModel->findAll();
        return $this->response->setJSON($academicYears);
    }
}
