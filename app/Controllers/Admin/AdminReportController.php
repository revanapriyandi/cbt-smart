<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamModel;
use App\Models\ExamSessionModel;
use App\Models\ExamParticipantModel;
use App\Models\ExamResultModel;
use App\Models\UserModel;
use App\Models\ClassModel;
use App\Models\SubjectModel;
use App\Models\ReportModel;

class AdminReportController extends BaseController
{
    protected $examModel;
    protected $examSessionModel;
    protected $examParticipantModel;
    protected $examResultModel;
    protected $userModel;
    protected $classModel;
    protected $subjectModel;
    protected $reportModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examSessionModel = new ExamSessionModel();
        $this->examParticipantModel = new ExamParticipantModel();
        $this->examResultModel = new ExamResultModel();
        $this->userModel = new UserModel();
        $this->classModel = new ClassModel();
        $this->subjectModel = new SubjectModel();
        $this->reportModel = new ReportModel();
    }

    /**
     * Reports Dashboard
     */
    public function index()
    {
        // Check user permission
        if (!$this->checkPermission('reports_view')) {
            return redirect()->to('/admin')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Reports Management',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Reports', 'url' => '/admin/reports']
            ],
            'report_types' => $this->getReportTypes(),
            'recent_reports' => $this->getRecentReports(10)
        ];

        return view('admin/reports/index', $data);
    }

    /**
     * Get available report types
     */
    private function getReportTypes()
    {
        return [
            'exam_results' => [
                'title' => 'Exam Results Report',
                'description' => 'Comprehensive exam results and performance analysis',
                'icon' => 'fas fa-chart-bar',
                'color' => 'blue'
            ],
            'student_performance' => [
                'title' => 'Student Performance Report',
                'description' => 'Individual and class-wide student performance analysis',
                'icon' => 'fas fa-user-graduate',
                'color' => 'green'
            ],
            'exam_analytics' => [
                'title' => 'Exam Analytics Report',
                'description' => 'Detailed analytics on exam difficulty and question performance',
                'icon' => 'fas fa-analytics',
                'color' => 'purple'
            ],
            'attendance' => [
                'title' => 'Attendance Report',
                'description' => 'Student attendance and participation tracking',
                'icon' => 'fas fa-calendar-check',
                'color' => 'yellow'
            ],
            'system_usage' => [
                'title' => 'System Usage Report',
                'description' => 'System utilization and performance metrics',
                'icon' => 'fas fa-server',
                'color' => 'red'
            ],
            'progress_tracking' => [
                'title' => 'Progress Tracking Report',
                'description' => 'Student progress over time and learning outcomes',
                'icon' => 'fas fa-chart-line',
                'color' => 'indigo'
            ]
        ];
    }

    /**
     * Get recent reports
     */
    private function getRecentReports($limit = 10)
    {
        return $this->reportModel
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Generate Report
     */
    public function generate()
    {
        if (!$this->checkPermission('reports_generate')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Generate Report',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Reports', 'url' => '/admin/reports'],
                ['title' => 'Generate Report', 'url' => '/admin/reports/generate']
            ],
            'classes' => $this->classModel->findAll(),
            'subjects' => $this->subjectModel->findAll(),
            'exams' => $this->examModel->where('status', 'active')->findAll()
        ];

        return view('admin/reports/generate', $data);
    }

    /**
     * Process Report Generation (AJAX)
     */
    public function processGeneration()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            $reportType = $this->request->getPost('report_type');
            $filters = $this->request->getPost('filters') ?? [];
            $format = $this->request->getPost('format') ?? 'pdf';
            $options = $this->request->getPost('options') ?? [];

            // Validate required fields
            if (empty($reportType)) {
                throw new \Exception('Tipe laporan harus dipilih');
            }

            // Generate report based on type
            $reportData = $this->generateReportData($reportType, $filters, $options);

            // Save report to database
            $reportId = $this->saveReport($reportType, $filters, $options, $reportData);

            // Generate file based on format
            $filePath = $this->generateReportFile($reportData, $format, $reportType, $reportId);

            // Update report with file path
            $this->reportModel->update($reportId, [
                'file_path' => $filePath,
                'status' => 'completed'
            ]);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'report_id' => $reportId,
                'download_url' => "/admin/reports/download/{$reportId}"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error generating report: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membuat laporan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Generate report data based on type
     */
    private function generateReportData($reportType, $filters, $options)
    {
        switch ($reportType) {
            case 'exam_results':
                return $this->generateExamResultsReport($filters, $options);
            case 'student_performance':
                return $this->generateStudentPerformanceReport($filters, $options);
            case 'exam_analytics':
                return $this->generateExamAnalyticsReport($filters, $options);
            case 'attendance':
                return $this->generateAttendanceReport($filters, $options);
            case 'system_usage':
                return $this->generateSystemUsageReport($filters, $options);
            case 'progress_tracking':
                return $this->generateProgressTrackingReport($filters, $options);
            default:
                throw new \Exception('Tipe laporan tidak valid');
        }
    }

    /**
     * Generate Exam Results Report
     */
    private function generateExamResultsReport($filters, $options)
    {
        $builder = $this->examResultModel->builder();

        $builder->select("
            er.id,
            er.score,
            er.total_questions,
            er.correct_answers,
            er.wrong_answers,
            er.time_taken,
            er.status,
            er.created_at,
            u.name as student_name,
            u.email as student_email,
            c.name as class_name,
            e.title as exam_title,
            s.name as subject_name
        ");

        $builder->join('users u', 'u.id = er.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');
        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('subjects s', 's.id = e.subject_id');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('er.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('er.created_at <=', $filters['end_date']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('u.class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('s.id', $filters['subject_id']);
        }

        if (!empty($filters['exam_id'])) {
            $builder->where('e.id', $filters['exam_id']);
        }

        $builder->where('er.status', 'graded');
        $builder->orderBy('er.created_at', 'DESC');

        $results = $builder->get()->getResultArray();

        // Calculate statistics
        $stats = $this->calculateExamResultsStats($results);

        return [
            'title' => 'Exam Results Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'statistics' => $stats,
            'results' => $results
        ];
    }

    /**
     * Generate Student Performance Report
     */
    private function generateStudentPerformanceReport($filters, $options)
    {
        $builder = $this->examResultModel->builder();

        $builder->select("
            u.id as student_id,
            u.name as student_name,
            u.email as student_email,
            c.name as class_name,
            COUNT(er.id) as total_exams,
            AVG(er.score) as average_score,
            MAX(er.score) as highest_score,
            MIN(er.score) as lowest_score,
            SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) as passed_exams,
            SUM(CASE WHEN er.score < 75 THEN 1 ELSE 0 END) as failed_exams
        ");

        $builder->join('users u', 'u.id = er.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('er.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('er.created_at <=', $filters['end_date']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('u.class_id', $filters['class_id']);
        }

        $builder->where('er.status', 'graded');
        $builder->groupBy(['u.id', 'u.name', 'u.email', 'c.name']);
        $builder->orderBy('average_score', 'DESC');

        $results = $builder->get()->getResultArray();

        // Get detailed performance for each student
        if (!empty($options['include_detailed'])) {
            foreach ($results as &$result) {
                $result['detailed_results'] = $this->getStudentDetailedResults($result['student_id'], $filters);
            }
        }

        return [
            'title' => 'Student Performance Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'results' => $results
        ];
    }

    /**
     * Generate Exam Analytics Report
     */
    private function generateExamAnalyticsReport($filters, $options)
    {
        $builder = $this->examResultModel->builder();

        $builder->select("
            e.id as exam_id,
            e.title as exam_title,
            e.total_questions,
            e.duration_minutes,
            s.name as subject_name,
            COUNT(er.id) as total_attempts,
            AVG(er.score) as average_score,
            MAX(er.score) as highest_score,
            MIN(er.score) as lowest_score,
            STDDEV(er.score) as score_deviation,
            AVG(er.time_taken) as average_time_taken,
            SUM(CASE WHEN er.score >= 90 THEN 1 ELSE 0 END) as grade_a,
            SUM(CASE WHEN er.score >= 80 AND er.score < 90 THEN 1 ELSE 0 END) as grade_b,
            SUM(CASE WHEN er.score >= 70 AND er.score < 80 THEN 1 ELSE 0 END) as grade_c,
            SUM(CASE WHEN er.score >= 60 AND er.score < 70 THEN 1 ELSE 0 END) as grade_d,
            SUM(CASE WHEN er.score < 60 THEN 1 ELSE 0 END) as grade_e
        ");

        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('subjects s', 's.id = e.subject_id');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('er.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('er.created_at <=', $filters['end_date']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('s.id', $filters['subject_id']);
        }

        if (!empty($filters['exam_id'])) {
            $builder->where('e.id', $filters['exam_id']);
        }

        $builder->where('er.status', 'graded');
        $builder->groupBy(['e.id', 'e.title', 'e.total_questions', 'e.duration_minutes', 's.name']);
        $builder->orderBy('average_score', 'ASC');

        $results = $builder->get()->getResultArray();

        // Add difficulty classification
        foreach ($results as &$result) {
            $result['difficulty_level'] = $this->classifyExamDifficulty($result['average_score']);
        }

        return [
            'title' => 'Exam Analytics Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'results' => $results
        ];
    }

    /**
     * Generate Attendance Report
     */
    private function generateAttendanceReport($filters, $options)
    {
        $builder = $this->examParticipantModel->builder();

        $builder->select("
            es.id as session_id,
            es.session_name,
            es.scheduled_date,
            e.title as exam_title,
            s.name as subject_name,
            c.name as class_name,
            COUNT(ep.id) as total_registered,
            SUM(CASE WHEN ep.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN ep.status = 'absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN ep.status = 'terminated' THEN 1 ELSE 0 END) as terminated
        ");

        $builder->join('exam_sessions es', 'es.id = ep.session_id');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->join('subjects s', 's.id = e.subject_id');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('es.scheduled_date >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('es.scheduled_date <=', $filters['end_date']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('u.class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('s.id', $filters['subject_id']);
        }

        $builder->groupBy(['es.id', 'es.session_name', 'es.scheduled_date', 'e.title', 's.name', 'c.name']);
        $builder->orderBy('es.scheduled_date', 'DESC');

        $results = $builder->get()->getResultArray();

        // Calculate attendance percentages
        foreach ($results as &$result) {
            $result['attendance_rate'] = $result['total_registered'] > 0 ?
                round(($result['completed'] / $result['total_registered']) * 100, 2) : 0;
        }

        return [
            'title' => 'Attendance Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'results' => $results
        ];
    }

    /**
     * Generate System Usage Report
     */
    private function generateSystemUsageReport($filters, $options)
    {
        // System usage metrics
        $totalUsers = $this->userModel->countAllResults();
        $activeUsers = $this->userModel->where('status', 'active')->countAllResults();
        $totalExams = $this->examModel->countAllResults();
        $totalSessions = $this->examSessionModel->countAllResults();

        // Usage over time
        $builder = $this->examSessionModel->builder();
        $builder->select("
            DATE(created_at) as date,
            COUNT(*) as sessions_count,
            COUNT(DISTINCT exam_id) as unique_exams,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_sessions
        ");

        if (!empty($filters['start_date'])) {
            $builder->where('created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('created_at <=', $filters['end_date']);
        }

        $builder->groupBy('DATE(created_at)');
        $builder->orderBy('date', 'ASC');

        $usageOverTime = $builder->get()->getResultArray();

        return [
            'title' => 'System Usage Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'summary' => [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'total_exams' => $totalExams,
                'total_sessions' => $totalSessions
            ],
            'usage_over_time' => $usageOverTime
        ];
    }

    /**
     * Generate Progress Tracking Report
     */
    private function generateProgressTrackingReport($filters, $options)
    {
        $builder = $this->examResultModel->builder();

        $builder->select("
            u.id as student_id,
            u.name as student_name,
            c.name as class_name,
            s.name as subject_name,
            DATE(er.created_at) as exam_date,
            er.score,
            er.exam_id,
            e.title as exam_title
        ");

        $builder->join('users u', 'u.id = er.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');
        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('subjects s', 's.id = e.subject_id');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('er.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('er.created_at <=', $filters['end_date']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('u.class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('s.id', $filters['subject_id']);
        }

        $builder->where('er.status', 'graded');
        $builder->orderBy('u.name', 'ASC');
        $builder->orderBy('er.created_at', 'ASC');

        $results = $builder->get()->getResultArray();

        // Group by student and calculate progress
        $progressData = [];
        foreach ($results as $result) {
            $studentId = $result['student_id'];
            if (!isset($progressData[$studentId])) {
                $progressData[$studentId] = [
                    'student_name' => $result['student_name'],
                    'class_name' => $result['class_name'],
                    'exams' => [],
                    'subjects' => []
                ];
            }

            $progressData[$studentId]['exams'][] = $result;

            // Group by subject
            $subjectName = $result['subject_name'];
            if (!isset($progressData[$studentId]['subjects'][$subjectName])) {
                $progressData[$studentId]['subjects'][$subjectName] = [];
            }
            $progressData[$studentId]['subjects'][$subjectName][] = $result;
        }

        // Calculate progress trends for each student
        foreach ($progressData as &$student) {
            $student['progress_trend'] = $this->calculateProgressTrend($student['exams']);

            // Calculate subject-wise progress
            foreach ($student['subjects'] as $subject => &$subjectExams) {
                $subjectExams['trend'] = $this->calculateProgressTrend($subjectExams);
            }
        }

        return [
            'title' => 'Progress Tracking Report',
            'generated_at' => date('Y-m-d H:i:s'),
            'filters' => $filters,
            'options' => $options,
            'progress_data' => $progressData
        ];
    }

    /**
     * Save report to database
     */
    private function saveReport($reportType, $filters, $options, $reportData)
    {
        $reportData = [
            'report_type' => $reportType,
            'title' => $reportData['title'],
            'filters' => json_encode($filters),
            'options' => json_encode($options),
            'status' => 'generating',
            'generated_by' => session()->get('user_id'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->reportModel->insert($reportData);
    }

    /**
     * Generate report file
     */
    private function generateReportFile($reportData, $format, $reportType, $reportId)
    {
        $filename = "report_{$reportType}_{$reportId}_" . date('Y-m-d_H-i-s');

        switch ($format) {
            case 'pdf':
                return $this->generatePDFReport($reportData, $filename);
            case 'excel':
                return $this->generateExcelReport($reportData, $filename);
            case 'csv':
                return $this->generateCSVReport($reportData, $filename);
            default:
                throw new \Exception('Format tidak didukung');
        }
    }

    /**
     * Download Report
     */
    public function download($reportId)
    {
        $report = $this->reportModel->find($reportId);

        if (!$report) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Laporan tidak ditemukan');
        }

        if (!$this->checkPermission('reports_download')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak');
        }

        $filePath = WRITEPATH . 'reports/' . $report['file_path'];

        if (!file_exists($filePath)) {
            return redirect()->to('/admin/reports')->with('error', 'File laporan tidak ditemukan');
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Delete Report
     */
    public function delete($reportId)
    {
        if (!$this->checkPermission('reports_delete')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak');
        }

        $report = $this->reportModel->find($reportId);

        if (!$report) {
            return redirect()->to('/admin/reports')->with('error', 'Laporan tidak ditemukan');
        }

        // Delete file
        if ($report['file_path']) {
            $filePath = WRITEPATH . 'reports/' . $report['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Delete from database
        $this->reportModel->delete($reportId);

        return redirect()->to('/admin/reports')->with('success', 'Laporan berhasil dihapus');
    }

    /**
     * Get Reports List (AJAX)
     */
    public function getReportsList()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            $start = $this->request->getGet('start') ?? 0;
            $length = $this->request->getGet('length') ?? 10;
            $search = $this->request->getGet('search')['value'] ?? '';

            $builder = $this->reportModel->builder();
            $builder->select('reports.*, users.name as generated_by_name');
            $builder->join('users', 'users.id = reports.generated_by');

            if (!empty($search)) {
                $builder->groupStart();
                $builder->like('reports.title', $search);
                $builder->orLike('reports.report_type', $search);
                $builder->orLike('users.name', $search);
                $builder->groupEnd();
            }

            $totalRecords = $builder->countAllResults(false);

            $reports = $builder
                ->orderBy('reports.created_at', 'DESC')
                ->limit($length, $start)
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $reports
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting reports list: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => 0,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Gagal mengambil data laporan'
            ]);
        }
    }

    // Helper methods

    private function calculateExamResultsStats($results)
    {
        if (empty($results)) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
                'pass_rate' => 0
            ];
        }

        $totalAttempts = count($results);
        $totalScore = array_sum(array_column($results, 'score'));
        $averageScore = $totalScore / $totalAttempts;
        $highestScore = max(array_column($results, 'score'));
        $lowestScore = min(array_column($results, 'score'));
        $passedCount = count(array_filter($results, function ($result) {
            return $result['score'] >= 75;
        }));
        $passRate = ($passedCount / $totalAttempts) * 100;

        return [
            'total_attempts' => $totalAttempts,
            'average_score' => round($averageScore, 2),
            'highest_score' => $highestScore,
            'lowest_score' => $lowestScore,
            'pass_rate' => round($passRate, 2)
        ];
    }

    private function getStudentDetailedResults($studentId, $filters)
    {
        $builder = $this->examResultModel->builder();
        $builder->select('exam_results.*, exams.title as exam_title, subjects.name as subject_name');
        $builder->join('exams', 'exams.id = exam_results.exam_id');
        $builder->join('subjects', 'subjects.id = exams.subject_id');
        $builder->where('exam_results.user_id', $studentId);
        $builder->where('exam_results.status', 'graded');

        if (!empty($filters['start_date'])) {
            $builder->where('exam_results.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('exam_results.created_at <=', $filters['end_date']);
        }

        $builder->orderBy('exam_results.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    private function classifyExamDifficulty($averageScore)
    {
        if ($averageScore >= 85) return 'Easy';
        if ($averageScore >= 70) return 'Moderate';
        if ($averageScore >= 55) return 'Hard';
        return 'Very Hard';
    }

    private function calculateProgressTrend($exams)
    {
        if (count($exams) < 2) {
            return 'insufficient_data';
        }

        $scores = array_column($exams, 'score');
        $firstHalf = array_slice($scores, 0, ceil(count($scores) / 2));
        $secondHalf = array_slice($scores, ceil(count($scores) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        $difference = $secondAvg - $firstAvg;

        if ($difference > 5) return 'improving';
        if ($difference < -5) return 'declining';
        return 'stable';
    }

    private function generatePDFReport($reportData, $filename)
    {
        // Implement PDF generation using TCPDF or similar
        // This is a placeholder implementation
        $pdfContent = $this->generatePDFContent($reportData);

        $filepath = "reports/{$filename}.pdf";
        $fullPath = WRITEPATH . $filepath;

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $pdfContent);

        return $filepath;
    }

    private function generateExcelReport($reportData, $filename)
    {
        // Implement Excel generation using PhpSpreadsheet
        // This is a placeholder implementation
        $excelContent = $this->generateExcelContent($reportData);

        $filepath = "reports/{$filename}.xlsx";
        $fullPath = WRITEPATH . $filepath;

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $excelContent);

        return $filepath;
    }

    private function generateCSVReport($reportData, $filename)
    {
        $csvContent = $this->generateCSVContent($reportData);

        $filepath = "reports/{$filename}.csv";
        $fullPath = WRITEPATH . $filepath;

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $csvContent);

        return $filepath;
    }

    private function generatePDFContent($reportData)
    {
        // Placeholder for PDF content generation
        return "PDF Report Content for: " . $reportData['title'];
    }

    private function generateExcelContent($reportData)
    {
        // Placeholder for Excel content generation
        return "Excel Report Content for: " . $reportData['title'];
    }

    private function generateCSVContent($reportData)
    {
        // Basic CSV generation
        $csv = "Report Title," . $reportData['title'] . "\n";
        $csv .= "Generated At," . $reportData['generated_at'] . "\n";
        $csv .= "\n";

        if (isset($reportData['results'])) {
            // Add headers
            if (!empty($reportData['results'])) {
                $headers = array_keys($reportData['results'][0]);
                $csv .= implode(',', $headers) . "\n";

                // Add data rows
                foreach ($reportData['results'] as $row) {
                    $csv .= implode(',', array_values($row)) . "\n";
                }
            }
        }

        return $csv;
    }

    private function checkPermission($permission)
    {
        // Implement permission checking logic
        // This is a placeholder
        return true;
    }
}
