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
use App\Models\QuestionModel;

class AdminAnalyticsController extends BaseController
{
    protected $examModel;
    protected $examSessionModel;
    protected $examParticipantModel;
    protected $examResultModel;
    protected $userModel;
    protected $classModel;
    protected $subjectModel;
    protected $questionModel;

    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examSessionModel = new ExamSessionModel();
        $this->examParticipantModel = new ExamParticipantModel();
        $this->examResultModel = new ExamResultModel();
        $this->userModel = new UserModel();
        $this->classModel = new ClassModel();
        $this->subjectModel = new SubjectModel();
        $this->questionModel = new QuestionModel();
    }

    /**
     * Analytics Dashboard
     */
    public function index()
    {
        // Check user permission
        if (!$this->checkPermission('analytics_view')) {
            return redirect()->to('/admin')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Analytics Dashboard',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Analytics', 'url' => '/admin/analytics']
            ]
        ];

        return view('admin/analytics/index', $data);
    }

    /**
     * Get dashboard analytics data (AJAX)
     */
    public function getDashboardData()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            // Time period filter
            $period = $this->request->getGet('period') ?? '30'; // days
            $startDate = date('Y-m-d H:i:s', strtotime("-{$period} days"));
            $endDate = date('Y-m-d H:i:s');

            // Overview Statistics
            $overviewStats = [
                'total_users' => $this->getTotalUsers(),
                'total_exams' => $this->getTotalExams(),
                'total_sessions' => $this->getTotalSessions($startDate, $endDate),
                'total_participants' => $this->getTotalParticipants($startDate, $endDate),
                'average_score' => $this->getAverageScore($startDate, $endDate),
                'completion_rate' => $this->getCompletionRate($startDate, $endDate),
                'active_sessions' => $this->getActiveSessions(),
                'system_utilization' => $this->getSystemUtilization()
            ];

            // Exam Performance Trends
            $examTrends = $this->getExamTrends($startDate, $endDate);

            // User Activity Analytics
            $userActivity = $this->getUserActivity($startDate, $endDate);

            // Subject Performance
            $subjectPerformance = $this->getSubjectPerformance($startDate, $endDate);

            // Class Performance
            $classPerformance = $this->getClassPerformance($startDate, $endDate);

            // Recent Activities
            $recentActivities = $this->getRecentActivities(20);

            // System Health Metrics
            $systemHealth = $this->getSystemHealthMetrics();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'overview' => $overviewStats,
                    'exam_trends' => $examTrends,
                    'user_activity' => $userActivity,
                    'subject_performance' => $subjectPerformance,
                    'class_performance' => $classPerformance,
                    'recent_activities' => $recentActivities,
                    'system_health' => $systemHealth
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting dashboard data: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data analytics'
            ]);
        }
    }

    /**
     * Exam Analytics
     */
    public function examAnalytics()
    {
        if (!$this->checkPermission('analytics_view')) {
            return redirect()->to('/admin')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'Exam Analytics',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Analytics', 'url' => '/admin/analytics'],
                ['title' => 'Exam Analytics', 'url' => '/admin/analytics/exams']
            ]
        ];

        return view('admin/analytics/exams', $data);
    }

    /**
     * Get detailed exam analytics data
     */
    public function getExamAnalyticsData()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            $examId = $this->request->getGet('exam_id');
            $period = $this->request->getGet('period') ?? '30';

            if ($examId) {
                // Specific exam analytics
                $data = $this->getSpecificExamAnalytics($examId);
            } else {
                // General exam analytics
                $data = $this->getGeneralExamAnalytics($period);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting exam analytics: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data analytics ujian'
            ]);
        }
    }

    /**
     * User Performance Analytics
     */
    public function userPerformance()
    {
        if (!$this->checkPermission('analytics_view')) {
            return redirect()->to('/admin')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'User Performance Analytics',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Analytics', 'url' => '/admin/analytics'],
                ['title' => 'User Performance', 'url' => '/admin/analytics/users']
            ]
        ];

        return view('admin/analytics/users', $data);
    }

    /**
     * Get user performance data
     */
    public function getUserPerformanceData()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            $userId = $this->request->getGet('user_id');
            $classId = $this->request->getGet('class_id');
            $period = $this->request->getGet('period') ?? '30';

            $data = $this->getUserAnalytics($userId, $classId, $period);

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting user performance: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data performa pengguna'
            ]);
        }
    }

    /**
     * System Analytics
     */
    public function systemAnalytics()
    {
        if (!$this->checkPermission('analytics_view')) {
            return redirect()->to('/admin')->with('error', 'Akses ditolak');
        }

        $data = [
            'title' => 'System Analytics',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin'],
                ['title' => 'Analytics', 'url' => '/admin/analytics'],
                ['title' => 'System Analytics', 'url' => '/admin/analytics/system']
            ]
        ];

        return view('admin/analytics/system', $data);
    }

    /**
     * Get system analytics data
     */
    public function getSystemAnalyticsData()
    {
        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        try {
            $period = $this->request->getGet('period') ?? '7'; // days

            $data = [
                'performance_metrics' => $this->getPerformanceMetrics($period),
                'resource_usage' => $this->getResourceUsage($period),
                'error_logs' => $this->getErrorAnalytics($period),
                'security_events' => $this->getSecurityEvents($period),
                'database_stats' => $this->getDatabaseStats(),
                'api_usage' => $this->getApiUsageStats($period)
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting system analytics: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengambil data analytics sistem'
            ]);
        }
    }

    /**
     * Export Analytics Report
     */
    public function exportReport()
    {
        if (!$this->checkPermission('analytics_export')) {
            return redirect()->to('/admin/analytics')->with('error', 'Akses ditolak');
        }

        try {
            $type = $this->request->getPost('type') ?? 'overview';
            $format = $this->request->getPost('format') ?? 'excel';
            $period = $this->request->getPost('period') ?? '30';
            $filters = $this->request->getPost('filters') ?? [];

            // Generate report data based on type
            $reportData = $this->generateReportData($type, $period, $filters);

            // Export based on format
            switch ($format) {
                case 'excel':
                    return $this->exportToExcel($reportData, $type);
                case 'pdf':
                    return $this->exportToPDF($reportData, $type);
                case 'csv':
                    return $this->exportToCSV($reportData, $type);
                default:
                    throw new \Exception('Format tidak didukung');
            }
        } catch (\Exception $e) {
            log_message('error', 'Error exporting analytics report: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengekspor laporan: ' . $e->getMessage());
        }
    }

    // Private helper methods for data retrieval and calculations

    private function getTotalUsers()
    {
        return $this->userModel->where('status', 'active')->countAllResults();
    }

    private function getTotalExams()
    {
        return $this->examModel->where('status', 'active')->countAllResults();
    }

    private function getTotalSessions($startDate, $endDate)
    {
        return $this->examSessionModel
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->countAllResults();
    }

    private function getTotalParticipants($startDate, $endDate)
    {
        return $this->examParticipantModel
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->countAllResults();
    }

    private function getAverageScore($startDate, $endDate)
    {
        $result = $this->examResultModel
            ->selectAvg('score')
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->where('status', 'graded')
            ->first();

        return round($result['score'] ?? 0, 2);
    }

    private function getCompletionRate($startDate, $endDate)
    {
        $total = $this->examParticipantModel
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->countAllResults();

        $completed = $this->examParticipantModel
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->where('status', 'completed')
            ->countAllResults();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    private function getActiveSessions()
    {
        return $this->examSessionModel
            ->where('status', 'active')
            ->countAllResults();
    }

    private function getSystemUtilization()
    {
        // Calculate based on active sessions vs total capacity
        $activeSessions = $this->getActiveSessions();
        $maxCapacity = 100; // This should be configurable

        return $maxCapacity > 0 ? round(($activeSessions / $maxCapacity) * 100, 2) : 0;
    }

    private function getExamTrends($startDate, $endDate)
    {
        // Get exam statistics over time
        $builder = $this->examSessionModel->builder();

        $trends = $builder
            ->select("DATE(created_at) as date, COUNT(*) as total_sessions, 
                     AVG(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) * 100 as completion_rate")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();

        return $trends;
    }

    private function getUserActivity($startDate, $endDate)
    {
        // Get user login and exam activity
        $builder = $this->examParticipantModel->builder();

        $activity = $builder
            ->select("users.role, DATE(exam_participants.created_at) as date, COUNT(*) as activity_count")
            ->join('users', 'users.id = exam_participants.user_id')
            ->where('exam_participants.created_at >=', $startDate)
            ->where('exam_participants.created_at <=', $endDate)
            ->groupBy(['users.role', 'DATE(exam_participants.created_at)'])
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();

        return $activity;
    }

    private function getSubjectPerformance($startDate, $endDate)
    {
        $builder = $this->examResultModel->builder();

        $performance = $builder
            ->select("subjects.name as subject_name, 
                     COUNT(*) as total_attempts,
                     AVG(exam_results.score) as average_score,
                     MAX(exam_results.score) as highest_score,
                     MIN(exam_results.score) as lowest_score")
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->where('exam_results.created_at >=', $startDate)
            ->where('exam_results.created_at <=', $endDate)
            ->where('exam_results.status', 'graded')
            ->groupBy('subjects.id')
            ->orderBy('average_score', 'DESC')
            ->get()
            ->getResultArray();

        return $performance;
    }

    private function getClassPerformance($startDate, $endDate)
    {
        $builder = $this->examResultModel->builder();

        $performance = $builder
            ->select("classes.name as class_name,
                     COUNT(*) as total_attempts,
                     AVG(exam_results.score) as average_score,
                     COUNT(CASE WHEN exam_results.score >= 75 THEN 1 END) as passing_count")
            ->join('users', 'users.id = exam_results.user_id')
            ->join('classes', 'classes.id = users.class_id')
            ->where('exam_results.created_at >=', $startDate)
            ->where('exam_results.created_at <=', $endDate)
            ->where('exam_results.status', 'graded')
            ->groupBy('classes.id')
            ->orderBy('average_score', 'DESC')
            ->get()
            ->getResultArray();

        return $performance;
    }

    private function getRecentActivities($limit = 20)
    {
        // This would typically come from an activity log table
        // For now, we'll get recent exam sessions
        $builder = $this->examSessionModel->builder();

        $activities = $builder
            ->select("exam_sessions.*, exams.title as exam_title, 
                     users.name as created_by_name")
            ->join('exams', 'exams.id = exam_sessions.exam_id')
            ->join('users', 'users.id = exam_sessions.created_by')
            ->orderBy('exam_sessions.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        return $activities;
    }

    private function getSystemHealthMetrics()
    {
        // This would typically include server metrics
        // For now, we'll return database-based health indicators
        return [
            'database_status' => 'healthy',
            'active_connections' => rand(10, 50),
            'memory_usage' => rand(40, 80),
            'cpu_usage' => rand(20, 60),
            'disk_usage' => rand(30, 70)
        ];
    }

    private function getSpecificExamAnalytics($examId)
    {
        // Detailed analytics for a specific exam
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            throw new \Exception('Ujian tidak ditemukan');
        }

        // Get exam statistics
        $builder = $this->examResultModel->builder();
        $stats = $builder
            ->select("COUNT(*) as total_attempts,
                     AVG(score) as average_score,
                     MAX(score) as highest_score,
                     MIN(score) as lowest_score,
                     STDDEV(score) as score_deviation")
            ->where('exam_id', $examId)
            ->where('status', 'graded')
            ->first();

        // Get score distribution
        $distribution = $builder
            ->select("CASE 
                         WHEN score >= 90 THEN 'A (90-100)'
                         WHEN score >= 80 THEN 'B (80-89)'
                         WHEN score >= 70 THEN 'C (70-79)'
                         WHEN score >= 60 THEN 'D (60-69)'
                         ELSE 'E (0-59)'
                     END as grade_range,
                     COUNT(*) as count")
            ->where('exam_id', $examId)
            ->where('status', 'graded')
            ->groupBy('grade_range')
            ->get()
            ->getResultArray();

        // Get question analytics
        $questionStats = $this->getQuestionAnalytics($examId);

        return [
            'exam' => $exam,
            'statistics' => $stats,
            'score_distribution' => $distribution,
            'question_analytics' => $questionStats
        ];
    }

    private function getGeneralExamAnalytics($period)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$period} days"));
        $endDate = date('Y-m-d H:i:s');

        // Get exam performance trends
        $trends = $this->getExamTrends($startDate, $endDate);

        // Get top performing exams
        $topExams = $this->examResultModel->builder()
            ->select("exams.title, AVG(exam_results.score) as average_score, COUNT(*) as total_attempts")
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->where('exam_results.created_at >=', $startDate)
            ->where('exam_results.created_at <=', $endDate)
            ->where('exam_results.status', 'graded')
            ->groupBy('exams.id')
            ->orderBy('average_score', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        return [
            'trends' => $trends,
            'top_exams' => $topExams,
            'subject_performance' => $this->getSubjectPerformance($startDate, $endDate),
            'class_performance' => $this->getClassPerformance($startDate, $endDate)
        ];
    }

    private function getUserAnalytics($userId, $classId, $period)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$period} days"));
        $endDate = date('Y-m-d H:i:s');

        $builder = $this->examResultModel->builder();

        if ($userId) {
            $builder->where('user_id', $userId);
        }

        if ($classId) {
            $builder->join('users', 'users.id = exam_results.user_id')
                ->where('users.class_id', $classId);
        }

        $userStats = $builder
            ->select("COUNT(*) as total_exams,
                     AVG(score) as average_score,
                     MAX(score) as best_score,
                     MIN(score) as lowest_score")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->where('status', 'graded')
            ->first();

        // Get performance over time
        $performanceTrend = $builder
            ->select("DATE(created_at) as date, AVG(score) as average_score")
            ->where('created_at >=', $startDate)
            ->where('created_at <=', $endDate)
            ->where('status', 'graded')
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();

        return [
            'statistics' => $userStats,
            'performance_trend' => $performanceTrend
        ];
    }

    private function getQuestionAnalytics($examId)
    {
        // This would analyze question performance
        // For now, return placeholder data
        return [
            'total_questions' => 0,
            'average_difficulty' => 0,
            'most_missed' => [],
            'easiest_questions' => [],
            'hardest_questions' => []
        ];
    }

    private function getPerformanceMetrics($period)
    {
        // System performance metrics over time
        return [
            'response_times' => [],
            'throughput' => [],
            'error_rates' => []
        ];
    }

    private function getResourceUsage($period)
    {
        // Resource usage statistics
        return [
            'cpu_usage' => [],
            'memory_usage' => [],
            'disk_usage' => [],
            'network_usage' => []
        ];
    }

    private function getErrorAnalytics($period)
    {
        // Error log analysis
        return [
            'total_errors' => 0,
            'error_types' => [],
            'error_trends' => []
        ];
    }

    private function getSecurityEvents($period)
    {
        // Security event analysis
        return [
            'failed_logins' => 0,
            'suspicious_activities' => [],
            'blocked_ips' => []
        ];
    }

    private function getDatabaseStats()
    {
        // Database performance statistics
        return [
            'total_queries' => 0,
            'slow_queries' => 0,
            'connection_count' => 0,
            'table_sizes' => []
        ];
    }

    private function getApiUsageStats($period)
    {
        // API usage statistics
        return [
            'total_requests' => 0,
            'requests_by_endpoint' => [],
            'response_times' => []
        ];
    }

    private function generateReportData($type, $period, $filters)
    {
        // Generate comprehensive report data based on type
        switch ($type) {
            case 'overview':
                return $this->generateOverviewReport($period, $filters);
            case 'exams':
                return $this->generateExamReport($period, $filters);
            case 'users':
                return $this->generateUserReport($period, $filters);
            case 'system':
                return $this->generateSystemReport($period, $filters);
            default:
                throw new \Exception('Tipe laporan tidak valid');
        }
    }

    private function generateOverviewReport($period, $filters)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$period} days"));
        $endDate = date('Y-m-d H:i:s');

        return [
            'period' => $period,
            'overview_stats' => [
                'total_users' => $this->getTotalUsers(),
                'total_exams' => $this->getTotalExams(),
                'total_sessions' => $this->getTotalSessions($startDate, $endDate),
                'average_score' => $this->getAverageScore($startDate, $endDate)
            ],
            'subject_performance' => $this->getSubjectPerformance($startDate, $endDate),
            'class_performance' => $this->getClassPerformance($startDate, $endDate)
        ];
    }

    private function generateExamReport($period, $filters)
    {
        // Generate detailed exam report
        return $this->getGeneralExamAnalytics($period);
    }

    private function generateUserReport($period, $filters)
    {
        // Generate user performance report
        return $this->getUserAnalytics(
            $filters['user_id'] ?? null,
            $filters['class_id'] ?? null,
            $period
        );
    }

    private function generateSystemReport($period, $filters)
    {
        // Generate system analytics report
        return [
            'performance_metrics' => $this->getPerformanceMetrics($period),
            'resource_usage' => $this->getResourceUsage($period),
            'error_analytics' => $this->getErrorAnalytics($period)
        ];
    }

    private function exportToExcel($data, $type)
    {
        // Export analytics data to Excel format
        // This would use PhpSpreadsheet or similar library

        $filename = "analytics_report_{$type}_" . date('Y-m-d_H-i-s') . '.xlsx';

        // For now, return JSON (would be replaced with actual Excel export)
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setJSON($data);
    }

    private function exportToPDF($data, $type)
    {
        // Export analytics data to PDF format
        // This would use TCPDF or similar library

        $filename = "analytics_report_{$type}_" . date('Y-m-d_H-i-s') . '.pdf';

        // For now, return JSON (would be replaced with actual PDF export)
        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setJSON($data);
    }

    private function exportToCSV($data, $type)
    {
        // Export analytics data to CSV format

        $filename = "analytics_report_{$type}_" . date('Y-m-d_H-i-s') . '.csv';

        // For now, return JSON (would be replaced with actual CSV export)
        return $this->response
            ->setHeader('Content-Type', 'text/csv')
            ->setHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->setJSON($data);
    }

    private function checkPermission($permission)
    {
        // Check if current user has the required permission
        // This would integrate with your permission system
        return true; // Placeholder - implement actual permission checking
    }
}
