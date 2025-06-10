<?php

namespace App\Controllers\Admin;

use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\QuestionBankModel;
use App\Models\SubjectModel;
use App\Models\ExamTypeModel;
use App\Libraries\PdfScrapingService;

class AdminQuestionController extends BaseAdminController
{
    protected $questionModel;
    protected $questionOptionModel;
    protected $questionBankModel;
    protected $subjectModel;
    protected $examTypeModel;
    protected $pdfScrapingService;

    public function __construct()
    {
        parent::__construct();
        $this->questionModel = new QuestionModel();
        $this->questionOptionModel = new QuestionOptionModel();
        $this->questionBankModel = new QuestionBankModel();
        $this->subjectModel = new SubjectModel();
        $this->examTypeModel = new ExamTypeModel();
        $this->pdfScrapingService = new PdfScrapingService();
    }
    public function index()
    {
        $data = [
            'title' => 'Manajemen Soal',
            'page' => 'questions',
            'questionBanks' => $this->questionBankModel->getQuestionBanksWithDetails()->findAll(),
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll()
        ];

        return view('admin/questions/index', $data);
    }

    public function getData()
    {
        $request = \Config\Services::request();

        $draw = (int) $request->getGet('draw') ?? 1;
        $start = (int) $request->getGet('start') ?? 0;
        $length = (int) $request->getGet('length') ?? 10;
        $searchValue = $request->getGet('search')['value'] ?? '';

        // Filters
        $bankFilter = $request->getGet('bank_id');
        $subjectFilter = $request->getGet('subject_id');
        $examTypeFilter = $request->getGet('exam_type_id');
        $difficultyFilter = $request->getGet('difficulty');
        $statusFilter = $request->getGet('status');

        $filters = [
            'bank_id' => $bankFilter,
            'subject_id' => $subjectFilter,
            'exam_type_id' => $examTypeFilter,
            'difficulty' => $difficultyFilter,
            'status' => $statusFilter
        ];

        // Get total records
        $totalRecords = $this->questionModel->countAll();

        // Build query
        $builder = $this->questionModel->getQuestionsWithDetails($filters);

        // Apply search
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('questions.question_text', $searchValue)
                ->orLike('question_banks.name', $searchValue)
                ->orLike('subjects.name', $searchValue)
                ->orLike('exam_types.name', $searchValue)
                ->groupEnd();
        }

        // Get filtered count
        $filteredRecords = $builder->countAllResults(false);

        // Apply pagination
        $questions = $builder->orderBy('questions.created_at', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        // Format data for DataTables
        $data = [];
        foreach ($questions as $question) {
            $data[] = [
                'id' => $question['id'],
                'question_text' => strlen($question['question_text']) > 100 ?
                    substr($question['question_text'], 0, 100) . '...' :
                    $question['question_text'],
                'question_type' => $question['question_type'],
                'difficulty_level' => $question['difficulty_level'],
                'points' => $question['points'],
                'bank_name' => $question['bank_name'],
                'subject_name' => $question['subject_name'],
                'exam_type_name' => $question['exam_type_name'],
                'status' => $question['status'],
                'created_at' => date('d/m/Y H:i', strtotime($question['created_at'])),
                'actions' => $this->generateActionButtons($question)
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
        if ($this->request->getMethod() === 'POST') {
            return $this->store();
        }
        $data = [
            'title' => 'Tambah Soal',
            'page' => 'questions',
            'questionBanks' => $this->questionBankModel->getQuestionBanksWithDetails()->findAll(),
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll()
        ];

        return view('admin/questions/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'question_bank_id' => 'required|integer',
            'question_text' => 'required|min_length[10]',
            'question_type' => 'required|in_list[multiple_choice,essay,true_false,fill_blank]',
            'difficulty_level' => 'required|in_list[easy,medium,hard]',
            'points' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'question_bank_id' => $this->request->getPost('question_bank_id'),
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'difficulty_level' => $this->request->getPost('difficulty_level'),
            'points' => $this->request->getPost('points'),
            'time_limit' => $this->request->getPost('time_limit'),
            'explanation' => $this->request->getPost('explanation'),
            'status' => $this->request->getPost('status'),
            'created_by' => session('user_id'),
            'updated_by' => session('user_id')
        ];

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            if ($image->move(WRITEPATH . 'uploads/questions/', $newName)) {
                $data['image_url'] = base_url('uploads/questions/' . $newName);
            }
        }

        $questionId = $this->questionModel->insert($data);

        if ($questionId) {
            // Handle options for multiple choice questions
            if ($data['question_type'] === 'multiple_choice') {
                $options = $this->request->getPost('options') ?? [];
                $correctOption = $this->request->getPost('correct_option');

                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        $this->questionOptionModel->insert([
                            'question_id' => $questionId,
                            'option_text' => trim($optionText),
                            'is_correct' => ($index == $correctOption) ? 1 : 0,
                            'order_number' => $index + 1
                        ]);
                    }
                }
            }

            session()->setFlashdata('success', 'Soal berhasil ditambahkan!');
            return redirect()->to('/admin/questions');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan soal!');
            return redirect()->back()->withInput();
        }
    }

    public function edit($id)
    {
        $question = $this->questionModel->find($id);
        if (!$question) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Soal tidak ditemukan');
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->update($id);
        }

        $data = [
            'title' => 'Edit Soal',
            'page' => 'questions',
            'question' => $question,
            'options' => $this->questionOptionModel->getByQuestionId($id),
            'questionBanks' => $this->questionBankModel->getQuestionBanksWithDetails()->findAll(),
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll()
        ];

        return view('admin/questions/edit', $data);
    }

    public function update($id)
    {
        $validation = \Config\Services::validation();

        $rules = [
            'question_bank_id' => 'required|integer',
            'question_text' => 'required|min_length[10]',
            'question_type' => 'required|in_list[multiple_choice,essay,true_false,fill_blank]',
            'difficulty_level' => 'required|in_list[easy,medium,hard]',
            'points' => 'required|numeric|greater_than[0]',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'question_bank_id' => $this->request->getPost('question_bank_id'),
            'question_text' => $this->request->getPost('question_text'),
            'question_type' => $this->request->getPost('question_type'),
            'difficulty_level' => $this->request->getPost('difficulty_level'),
            'points' => $this->request->getPost('points'),
            'time_limit' => $this->request->getPost('time_limit'),
            'explanation' => $this->request->getPost('explanation'),
            'status' => $this->request->getPost('status'),
            'updated_by' => session('user_id')
        ];

        // Handle image upload
        $image = $this->request->getFile('image');
        if ($image && $image->isValid() && !$image->hasMoved()) {
            $newName = $image->getRandomName();
            if ($image->move(WRITEPATH . 'uploads/questions/', $newName)) {
                $data['image_url'] = base_url('uploads/questions/' . $newName);
            }
        }

        if ($this->questionModel->update($id, $data)) {
            // Update options for multiple choice questions
            if ($data['question_type'] === 'multiple_choice') {
                $options = $this->request->getPost('options') ?? [];
                $correctOption = $this->request->getPost('correct_option');

                // Delete existing options
                $this->questionOptionModel->where('question_id', $id)->delete();

                // Insert new options
                foreach ($options as $index => $optionText) {
                    if (!empty(trim($optionText))) {
                        $this->questionOptionModel->insert([
                            'question_id' => $id,
                            'option_text' => trim($optionText),
                            'is_correct' => ($index == $correctOption) ? 1 : 0,
                            'order_number' => $index + 1
                        ]);
                    }
                }
            }

            session()->setFlashdata('success', 'Soal berhasil diperbarui!');
            return redirect()->to('/admin/questions');
        } else {
            session()->setFlashdata('error', 'Gagal memperbarui soal!');
            return redirect()->back()->withInput();
        }
    }

    public function delete($id)
    {
        $question = $this->questionModel->find($id);
        if (!$question) {
            return $this->response->setJSON(['success' => false, 'message' => 'Soal tidak ditemukan']);
        }

        if ($this->questionModel->delete($id)) {
            // Options will be deleted automatically due to cascade
            return $this->response->setJSON(['success' => true, 'message' => 'Soal berhasil dihapus']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus soal']);
        }
    }

    public function uploadPdf()
    {
        $bankId = $this->request->getPost('question_bank_id');
        if (!$bankId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Bank soal harus dipilih terlebih dahulu'
            ]);
        }

        $file = $this->request->getFile('pdf_file');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File PDF tidak valid'
            ]);
        }

        if ($file->getClientMimeType() !== 'application/pdf') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File harus berformat PDF'
            ]);
        }

        try {
            // Upload file
            $fileName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/pdfs/';

            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }
            if ($file->move($uploadPath, $fileName)) {
                $filePath = $uploadPath . $fileName;

                // Get additional form data
                $questionBankId = $bankId;
                $subjectId = $this->request->getPost('subject_id');
                $examTypeId = $this->request->getPost('exam_type_id');
                $difficultyLevel = $this->request->getPost('difficulty_level') ?: 'medium';

                try {
                    // Extract text from PDF
                    $extractedText = $this->pdfScrapingService->extractTextFromPdf($filePath);

                    if (!$extractedText) {
                        unlink($filePath);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Tidak dapat membaca file PDF.'
                        ]);
                    }

                    // Parse questions from extracted text
                    $parsedQuestions = $this->pdfScrapingService->parseQuestions($extractedText);

                    // Auto-detect correct answers if available
                    $this->pdfScrapingService->detectCorrectAnswers($extractedText, $parsedQuestions);

                    // Validate questions
                    $questions = $this->pdfScrapingService->validateQuestions($parsedQuestions);

                    if (empty($questions)) {
                        unlink($filePath);
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Tidak dapat mengekstrak soal dari file PDF. Pastikan format PDF sesuai dengan panduan.'
                        ]);
                    }

                    $successCount = 0;
                    $failedCount = 0;

                    // Save questions to database
                    foreach ($questions as $questionData) {
                        $data = [
                            'question_bank_id' => $questionBankId,
                            'question_text' => $questionData['question_text'],
                            'question_type' => $questionData['question_type'],
                            'difficulty_level' => $difficultyLevel,
                            'points' => $questionData['points'],
                            'status' => $questionData['status'],
                            'created_by' => session('user_id'),
                            'updated_by' => session('user_id'),
                            'order_number' => $questionData['order_number'] ?? null
                        ];

                        $questionId = $this->questionModel->insert($data);

                        if ($questionId) {
                            // Save options for multiple choice questions
                            if ($questionData['question_type'] === 'multiple_choice' && !empty($questionData['options'])) {
                                foreach ($questionData['options'] as $option) {
                                    $this->questionOptionModel->insert([
                                        'question_id' => $questionId,
                                        'option_text' => $option['text'],
                                        'is_correct' => $option['is_correct'] ? 1 : 0,
                                        'order_number' => array_search($option['letter'], ['A', 'B', 'C', 'D', 'E']) + 1
                                    ]);
                                }
                            }
                            $successCount++;
                        } else {
                            $failedCount++;
                        }
                    }

                    // Clean up uploaded file
                    unlink($filePath);

                    // Get extraction statistics
                    $stats = $this->pdfScrapingService->getExtractionStats($questions);

                    return $this->response->setJSON([
                        'success' => true,
                        'message' => "PDF berhasil diproses. {$successCount} soal berhasil disimpan.",
                        'questions_extracted' => $successCount,
                        'questions_failed' => $failedCount,
                        'stats' => $stats,
                        'questions' => array_slice($questions, 0, 5) // Preview first 5 questions
                    ]);
                } catch (\Exception $e) {
                    // Clean up uploaded file on error
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Terjadi kesalahan saat memproses PDF: ' . $e->getMessage()
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupload file'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function bulkDelete()
    {
        $ids = $this->request->getPost('ids');

        if (empty($ids) || !is_array($ids)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tidak ada soal yang dipilih'
            ]);
        }

        $deletedCount = 0;
        foreach ($ids as $id) {
            if ($this->questionModel->delete($id)) {
                $deletedCount++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Berhasil menghapus {$deletedCount} soal"
        ]);
    }

    public function duplicate($id)
    {
        $result = $this->questionModel->duplicateQuestion($id);

        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Soal berhasil diduplikasi'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menduplikasi soal'
            ]);
        }
    }

    private function generateActionButtons($question)
    {
        return "
            <div class='flex space-x-2'>
                <button onclick='editQuestion({$question['id']})' 
                        class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm'>
                    Edit
                </button>
                <button onclick='duplicateQuestion({$question['id']})' 
                        class='bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-sm'>
                    Duplikasi
                </button>
                <button onclick='deleteQuestion({$question['id']})' 
                        class='bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm'>
                    Hapus
                </button>
            </div>
        ";
    }
}
