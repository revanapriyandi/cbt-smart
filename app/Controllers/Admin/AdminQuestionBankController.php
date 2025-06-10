<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\QuestionBankModel;
use App\Models\SubjectModel;
use App\Models\ExamTypeModel;
use App\Models\UserModel;

class AdminQuestionBankController extends BaseController
{
    protected $questionBankModel;
    protected $subjectModel;
    protected $examTypeModel;
    protected $userModel;

    public function __construct()
    {
        $this->questionBankModel = new QuestionBankModel();
        $this->subjectModel = new SubjectModel();
        $this->examTypeModel = new ExamTypeModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        // Get statistics
        $stats = [
            'total' => $this->questionBankModel->countAll(),
            'active' => $this->questionBankModel->where('status', 'active')->countAllResults(),
            'draft' => $this->questionBankModel->where('status', 'draft')->countAllResults(),
            'total_questions' => $this->questionBankModel->getTotalQuestions()
        ];        // Get filter options
        $subjects = $this->subjectModel->findAll();
        $examTypes = $this->examTypeModel->where('status', 'active')->findAll();
        $teachers = $this->userModel->where('role', 'teacher')->where('is_active', 1)->findAll();

        return view('admin/question-banks/index', [
            'title' => 'Manajemen Bank Soal',
            'stats' => $stats,
            'subjects' => $subjects,
            'examTypes' => $examTypes,
            'teachers' => $teachers
        ]);
    }
    public function getData()
    {
        $request = $this->request;

        $draw = $request->getPost('draw');
        $start = (int)($request->getPost('start') ?? 0);
        $length = (int)($request->getPost('length') ?? 10);
        $searchValue = $request->getPost('search')['value'] ?? '';

        // Filters
        $subjectFilter = $request->getPost('subject_filter');
        $examTypeFilter = $request->getPost('exam_type_filter');
        $difficultyFilter = $request->getPost('difficulty_filter');
        $statusFilter = $request->getPost('status_filter');
        $createdByFilter = $request->getPost('created_by_filter');

        $builder = $this->questionBankModel->getQuestionBanksWithDetails();

        // Apply filters
        if (!empty($subjectFilter)) {
            $builder->where('question_banks.subject_id', $subjectFilter);
        }
        if (!empty($examTypeFilter)) {
            $builder->where('question_banks.exam_type_id', $examTypeFilter);
        }
        if (!empty($difficultyFilter)) {
            $builder->where('question_banks.difficulty_level', $difficultyFilter);
        }
        if (!empty($statusFilter)) {
            $builder->where('question_banks.status', $statusFilter);
        }
        if (!empty($createdByFilter)) {
            $builder->where('question_banks.created_by', $createdByFilter);
        }

        // Search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('question_banks.name', $searchValue)
                ->orLike('question_banks.description', $searchValue)
                ->orLike('subjects.name', $searchValue)
                ->orLike('exam_types.name', $searchValue)
                ->groupEnd();
        }

        $totalRecords = $this->questionBankModel->countAll();
        $filteredRecords = $builder->countAllResults(false);

        $questionBanks = $builder->limit($length, $start)
            ->orderBy('question_banks.created_at', 'DESC')
            ->get()
            ->getResultArray();

        $data = [];
        foreach ($questionBanks as $bank) {
            $data[] = [
                'id' => $bank['id'],
                'name' => $bank['name'],
                'subject_name' => $bank['subject_name'],
                'exam_type_name' => $bank['exam_type_name'],
                'difficulty_level' => $bank['difficulty_level'],
                'question_count' => $bank['question_count'],
                'used_count' => $bank['used_count'],
                'status' => $bank['status'],
                'created_by_name' => $bank['created_by_name'],
                'created_at' => date('d/m/Y H:i', strtotime($bank['created_at']))
            ];
        }

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
    public function create()
    {
        $subjects = $this->subjectModel->findAll();
        $examTypes = $this->examTypeModel->where('status', 'active')->findAll();

        return view('admin/question-banks/create', [
            'title' => 'Tambah Bank Soal',
            'subjects' => $subjects,
            'examTypes' => $examTypes
        ]);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|max_length[200]',
            'subject_id' => 'required|integer',
            'exam_type_id' => 'required|integer',
            'difficulty_level' => 'required|in_list[easy,medium,hard]',
            'description' => 'permit_empty|max_length[1000]',
            'instructions' => 'permit_empty|max_length[2000]',
            'time_per_question' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[300]',
            'negative_marking' => 'permit_empty|in_list[0,1]',
            'negative_marks' => 'permit_empty|numeric|less_than[0]',
            'randomize_questions' => 'permit_empty|in_list[0,1]',
            'show_correct_answer' => 'permit_empty|in_list[0,1]',
            'allow_calculator' => 'permit_empty|in_list[0,1]',
            'tags' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,draft,archived]'
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
        $data['negative_marking'] = $data['negative_marking'] ?? 0;
        $data['randomize_questions'] = $data['randomize_questions'] ?? 0;
        $data['show_correct_answer'] = $data['show_correct_answer'] ?? 0;
        $data['allow_calculator'] = $data['allow_calculator'] ?? 0;

        // Process tags
        if (!empty($data['tags'])) {
            $data['tags'] = implode(',', array_map('trim', explode(',', $data['tags'])));
        }

        $data['created_by'] = session()->get('user_id');

        if ($this->questionBankModel->insert($data)) {
            $bankId = $this->questionBankModel->getInsertID();

            // Log activity
            $this->questionBankModel->logActivity(
                $bankId,
                'create',
                'Bank soal baru ditambahkan: ' . $data['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank soal berhasil ditambahkan',
                'redirect' => base_url('admin/question-banks/view/' . $bankId)
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menambahkan bank soal'
        ]);
    }
    public function show($id)
    {
        $questionBank = $this->questionBankModel->getQuestionBankWithDetails($id);

        if (!$questionBank) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Bank soal tidak ditemukan'
                ]);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Bank soal tidak ditemukan');
        }

        // Get questions in this bank
        $questions = $this->questionBankModel->getQuestionsByBank($id);

        // Get bank statistics  
        $stats = $this->questionBankModel->getBankStatistics($id);

        // If AJAX request, return JSON with HTML content
        if ($this->request->isAJAX()) {
            $html = view('admin/question-banks/components/view-details', [
                'questionBank' => $questionBank,
                'questions' => $questions,
                'stats' => $stats
            ]);

            return $this->response->setJSON([
                'success' => true,
                'html' => $html
            ]);
        }

        // Regular view request
        return view('admin/question-banks/view', [
            'title' => 'Detail Bank Soal',
            'questionBank' => $questionBank,
            'questions' => $questions,
            'stats' => $stats
        ]);
    }
    public function edit($id)
    {
        $questionBank = $this->questionBankModel->find($id);

        if (!$questionBank) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Bank soal tidak ditemukan'
                ]);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Bank soal tidak ditemukan');
        }

        // If AJAX request, return JSON data
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $questionBank
            ]);
        }        // Regular view request
        $subjects = $this->subjectModel->findAll();
        $examTypes = $this->examTypeModel->where('status', 'active')->findAll();

        return view('admin/question-banks/edit', [
            'title' => 'Edit Bank Soal',
            'questionBank' => $questionBank,
            'subjects' => $subjects,
            'examTypes' => $examTypes
        ]);
    }

    public function update($id)
    {
        $questionBank = $this->questionBankModel->find($id);

        if (!$questionBank) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank soal tidak ditemukan'
            ]);
        }

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|max_length[200]',
            'subject_id' => 'required|integer',
            'exam_type_id' => 'required|integer',
            'difficulty_level' => 'required|in_list[easy,medium,hard]',
            'description' => 'permit_empty|max_length[1000]',
            'instructions' => 'permit_empty|max_length[2000]',
            'time_per_question' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[300]',
            'negative_marking' => 'permit_empty|in_list[0,1]',
            'negative_marks' => 'permit_empty|numeric|less_than[0]',
            'randomize_questions' => 'permit_empty|in_list[0,1]',
            'show_correct_answer' => 'permit_empty|in_list[0,1]',
            'allow_calculator' => 'permit_empty|in_list[0,1]',
            'tags' => 'permit_empty|max_length[500]',
            'status' => 'required|in_list[active,draft,archived]'
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
        $data['negative_marking'] = $data['negative_marking'] ?? 0;
        $data['randomize_questions'] = $data['randomize_questions'] ?? 0;
        $data['show_correct_answer'] = $data['show_correct_answer'] ?? 0;
        $data['allow_calculator'] = $data['allow_calculator'] ?? 0;

        // Process tags
        if (!empty($data['tags'])) {
            $data['tags'] = implode(',', array_map('trim', explode(',', $data['tags'])));
        }

        $data['updated_by'] = session()->get('user_id');

        if ($this->questionBankModel->update($id, $data)) {
            // Log activity
            $this->questionBankModel->logActivity(
                $id,
                'update',
                'Bank soal diperbarui: ' . $data['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank soal berhasil diperbarui'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal memperbarui bank soal'
        ]);
    }

    public function delete($id)
    {
        $questionBank = $this->questionBankModel->find($id);

        if (!$questionBank) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank soal tidak ditemukan'
            ]);
        }

        // Check if bank is being used
        if ($this->questionBankModel->isBeingUsed($id)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank soal tidak dapat dihapus karena sedang digunakan dalam ujian'
            ]);
        }

        if ($this->questionBankModel->delete($id)) {
            // Also delete related questions
            $this->questionBankModel->deleteQuestionsByBank($id);

            // Log activity
            $this->questionBankModel->logActivity(
                $id,
                'delete',
                'Bank soal dihapus: ' . $questionBank['name'],
                session()->get('user_id')
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank soal berhasil dihapus'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus bank soal'
        ]);
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih bank soal terlebih dahulu'
            ]);
        }

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                switch ($action) {
                    case 'activate':
                        if ($this->questionBankModel->update($id, ['status' => 'active', 'updated_by' => session()->get('user_id')])) {
                            $count++;
                            $this->questionBankModel->logActivity($id, 'activate', 'Bank soal diaktifkan (bulk)', session()->get('user_id'));
                        }
                        break;
                    case 'draft':
                        if ($this->questionBankModel->update($id, ['status' => 'draft', 'updated_by' => session()->get('user_id')])) {
                            $count++;
                            $this->questionBankModel->logActivity($id, 'draft', 'Bank soal dijadikan draft (bulk)', session()->get('user_id'));
                        }
                        break;
                    case 'archive':
                        if ($this->questionBankModel->update($id, ['status' => 'archived', 'updated_by' => session()->get('user_id')])) {
                            $count++;
                            $this->questionBankModel->logActivity($id, 'archive', 'Bank soal diarsipkan (bulk)', session()->get('user_id'));
                        }
                        break;
                    case 'delete':
                        if (!$this->questionBankModel->isBeingUsed($id)) {
                            if ($this->questionBankModel->delete($id)) {
                                $this->questionBankModel->deleteQuestionsByBank($id);
                                $count++;
                                $this->questionBankModel->logActivity($id, 'delete', 'Bank soal dihapus (bulk)', session()->get('user_id'));
                            }
                        } else {
                            $errors[] = "Bank soal ID $id sedang digunakan";
                        }
                        break;
                }
            } catch (\Exception $e) {
                $errors[] = "Bank soal ID $id: " . $e->getMessage();
            }
        }

        $message = '';
        switch ($action) {
            case 'activate':
                $message = "$count bank soal berhasil diaktifkan";
                break;
            case 'draft':
                $message = "$count bank soal berhasil dijadikan draft";
                break;
            case 'archive':
                $message = "$count bank soal berhasil diarsipkan";
                break;
            case 'delete':
                $message = "$count bank soal berhasil dihapus";
                break;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'errors' => $errors
        ]);
    }

    public function duplicate($id)
    {
        $questionBank = $this->questionBankModel->find($id);

        if (!$questionBank) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank soal tidak ditemukan'
            ]);
        }

        $result = $this->questionBankModel->duplicateBank($id, session()->get('user_id'));

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Bank soal berhasil diduplikasi',
                'redirect' => base_url('admin/question-banks/edit/' . $result['new_bank_id'])
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menduplikasi bank soal: ' . $result['message']
        ]);
    }
    public function import()
    {
        $subjects = $this->subjectModel->findAll();
        $examTypes = $this->examTypeModel->where('status', 'active')->findAll();

        return view('admin/question-banks/import', [
            'title' => 'Import Bank Soal',
            'subjects' => $subjects,
            'examTypes' => $examTypes
        ]);
    }

    public function processImport()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'import_file' => 'uploaded[import_file]|ext_in[import_file,xlsx,xls,csv]|max_size[import_file,5120]',
            'subject_id' => 'required|integer',
            'exam_type_id' => 'required|integer',
            'difficulty_level' => 'required|in_list[easy,medium,hard]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validation->getErrors()
            ]);
        }

        $file = $this->request->getFile('import_file');
        $data = $this->request->getPost();

        try {
            $result = $this->questionBankModel->importFromFile($file, $data, session()->get('user_id'));

            if ($result['success']) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $result['message'],
                    'stats' => $result['stats']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $result['message'],
                    'errors' => $result['errors'] ?? []
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error importing file: ' . $e->getMessage()
            ]);
        }
    }

    public function export()
    {
        $format = $this->request->getGet('format') ?? 'excel';
        $bankIds = $this->request->getGet('banks');

        if ($bankIds) {
            $bankIds = explode(',', $bankIds);
            $questionBanks = $this->questionBankModel->getQuestionBanksWithDetails()
                ->whereIn('question_banks.id', $bankIds)
                ->findAll();
        } else {
            $questionBanks = $this->questionBankModel->getQuestionBanksWithDetails()->findAll();
        }
        if ($format === 'excel') {
            return $this->exportToExcel($questionBanks);
        }

        // For now, only Excel export is supported
        return $this->exportToExcel($questionBanks);
    }

    private function exportToExcel($questionBanks)
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'No',
            'B1' => 'Nama Bank Soal',
            'C1' => 'Mata Pelajaran',
            'D1' => 'Jenis Ujian',
            'E1' => 'Tingkat Kesulitan',
            'F1' => 'Jumlah Soal',
            'G1' => 'Waktu per Soal',
            'H1' => 'Nilai Negatif',
            'I1' => 'Status',
            'J1' => 'Dibuat Oleh',
            'K1' => 'Dibuat'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Data
        $row = 2;
        foreach ($questionBanks as $index => $bank) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $bank['name']);
            $sheet->setCellValue('C' . $row, $bank['subject_name']);
            $sheet->setCellValue('D' . $row, $bank['exam_type_name']);
            $sheet->setCellValue('E' . $row, ucfirst($bank['difficulty_level']));
            $sheet->setCellValue('F' . $row, $bank['question_count']);
            $sheet->setCellValue('G' . $row, $bank['time_per_question'] ? $bank['time_per_question'] . ' detik' : '-');
            $sheet->setCellValue('H' . $row, $bank['negative_marking'] ? 'Ya' : 'Tidak');
            $sheet->setCellValue('I' . $row, ucfirst($bank['status']));
            $sheet->setCellValue('J' . $row, $bank['created_by_name']);
            $sheet->setCellValue('K' . $row, date('d/m/Y H:i', strtotime($bank['created_at'])));
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'bank_soal_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function getQuestionBankTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Headers
        $headers = [
            'A1' => 'Question Text',
            'B1' => 'Option A',
            'C1' => 'Option B',
            'D1' => 'Option C',
            'E1' => 'Option D',
            'F1' => 'Option E',
            'G1' => 'Correct Answer (A/B/C/D/E)',
            'H1' => 'Explanation',
            'I1' => 'Points',
            'J1' => 'Category'
        ];

        foreach ($headers as $cell => $header) {
            $sheet->setCellValue($cell, $header);
        }

        // Sample data
        $sheet->setCellValue('A2', 'What is the capital of Indonesia?');
        $sheet->setCellValue('B2', 'Jakarta');
        $sheet->setCellValue('C2', 'Surabaya');
        $sheet->setCellValue('D2', 'Bandung');
        $sheet->setCellValue('E2', 'Medan');
        $sheet->setCellValue('F2', '');
        $sheet->setCellValue('G2', 'A');
        $sheet->setCellValue('H2', 'Jakarta is the capital and largest city of Indonesia.');
        $sheet->setCellValue('I2', '1');
        $sheet->setCellValue('J2', 'Geography');

        // Auto-size columns
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $filename = 'template_bank_soal.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih bank soal terlebih dahulu'
            ]);
        }

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $questionBank = $this->questionBankModel->find($id);
                if (!$questionBank) {
                    $errors[] = "Bank soal ID $id tidak ditemukan";
                    continue;
                }

                // Check if being used
                if ($this->questionBankModel->isBeingUsed($id)) {
                    $errors[] = "Bank soal '{$questionBank['name']}' sedang digunakan dan tidak dapat dihapus";
                    continue;
                }
                if ($this->questionBankModel->delete($id)) {
                    // Delete associated questions using model
                    $db = \Config\Database::connect();
                    $db->table('questions')->where('question_bank_id', $id)->delete();

                    // Log activity
                    $this->logActivity($id, 'delete', 'Bank soal dihapus (bulk): ' . $questionBank['name'], session()->get('user_id'));
                    $count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error deleting ID $id: " . $e->getMessage();
            }
        }

        $message = $count > 0 ? "$count bank soal berhasil dihapus" : "Tidak ada bank soal yang dihapus";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', $errors);
        }

        return $this->response->setJSON([
            'success' => $count > 0,
            'message' => $message
        ]);
    }

    public function bulkActivate()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih bank soal terlebih dahulu'
            ]);
        }

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $questionBank = $this->questionBankModel->find($id);
                if (!$questionBank) {
                    $errors[] = "Bank soal ID $id tidak ditemukan";
                    continue;
                }

                if ($this->questionBankModel->update($id, [
                    'status' => 'active',
                    'updated_by' => session()->get('user_id')
                ])) {
                    // Log activity
                    $this->logActivity($id, 'activate', 'Bank soal diaktifkan (bulk): ' . $questionBank['name'], session()->get('user_id'));
                    $count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error activating ID $id: " . $e->getMessage();
            }
        }

        $message = $count > 0 ? "$count bank soal berhasil diaktifkan" : "Tidak ada bank soal yang diaktifkan";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', $errors);
        }

        return $this->response->setJSON([
            'success' => $count > 0,
            'message' => $message
        ]);
    }

    public function bulkArchive()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih bank soal terlebih dahulu'
            ]);
        }

        $count = 0;
        $errors = [];

        foreach ($ids as $id) {
            try {
                $questionBank = $this->questionBankModel->find($id);
                if (!$questionBank) {
                    $errors[] = "Bank soal ID $id tidak ditemukan";
                    continue;
                }

                if ($this->questionBankModel->update($id, [
                    'status' => 'archived',
                    'updated_by' => session()->get('user_id')
                ])) {
                    // Log activity
                    $this->logActivity($id, 'archive', 'Bank soal diarsipkan (bulk): ' . $questionBank['name'], session()->get('user_id'));
                    $count++;
                }
            } catch (\Exception $e) {
                $errors[] = "Error archiving ID $id: " . $e->getMessage();
            }
        }

        $message = $count > 0 ? "$count bank soal berhasil diarsipkan" : "Tidak ada bank soal yang diarsipkan";
        if (!empty($errors)) {
            $message .= ". Errors: " . implode(', ', $errors);
        }

        return $this->response->setJSON([
            'success' => $count > 0,
            'message' => $message
        ]);
    }

    // Helper method for logging activities
    private function logActivity($bankId, $action, $description, $userId)
    {
        try {
            $activityModel = new \App\Models\UserActivityLogModel();
            $activityModel->insert([
                'user_id' => $userId,
                'action' => $action,
                'description' => $description,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'created_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            // Log error but don't fail the operation
            log_message('error', 'Failed to log activity: ' . $e->getMessage());
        }
    }
}
