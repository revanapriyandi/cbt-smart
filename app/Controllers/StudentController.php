<?php

namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\ExamQuestionModel;
use App\Models\StudentAnswerModel;
use App\Models\ExamResultModel;
use App\Models\UserActivityLogModel;

class StudentController extends BaseController
{
    protected $examModel;
    protected $examQuestionModel;
    protected $studentAnswerModel;
    protected $examResultModel;
    protected $userActivityLogModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examQuestionModel = new ExamQuestionModel();
        $this->studentAnswerModel = new StudentAnswerModel();
        $this->examResultModel = new ExamResultModel();
        $this->userActivityLogModel = new UserActivityLogModel();
    }

    public function dashboard()
    {
        $studentId = session()->get('user_id');

        $data = [
            'activeExams' => $this->examModel->getActiveExams(),
            'upcomingExams' => $this->examModel->getUpcomingExams(),
            'completedExams' => $this->examResultModel->getResultsByStudent($studentId),
            'totalCompleted' => $this->examResultModel->where('student_id', $studentId)
                ->where('status', EXAM_STATUS_GRADED)
                ->countAllResults()
        ];

        return view('student/dashboard', $data);
    }

    public function takeExam($examId)
    {
        $studentId = session()->get('user_id');

        // Get exam details
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        // Check if exam is active
        $currentTime = date('Y-m-d H:i:s');
        if ($exam['start_time'] > $currentTime) {
            session()->setFlashdata('error', 'Ujian belum dimulai!');
            return redirect()->to('/student/dashboard');
        }

        if ($exam['end_time'] < $currentTime) {
            session()->setFlashdata('error', 'Ujian sudah berakhir!');
            return redirect()->to('/student/dashboard');
        }

        if (!$exam['is_active']) {
            session()->setFlashdata('error', 'Ujian tidak aktif!');
            return redirect()->to('/student/dashboard');
        }        // Get or create exam result
        $examResult = $this->examResultModel->getOrCreateResult($examId, $studentId);

        // Log exam start activity (only if just created)
        if ($examResult['started_at'] === date('Y-m-d H:i:s', strtotime('-5 seconds'))) {
            $this->userActivityLogModel->logActivity(
                $studentId,
                'exam_start',
                "Started exam: {$exam['title']}",
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );
        }

        // Check if already submitted
        if ($examResult['status'] === EXAM_STATUS_SUBMITTED || $examResult['status'] === EXAM_STATUS_GRADED) {
            session()->setFlashdata('info', 'Anda sudah menyelesaikan ujian ini.');
            return redirect()->to('/student/result-detail/' . $examId);
        }

        // Get questions and existing answers
        $questions = $this->examQuestionModel->getQuestionsByExam($examId);
        $answers = $this->studentAnswerModel->getAnswersByExamAndStudent($examId, $studentId);

        // Create answers array indexed by question number
        $answersMap = [];
        foreach ($answers as $answer) {
            $answersMap[$answer['question_number']] = $answer['answer_text'];
        }

        $data = [
            'exam' => $exam,
            'questions' => $questions,
            'answers' => $answersMap,
            'examResult' => $examResult,
            'timeRemaining' => $this->calculateTimeRemaining($exam, $examResult)
        ];

        return view('student/take_exam', $data);
    }

    public function saveAnswer()
    {
        $studentId = session()->get('user_id');

        $data = [
            'exam_id' => $this->request->getPost('exam_id'),
            'student_id' => $studentId,
            'question_number' => $this->request->getPost('question_number'),
            'answer_text' => $this->request->getPost('answer_text')
        ];

        if ($this->studentAnswerModel->saveOrUpdateAnswer($data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Jawaban berhasil disimpan']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan jawaban']);
        }
    }

    public function submitExam($examId)
    {
        $studentId = session()->get('user_id');

        // Check if exam exists and is accessible
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ujian tidak ditemukan']);
        }

        // Get exam result
        $examResult = $this->examResultModel->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$examResult) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data ujian tidak ditemukan']);
        }

        if ($examResult['status'] === EXAM_STATUS_SUBMITTED || $examResult['status'] === EXAM_STATUS_GRADED) {
            return $this->response->setJSON(['success' => false, 'message' => 'Ujian sudah diselesaikan']);
        }        // Submit exam
        if ($this->examResultModel->submitExam($examId, $studentId)) {
            // Log exam submission activity
            $exam = $this->examModel->find($examId);
            $this->userActivityLogModel->logActivity(
                $studentId,
                'exam_submit',
                "Submitted exam: {$exam['title']}",
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            session()->setFlashdata('success', 'Ujian berhasil diselesaikan!');
            return $this->response->setJSON(['success' => true, 'message' => 'Ujian berhasil diselesaikan', 'redirect' => '/student/dashboard']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyelesaikan ujian']);
        }
    }

    public function results()
    {
        $studentId = session()->get('user_id');
        $data = ['results' => $this->examResultModel->getResultsByStudent($studentId)];
        return view('student/results', $data);
    }

    public function resultDetail($examId)
    {
        $studentId = session()->get('user_id');

        $examResult = $this->examResultModel->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$examResult) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Hasil ujian tidak ditemukan');
        }

        $exam = $this->examModel->find($examId);
        $questions = $this->examQuestionModel->getQuestionsByExam($examId);
        $answers = $this->studentAnswerModel->getAnswersByExamAndStudent($examId, $studentId);

        $data = [
            'exam' => $exam,
            'examResult' => $examResult,
            'questions' => $questions,
            'answers' => $answers
        ];

        return view('student/result_detail', $data);
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
}
