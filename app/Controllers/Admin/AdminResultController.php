<?php

namespace App\Controllers\Admin;

use App\Models\ExamResultModel;
use App\Services\ExamResultService;
use App\Models\ExamModel;
use App\Models\ExamSessionModel;
use App\Models\ClassModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminResultController extends BaseAdminController
{
    protected $examResultModel;
    protected $examResultService;
    protected $examModel;
    protected $examSessionModel;
    protected $classModel;
    protected $userModel;
    public function __construct()
    {
        parent::__construct();
        $this->examResultModel = new ExamResultModel();
        $this->examResultService = new ExamResultService();
        $this->examModel = new ExamModel();
        $this->examSessionModel = new ExamSessionModel();
        $this->classModel = new ClassModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Hasil Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Hasil Ujian', 'url' => '/admin/results']
            ]
        ];

        // Get filter parameters
        $filters = [
            'exam_id' => $this->request->getGet('exam_id'),
            'class_id' => $this->request->getGet('class_id'),
            'session_id' => $this->request->getGet('session_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to'),
            'score_min' => $this->request->getGet('score_min'),
            'score_max' => $this->request->getGet('score_max'),
            'search' => $this->request->getGet('search')
        ];

        // Get exam results with filters
        $results = $this->examResultModel->getResultsWithDetails($filters);

        // Get statistics
        $statistics = $this->examResultModel->getResultStatistics($filters);        // Get filter options
        $data['results'] = $results;
        $data['statistics'] = $statistics;
        $data['exams'] = $this->examModel->findAll();
        $data['classes'] = $this->classModel->findAll();
        $data['sessions'] = $this->examSessionModel->findAll();
        $data['filters'] = $filters; // Pass filters to view

        return view('admin/results/index', $data);
    }

    public function show($id)
    {
        $result = $this->examResultModel->getResultWithDetails($id);
        if (!$result) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Hasil ujian dengan ID {$id} tidak ditemukan");
        }

        // Get detailed answers
        $answers = $this->examResultModel->getDetailedAnswers($id);

        // Get performance analysis
        $analysis = $this->examResultModel->getPerformanceAnalysis($id);

        $data = [
            'title' => 'Detail Hasil Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Hasil Ujian', 'url' => '/admin/results'],
                ['title' => 'Detail', 'url' => "/admin/results/{$id}"]
            ],
            'result' => $result,
            'answers' => $answers,
            'analysis' => $analysis
        ];

        return view('admin/results/view', $data);
    }

    public function analytics()
    {
        $data = [
            'title' => 'Analisis Hasil',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Hasil Ujian', 'url' => '/admin/results'],
                ['title' => 'Analisis', 'url' => '/admin/results/analytics']
            ]
        ];

        // Get filter parameters
        $filters = [
            'exam_id' => $this->request->getGet('exam_id'),
            'class_id' => $this->request->getGet('class_id'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        // Get analytics data
        $analytics = $this->examResultModel->getAnalyticsData($filters);

        // Get comparison data
        $comparison = $this->examResultModel->getComparisonData($filters);
        $data['analytics'] = $analytics;
        $data['comparison'] = $comparison;
        $data['exams'] = $this->examModel->findAll();
        $data['classes'] = $this->classModel->findAll();
        $data['filters'] = $filters; // Pass filters to view

        return view('admin/results/analytics', $data);
    }

    public function grading()
    {
        $data = [
            'title' => 'Penilaian Ujian',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Hasil Ujian', 'url' => '/admin/results'],
                ['title' => 'Penilaian', 'url' => '/admin/results/grading']
            ]
        ];

        // Get pending grading results (for essay questions)
        $pendingResults = $this->examResultModel->getPendingGrading();

        $data['pendingResults'] = $pendingResults;

        return view('admin/results/grading', $data);
    }

    public function gradeEssay($resultId)
    {
        $result = $this->examResultModel->getResultWithDetails($resultId);
        if (!$result) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Hasil ujian tidak ditemukan");
        }

        if ($this->request->getMethod() === 'POST') {
            return $this->processEssayGrading($resultId);
        }

        // Get essay answers that need grading
        $essayAnswers = $this->examResultModel->getEssayAnswers($resultId);

        $data = [
            'title' => 'Penilaian Essay',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Hasil Ujian', 'url' => '/admin/results'],
                ['title' => 'Penilaian', 'url' => '/admin/results/grading'],
                ['title' => 'Penilaian Essay', 'url' => "/admin/results/{$resultId}/grade"]
            ],
            'result' => $result,
            'essayAnswers' => $essayAnswers
        ];

        return view('admin/results/grade-essay', $data);
    }

    public function processEssayGrading($resultId)
    {
        $rules = [
            'grades' => 'required|array',
            'grades.*' => 'required|decimal|greater_than_equal_to[0]',
            'feedback' => 'permit_empty|array',
            'feedback.*' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $grades = $this->request->getPost('grades');
        $feedback = $this->request->getPost('feedback') ?: [];

        // Update essay answer grades
        $totalEssayScore = 0;
        foreach ($grades as $answerId => $grade) {
            $updateData = [
                'essay_score' => $grade,
                'essay_feedback' => $feedback[$answerId] ?? '',
                'graded_by' => session()->get('user_id'),
                'graded_at' => date('Y-m-d H:i:s')
            ];

            $this->examResultModel->updateAnswerGrade($answerId, $updateData);
            $totalEssayScore += $grade;
        }

        // Recalculate total score
        $this->examResultService->recalculateScore($resultId);

        // Log activity
        $this->logActivity('essay_graded', "Essay untuk hasil ujian ID: {$resultId} telah dinilai");

        return redirect()->to('/admin/results/grading')->with('success', 'Penilaian essay berhasil disimpan');
    }

    public function bulkAction()
    {
        $action = $this->request->getPost('action');
        $resultIds = $this->request->getPost('result_ids');

        if (empty($resultIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pilih minimal satu hasil ujian']);
        }

        $result = false;
        $message = '';

        switch ($action) {
            case 'delete':
                $result = $this->examResultService->bulkDelete($resultIds);
                $message = $result ? 'Hasil ujian berhasil dihapus' : 'Gagal menghapus hasil ujian';
                break;
            case 'publish':
                $result = $this->examResultService->bulkPublish($resultIds);
                $message = $result ? 'Hasil ujian berhasil dipublikasi' : 'Gagal mempublikasi hasil ujian';
                break;
            case 'unpublish':
                $result = $this->examResultService->bulkUnpublish($resultIds);
                $message = $result ? 'Hasil ujian berhasil disembunyikan' : 'Gagal menyembunyikan hasil ujian';
                break;
            case 'recalculate':
                $result = $this->examResultService->bulkRecalculate($resultIds);
                $message = $result ? 'Skor berhasil dihitung ulang' : 'Gagal menghitung ulang skor';
                break;
            default:
                return $this->response->setJSON(['success' => false, 'message' => 'Aksi tidak valid']);
        }

        if ($result) {
            $this->logActivity('results_bulk_action', "Bulk action '{$action}' dilakukan pada " . count($resultIds) . " hasil ujian");
        }

        return $this->response->setJSON(['success' => $result, 'message' => $message]);
    }

    public function export()
    {
        $format = $this->request->getGet('format') ?: 'excel';
        $filters = [
            'exam_id' => $this->request->getGet('exam_id'),
            'class_id' => $this->request->getGet('class_id'),
            'session_id' => $this->request->getGet('session_id'),
            'status' => $this->request->getGet('status'),
            'date_from' => $this->request->getGet('date_from'),
            'date_to' => $this->request->getGet('date_to')
        ];

        $results = $this->examResultModel->getResultsWithDetails($filters);

        if ($format === 'excel') {
            return $this->exportToExcel($results);
        } elseif ($format === 'pdf') {
            return $this->exportToPDF($results);
        }

        return $this->exportToCSV($results);
    }

    private function exportToExcel($results)
    {
        $filename = 'exam_results_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Create spreadsheet content
        $data = [
            ['No', 'Nama Siswa', 'NIS', 'Kelas', 'Ujian', 'Sesi', 'Skor', 'Nilai', 'Status', 'Waktu Mulai', 'Waktu Selesai', 'Durasi (menit)']
        ];

        $no = 1;
        foreach ($results as $result) {
            $duration = $result->start_time && $result->end_time ?
                round((strtotime($result->end_time) - strtotime($result->start_time)) / 60, 2) : 0;

            $data[] = [
                $no++,
                $result->student_name,
                $result->student_id,
                $result->class_name,
                $result->exam_title,
                $result->session_name,
                $result->score,
                $result->final_grade ?: '-',
                ucfirst($result->status),
                $result->start_time,
                $result->end_time,
                $duration
            ];
        }

        // Output as tab-separated values for Excel compatibility
        foreach ($data as $row) {
            echo implode("\t", $row) . "\n";
        }
    }

    private function exportToPDF($results)
    {
        // Implementation would use a PDF library like TCPDF or mPDF
        // For now, return simple text format
        $filename = 'exam_results_' . date('Y-m-d_H-i-s') . '.pdf';

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment;filename="' . $filename . '"');

        echo "PDF export not implemented yet";
    }

    private function exportToCSV($results)
    {
        $filename = 'exam_results_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $output = fopen('php://output', 'w');

        // Header row
        fputcsv($output, ['No', 'Nama Siswa', 'NIS', 'Kelas', 'Ujian', 'Sesi', 'Skor', 'Nilai', 'Status', 'Waktu Mulai', 'Waktu Selesai', 'Durasi (menit)']);

        // Data rows
        $no = 1;
        foreach ($results as $result) {
            $duration = $result->start_time && $result->end_time ?
                round((strtotime($result->end_time) - strtotime($result->start_time)) / 60, 2) : 0;

            fputcsv($output, [
                $no++,
                $result->student_name,
                $result->student_id,
                $result->class_name,
                $result->exam_title,
                $result->session_name,
                $result->score,
                $result->final_grade ?: '-',
                ucfirst($result->status),
                $result->start_time,
                $result->end_time,
                $duration
            ]);
        }

        fclose($output);
    }

    public function generateReport()
    {
        $examId = $this->request->getPost('exam_id');
        $classId = $this->request->getPost('class_id');
        $reportType = $this->request->getPost('report_type');

        if (!$examId || !$reportType) {
            return $this->response->setJSON(['success' => false, 'message' => 'Parameter tidak lengkap']);
        }

        $filename = $this->examResultService->createReport($examId, $classId, $reportType);

        if ($filename) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'download_url' => "/admin/results/download/{$filename}"
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat laporan']);
    }

    private function createReportFile($data, $type)
    {
        $filename = 'report_' . $type . '_' . date('Y-m-d_H-i-s') . '.html';
        $filepath = WRITEPATH . 'uploads/reports/' . $filename;

        // Ensure directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        // Generate HTML report
        $html = $this->generateReportHTML($data, $type);
        file_put_contents($filepath, $html);

        return $filename;
    }

    private function generateReportHTML($data, $type)
    {
        $html = "<!DOCTYPE html>
        <html>
        <head>
            <title>Laporan {$type}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; }
                .table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                .table th { background-color: #f2f2f2; }
                .stats { display: flex; justify-content: space-around; margin-bottom: 30px; }
                .stat-box { text-align: center; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
            </style>
        </head>
        <body>";

        switch ($type) {
            case 'summary':
                $html .= $this->generateSummaryReport($data);
                break;
            case 'detailed':
                $html .= $this->generateDetailedReport($data);
                break;
            case 'analysis':
                $html .= $this->generateAnalysisReport($data);
                break;
        }

        $html .= "</body></html>";

        return $html;
    }

    private function generateSummaryReport($data)
    {
        $html = "<div class='header'>
            <h1>Laporan Ringkasan Hasil Ujian</h1>
            <p>Tanggal: " . date('d/m/Y H:i') . "</p>
        </div>";

        $html .= "<div class='stats'>
            <div class='stat-box'>
                <h3>{$data['total_participants']}</h3>
                <p>Total Peserta</p>
            </div>
            <div class='stat-box'>
                <h3>{$data['average_score']}</h3>
                <p>Rata-rata Skor</p>
            </div>
            <div class='stat-box'>
                <h3>{$data['pass_rate']}%</h3>
                <p>Tingkat Kelulusan</p>
            </div>
        </div>";

        return $html;
    }

    private function generateDetailedReport($data)
    {
        // Implementation for detailed report
        return "<h1>Detailed Report</h1>";
    }

    private function generateAnalysisReport($data)
    {
        // Implementation for analysis report
        return "<h1>Analysis Report</h1>";
    }

    public function publishResults()
    {
        $sessionId = $this->request->getPost('session_id');
        $message = $this->request->getPost('message');

        if (!$sessionId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Session ID tidak valid']);
        }

        if ($this->examResultService->publishSessionResults($sessionId, $message)) {
            // Send notification to students
            $this->sendResultNotifications($sessionId);

            // Log activity
            $this->logActivity('results_published', "Hasil ujian sesi ID: {$sessionId} dipublikasi");

            return $this->response->setJSON(['success' => true, 'message' => 'Hasil ujian berhasil dipublikasi']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Gagal mempublikasi hasil ujian']);
    }

    private function sendResultNotifications($sessionId)
    {
        // Get session and participants
        $session = $this->examSessionModel->getSessionWithDetails($sessionId);
        $results = $this->examResultModel->getSessionResults($sessionId);

        foreach ($results as $result) {
            // Implementation for sending email/SMS notifications
            // This could be integrated with email service or notification system
        }
    }

    public function getChartData()
    {
        $type = $this->request->getGet('type');
        $examId = $this->request->getGet('exam_id');
        $classId = $this->request->getGet('class_id');

        $chartData = $this->examResultModel->getChartData($type, $examId, $classId);

        return $this->response->setJSON([
            'success' => true,
            'data' => $chartData
        ]);
    }
}
