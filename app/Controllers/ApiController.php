<?php

namespace App\Controllers;

use App\Libraries\OpenAIService;
use App\Models\ExamModel;
use App\Models\ExamResultModel;
use App\Models\ExamActivityLogModel;

class ApiController extends BaseController
{
    protected $openAIService;
    protected $examModel;
    protected $examResultModel;
    protected $activityLogModel;

    public function __construct()
    {
        $this->openAIService = new OpenAIService();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
        $this->activityLogModel = new ExamActivityLogModel();
    }

    public function parsePdf()
    {
        $pdfUrl = $this->request->getPost('pdf_url');

        if (empty($pdfUrl)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'URL PDF tidak boleh kosong'
            ]);
        }

        $result = $this->openAIService->extractTextFromPDF($pdfUrl);

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'text' => substr($result['text'], 0, 500) . '...', // Preview only
                'message' => 'PDF berhasil diparse'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memparse PDF: ' . ($result['error'] ?? 'Unknown error')
            ]);
        }
    }

    public function gradeAnswer()
    {
        $question = $this->request->getPost('question');
        $answer = $this->request->getPost('answer');
        $maxScore = $this->request->getPost('max_score') ?: 10;

        if (empty($question) || empty($answer)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Soal dan jawaban tidak boleh kosong'
            ]);
        }

        $result = $this->openAIService->gradeEssayAnswer($question, $answer, $maxScore);

        return $this->response->setJSON($result);
    }

    public function getTimeRemaining($examId)
    {
        $studentId = session()->get('user_id');

        if (!$studentId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User tidak terautentikasi'
            ]);
        }

        $exam = $this->examModel->find($examId);
        $examResult = $this->examResultModel->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$exam || !$examResult) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data ujian tidak ditemukan'
            ]);
        }

        $timeRemaining = $this->calculateTimeRemaining($exam, $examResult);

        return $this->response->setJSON([
            'success' => true,
            'timeRemaining' => $timeRemaining,
            'timeDisplay' => $this->formatTime($timeRemaining)
        ]);
    }

    private function calculateTimeRemaining($exam, $examResult)
    {
        $startTime = new \DateTime($examResult['started_at']);
        $endTime = new \DateTime($exam['end_time']);
        $currentTime = new \DateTime();

        // Calculate exam duration end time
        $examDurationEnd = clone $startTime;
        $examDurationEnd->add(new \DateInterval('PT' . $exam['duration_minutes'] . 'M'));

        // Use the earlier of exam end time or duration end time
        $actualEndTime = ($examDurationEnd < $endTime) ? $examDurationEnd : $endTime;

        if ($currentTime >= $actualEndTime) {
            return 0;
        }

        $interval = $currentTime->diff($actualEndTime);
        return ($interval->h * 60) + $interval->i;
    }

    private function formatTime($minutes)
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        if ($hours > 0) {
            return sprintf('%d jam %d menit', $hours, $mins);
        } else {
            return sprintf('%d menit', $mins);
        }
    }

    public function logActivity()
    {
        $studentId = session()->get('user_id');
        $examId = $this->request->getPost('exam_id');
        $eventType = $this->request->getPost('event_type');
        $details = $this->request->getPost('details');

        if (!$studentId || !$examId || !$eventType) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data']);
        }

        $this->activityLogModel->insert([
            'exam_id' => $examId,
            'student_id' => $studentId,
            'event_type' => $eventType,
            'details' => $details,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function parsePdfAdmin()
    {
        $inputMethod = $this->request->getPost('input_method');
        $pdfUrl = '';
        $uploadedFile = null;

        if ($inputMethod === 'upload') {
            // Handle file upload
            $uploadedFile = $this->request->getFile('pdf_file');

            if (!$uploadedFile || !$uploadedFile->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No valid PDF file uploaded'
                ]);
            }

            if ($uploadedFile->getSize() > 10 * 1024 * 1024) { // 10MB limit
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'PDF file size exceeds 10MB limit'
                ]);
            }

            if ($uploadedFile->getExtension() !== 'pdf') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Only PDF files are allowed'
                ]);
            }

            // Generate unique filename
            $fileName = 'exam_' . time() . '_' . uniqid() . '.' . $uploadedFile->getExtension();

            // Move file to uploads directory
            if ($uploadedFile->move(FCPATH . 'uploads/pdfs', $fileName)) {
                $pdfUrl = base_url('uploads/pdfs/' . $fileName);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to upload PDF file'
                ]);
            }
        } else {
            // Handle URL input
            $pdfUrl = $this->request->getPost('pdf_url');

            if (empty($pdfUrl)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'PDF URL cannot be empty'
                ]);
            }
        }

        // Parse PDF content
        $result = $this->openAIService->extractTextFromPDF($pdfUrl);

        if ($result['success']) {
            // Estimate number of questions based on content
            $estimatedQuestions = $this->estimateQuestionCount($result['text']);

            return $this->response->setJSON([
                'success' => true,
                'text' => substr($result['text'], 0, 500) . '...', // Preview only
                'pdf_url' => $pdfUrl,
                'estimated_questions' => $estimatedQuestions,
                'message' => 'PDF parsed successfully'
            ]);
        } else {
            // If file was uploaded but parsing failed, clean up
            if ($inputMethod === 'upload' && $uploadedFile && file_exists(FCPATH . 'uploads/pdfs/' . $fileName)) {
                unlink(FCPATH . 'uploads/pdfs/' . $fileName);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to parse PDF: ' . ($result['error'] ?? 'Unknown error')
            ]);
        }
    }

    private function estimateQuestionCount($text)
    {
        // Simple heuristic to estimate question count based on common patterns
        $patterns = [
            '/(?:^|\n)\s*(?:\d+[\.\)]|\(?[a-z]\)|\b(?:Question|Soal|Problem|Q\.?)\s*\d+)/im',
            '/(?:^|\n)\s*(?:What|How|Why|When|Where|Who|Explain|Describe|Analyze|Compare|Discuss)/im',
            '/\?/m' // Question marks
        ];

        $maxCount = 0;

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $text, $matches);
            $count = count($matches[0]);
            $maxCount = max($maxCount, $count);
        }

        // If we find question marks, divide by reasonable factor
        if (preg_match_all('/\?/', $text, $matches)) {
            $questionMarks = count($matches[0]);
            $estimatedFromMarks = min($questionMarks, 50); // Cap at 50
            $maxCount = max($maxCount, $estimatedFromMarks);
        }

        // Return reasonable estimate (between 1 and 50)
        return max(1, min($maxCount, 50));
    }
}
