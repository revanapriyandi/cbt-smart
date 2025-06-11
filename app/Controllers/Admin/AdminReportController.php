<?php

namespace App\Controllers\Admin;

use App\Models\ExamModel;
use App\Models\ExamSessionModel;
use App\Models\ExamParticipantModel;
use App\Models\ExamResultModel;
use App\Models\UserModel;
use App\Models\ClassModel;
use App\Models\SubjectModel;
use App\Models\UserActivityLogModel;

class AdminReportController extends BaseAdminController
{
    protected $examSessionModel;
    protected $examParticipantModel;
    protected $classModel;
    protected $activityLogModel;
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->examSessionModel = new ExamSessionModel();
        $this->examParticipantModel = new ExamParticipantModel();
        $this->classModel = new ClassModel();
        $this->activityLogModel = new UserActivityLogModel();
        $this->db = \Config\Database::connect();
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
            'recent_reports' => $this->getRecentReports(10),
            'classes' => $this->classModel->findAll(),
            'subjects' => $this->subjectModel->findAll(),
            'exams' => $this->examModel->where('status', 'active')->findAll()
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
     * Get recent reports from exam results and sessions
     */
    private function getRecentReports($limit = 10)
    {
        try {
            // Get recent exam sessions as "reports"
            $sessions = $this->examSessionModel
                ->select('exam_sessions.id, exam_sessions.session_name as title, exam_sessions.created_at, exams.title as exam_title, exam_sessions.status')
                ->join('exams', 'exams.id = exam_sessions.exam_id')
                ->orderBy('exam_sessions.created_at', 'DESC')
                ->limit($limit)
                ->findAll();

            // Add type field for consistency
            foreach ($sessions as &$session) {
                $session['type'] = 'exam_session';
            }

            return $sessions;
        } catch (\Exception $e) {
            log_message('error', 'Error fetching recent reports: ' . $e->getMessage());
            return [];
        }
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
     * Create Report (alias for processGeneration for route compatibility)
     */
    public function create()
    {
        return $this->processGeneration();
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

            // Generate file based on format
            $filename = $this->generateReportFileName($reportType, $format);
            $filePath = $this->generateReportFile($reportData, $format, $reportType, $filename);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Laporan berhasil dibuat',
                'filename' => $filename,
                'download_url' => "/admin/reports/download/{$filename}"
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
        try {
            $examResultModel = new \App\Models\ExamResultModel();
            $builder = $examResultModel->builder();

            $builder->select("
                exam_results.id,
                exam_results.total_score as score,
                exam_results.percentage,
                exam_results.status,
                exam_results.created_at,
                users.name as student_name,
                users.email as student_email,
                classes.name as class_name,
                exams.title as exam_title,
                subjects.name as subject_name
            ");

            $builder->join('users', 'users.id = exam_results.student_id');
            $builder->join('classes', 'classes.id = users.class_id', 'left');
            $builder->join('exams', 'exams.id = exam_results.exam_id');
            $builder->join('subjects', 'subjects.id = exams.subject_id');

            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('exam_results.created_at >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('exam_results.created_at <=', $filters['end_date']);
            }

            if (!empty($filters['class_id'])) {
                $builder->where('users.class_id', $filters['class_id']);
            }

            if (!empty($filters['subject_id'])) {
                $builder->where('subjects.id', $filters['subject_id']);
            }

            if (!empty($filters['exam_id'])) {
                $builder->where('exams.id', $filters['exam_id']);
            }

            $builder->where('exam_results.status', 'graded');
            $builder->orderBy('exam_results.created_at', 'DESC');

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
        } catch (\Exception $e) {
            log_message('error', 'Error generating exam results report: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Generate Student Performance Report
     */
    private function generateStudentPerformanceReport($filters, $options)
    {
        try {
            $examResultModel = new \App\Models\ExamResultModel();
            $builder = $examResultModel->builder();

            $builder->select("
                users.id as student_id,
                users.name as student_name,
                users.email as student_email,
                classes.name as class_name,
                COUNT(exam_results.id) as total_exams,
                AVG(exam_results.total_score) as average_score,
                MAX(exam_results.total_score) as highest_score,
                MIN(exam_results.total_score) as lowest_score,
                SUM(CASE WHEN exam_results.percentage >= 75 THEN 1 ELSE 0 END) as passed_exams,
                SUM(CASE WHEN exam_results.percentage < 75 THEN 1 ELSE 0 END) as failed_exams
            ");

            $builder->join('users', 'users.id = exam_results.student_id');
            $builder->join('classes', 'classes.id = users.class_id', 'left');

            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('exam_results.created_at >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('exam_results.created_at <=', $filters['end_date']);
            }

            if (!empty($filters['class_id'])) {
                $builder->where('users.class_id', $filters['class_id']);
            }

            $builder->where('exam_results.status', 'graded');
            $builder->groupBy(['users.id', 'users.name', 'users.email', 'classes.name']);
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
        } catch (\Exception $e) {
            log_message('error', 'Error generating student performance report: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Generate Exam Analytics Report
     */
    private function generateExamAnalyticsReport($filters, $options)
    {
        try {
            $builder = $this->db->table('exam_results');

            $builder->select("
                exams.id as exam_id,
                exams.title as exam_title,
                exams.duration_minutes,
                subjects.name as subject_name,
                COUNT(exam_results.id) as total_attempts,
                AVG(exam_results.total_score) as average_score,
                MAX(exam_results.total_score) as highest_score,
                MIN(exam_results.total_score) as lowest_score,
                STDDEV(exam_results.total_score) as score_deviation,
                SUM(CASE WHEN exam_results.percentage >= 90 THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN exam_results.percentage >= 80 AND exam_results.percentage < 90 THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN exam_results.percentage >= 70 AND exam_results.percentage < 80 THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN exam_results.percentage >= 60 AND exam_results.percentage < 70 THEN 1 ELSE 0 END) as grade_d,
                SUM(CASE WHEN exam_results.percentage < 60 THEN 1 ELSE 0 END) as grade_e
            ");

            $builder->join('exams', 'exams.id = exam_results.exam_id');
            $builder->join('subjects', 'subjects.id = exams.subject_id');

            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('exam_results.created_at >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('exam_results.created_at <=', $filters['end_date']);
            }

            if (!empty($filters['subject_id'])) {
                $builder->where('subjects.id', $filters['subject_id']);
            }

            if (!empty($filters['exam_id'])) {
                $builder->where('exams.id', $filters['exam_id']);
            }

            $builder->where('exam_results.status', 'graded');
            $builder->groupBy(['exams.id', 'exams.title', 'exams.duration_minutes', 'subjects.name']);
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
        } catch (\Exception $e) {
            log_message('error', 'Error generating exam analytics report: ' . $e->getMessage());
            throw $e;
        }
    }
    /**
     * Generate Attendance Report
     */
    private function generateAttendanceReport($filters, $options)
    {
        try {
            $examParticipantModel = new \App\Models\ExamParticipantModel();
            $builder = $examParticipantModel->builder();

            $builder->select("
                exam_sessions.id as session_id,
                exam_sessions.session_name,
                exam_sessions.start_time as scheduled_date,
                exams.title as exam_title,
                subjects.name as subject_name,
                classes.name as class_name,
                COUNT(exam_participants.id) as total_registered,
                SUM(CASE WHEN exam_participants.status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN exam_participants.status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN exam_participants.status = 'not_started' THEN 1 ELSE 0 END) as not_started
            ");

            $builder->join('exam_sessions', 'exam_sessions.id = exam_participants.exam_session_id');
            $builder->join('exams', 'exams.id = exam_sessions.exam_id');
            $builder->join('subjects', 'subjects.id = exams.subject_id');
            $builder->join('users', 'users.id = exam_participants.user_id');
            $builder->join('classes', 'classes.id = users.class_id', 'left');

            // Apply filters
            if (!empty($filters['start_date'])) {
                $builder->where('exam_sessions.start_time >=', $filters['start_date']);
            }

            if (!empty($filters['end_date'])) {
                $builder->where('exam_sessions.start_time <=', $filters['end_date']);
            }

            if (!empty($filters['class_id'])) {
                $builder->where('users.class_id', $filters['class_id']);
            }

            if (!empty($filters['subject_id'])) {
                $builder->where('subjects.id', $filters['subject_id']);
            }

            $builder->groupBy(['exam_sessions.id', 'exam_sessions.session_name', 'exam_sessions.start_time', 'exams.title', 'subjects.name', 'classes.name']);
            $builder->orderBy('exam_sessions.start_time', 'DESC');

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
        } catch (\Exception $e) {
            log_message('error', 'Error generating attendance report: ' . $e->getMessage());
            throw $e;
        }
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
        $builder = $this->db->table('exam_sessions');
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
     */    private function generateProgressTrackingReport($filters, $options)
    {
        $builder = $this->db->table('exam_results');

        $builder->select("
            users.id as student_id,
            users.name as student_name,
            classes.name as class_name,
            subjects.name as subject_name,
            DATE(exam_results.created_at) as exam_date,
            exam_results.total_score as score,
            exam_results.exam_id,
            exams.title as exam_title
        ");

        $builder->join('users', 'users.id = exam_results.student_id');
        $builder->join('classes', 'classes.id = users.class_id', 'left');
        $builder->join('exams', 'exams.id = exam_results.exam_id');
        $builder->join('subjects', 'subjects.id = exams.subject_id');

        // Apply filters
        if (!empty($filters['start_date'])) {
            $builder->where('exam_results.created_at >=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $builder->where('exam_results.created_at <=', $filters['end_date']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('users.class_id', $filters['class_id']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('subjects.id', $filters['subject_id']);
        }

        $builder->where('exam_results.status', 'graded');
        $builder->orderBy('users.name', 'ASC');
        $builder->orderBy('exam_results.created_at', 'ASC');

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
     * Generate report filename
     */
    private function generateReportFileName($reportType, $format)
    {
        return "report_{$reportType}_" . date('Y-m-d_H-i-s') . ".{$format}";
    }

    /**
     * Generate report file
     */
    private function generateReportFile($reportData, $format, $reportType, $filename)
    {
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
    public function download($filename)
    {
        if (!$this->checkPermission('reports_download')) {
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak');
        }

        $filePath = WRITEPATH . 'reports/' . $filename;

        if (!file_exists($filePath)) {
            return redirect()->to('/admin/reports')->with('error', 'File laporan tidak ditemukan');
        }

        return $this->response->download($filePath, null);
    }
    /**
     * Delete Report
     */
    public function delete($filename)
    {
        if (!$this->checkPermission('reports_delete')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Akses ditolak'
                ]);
            }
            return redirect()->to('/admin/reports')->with('error', 'Akses ditolak');
        }

        try {
            $filePath = WRITEPATH . 'reports/' . $filename;

            if (!file_exists($filePath)) {
                throw new \Exception('File laporan tidak ditemukan');
            }

            // Delete file
            if (unlink($filePath)) {
                $message = 'Laporan berhasil dihapus';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message
                    ]);
                }
                return redirect()->to('/admin/reports')->with('success', $message);
            } else {
                throw new \Exception('Gagal menghapus file laporan');
            }
        } catch (\Exception $e) {
            $message = 'Gagal menghapus laporan: ' . $e->getMessage();
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            }
            return redirect()->to('/admin/reports')->with('error', $message);
        }
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

            // Get generated report files from filesystem
            $reportsDir = WRITEPATH . 'reports/';
            $files = [];

            if (is_dir($reportsDir)) {
                $fileList = scandir($reportsDir);
                foreach ($fileList as $file) {
                    if ($file !== '.' && $file !== '..' && !is_dir($reportsDir . $file)) {
                        $filePath = $reportsDir . $file;
                        $fileInfo = [
                            'filename' => $file,
                            'title' => $this->getReportTitleFromFilename($file),
                            'report_type' => $this->getReportTypeFromFilename($file),
                            'created_at' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'file_size' => filesize($filePath),
                            'status' => 'completed'
                        ];

                        if (
                            empty($search) ||
                            stripos($fileInfo['title'], $search) !== false ||
                            stripos($fileInfo['report_type'], $search) !== false
                        ) {
                            $files[] = $fileInfo;
                        }
                    }
                }
            }

            // Sort by creation date (newest first)
            usort($files, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            $totalRecords = count($files);
            $files = array_slice($files, $start, $length);
            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw')),
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $files
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
        $builder->where('exam_results.student_id', $studentId);
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

        $fullPath = WRITEPATH . "reports/{$filename}";

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $pdfContent);

        return $filename;
    }
    private function generateExcelReport($reportData, $filename)
    {
        // Implement Excel generation using PhpSpreadsheet
        // This is a placeholder implementation
        $excelContent = $this->generateExcelContent($reportData);

        $fullPath = WRITEPATH . "reports/{$filename}";

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $excelContent);

        return $filename;
    }

    private function generateCSVReport($reportData, $filename)
    {
        $csvContent = $this->generateCSVContent($reportData);

        $fullPath = WRITEPATH . "reports/{$filename}";

        // Ensure directory exists
        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        file_put_contents($fullPath, $csvContent);

        return $filename;
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
        // Check if user is logged in and has admin role
        $userRole = session()->get('role');
        return $userRole === 'admin';
    }

    /**
     * Get report title from filename
     */
    private function getReportTitleFromFilename($filename)
    {
        $parts = explode('_', $filename);
        if (count($parts) >= 2) {
            $type = $parts[1];
            $titles = [
                'exam' => 'Exam Results Report',
                'student' => 'Student Performance Report',
                'analytics' => 'Exam Analytics Report',
                'attendance' => 'Attendance Report',
                'system' => 'System Usage Report',
                'progress' => 'Progress Tracking Report'
            ];
            return $titles[$type] ?? 'Generated Report';
        }
        return 'Generated Report';
    }

    /**
     * Get report type from filename
     */
    private function getReportTypeFromFilename($filename)
    {
        $parts = explode('_', $filename);
        if (count($parts) >= 2) {
            return $parts[1];
        }
        return 'unknown';
    }
}
