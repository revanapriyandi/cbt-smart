<?php

namespace App\Controllers\Admin;

use App\Models\ClassModel;
use App\Models\UserModel;

class AdminClassController extends BaseAdminController
{
    protected $classModel;

    public function __construct()
    {
        parent::__construct();
        $this->classModel = new ClassModel();
    }

    /**
     * Display classes management page
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Kelas',
            'classes' => $this->classModel->getClassesWithDetails(),
            'totalClasses' => $this->classModel->countAllResults(),
            'activeClasses' => $this->classModel->where('is_active', 1)->countAllResults(),
        ];

        return view('admin/classes/index', $data);
    }

    /**
     * Show form for creating new class
     */
    public function create()
    {
        $data = [
            'title' => 'Tambah Kelas Baru'
        ];

        return view('admin/classes/create', $data);
    }

    /**
     * Store new class
     */
    public function store()
    {
        $validationRules = [
            'name' => 'required|max_length[100]|is_unique[classes.name]',
            'level' => 'required|max_length[50]',
            'academic_year' => 'required|max_length[20]',
            'capacity' => 'required|integer|greater_than[0]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($validationRules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'level' => $this->request->getPost('level'),
            'academic_year' => $this->request->getPost('academic_year'),
            'capacity' => $this->request->getPost('capacity'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->classModel->insert($data)) {
            // Log activity
            $this->logActivity('class_create', "Created new class: {$data['name']}");

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kelas berhasil ditambahkan!'
                ]);
            }
            return redirect()->to('/admin/classes')->with('success', 'Kelas berhasil ditambahkan!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menambahkan kelas!'
            ]);
        }
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kelas!');
    }

    /**
     * Show class details
     */
    public function show($id)
    {
        $class = $this->classModel->getClassWithDetails($id);
        if (!$class) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Class not found'
                ], 404);
            }
            return redirect()->to('/admin/classes')->with('error', 'Kelas tidak ditemukan!');
        }

        $userModel = new UserModel();
        $students = $userModel->getStudentsByClass($id);

        // If this is an AJAX request, return JSON data
        if ($this->request->isAJAX()) {
            $responseData = [
                'id' => $class['id'],
                'name' => $class['name'],
                'level' => $class['level'],
                'capacity' => $class['capacity'],
                'teacher_id' => $class['homeroom_teacher_id'],
                'teacher_name' => $class['teacher_name'] ?? 'Not assigned',
                'academic_year' => $class['academic_year'],
                'description' => $class['description'],
                'is_active' => (bool)$class['is_active'],
                'student_count' => count($students),
                'created_at' => $class['created_at'],
                'updated_at' => $class['updated_at']
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $responseData
            ]);
        }
        $data = [
            'title' => 'Detail Kelas - ' . $class['name'],
            'class' => $class,
            'students' => $students,
            'studentCount' => count($students)
        ];

        return view('admin/classes/view', $data);
    }

    /**
     * Show form for editing class
     */
    public function edit($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Kelas tidak ditemukan!');
        }

        $data = [
            'title' => 'Edit Kelas - ' . $class['name'],
            'class' => $class
        ];

        return view('admin/classes/edit', $data);
    }

    /**
     * Update class
     */
    public function update($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kelas tidak ditemukan!'
                ]);
            }
            return redirect()->to('/admin/classes')->with('error', 'Kelas tidak ditemukan!');
        }
        $validationRules = [
            'name' => "required|max_length[100]|is_unique[classes.name,id,{$id}]",
            'level' => 'required|max_length[50]',
            'academic_year' => 'required|max_length[20]',
            'capacity' => 'required|integer|greater_than[0]',
            'description' => 'permit_empty|max_length[500]'
        ];

        if (!$this->validate($validationRules)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        $data = [
            'name' => $this->request->getPost('name'),
            'level' => $this->request->getPost('level'),
            'academic_year' => $this->request->getPost('academic_year'),
            'capacity' => $this->request->getPost('capacity'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        if ($this->classModel->update($id, $data)) {
            // Log activity
            $this->logActivity('class_update', "Updated class: {$data['name']}");

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Kelas berhasil diupdate!'
                ]);
            }
            return redirect()->to('/admin/classes')->with('success', 'Kelas berhasil diupdate!');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupdate kelas!'
            ]);
        }
        return redirect()->back()->withInput()->with('error', 'Gagal mengupdate kelas!');
    }

    /**
     * Delete class
     */
    public function delete($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kelas tidak ditemukan!'
            ]);
        }        // Check if class has students
        $db = \Config\Database::connect();
        $studentCount = $db->table('user_classes')
            ->where('class_id', $id)
            ->countAllResults();

        if ($studentCount > 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Tidak dapat menghapus kelas yang masih memiliki {$studentCount} siswa!"
            ]);
        }

        if ($this->classModel->delete($id)) {
            // Log activity
            $this->logActivity('class_delete', "Deleted class: {$class['name']}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Kelas berhasil dihapus!'
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus kelas!'
        ]);
    }

    /**
     * Get classes statistics
     */
    public function statistics()
    {
        $stats = $this->classModel->getStatistics();
        return $this->response->setJSON($stats);
    }

    /**
     * Get classes data for DataTables
     */    public function datatables()
    {
        $request = $this->request;
        $draw = $request->getPost('draw') ?? $request->getGet('draw');
        $start = intval($request->getPost('start') ?? $request->getGet('start') ?? 0);
        $length = intval($request->getPost('length') ?? $request->getGet('length') ?? 10);
        $search = $request->getPost('search')['value'] ?? $request->getGet('search')['value'] ?? '';
        $result = $this->classModel->getClassesForDataTable($start, $length, $search);

        // Debug: log what fields are available
        if (!empty($result['data'])) {
            log_message('debug', 'Available fields in first row: ' . implode(', ', array_keys($result['data'][0])));
        }        // Format data for DataTables with HTML columns
        foreach ($result['data'] as &$class) {
            // Handle null values and ensure all fields exist
            $class['name'] = $class['name'] ?? '';
            $class['level'] = $class['level'] ?? '';
            $class['capacity'] = $class['capacity'] ?? 0;
            $class['student_count'] = $class['student_count'] ?? 0;
            $class['academic_year'] = $class['academic_year'] ?? '';
            $class['description'] = $class['description'] ?? '';

            // Handle teacher_name - keep it as plain text, let frontend handle formatting
            $class['teacher_name'] = $class['teacher_name'] ?? null;

            // Format checkbox column
            $class['checkbox'] = '<input type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600" value="' . $class['id'] . '">';

            // Format status column
            $statusClass = $class['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            $statusText = $class['is_active'] ? 'Active' : 'Inactive';
            $class['status'] = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' . $statusClass . '">' . $statusText . '</span>';

            // Format actions column
            $class['actions'] = '
                <div class="flex items-center space-x-2">
                    <button onclick="viewClass(' . $class['id'] . ')" class="text-blue-600 hover:text-blue-900" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button onclick="openEditModal(' . $class['id'] . ')" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteClass(' . $class['id'] . ', \'' . htmlspecialchars($class['name']) . '\')" class="text-red-600 hover:text-red-900" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>';
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $result['data']
        ]);
    }
    /**
     * Get classes data for DataTables (legacy method)
     */
    public function getClassesData()
    {
        $request = $this->request;
        $draw = $request->getGet('draw');
        $start = $request->getGet('start') ?? 0;
        $length = $request->getGet('length') ?? 10;
        $search = $request->getGet('search')['value'] ?? '';

        $builder = $this->classModel->select('classes.*, users.full_name as teacher_name')
            ->join('users', 'users.id = classes.homeroom_teacher_id', 'left');

        // Total records
        $totalRecords = $this->classModel->countAllResults();

        // Filter records
        if (!empty($search)) {
            $builder->groupStart()
                ->like('classes.name', $search)
                ->orLike('classes.academic_year', $search)
                ->orLike('classes.level', $search)
                ->orLike('classes.description', $search)
                ->orLike('users.full_name', $search)
                ->groupEnd();
        }

        $filteredRecords = $builder->countAllResults(false);

        // Get data
        $classes = $builder->orderBy('classes.level', 'ASC')
            ->orderBy('classes.name', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // Add student count and format data for DataTables
        $db = \Config\Database::connect();
        foreach ($classes as &$class) {
            $class['student_count'] = $db->table('user_classes')
                ->where('class_id', $class['id'])
                ->countAllResults();

            // Format checkbox column
            $class['checkbox'] = '<input type="checkbox" class="form-checkbox h-4 w-4 text-indigo-600" value="' . $class['id'] . '">';

            // Format status column
            $statusClass = $class['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
            $statusText = $class['is_active'] ? 'Active' : 'Inactive';
            $class['status'] = '<span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full ' . $statusClass . '">' . $statusText . '</span>';

            // Format actions column
            $class['actions'] = '
                <div class="flex items-center space-x-2">
                    <button onclick="viewClass(' . $class['id'] . ')" class="text-blue-600 hover:text-blue-900" title="View">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </button>
                    <button onclick="openEditModal(' . $class['id'] . ')" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                    <button onclick="deleteClass(' . $class['id'] . ', \'' . htmlspecialchars($class['name']) . '\')" class="text-red-600 hover:text-red-900" title="Delete">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                </div>';
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $classes
        ]);
    }

    /**
     * Export classes to CSV
     */
    public function export()
    {
        $format = $this->request->getGet('format') ?? 'csv';
        $classes = $this->classModel->getClassesWithDetails();

        if ($format === 'csv') {
            return $this->exportCSV($classes);
        } elseif ($format === 'excel') {
            return $this->exportExcel($classes);
        }

        return redirect()->back()->with('error', 'Format tidak didukung!');
    }

    /**
     * Export classes to CSV format
     */
    private function exportCSV($classes)
    {
        $filename = 'classes_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [
            'Class Name',
            'Grade Level',
            'Academic Year',
            'Capacity',
            'Current Students',
            'Class Teacher',
            'Status',
            'Description',
            'Created At'
        ]);

        // CSV Data
        foreach ($classes as $class) {
            fputcsv($output, [
                $class['name'],
                $class['level'],
                $class['academic_year'],
                $class['capacity'],
                $class['student_count'] ?? 0,
                $class['teacher_name'] ?? 'Not Assigned',
                $class['is_active'] ? 'Active' : 'Inactive',
                $class['description'] ?? '',
                $class['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export classes to Excel format
     */
    private function exportExcel($classes)
    {
        // For now, use CSV format - can be enhanced later with PhpSpreadsheet
        return $this->exportCSV($classes);
    }

    /**
     * Download CSV import template
     */
    public function downloadTemplate()
    {
        $filename = 'classes_import_template.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Template header with sample data
        fputcsv($output, [
            'name',
            'level',
            'academic_year',
            'capacity',
            'teacher_id',
            'description',
            'is_active'
        ]);

        // Sample data rows
        fputcsv($output, [
            'X IPA 1',
            '10',
            '2024/2025',
            '35',
            '',
            'Kelas IPA untuk tingkat X',
            '1'
        ]);

        fputcsv($output, [
            'X IPS 1',
            '10',
            '2024/2025',
            '32',
            '',
            'Kelas IPS untuk tingkat X',
            '1'
        ]);
        fclose($output);
        exit;
    }

    /**
     * Import classes from CSV
     */
    public function import()
    {
        if ($this->request->getMethod() === 'POST') {
            $file = $this->request->getFile('csv_file');

            if (!$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid!'
                ]);
            }

            if ($file->getClientExtension() !== 'csv') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File harus berformat CSV!'
                ]);
            }

            try {
                $csvData = array_map('str_getcsv', file($file->getTempName()));
                $headers = array_shift($csvData); // Remove header row

                $imported = 0;
                $errors = [];

                foreach ($csvData as $index => $row) {
                    $rowNumber = $index + 2; // Account for header row

                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    // Map CSV columns to data array
                    $data = [
                        'name' => $row[0] ?? '',
                        'level' => $row[1] ?? '',
                        'academic_year' => $row[2] ?? '',
                        'capacity' => (int)($row[3] ?? 0),
                        'teacher_id' => !empty($row[4]) ? (int)$row[4] : null,
                        'description' => $row[5] ?? '',
                        'is_active' => isset($row[6]) ? (int)$row[6] : 1
                    ];

                    // Validate required fields
                    if (empty($data['name']) || empty($data['level']) || empty($data['academic_year'])) {
                        $errors[] = "Row {$rowNumber}: Name, level, and academic year are required";
                        continue;
                    }

                    // Check for duplicate names
                    if ($this->classModel->where('name', $data['name'])->first()) {
                        $errors[] = "Row {$rowNumber}: Class name '{$data['name']}' already exists";
                        continue;
                    }

                    if ($this->classModel->insert($data)) {
                        $imported++;
                    } else {
                        $errors[] = "Row {$rowNumber}: Failed to import class '{$data['name']}'";
                    }
                }

                $message = "Successfully imported {$imported} classes";
                if (!empty($errors)) {
                    $message .= ". " . count($errors) . " errors occurred.";
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'imported' => $imported,
                    'errors' => $errors
                ]);
            } catch (\Exception $e) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error processing CSV file: ' . $e->getMessage()
                ]);
            }
        }        // GET request - show import page
        return view('admin/classes/import');
    }

    /**
     * Handle bulk actions
     */
    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (!$action || !$ids || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action or selection!'
            ]);
        }

        $processed = 0;
        $errors = [];

        switch ($action) {
            case 'activate':
                foreach ($ids as $id) {
                    if ($this->classModel->update($id, ['is_active' => 1])) {
                        $processed++;
                    } else {
                        $errors[] = "Failed to activate class ID: {$id}";
                    }
                }
                break;

            case 'deactivate':
                foreach ($ids as $id) {
                    if ($this->classModel->update($id, ['is_active' => 0])) {
                        $processed++;
                    } else {
                        $errors[] = "Failed to deactivate class ID: {$id}";
                    }
                }
                break;

            case 'delete':
                foreach ($ids as $id) {
                    // Check if class has students
                    $db = \Config\Database::connect();
                    $studentCount = $db->table('user_classes')
                        ->where('class_id', $id)
                        ->countAllResults();

                    if ($studentCount > 0) {
                        $class = $this->classModel->find($id);
                        $errors[] = "Cannot delete class '{$class['name']}' - has {$studentCount} students";
                        continue;
                    }

                    if ($this->classModel->delete($id)) {
                        $processed++;
                    } else {
                        $errors[] = "Failed to delete class ID: {$id}";
                    }
                }
                break;

            default:
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Unknown action!'
                ]);
        }

        // Log bulk action
        $this->logActivity('class_bulk_action', "Performed bulk action '{$action}' on {$processed} classes");

        $message = "Successfully processed {$processed} classes";
        if (!empty($errors)) {
            $message .= ". " . count($errors) . " errors occurred.";
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'processed' => $processed,
            'errors' => $errors
        ]);
    }

    /**
     * Toggle class status
     */
    public function toggleStatus($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Class not found!'
            ]);
        }

        $newStatus = $class['is_active'] ? 0 : 1;

        if ($this->classModel->update($id, ['is_active' => $newStatus])) {
            // Log activity
            $statusText = $newStatus ? 'activated' : 'deactivated';
            $this->logActivity('class_status_toggle', "Status changed for class '{$class['name']}' - {$statusText}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Class status updated successfully!',
                'new_status' => $newStatus
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update class status!'
        ]);
    }

    /**
     * Get class-specific statistics
     */
    public function classStatistics($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Class not found'
            ]);
        }

        $userModel = new UserModel();
        $students = $userModel->getStudentsByClass($id);
        $studentCount = count($students);
        $capacity = $class['capacity'];
        $capacityUsage = $capacity > 0 ? round(($studentCount / $capacity) * 100, 1) : 0;

        // Get active exams for this class (would need ExamModel for real implementation)
        $activeExams = 0; // Placeholder - implement when ExamModel is available

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'students' => $studentCount,
                'capacity_usage' => $capacityUsage . '%',
                'active_exams' => $activeExams
            ]
        ]);
    }

    /**
     * Get students in a class
     */
    public function classStudents($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Class not found'
            ]);
        }

        $userModel = new UserModel();
        $students = $userModel->getStudentsByClass($id);

        // Format students data for the modal
        $formattedStudents = [];
        foreach ($students as $student) {
            $formattedStudents[] = [
                'id' => $student['id'],
                'username' => $student['username'],
                'full_name' => $student['full_name'],
                'email' => $student['email'],
                'last_login' => $student['last_login']
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $formattedStudents
        ]);
    }

    /**
     * Quick stats for a class (for /admin/classes/{id}/stats)
     */
    public function quickStats($id)
    {
        $class = $this->classModel->find($id);
        if (!$class) {
            return $this->response->setJSON([
                'activeStudents' => 0,
                'inactiveStudents' => 0,
                'activeExams' => 0
            ]);
        }
        $userModel = new \App\Models\UserModel();
        $students = $userModel->getStudentsByClass($id);
        $active = 0;
        $inactive = 0;
        foreach ($students as $s) {
            if (isset($s['status']) && $s['status'] === 'active') $active++;
            else $inactive++;
        }
        // TODO: Hitung ujian aktif jika ada relasi
        $activeExams = 0;
        return $this->response->setJSON([
            'activeStudents' => $active,
            'inactiveStudents' => $inactive,
            'activeExams' => $activeExams
        ]);
    }

    /**
     * Recent activity for a class (for /admin/classes/{id}/activity)
     */
    public function recentActivity($id)
    {
        // Dummy: tampilkan kosong atau ambil dari log jika ada
        $activities = [];
        // TODO: Implementasi ambil log aktivitas kelas jika ada
        return $this->response->setJSON([
            'activities' => $activities
        ]);
    }
}
