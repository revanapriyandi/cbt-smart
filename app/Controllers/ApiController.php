<?php

namespace App\Controllers;

use App\Libraries\OpenAIService;
use App\Models\ExamModel;
use App\Models\ExamResultModel;

class ApiController extends BaseController
{
    protected $openAIService;
    protected $examModel;
    protected $examResultModel;

    public function __construct()
    {
        $this->openAIService = new OpenAIService();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
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
}
