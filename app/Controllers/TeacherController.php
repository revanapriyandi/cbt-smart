<?php

namespace App\Controllers;

use App\Models\ExamModel;
use App\Models\SubjectModel;
use App\Models\ExamQuestionModel;
use App\Models\StudentAnswerModel;
use App\Models\ExamResultModel;
use App\Libraries\OpenAIService;

class TeacherController extends BaseController
{
    protected $examModel;
    protected $subjectModel;
    protected $examQuestionModel;
    protected $studentAnswerModel;
    protected $examResultModel;
    protected $openAIService;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->subjectModel = new SubjectModel();
        $this->examQuestionModel = new ExamQuestionModel();
        $this->studentAnswerModel = new StudentAnswerModel();
        $this->examResultModel = new ExamResultModel();
        $this->openAIService = new OpenAIService();
    }

    public function dashboard()
    {
        $teacherId = session()->get('user_id');

        $data = [
            'totalExams' => $this->examModel->where('teacher_id', $teacherId)->countAllResults(),
            'totalSubjects' => $this->subjectModel->where('teacher_id', $teacherId)->countAllResults(),
            'recentExams' => $this->examModel->getExamsByTeacher($teacherId),
            'activeExams' => $this->examModel->where('teacher_id', $teacherId)
                ->where('is_active', 1)
                ->where('start_time <=', date('Y-m-d H:i:s'))
                ->where('end_time >=', date('Y-m-d H:i:s'))
                ->countAllResults()
        ];

        return view('teacher/dashboard', $data);
    }

    public function exams()
    {
        $teacherId = session()->get('user_id');
        $data = ['exams' => $this->examModel->getExamsByTeacher($teacherId)];
        return view('teacher/exams', $data);
    }

    public function createExam()
    {
        $teacherId = session()->get('user_id');

        if ($this->request->getMethod() === 'POST') {
            // Parse PDF and extract content
            $pdfUrl = $this->request->getPost('pdf_url');
            $questionCount = $this->request->getPost('question_count');

            $pdfResult = $this->openAIService->extractTextFromPDF($pdfUrl);

            if (!$pdfResult['success']) {
                session()->setFlashdata('error', 'Gagal mengekstrak konten PDF: ' . ($pdfResult['error'] ?? 'Unknown error'));
                return redirect()->back()->withInput();
            }

            // Create exam
            $examData = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'teacher_id' => $teacherId,
                'pdf_url' => $pdfUrl,
                'pdf_content' => $pdfResult['text'],
                'question_count' => $questionCount,
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time')
            ];

            $examId = $this->examModel->insert($examData);

            if ($examId) {
                // Generate questions using AI (optional)
                $generateQuestions = $this->request->getPost('generate_questions');

                if ($generateQuestions) {
                    $questionsResult = $this->openAIService->generateQuestionsFromPDF($pdfResult['text'], $questionCount);

                    if ($questionsResult['success'] && !empty($questionsResult['questions'])) {
                        foreach ($questionsResult['questions'] as $question) {
                            $this->examQuestionModel->insert([
                                'exam_id' => $examId,
                                'question_number' => $question['number'],
                                'question_text' => $question['text'],
                                'max_score' => 10.00
                            ]);
                        }
                    }
                } else {
                    // Create empty questions for manual input
                    for ($i = 1; $i <= $questionCount; $i++) {
                        $this->examQuestionModel->insert([
                            'exam_id' => $examId,
                            'question_number' => $i,
                            'question_text' => "Soal nomor {$i} (belum diisi)",
                            'max_score' => 10.00
                        ]);
                    }
                }

                session()->setFlashdata('success', 'Ujian berhasil dibuat!');
                return redirect()->to('/teacher/exams');
            } else {
                session()->setFlashdata('error', 'Gagal membuat ujian! ' . implode(', ', $this->examModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = ['subjects' => $this->subjectModel->getSubjectsByTeacher($teacherId)];
        return view('teacher/create_exam', $data);
    }

    public function editExam($id)
    {
        $teacherId = session()->get('user_id');
        $exam = $this->examModel->where('id', $id)
            ->where('teacher_id', $teacherId)
            ->first();

        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        if ($this->request->getMethod() === 'POST') {
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'subject_id' => $this->request->getPost('subject_id'),
                'pdf_url' => $this->request->getPost('pdf_url'),
                'question_count' => $this->request->getPost('question_count'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'start_time' => $this->request->getPost('start_time'),
                'end_time' => $this->request->getPost('end_time'),
                'is_active' => $this->request->getPost('is_active') ? 1 : 0
            ];

            // Update PDF content if URL changed
            if ($data['pdf_url'] !== $exam['pdf_url']) {
                $pdfResult = $this->openAIService->extractTextFromPDF($data['pdf_url']);
                if ($pdfResult['success']) {
                    $data['pdf_content'] = $pdfResult['text'];
                }
            }

            if ($this->examModel->update($id, $data)) {
                session()->setFlashdata('success', 'Ujian berhasil diupdate!');
                return redirect()->to('/teacher/exams');
            } else {
                session()->setFlashdata('error', 'Gagal mengupdate ujian! ' . implode(', ', $this->examModel->errors()));
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'exam' => $exam,
            'subjects' => $this->subjectModel->getSubjectsByTeacher($teacherId),
            'questions' => $this->examQuestionModel->getQuestionsByExam($id)
        ];

        return view('teacher/edit_exam', $data);
    }

    public function deleteExam($id)
    {
        $teacherId = session()->get('user_id');
        $exam = $this->examModel->where('id', $id)
            ->where('teacher_id', $teacherId)
            ->first();

        if (!$exam) {
            session()->setFlashdata('error', 'Ujian tidak ditemukan!');
            return redirect()->to('/teacher/exams');
        }

        if ($this->examModel->delete($id)) {
            session()->setFlashdata('success', 'Ujian berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus ujian!');
        }

        return redirect()->to('/teacher/exams');
    }

    public function examResults($examId)
    {
        $teacherId = session()->get('user_id');
        $exam = $this->examModel->where('id', $examId)
            ->where('teacher_id', $teacherId)
            ->first();

        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        $data = [
            'exam' => $exam,
            'results' => $this->examResultModel->getResultsByExam($examId)
        ];

        return view('teacher/exam_results', $data);
    }

    public function gradeAnswers($examId)
    {
        $teacherId = session()->get('user_id');
        $exam = $this->examModel->where('id', $examId)
            ->where('teacher_id', $teacherId)
            ->first();

        if (!$exam) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Ujian tidak ditemukan');
        }

        // Auto-grade all answers using AI
        if ($this->request->getMethod() === 'POST') {
            $answers = $this->studentAnswerModel->getAnswersByExam($examId);
            $questions = $this->examQuestionModel->getQuestionsByExam($examId);

            $questionsMap = [];
            foreach ($questions as $question) {
                $questionsMap[$question['question_number']] = $question;
            }

            $gradedCount = 0;
            foreach ($answers as $answer) {
                if (empty($answer['ai_score']) && isset($questionsMap[$answer['question_number']])) {
                    $question = $questionsMap[$answer['question_number']];

                    $gradeResult = $this->openAIService->gradeEssayAnswer(
                        $question['question_text'],
                        $answer['answer_text'],
                        $question['max_score']
                    );

                    if ($gradeResult['success']) {
                        $this->studentAnswerModel->update($answer['id'], [
                            'ai_score' => $gradeResult['score'],
                            'ai_feedback' => $gradeResult['feedback'],
                            'final_score' => $gradeResult['score']
                        ]);
                        $gradedCount++;
                    }
                }
            }

            // Update exam results
            $this->updateExamResults($examId);

            session()->setFlashdata('success', "Berhasil menilai {$gradedCount} jawaban menggunakan AI!");
            return redirect()->back();
        }

        $data = [
            'exam' => $exam,
            'answers' => $this->studentAnswerModel->getAnswersByExam($examId),
            'questions' => $this->examQuestionModel->getQuestionsByExam($examId)
        ];

        return view('teacher/grade_answers', $data);
    }

    public function saveManualGrade()
    {
        $answerId = $this->request->getPost('answer_id');
        $manualScore = $this->request->getPost('manual_score');
        $manualFeedback = $this->request->getPost('manual_feedback');

        $answer = $this->studentAnswerModel->find($answerId);
        if (!$answer) {
            return $this->response->setJSON(['success' => false, 'message' => 'Jawaban tidak ditemukan']);
        }

        $updateData = [
            'manual_score' => $manualScore,
            'manual_feedback' => $manualFeedback,
            'final_score' => $manualScore // Manual score overrides AI score
        ];

        if ($this->studentAnswerModel->update($answerId, $updateData)) {
            // Update exam results
            $this->updateExamResults($answer['exam_id']);

            return $this->response->setJSON(['success' => true, 'message' => 'Nilai berhasil disimpan']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan nilai']);
        }
    }

    private function updateExamResults($examId)
    {
        $results = $this->examResultModel->where('exam_id', $examId)->findAll();

        foreach ($results as $result) {
            $answers = $this->studentAnswerModel->getAnswersByExamAndStudent($examId, $result['student_id']);

            $totalScore = 0;
            $maxTotalScore = 0;

            foreach ($answers as $answer) {
                $totalScore += $answer['final_score'] ?? 0;

                $question = $this->examQuestionModel->where('exam_id', $examId)
                    ->where('question_number', $answer['question_number'])
                    ->first();
                if ($question) {
                    $maxTotalScore += $question['max_score'];
                }
            }

            $this->examResultModel->updateScores($examId, $result['student_id'], $totalScore, $maxTotalScore);
        }
    }

    public function downloadResults($examId)
    {
        $teacherId = session()->get('user_id');
        $exam = $this->examModel->where('id', $examId)
            ->where('teacher_id', $teacherId)
            ->first();

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
}
