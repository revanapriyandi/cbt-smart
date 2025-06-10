<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamTypeModel;

class AdminExamTypeController extends BaseController
{
    protected $examTypeModel;

    public function __construct()
    {
        $this->examTypeModel = new ExamTypeModel();
    }
    public function index()
    {
        // Get statistics
        $stats = [
            'total' => $this->examTypeModel->countAll(),
            'active' => $this->examTypeModel->where('exam_types.status', 'active')->countAllResults(false),
            'inactive' => $this->examTypeModel->where('exam_types.status', 'inactive')->countAllResults(false),
            'used_in_exams' => $this->examTypeModel->select('DISTINCT exam_types.id')
                ->join('exams', 'exams.exam_type_id = exam_types.id', 'inner')
                ->countAllResults(false)
        ];

        return view('admin/exam-types/index', [
            'title' => 'Manajemen Jenis Ujian',
            'stats' => $stats
        ]);
    }
    public function getData()
    {
        // Debug: Log that method is called
        log_message('debug', 'getData method called');

        $request = $this->request;
        $draw = (int) $request->getPost('draw');
        $start = (int) ($request->getPost('start') ?? 0);
        $length = (int) ($request->getPost('length') ?? 10);
        $searchValue = $request->getPost('search')['value'] ?? '';

        // Debug: Log request parameters
        log_message('debug', 'DataTables params: ' . json_encode([
            'draw' => $draw,
            'start' => $start,
            'length' => $length,
            'search' => $searchValue
        ]));

        // Filters
        $statusFilter = $request->getPost('status_filter');
        $categoryFilter = $request->getPost('category_filter');
        $builder = $this->examTypeModel->select('exam_types.*, 
            (SELECT COUNT(*) FROM exams WHERE exams.exam_type_id = exam_types.id) as exam_count'); // Apply filters
        if (!empty($statusFilter)) {
            $builder->where('exam_types.status', $statusFilter);
        }
        if (!empty($categoryFilter)) {
            $builder->where('exam_types.category', $categoryFilter);
        }

        // Search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('exam_types.name', $searchValue)
                ->orLike('exam_types.description', $searchValue)
                ->orLike('exam_types.category', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->examTypeModel->countAll();

        // Clone the builder for count query
        $countBuilder = clone $builder;
        $filteredRecords = $countBuilder->countAllResults();

        $examTypes = $builder->limit($length, $start)
            ->orderBy('exam_types.created_at', 'DESC')
            ->get()
            ->getResultArray();
        // Debug: Log query results
        log_message('debug', 'Query returned ' . count($examTypes) . ' exam types');
        if (!empty($examTypes)) {
            log_message('debug', 'First exam type fields: ' . implode(', ', array_keys($examTypes[0])));
        }
        log_message('debug', 'Total records: ' . $totalRecords . ', Filtered: ' . $filteredRecords);

        $data = [];
        foreach ($examTypes as $examType) {
            $data[] = [
                'id' => $examType['id'],
                'name' => $examType['name'],
                'category' => $examType['category'],
                'description' => $examType['description'],
                'duration_minutes' => $examType['duration_minutes'],
                'max_attempts' => $examType['max_attempts'],
                'passing_score' => $examType['passing_score'],
                'show_result_immediately' => $examType['show_result_immediately'] ?? 0,
                'allow_review' => $examType['allow_review'] ?? 0,
                'randomize_questions' => $examType['randomize_questions'] ?? 0,
                'randomize_options' => $examType['randomize_options'] ?? 0,
                'auto_submit' => $examType['auto_submit'] ?? 0,
                'instructions' => $examType['instructions'] ?? '',
                'exam_count' => $examType['exam_count'] ?? 0,
                'status' => $examType['status'],
                'created_at' => date('d/m/Y H:i', strtotime($examType['created_at']))
            ];
        }

        $response = [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];

        // Debug: Log response
        log_message('debug', 'Returning response with ' . count($data) . ' items');

        return $this->response->setJSON($response);
    }

    public function create()
    {
        return view('admin/exam-types/create', [
            'title' => 'Tambah Jenis Ujian'
        ]);
    }
    public function store()
    {
        // Debug: Log incoming data
        log_message('debug', 'Store method called with data: ' . json_encode($this->request->getPost()));

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|max_length[100]|is_unique[exam_types.name]',
            'category' => 'required|in_list[daily,mid_semester,final_semester,national,practice,simulation]',
            'description' => 'permit_empty|max_length[500]',
            'duration_minutes' => 'required|integer|greater_than[0]|less_than_equal_to[480]',
            'max_attempts' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
            'passing_score' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'show_result_immediately' => 'permit_empty|in_list[0,1]',
            'allow_review' => 'permit_empty|in_list[0,1]',
            'randomize_questions' => 'permit_empty|in_list[0,1]',
            'randomize_options' => 'permit_empty|in_list[0,1]',
            'auto_submit' => 'permit_empty|in_list[0,1]',
            'instructions' => 'permit_empty|max_length[2000]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            log_message('debug', 'Validation errors: ' . json_encode($validation->getErrors()));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        // Remove CSRF token and id from data
        unset($data['csrf_token_name']);
        unset($data['id']);

        // Set default values for checkboxes (if not present in POST, set to 0)
        $data['show_result_immediately'] = isset($data['show_result_immediately']) ? 1 : 0;
        $data['allow_review'] = isset($data['allow_review']) ? 1 : 0;
        $data['randomize_questions'] = isset($data['randomize_questions']) ? 1 : 0;
        $data['randomize_options'] = isset($data['randomize_options']) ? 1 : 0;
        $data['auto_submit'] = isset($data['auto_submit']) ? 1 : 0;

        $data['created_by'] = session()->get('user_id');

        log_message('debug', 'Final data to insert: ' . json_encode($data));

        try {
            if ($this->examTypeModel->insert($data)) {
                // Log activity
                $this->examTypeModel->logActivity(
                    $this->examTypeModel->getInsertID(),
                    'create',
                    'Jenis ujian baru ditambahkan: ' . $data['name'],
                    session()->get('user_id')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Jenis ujian berhasil ditambahkan'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error inserting exam type: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menambahkan jenis ujian'
        ]);
    }

    public function show($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis ujian tidak ditemukan');
        }

        // Get exam type statistics
        $stats = $this->examTypeModel->getExamTypeStatistics($id);

        // Get recent exams using this type
        $recentExams = $this->examTypeModel->getRecentExamsByType($id, 10);

        return view('admin/exam-types/view', [
            'title' => 'Detail Jenis Ujian',
            'examType' => $examType,
            'stats' => $stats,
            'recentExams' => $recentExams
        ]);
    }

    public function edit($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Jenis ujian tidak ditemukan');
        }

        return view('admin/exam-types/edit', [
            'title' => 'Edit Jenis Ujian',
            'examType' => $examType
        ]);
    }

    public function update($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis ujian tidak ditemukan'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'name' => "required|max_length[100]|is_unique[exam_types.name,id,$id]",
            'category' => 'required|in_list[daily,mid_semester,final_semester,national,practice,simulation]',
            'description' => 'permit_empty|max_length[500]',
            'duration_minutes' => 'required|integer|greater_than[0]|less_than_equal_to[480]',
            'max_attempts' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
            'passing_score' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
            'show_result_immediately' => 'permit_empty|in_list[0,1]',
            'allow_review' => 'permit_empty|in_list[0,1]',
            'randomize_questions' => 'permit_empty|in_list[0,1]',
            'randomize_options' => 'permit_empty|in_list[0,1]',
            'auto_submit' => 'permit_empty|in_list[0,1]',
            'instructions' => 'permit_empty|max_length[2000]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = $this->request->getPost();

        // Set default values for checkboxes
        $data['show_result_immediately'] = $data['show_result_immediately'] ?? 0;
        $data['allow_review'] = $data['allow_review'] ?? 0;
        $data['randomize_questions'] = $data['randomize_questions'] ?? 0;
        $data['randomize_options'] = $data['randomize_options'] ?? 0;
        $data['auto_submit'] = $data['auto_submit'] ?? 0;

        $data['updated_by'] = session()->get('user_id');

        if ($this->examTypeModel->update($id, $data)) {
            // Log activity
            $this->examTypeModel->logActivity(
                $id,
                'update',
                'Jenis ujian diperbarui: ' . $data['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jenis ujian berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui jenis ujian'
        ]);
    }

    public function delete($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis ujian tidak ditemukan'
            ]);
        }

        // Check if exam type is being used
        if ($this->examTypeModel->isBeingUsed($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis ujian tidak dapat dihapus karena sedang digunakan'
            ]);
        }

        if ($this->examTypeModel->delete($id)) {
            // Log activity
            $this->examTypeModel->logActivity(
                $id,
                'delete',
                'Jenis ujian dihapus: ' . $examType['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jenis ujian berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus jenis ujian'
        ]);
    }

    public function bulkAction()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($action) || empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        // Validate IDs
        $ids = array_filter($ids, 'is_numeric');
        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID tidak valid'
            ]);
        }

        try {
            $count = 0;
            $message = '';

            switch ($action) {
                case 'activate':
                    $count = $this->examTypeModel->whereIn('id', $ids)
                        ->set('status', 'active')
                        ->update();
                    $message = "{$count} jenis ujian berhasil diaktifkan";
                    break;

                case 'deactivate':
                    $count = $this->examTypeModel->whereIn('id', $ids)
                        ->set('status', 'inactive')
                        ->update();
                    $message = "{$count} jenis ujian berhasil dinonaktifkan";
                    break;

                case 'delete':
                    // Check if any exam type is being used in exams
                    $usedCount = $this->examTypeModel->select('exam_types.id')
                        ->join('exams', 'exams.exam_type_id = exam_types.id', 'left')
                        ->whereIn('exam_types.id', $ids)
                        ->where('exams.id IS NOT NULL')
                        ->countAllResults(false);

                    if ($usedCount > 0) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => "Tidak dapat menghapus {$usedCount} jenis ujian karena masih digunakan dalam ujian"
                        ]);
                    }

                    $count = $this->examTypeModel->whereIn('id', $ids)->delete();
                    $message = "{$count} jenis ujian berhasil dihapus";
                    break;

                default:
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Aksi tidak valid'
                    ]);
            }

            // Log activity
            $currentUser = session()->get('user_id');
            if ($currentUser) {
                $userActivityModel = new \App\Models\UserActivityLogModel();
                $userActivityModel->insert([
                    'user_id' => $currentUser,
                    'activity' => "Bulk {$action} exam types",
                    'description' => "Performed {$action} on " . count($ids) . " exam types",
                    'ip_address' => $this->request->getIPAddress(),
                    'user_agent' => $this->request->getUserAgent()->getAgentString(),
                    'created_at' => date('Y-m-d H:i:s')
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Bulk action error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function export()
    {
        $format = $this->request->getGet('format') ?? 'excel';

        $examTypes = $this->examTypeModel->select('exam_types.*, 
            (SELECT COUNT(*) FROM exams WHERE exams.exam_type_id = exam_types.id) as exam_count')
            ->findAll();

        if ($format === 'excel') {
            return $this->exportToExcel($examTypes);
        }

        return $this->exportToPDF($examTypes);
    }

    private function exportToExcel($examTypes)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Jenis Ujian',
            'C1' => 'Kategori',
            'D1' => 'Durasi (menit)',
            'E1' => 'Maksimal Attempt',
            'F1' => 'Nilai Lulus',
            'G1' => 'Tampil Hasil',
            'H1' => 'Izin Review',
            'I1' => 'Acak Soal',
            'J1' => 'Acak Opsi',
            'K1' => 'Auto Submit',
            'L1' => 'Jumlah Ujian',
            'M1' => 'Status',
            'N1' => 'Dibuat'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Data
        $row = 2;
        foreach ($examTypes as $index => $examType) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $examType['name']);
            $sheet->setCellValue('C' . $row, $this->getCategoryName($examType['category']));
            $sheet->setCellValue('D' . $row, $examType['duration_minutes']);
            $sheet->setCellValue('E' . $row, $examType['max_attempts'] ?? '-');
            $sheet->setCellValue('F' . $row, $examType['passing_score']);
            $sheet->setCellValue('G' . $row, $examType['show_result_immediately'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('H' . $row, $examType['allow_review'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('I' . $row, $examType['randomize_questions'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('J' . $row, $examType['randomize_options'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('K' . $row, $examType['auto_submit'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('L' . $row, $examType['exam_count']);
            $sheet->setCellValue('M' . $row, ucfirst($examType['status']));
            $sheet->setCellValue('N' . $row, date('d/m/Y H:i', strtotime($examType['created_at'])));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'jenis_ujian_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function duplicate($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis ujian tidak ditemukan'
            ]);
        }

        // Remove ID and modify name
        unset($examType['id']);
        $examType['name'] = 'Copy of ' . $examType['name'];
        $examType['created_by'] = session()->get('user_id');
        $examType['updated_by'] = null;
        $examType['created_at'] = date('Y-m-d H:i:s');
        $examType['updated_at'] = date('Y-m-d H:i:s');

        if ($this->examTypeModel->insert($examType)) {
            $newId = $this->examTypeModel->getInsertID();

            // Log activity
            $this->examTypeModel->logActivity(
                $newId,
                'duplicate',
                'Jenis ujian diduplikasi dari: ' . $examType['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Jenis ujian berhasil diduplikasi',
                'redirect' => base_url('admin/exam-types/edit/' . $newId)
            ]);
        }
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menduplikasi jenis ujian'
        ]);
    }

    /**
     * Export exam types to PDF
     */
    private function exportToPDF($examTypes)
    {
        // Simple PDF export implementation
        // For now, return a simple response
        return $this->response->setContentType('application/json')->setJSON([
            'success' => false,
            'message' => 'PDF export not implemented yet'
        ]);
    }

    private function getCategoryName($category)
    {
        $categories = [
            'daily' => 'Harian',
            'mid_semester' => 'UTS',
            'final_semester' => 'UAS',
            'national' => 'Ujian Nasional',
            'practice' => 'Latihan',
            'simulation' => 'Simulasi'
        ];

        return $categories[$category] ?? $category;
    }

    /**
     * Get exam type data for API/AJAX requests
     */
    public function getExamType($id)
    {
        $examType = $this->examTypeModel->find($id);

        if (!$examType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis ujian tidak ditemukan'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $examType
        ]);
    }
}
