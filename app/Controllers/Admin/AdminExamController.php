<?php

namespace App\Controllers\Admin;

use App\Models\QuestionBankModel;
use App\Models\QuestionModel;
use App\Models\QuestionOptionModel;
use App\Models\ExamTypeModel;
use App\Libraries\PdfScrapingService;

class AdminExamController extends BaseAdminController
{
    protected $questionBankModel;
    protected $questionModel;
    protected $questionOptionModel;
    protected $examTypeModel;
    protected $pdfScrapingService;

    public function __construct()
    {
        parent::__construct();
        $this->questionBankModel = new QuestionBankModel();
        $this->questionModel = new QuestionModel();
        $this->questionOptionModel = new QuestionOptionModel();
        $this->examTypeModel = new ExamTypeModel();
        $this->pdfScrapingService = new PdfScrapingService();
    }

    public function index()
    {
        return $this->exams();
    }
    public function exams()
    {
        $search = $this->request->getGet('search');
        $subjectFilter = $this->request->getGet('subject_id');
        $examTypeFilter = $this->request->getGet('exam_type_id');
        $statusFilter = $this->request->getGet('status');

        // Get exams with full details including question banks
        $filters = [
            'subject_id' => $subjectFilter,
            'exam_type_id' => $examTypeFilter,
            'status' => $statusFilter
        ];

        $exams = $this->examModel->getExamsWithFullDetails($filters);

        // Add search functionality
        if ($search) {
            $exams = array_filter($exams, function ($exam) use ($search) {
                return stripos($exam['title'], $search) !== false ||
                    stripos($exam['description'], $search) !== false;
            });
        }

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

            // Get question count from question bank if available
            if ($exam['question_bank_id']) {
                $exam['available_questions'] = $this->questionModel->getCountByBankId($exam['question_bank_id']);
            } else {
                $exam['available_questions'] = 0;
            }
        }

        // Get statistics
        $stats = [
            'total' => count($exams),
            'active' => count(array_filter($exams, fn($e) => $e['status'] === 'active')),
            'scheduled' => count(array_filter($exams, fn($e) => $e['status'] === 'scheduled')),
            'completed' => count(array_filter($exams, fn($e) => $e['status'] === 'completed')),
            'draft' => count(array_filter($exams, fn($e) => $e['status'] === 'draft'))
        ];

        $data = [
            'title' => 'Kelola Ujian',
            'exams' => $exams,
            'stats' => $stats,
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll(),
            'teachers' => $this->userModel->getTeachers(),
            'search' => $search,
            'subjectFilter' => $subjectFilter,
            'examTypeFilter' => $examTypeFilter,
            'statusFilter' => $statusFilter,
        ];

        return view('admin/exams/index', $data);
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
            $createType = $this->request->getPost('create_type'); // 'manual', 'question_bank', 'pdf_scrape'

            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'exam_type_id' => $this->request->getPost('exam_type_id'),
                'teacher_id' => session()->get('user_id'),
                'question_count' => $this->request->getPost('question_count'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Handle different creation types
            switch ($createType) {
                case 'question_bank':
                    return $this->createFromQuestionBank($data);

                case 'pdf_scrape':
                    return $this->createFromPdfScrape($data);

                default: // manual
                    return $this->createManualExam($data);
            }
        }

        // Get available question banks
        $questionBanks = $this->questionBankModel->getQuestionBanksWithStats();

        $data = [
            'title' => 'Buat Ujian Baru',
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll(),
            'teachers' => $this->userModel->getTeachers(),
            'questionBanks' => $questionBanks
        ];

        return view('admin/exams/create', $data);
    }
    /**
     * Create exam from manual input
     */
    public function createManualExam()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/exams/create');
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'subject_id' => $this->request->getPost('subject_id'),
            'exam_type_id' => $this->request->getPost('exam_type_id'),
            'teacher_id' => session()->get('user_id'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'max_attempts' => $this->request->getPost('max_attempts') ?? 1,
            'passing_score' => $this->request->getPost('passing_score') ?? 60,
            'shuffle_questions' => $this->request->getPost('shuffle_questions') ?? 0,
            'is_active' => 0,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examModel->insert($data)) {
            $examId = $this->examModel->getInsertID();
            session()->setFlashdata('success', 'Exam created successfully! You can now add questions manually.');
            return redirect()->to("/admin/exams/edit/{$examId}");
        } else {
            session()->setFlashdata('error', 'Failed to create exam: ' . implode(', ', $this->examModel->errors()));
            return redirect()->back()->withInput();
        }
    }
    /**
     * Create exam from question bank
     */
    public function createFromQuestionBank()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/exams/create');
        }

        $questionBankId = $this->request->getPost('question_bank_id');
        $questionCount = $this->request->getPost('question_count');

        if (!$questionBankId) {
            session()->setFlashdata('error', 'Please select a question bank!');
            return redirect()->back()->withInput();
        }

        // Validate question bank
        $questionBank = $this->questionBankModel->find($questionBankId);
        if (!$questionBank) {
            session()->setFlashdata('error', 'Question bank not found!');
            return redirect()->back()->withInput();
        }

        $availableQuestions = $this->questionModel->getCountByBankId($questionBankId);
        if ($questionCount && $availableQuestions < $questionCount) {
            session()->setFlashdata('error', "Question bank only has {$availableQuestions} active questions!");
            return redirect()->back()->withInput();
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'subject_id' => $questionBank['subject_id'],
            'exam_type_id' => $questionBank['exam_type_id'],
            'question_bank_id' => $questionBankId,
            'teacher_id' => session()->get('user_id'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'max_attempts' => $this->request->getPost('max_attempts') ?? 1,
            'passing_score' => $this->request->getPost('passing_score') ?? 60,
            'shuffle_questions' => $this->request->getPost('shuffle_questions') ?? 0,
            'question_count' => $questionCount ?: $availableQuestions,
            'is_active' => 0,
            'status' => 'draft',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examModel->insert($data)) {
            session()->setFlashdata('success', 'Exam created successfully from question bank!');
            return redirect()->to('/admin/exams');
        } else {
            session()->setFlashdata('error', 'Failed to create exam: ' . implode(', ', $this->examModel->errors()));
            return redirect()->back()->withInput();
        }
    }

    private function createFromPdfScrape($data)
    {
        $pdfFile = $this->request->getFile('pdf_file');
        $pdfUrl = $this->request->getPost('pdf_url');

        if (!$pdfFile && !$pdfUrl) {
            session()->setFlashdata('error', 'File PDF atau URL PDF harus disediakan!');
            return redirect()->back()->withInput();
        }

        try {
            // Extract text from PDF
            $pdfText = '';
            if ($pdfFile && $pdfFile->isValid()) {
                // Handle uploaded file
                $uploadPath = WRITEPATH . 'uploads/temp/';
                if (!is_dir($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $fileName = $pdfFile->getRandomName();
                $pdfFile->move($uploadPath, $fileName);
                $pdfText = $this->pdfScrapingService->extractTextFromPdf($uploadPath . $fileName);

                // Clean up temp file
                unlink($uploadPath . $fileName);
            } elseif ($pdfUrl) {
                // Handle URL
                $pdfText = $this->pdfScrapingService->extractTextFromPdf($pdfUrl);
            }

            if (!$pdfText) {
                session()->setFlashdata('error', 'Gagal mengekstrak teks dari PDF!');
                return redirect()->back()->withInput();
            }

            // Parse questions from text
            $questions = $this->pdfScrapingService->parseQuestions($pdfText);

            if (empty($questions)) {
                session()->setFlashdata('error', 'Tidak ada soal yang berhasil diparse dari PDF!');
                return redirect()->back()->withInput();
            }

            // Create question bank first
            $questionBankData = [
                'name' => $data['title'] . ' - Bank Soal',
                'description' => 'Bank soal dari PDF untuk ujian: ' . $data['title'],
                'subject_id' => $data['subject_id'],
                'exam_type_id' => $data['exam_type_id'],
                'difficulty_level' => 'medium',
                'total_questions' => count($questions),
                'status' => 'active',
                'created_by' => session()->get('user_id')
            ];

            $questionBankId = $this->questionBankModel->insert($questionBankData);
            if (!$questionBankId) {
                session()->setFlashdata('error', 'Gagal membuat bank soal!');
                return redirect()->back()->withInput();
            }

            // Save questions to database
            $savedQuestions = 0;
            foreach ($questions as $questionData) {
                $questionInfo = [
                    'question_bank_id' => $questionBankId,
                    'question_text' => $questionData['question'],
                    'question_type' => $questionData['type'],
                    'difficulty_level' => $questionData['difficulty'] ?? 'medium',
                    'points' => $questionData['points'] ?? 10,
                    'explanation' => $questionData['explanation'] ?? '',
                    'status' => 'active',
                    'created_by' => session()->get('user_id')
                ];

                $questionId = $this->questionModel->insert($questionInfo);
                if ($questionId && !empty($questionData['options'])) {
                    // Save options for multiple choice questions
                    foreach ($questionData['options'] as $index => $option) {
                        $optionData = [
                            'question_id' => $questionId,
                            'option_text' => $option['text'],
                            'is_correct' => $option['is_correct'] ? 1 : 0,
                            'order_number' => $index + 1
                        ];
                        $this->questionOptionModel->insert($optionData);
                    }
                }

                if ($questionId) $savedQuestions++;
            }

            if ($savedQuestions > 0) {
                $data['question_bank_id'] = $questionBankId;
                $data['question_count'] = min($data['question_count'], $savedQuestions);

                if ($this->examModel->insert($data)) {
                    session()->setFlashdata('success', "Ujian berhasil dibuat dengan {$savedQuestions} soal dari PDF!");
                    return redirect()->to('/admin/exams');
                } else {
                    session()->setFlashdata('error', 'Gagal membuat ujian: ' . implode(', ', $this->examModel->errors()));
                    return redirect()->back()->withInput();
                }
            } else {
                session()->setFlashdata('error', 'Gagal menyimpan soal ke database!');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'PDF scraping error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Terjadi kesalahan saat memproses PDF: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
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
            'completed_participants' => $this->examResultModel->where('exam_id', $id)->where('status', EXAM_STATUS_SUBMITTED)->countAllResults(),
            'graded_participants' => $this->examResultModel->where('exam_id', $id)->where('status', EXAM_STATUS_GRADED)->countAllResults(),
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

    /**
     * Get question banks for AJAX requests
     */
    public function getQuestionBanks()
    {
        $subjectId = $this->request->getGet('subject_id');
        $examTypeId = $this->request->getGet('exam_type_id');

        $questionBanks = $this->examModel->getAvailableQuestionBanks($subjectId, $examTypeId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $questionBanks
        ]);
    }

    /**
     * DataTables server-side processing
     */
    public function getData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $draw = $this->request->getPost('draw');
        $start = $this->request->getPost('start') ?? 0;
        $length = $this->request->getPost('length') ?? 10;
        $searchValue = $this->request->getPost('search')['value'] ?? '';

        // Get filters
        $subjectFilter = $this->request->getPost('subject_filter');
        $examTypeFilter = $this->request->getPost('exam_type_filter');
        $statusFilter = $this->request->getPost('status_filter');
        $teacherFilter = $this->request->getPost('teacher_filter');

        $filters = array_filter([
            'subject_id' => $subjectFilter,
            'exam_type_id' => $examTypeFilter,
            'teacher_id' => $teacherFilter
        ]);

        // Get total records
        $totalRecords = $this->examModel->countAll();

        // Get filtered data
        $exams = $this->examModel->getExamsWithFullDetails($filters);

        // Apply search
        if (!empty($searchValue)) {
            $exams = array_filter($exams, function ($exam) use ($searchValue) {
                return stripos($exam['title'], $searchValue) !== false ||
                    stripos($exam['description'], $searchValue) !== false ||
                    stripos($exam['subject_name'], $searchValue) !== false ||
                    stripos($exam['teacher_name'], $searchValue) !== false;
            });
        }

        // Apply status filter
        if (!empty($statusFilter)) {
            $now = new \DateTime();
            $exams = array_filter($exams, function ($exam) use ($statusFilter, $now) {
                $start = new \DateTime($exam['start_time']);
                $end = new \DateTime($exam['end_time']);

                $status = 'draft';
                if ($exam['is_active']) {
                    if ($now < $start) $status = 'scheduled';
                    elseif ($now >= $start && $now <= $end) $status = 'active';
                    else $status = 'completed';
                }

                return $status === $statusFilter;
            });
        }

        $filteredRecords = count($exams);

        // Apply pagination
        $exams = array_slice($exams, $start, $length);

        // Format data for DataTables
        $data = [];
        foreach ($exams as $exam) {
            $now = new \DateTime();
            $start = new \DateTime($exam['start_time']);
            $end = new \DateTime($exam['end_time']);

            // Determine status
            $status = 'draft';
            $statusClass = 'bg-gray-100 text-gray-800';
            if ($exam['is_active']) {
                if ($now < $start) {
                    $status = 'scheduled';
                    $statusClass = 'bg-blue-100 text-blue-800';
                } elseif ($now >= $start && $now <= $end) {
                    $status = 'active';
                    $statusClass = 'bg-green-100 text-green-800';
                } else {
                    $status = 'completed';
                    $statusClass = 'bg-red-100 text-red-800';
                }
            }

            // Get question count
            $questionCount = $exam['question_bank_id']
                ? $this->questionModel->getCountByBankId($exam['question_bank_id'])
                : 0;

            $data[] = [
                'id' => $exam['id'],
                'title' => $exam['title'],
                'subject_name' => $exam['subject_name'],
                'exam_type_name' => $exam['exam_type_name'] ?? '-',
                'question_bank_name' => $exam['question_bank_name'] ?? '-',
                'teacher_name' => $exam['teacher_name'],
                'question_count' => $questionCount,
                'duration_minutes' => $exam['duration_minutes'],
                'start_time' => date('d/m/Y H:i', strtotime($exam['start_time'])),
                'end_time' => date('d/m/Y H:i', strtotime($exam['end_time'])),
                'status' => $status,
                'status_badge' => "<span class='px-2 py-1 text-xs font-medium rounded-full {$statusClass}'>" . ucfirst($status) . "</span>",
                'actions' => $this->generateActionButtons($exam)
            ];
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    private function generateActionButtons($exam)
    {
        $buttons = [];

        // View button
        $buttons[] = "<a href='" . base_url("admin/exams/view/{$exam['id']}") . "' 
                        class='inline-flex items-center px-2 py-1 bg-blue-600 text-white text-xs rounded hover:bg-blue-700 transition-colors'>
                        <i class='fas fa-eye mr-1'></i> Lihat
                      </a>";

        // Edit button
        $buttons[] = "<a href='" . base_url("admin/exams/edit/{$exam['id']}") . "' 
                        class='inline-flex items-center px-2 py-1 bg-yellow-600 text-white text-xs rounded hover:bg-yellow-700 transition-colors'>
                        <i class='fas fa-edit mr-1'></i> Edit
                      </a>";

        // Questions button if has question bank
        if ($exam['question_bank_id']) {
            $buttons[] = "<a href='" . base_url("admin/questions?bank_id={$exam['question_bank_id']}") . "' 
                            class='inline-flex items-center px-2 py-1 bg-green-600 text-white text-xs rounded hover:bg-green-700 transition-colors'>
                            <i class='fas fa-question-circle mr-1'></i> Soal
                          </a>";
        }

        // Results button if exam is completed or active
        $now = new \DateTime();
        $start = new \DateTime($exam['start_time']);
        if ($exam['is_active'] && $now >= $start) {
            $buttons[] = "<a href='" . base_url("admin/exam-results/{$exam['id']}") . "' 
                            class='inline-flex items-center px-2 py-1 bg-purple-600 text-white text-xs rounded hover:bg-purple-700 transition-colors'>
                            <i class='fas fa-chart-bar mr-1'></i> Hasil
                          </a>";
        }

        // Delete button
        $buttons[] = "<button onclick='deleteExam({$exam['id']})' 
                        class='inline-flex items-center px-2 py-1 bg-red-600 text-white text-xs rounded hover:bg-red-700 transition-colors'>
                        <i class='fas fa-trash mr-1'></i> Hapus
                      </button>";

        return '<div class="flex flex-wrap gap-1">' . implode('', $buttons) . '</div>';
    }

    /**
     * Create exam from PDF
     */
    public function createFromPdf()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/admin/exams/create');
        }

        $pdfFile = $this->request->getFile('pdf_file');

        if (!$pdfFile || !$pdfFile->isValid()) {
            session()->setFlashdata('error', 'Please upload a valid PDF file!');
            return redirect()->back()->withInput();
        }

        try {
            // Handle uploaded file
            $uploadPath = WRITEPATH . 'uploads/temp/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = $pdfFile->getRandomName();
            $pdfFile->move($uploadPath, $fileName);
            $pdfText = $this->pdfScrapingService->extractTextFromPdf($uploadPath . $fileName);

            // Clean up temp file
            unlink($uploadPath . $fileName);

            if (!$pdfText) {
                session()->setFlashdata('error', 'Failed to extract text from PDF!');
                return redirect()->back()->withInput();
            }

            // Parse questions from text
            $questions = $this->pdfScrapingService->parseQuestions($pdfText);

            if (empty($questions)) {
                session()->setFlashdata('error', 'No questions could be parsed from the PDF!');
                return redirect()->back()->withInput();
            }

            // Create question bank if requested
            $questionBankId = null;
            if ($this->request->getPost('create_question_bank')) {
                $questionBankData = [
                    'name' => $this->request->getPost('title') . ' - Question Bank',
                    'description' => 'Question bank from PDF for exam: ' . $this->request->getPost('title'),
                    'subject_id' => $this->request->getPost('subject_id'),
                    'exam_type_id' => $this->request->getPost('exam_type_id'),
                    'difficulty_level' => 'medium',
                    'total_questions' => count($questions),
                    'status' => 'active',
                    'created_by' => session()->get('user_id'),
                    'created_at' => date('Y-m-d H:i:s')
                ];

                $questionBankId = $this->questionBankModel->insert($questionBankData);
                if (!$questionBankId) {
                    session()->setFlashdata('error', 'Failed to create question bank!');
                    return redirect()->back()->withInput();
                }

                // Save questions to database
                $savedQuestions = 0;
                foreach ($questions as $questionData) {
                    $questionInfo = [
                        'question_bank_id' => $questionBankId,
                        'question_text' => $questionData['question'],
                        'question_type' => $questionData['type'],
                        'difficulty_level' => $questionData['difficulty'] ?? 'medium',
                        'points' => $questionData['points'] ?? 10,
                        'explanation' => $questionData['explanation'] ?? '',
                        'status' => 'active',
                        'created_by' => session()->get('user_id'),
                        'created_at' => date('Y-m-d H:i:s')
                    ];

                    $questionId = $this->questionModel->insert($questionInfo);
                    if ($questionId && !empty($questionData['options'])) {
                        // Save options for multiple choice questions
                        foreach ($questionData['options'] as $index => $option) {
                            $optionData = [
                                'question_id' => $questionId,
                                'option_text' => $option['text'],
                                'is_correct' => $option['is_correct'] ? 1 : 0,
                                'order_number' => $index + 1
                            ];
                            $this->questionOptionModel->insert($optionData);
                        }
                    }

                    if ($questionId) $savedQuestions++;
                }
            }

            // Create exam
            $examData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'exam_type_id' => $this->request->getPost('exam_type_id'),
                'question_bank_id' => $questionBankId,
                'teacher_id' => session()->get('user_id'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'max_attempts' => $this->request->getPost('max_attempts') ?? 1,
                'passing_score' => $this->request->getPost('passing_score') ?? 60,
                'shuffle_questions' => $this->request->getPost('shuffle_questions') ?? 0,
                'question_count' => count($questions),
                'is_active' => 0,
                'status' => 'draft',
                'created_at' => date('Y-m-d H:i:s')
            ];

            if ($this->examModel->insert($examData)) {
                $message = "Exam created successfully with " . count($questions) . " questions from PDF!";
                if ($questionBankId) {
                    $message .= " Questions have been saved to a new question bank.";
                }
                session()->setFlashdata('success', $message);
                return redirect()->to('/admin/exams');
            } else {
                session()->setFlashdata('error', 'Failed to create exam: ' . implode(', ', $this->examModel->errors()));
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'PDF processing error: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error processing PDF: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Update exam
     */
    public function update($id)
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to("/admin/exams/edit/{$id}");
        }

        $exam = $this->examModel->find($id);
        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Exam not found');
        }

        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'subject_id' => $this->request->getPost('subject_id'),
            'exam_type_id' => $this->request->getPost('exam_type_id'),
            'question_bank_id' => $this->request->getPost('question_bank_id') ?: null,
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'start_time' => $this->request->getPost('start_time'),
            'end_time' => $this->request->getPost('end_time'),
            'max_attempts' => $this->request->getPost('max_attempts') ?? 1,
            'passing_score' => $this->request->getPost('passing_score') ?? 60,
            'status' => $this->request->getPost('status'),
            'shuffle_questions' => $this->request->getPost('shuffle_questions') ?? 0,
            'show_results' => $this->request->getPost('show_results') ?? 'immediately',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->examModel->update($id, $data)) {
            session()->setFlashdata('success', 'Exam updated successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to update exam: ' . implode(', ', $this->examModel->errors()));
        }

        return redirect()->to("/admin/exams/edit/{$id}");
    }

    /**
     * Enhanced edit method with question bank support
     */
    public function edit($id)
    {
        $exam = $this->examModel->find($id);
        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Exam not found');
        }

        $data = [
            'title' => 'Edit Exam',
            'exam' => $exam,
            'subjects' => $this->subjectModel->findAll(),
            'examTypes' => $this->examTypeModel->findAll(),
            'questionBanks' => $this->questionBankModel->getQuestionBanksWithStats(),
            'teachers' => $this->userModel->getTeachers()
        ];

        return view('admin/exams/edit', $data);
    }

    /**
     * Get exam questions for management
     */
    public function getQuestions($examId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON(['success' => false, 'message' => 'Exam not found']);
        }

        // Get questions from exam_questions table or question bank
        $questions = [];
        if ($exam['question_bank_id']) {
            $questions = $this->questionModel->getQuestionsByBankId($exam['question_bank_id']);
        } else {
            // Get questions directly associated with exam
            $questions = $this->questionModel
                ->select('questions.*, question_banks.name as question_bank_name')
                ->join('exam_questions', 'exam_questions.question_id = questions.id', 'left')
                ->join('question_banks', 'question_banks.id = questions.question_bank_id', 'left')
                ->where('exam_questions.exam_id', $examId)
                ->orderBy('exam_questions.order_number', 'ASC')
                ->findAll();
        }

        return $this->response->setJSON([
            'success' => true,
            'questions' => $questions
        ]);
    }

    /**
     * Add questions to exam
     */
    public function addQuestions($examId)
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(403);
        }

        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON(['success' => false, 'message' => 'Exam not found']);
        }

        $questionIds = $this->request->getJSON(true)['question_ids'] ?? [];
        if (empty($questionIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No questions selected']);
        }

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('exam_questions');

            // Get current max order
            $maxOrder = $builder->selectMax('order_number')->where('exam_id', $examId)->get()->getRow()->order_number ?? 0;

            // Add questions
            $insertData = [];
            foreach ($questionIds as $index => $questionId) {
                $insertData[] = [
                    'exam_id' => $examId,
                    'question_id' => $questionId,
                    'order_number' => $maxOrder + $index + 1,
                    'points' => 10, // Default points
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }

            if ($builder->insertBatch($insertData)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Questions added successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to add questions']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove question from exam
     */
    public function removeQuestion()
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $examId = $data['exam_id'] ?? null;
        $questionId = $data['question_id'] ?? null;

        if (!$examId || !$questionId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('exam_questions');

            $result = $builder->where('exam_id', $examId)->where('question_id', $questionId)->delete();

            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'Question removed successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to remove question']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Update question order in exam
     */
    public function updateQuestionOrder()
    {
        if (!$this->request->isAJAX() || $this->request->getMethod() !== 'POST') {
            return $this->response->setStatusCode(403);
        }

        $data = $this->request->getJSON(true);
        $examId = $data['exam_id'] ?? null;
        $questionId = $data['question_id'] ?? null;
        $newOrder = $data['order'] ?? null;

        if (!$examId || !$questionId || !$newOrder) {
            return $this->response->setJSON(['success' => false, 'message' => 'Missing required data']);
        }

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('exam_questions');

            $result = $builder->where('exam_id', $examId)
                ->where('question_id', $questionId)
                ->update(['order_number' => $newOrder]);

            if ($result) {
                return $this->response->setJSON(['success' => true, 'message' => 'Question order updated successfully']);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update question order']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    /**
     * Import exams from Excel/CSV file
     */
    public function importExams()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $file = $this->request->getFile('import_file');
            $replaceExisting = $this->request->getPost('replace_existing') === '1';

            if (!$file || !$file->isValid()) {
                return $this->response->setJSON(['success' => false, 'message' => 'File tidak valid atau tidak ditemukan']);
            }

            // Validate file type
            $allowedTypes = ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel', 'text/csv'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Format file tidak didukung. Gunakan Excel (.xlsx) atau CSV']);
            }

            // Process the file based on type
            $importedCount = 0;
            $errors = [];

            if ($file->getExtension() === 'csv') {
                $importedCount = $this->processCSVImport($file, $replaceExisting, $errors);
            } else {
                $importedCount = $this->processExcelImport($file, $replaceExisting, $errors);
            }

            if ($importedCount > 0) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "Import berhasil! $importedCount data telah diimport",
                    'imported_count' => $importedCount,
                    'errors' => $errors
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak ada data yang berhasil diimport',
                    'errors' => $errors
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Import error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Export exams to Excel/CSV/PDF
     */
    public function exportExams()
    {
        try {
            $format = $this->request->getPost('export_format') ?: 'excel';
            $includeQuestions = $this->request->getPost('include_questions') === '1';

            // Get filters
            $filters = [
                'subject_id' => $this->request->getPost('export_subject') ?: $this->request->getPost('subject_filter'),
                'exam_type_id' => $this->request->getPost('exam_type_filter'),
                'status' => $this->request->getPost('export_status') ?: $this->request->getPost('status_filter'),
                'teacher_id' => $this->request->getPost('teacher_filter')
            ];

            // Get exam data
            $exams = $this->examModel->getExamsWithFullDetails($filters);

            switch ($format) {
                case 'csv':
                    return $this->exportToCSV($exams, $includeQuestions);
                case 'pdf':
                    return $this->exportToPDF($exams, $includeQuestions);
                default:
                    return $this->exportToExcel($exams, $includeQuestions);
            }
        } catch (\Exception $e) {
            log_message('error', 'Export error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        try {
            $filename = 'template_import_ujian.xlsx';
            $filepath = WRITEPATH . 'uploads/templates/' . $filename;

            // Create template if doesn't exist
            if (!file_exists($filepath)) {
                $this->createImportTemplate($filepath);
            }

            return $this->response->download($filepath, null);
        } catch (\Exception $e) {
            log_message('error', 'Template download error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mendownload template');
        }
    }

    /**
     * Process CSV import
     */
    private function processCSVImport($file, $replaceExisting, &$errors)
    {
        $importedCount = 0;
        $handle = fopen($file->getTempName(), 'r');

        if ($handle !== false) {
            // Skip header row
            fgetcsv($handle);

            while (($data = fgetcsv($handle)) !== false) {
                try {
                    $examData = $this->mapCSVRowToExamData($data);

                    if ($this->validateExamData($examData)) {
                        if ($replaceExisting) {
                            // Check if exam exists by title
                            $existing = $this->examModel->where('title', $examData['title'])->first();
                            if ($existing) {
                                $this->examModel->update($existing['id'], $examData);
                            } else {
                                $this->examModel->insert($examData);
                            }
                        } else {
                            $this->examModel->insert($examData);
                        }
                        $importedCount++;
                    }
                } catch (\Exception $e) {
                    $errors[] = "Row error: " . $e->getMessage();
                }
            }

            fclose($handle);
        }

        return $importedCount;
    }

    /**
     * Process Excel import
     */
    private function processExcelImport($file, $replaceExisting, &$errors)
    {
        // For now, return a simple implementation
        // You can enhance this with a proper Excel library like PhpSpreadsheet
        $importedCount = 0;
        $errors[] = "Excel import akan segera tersedia. Gunakan format CSV untuk sementara.";
        return $importedCount;
    }

    /**
     * Export to CSV
     */
    private function exportToCSV($exams, $includeQuestions)
    {
        $filename = 'export_ujian_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header
        $headers = ['ID', 'Judul', 'Deskripsi', 'Mata Pelajaran', 'Jenis Ujian', 'Guru', 'Durasi (menit)', 'Nilai Lulus', 'Waktu Mulai', 'Waktu Selesai', 'Status'];
        if ($includeQuestions) {
            $headers[] = 'Jumlah Soal';
            $headers[] = 'Bank Soal';
        }

        fputcsv($output, $headers);

        // Data
        foreach ($exams as $exam) {
            $row = [
                $exam['id'],
                $exam['title'],
                $exam['description'],
                $exam['subject_name'],
                $exam['exam_type_name'],
                $exam['teacher_name'],
                $exam['duration_minutes'],
                $exam['passing_score'],
                $exam['start_time'],
                $exam['end_time'],
                $exam['status']
            ];

            if ($includeQuestions) {
                $row[] = $exam['question_count'] ?? 0;
                $row[] = $exam['question_bank_name'] ?? '-';
            }

            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($exams, $includeQuestions)
    {
        // For now, export as CSV with Excel-friendly format
        return $this->exportToCSV($exams, $includeQuestions);
    }

    /**
     * Export to PDF
     */
    private function exportToPDF($exams, $includeQuestions)
    {
        // Simple PDF export implementation
        $filename = 'export_ujian_' . date('Y-m-d_H-i-s') . '.pdf';

        // For now, return a simple text response
        header('Content-Type: application/json');
        return $this->response->setJSON(['success' => false, 'message' => 'Export PDF akan segera tersedia']);
    }

    /**
     * Map CSV row to exam data
     */
    private function mapCSVRowToExamData($data)
    {
        return [
            'title' => $data[0] ?? '',
            'description' => $data[1] ?? '',
            'subject_id' => $this->getSubjectIdByName($data[2] ?? ''),
            'exam_type_id' => $this->getExamTypeIdByName($data[3] ?? ''),
            'teacher_id' => session()->get('user_id'),
            'duration_minutes' => intval($data[4] ?? 60),
            'passing_score' => intval($data[5] ?? 60),
            'start_time' => $data[6] ?? null,
            'end_time' => $data[7] ?? null,
            'status' => $data[8] ?? 'draft',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Validate exam data
     */
    private function validateExamData($data)
    {
        return !empty($data['title']) &&
            !empty($data['subject_id']) &&
            !empty($data['exam_type_id']) &&
            $data['duration_minutes'] > 0;
    }

    /**
     * Get subject ID by name
     */
    private function getSubjectIdByName($name)
    {
        if (empty($name)) return null;

        $subject = $this->subjectModel->where('name', $name)->first();
        return $subject ? $subject['id'] : null;
    }

    /**
     * Get exam type ID by name
     */
    private function getExamTypeIdByName($name)
    {
        if (empty($name)) return null;

        $examType = $this->examTypeModel->where('name', $name)->first();
        return $examType ? $examType['id'] : null;
    }

    /**
     * Create import template
     */    private function createImportTemplate($filepath)
    {
        // Create directory if doesn't exist
        $dir = dirname($filepath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        // Use PhpSpreadsheet to create Excel file
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $headers = [
            'A1' => 'Judul Ujian',
            'B1' => 'Deskripsi',
            'C1' => 'Mata Pelajaran',
            'D1' => 'Jenis Ujian',
            'E1' => 'Durasi (menit)',
            'F1' => 'Nilai Lulus (%)',
            'G1' => 'Waktu Mulai (YYYY-MM-DD HH:MM:SS)',
            'H1' => 'Waktu Selesai (YYYY-MM-DD HH:MM:SS)',
            'I1' => 'Status (draft/scheduled/active/completed)',
            'J1' => 'Max Attempts',
            'K1' => 'Shuffle Questions (0/1)'
        ];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'color' => ['rgb' => '4472C4']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];

        $sheet->getStyle('A1:K1')->applyFromArray($headerStyle);

        // Add sample data
        $sampleData = [
            'A2' => 'Contoh Ujian Matematika',
            'B2' => 'Ujian matematika untuk kelas 10',
            'C2' => 'Matematika',
            'D2' => 'Ulangan Harian',
            'E2' => '90',
            'F2' => '70',
            'G2' => '2025-06-15 08:00:00',
            'H2' => '2025-06-15 17:00:00',
            'I2' => 'draft',
            'J2' => '1',
            'K2' => '1'
        ];

        foreach ($sampleData as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Auto-size columns
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add instructions sheet
        $instructionSheet = $spreadsheet->createSheet();
        $instructionSheet->setTitle('Petunjuk');

        $instructions = [
            'A1' => 'PETUNJUK IMPORT DATA UJIAN',
            'A3' => 'Format yang diperlukan:',
            'A4' => '• Judul Ujian: Nama ujian (wajib)',
            'A5' => '• Deskripsi: Deskripsi ujian (opsional)',
            'A6' => '• Mata Pelajaran: Nama mata pelajaran yang sudah ada di sistem',
            'A7' => '• Jenis Ujian: Nama jenis ujian yang sudah ada di sistem',
            'A8' => '• Durasi: Durasi dalam menit (angka)',
            'A9' => '• Nilai Lulus: Nilai lulus dalam persen (0-100)',
            'A10' => '• Waktu Mulai: Format YYYY-MM-DD HH:MM:SS',
            'A11' => '• Waktu Selesai: Format YYYY-MM-DD HH:MM:SS',
            'A12' => '• Status: draft, scheduled, active, atau completed',
            'A13' => '• Max Attempts: Jumlah maksimal percobaan (angka)',
            'A14' => '• Shuffle Questions: 1 untuk acak, 0 untuk tidak acak',
            'A16' => 'Catatan:',
            'A17' => '• Mata Pelajaran dan Jenis Ujian harus sudah ada di sistem',
            'A18' => '• Waktu selesai harus lebih besar dari waktu mulai',
            'A19' => '• Jika terjadi error, periksa format data sesuai petunjuk di atas'
        ];

        foreach ($instructions as $cell => $value) {
            $instructionSheet->setCellValue($cell, $value);
        }

        $instructionSheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '4472C4']]
        ]);

        $instructionSheet->getStyle('A3')->applyFromArray([
            'font' => ['bold' => true]
        ]);

        $instructionSheet->getStyle('A16')->applyFromArray([
            'font' => ['bold' => true]
        ]);

        $instructionSheet->getColumnDimension('A')->setWidth(60);

        // Set active sheet back to data sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Save file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($filepath);
    }
}
