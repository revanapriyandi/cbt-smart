<?php

namespace App\Controllers\Admin;

use App\Models\UserActivityLogModel;

class AdminActivityLogController extends BaseAdminController
{
    protected $activityLogModel;

    public function __construct()
    {
        parent::__construct();
        $this->activityLogModel = new UserActivityLogModel();
    }

    /**
     * Display activity logs page
     */
    public function index()
    {
        $data = [
            'activityStats' => $this->getActivityStats(),
            'recentActivities' => $this->getRecentActivities(20)
        ];

        return view('admin/activity-logs/index', $data);
    }

    /**
     * Get activity logs data for DataTables
     */
    public function getActivityLogsData()
    {
        try {
            // Get DataTables parameters
            $draw = intval($this->request->getGet('draw') ?? 1);
            $start = intval($this->request->getGet('start') ?? 0);
            $length = intval($this->request->getGet('length') ?? 25);

            // Get filters
            $dateFrom = $this->request->getGet('date_from');
            $dateTo = $this->request->getGet('date_to');
            $activityType = $this->request->getGet('activity_type');
            $userId = $this->request->getGet('user_id');
            $searchValue = $this->request->getGet('search')['value'] ?? '';

            // Build query
            $builder = $this->activityLogModel
                ->select('user_activity_logs.*, users.username, users.full_name, users.role')
                ->join('users', 'users.id = user_activity_logs.user_id', 'left');

            // Apply filters
            if ($dateFrom) {
                $builder->where('user_activity_logs.created_at >=', $dateFrom . ' 00:00:00');
            }

            if ($dateTo) {
                $builder->where('user_activity_logs.created_at <=', $dateTo . ' 23:59:59');
            }

            if ($activityType) {
                $builder->where('user_activity_logs.activity_type', $activityType);
            }

            if ($userId) {
                $builder->where('user_activity_logs.user_id', $userId);
            }

            // Apply search
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('users.username', $searchValue)
                    ->orLike('users.full_name', $searchValue)
                    ->orLike('user_activity_logs.activity_type', $searchValue)
                    ->orLike('user_activity_logs.activity_description', $searchValue)
                    ->orLike('user_activity_logs.ip_address', $searchValue)
                    ->groupEnd();
            }

            // Get total records
            $totalRecords = $this->activityLogModel->countAllResults(false);

            // Get filtered count
            $filteredBuilder = clone $builder;
            $filteredCount = $filteredBuilder->countAllResults(false);

            // Apply pagination and ordering
            $activities = $builder
                ->orderBy('user_activity_logs.created_at', 'DESC')
                ->limit($length, $start)
                ->findAll();            // Format data for DataTables
            foreach ($activities as &$activity) {
                $activity['icon'] = $this->getActivityIcon($activity['activity_type']);
                $activity['time_ago'] = $this->timeAgo($activity['created_at']);
                $activity['formatted_date'] = date('M d, Y H:i:s', strtotime($activity['created_at']));
                $activity['user_display'] = $activity['full_name'] . ' (' . $activity['username'] . ')';
                $activity['user_name'] = $activity['full_name'] ?: $activity['username'];
                $activity['activity'] = $activity['activity_description'];
            }

            return $this->response->setJSON([
                'draw' => $draw,
                'data' => $activities,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getActivityLogsData: ' . $e->getMessage());
            return $this->response->setJSON([
                'draw' => intval($this->request->getGet('draw') ?? 1),
                'data' => [],
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'error' => 'Failed to fetch activity logs data'
            ]);
        }
    }

    /**
     * Get activity logs data for DataTables (alias method)
     */
    public function data()
    {
        return $this->getActivityLogsData();
    }

    /**
     * Get activity statistics
     */
    public function getActivityStats()
    {
        try {
            $today = date('Y-m-d');
            $weekAgo = date('Y-m-d', strtotime('-7 days'));
            $monthAgo = date('Y-m-d', strtotime('-30 days'));

            return [
                'total_activities' => $this->activityLogModel->countAllResults(),
                'today_activities' => $this->activityLogModel
                    ->where('DATE(created_at)', $today)
                    ->countAllResults(),
                'week_activities' => $this->activityLogModel
                    ->where('created_at >=', $weekAgo . ' 00:00:00')
                    ->countAllResults(),
                'month_activities' => $this->activityLogModel
                    ->where('created_at >=', $monthAgo . ' 00:00:00')
                    ->countAllResults(),
                'top_activities' => $this->getTopActivities(),
                'active_users_today' => $this->getActiveUsersToday()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting activity stats: ' . $e->getMessage());
            return [
                'total_activities' => 0,
                'today_activities' => 0,
                'week_activities' => 0,
                'month_activities' => 0,
                'top_activities' => [],
                'active_users_today' => 0
            ];
        }
    }

    /**
     * Get activity statistics for AJAX
     */
    public function stats()
    {
        try {
            $stats = $this->getActivityStats();
            return $this->response->setJSON([
                'success' => true,
                'stats' => [
                    'today_activities' => $stats['today_activities'],
                    'week_activities' => $stats['week_activities'],
                    'month_activities' => $stats['month_activities'],
                    'active_users' => $stats['active_users_today']
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting stats: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading statistics'
            ]);
        }
    }

    /**
     * Get top activity types
     */
    private function getTopActivities($limit = 5)
    {
        return $this->activityLogModel
            ->select('activity_type, COUNT(*) as count')
            ->where('created_at >=', date('Y-m-d', strtotime('-30 days')) . ' 00:00:00')
            ->groupBy('activity_type')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get active users today
     */
    private function getActiveUsersToday()
    {
        return $this->activityLogModel
            ->select('user_id')
            ->where('DATE(created_at)', date('Y-m-d'))
            ->groupBy('user_id')
            ->countAllResults();
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 20)
    {
        $activities = $this->activityLogModel
            ->select('user_activity_logs.*, users.username, users.full_name, users.role')
            ->join('users', 'users.id = user_activity_logs.user_id', 'left')
            ->orderBy('user_activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();

        foreach ($activities as &$activity) {
            $activity['icon'] = $this->getActivityIcon($activity['activity_type']);
            $activity['time_ago'] = $this->timeAgo($activity['created_at']);
        }

        return $activities;
    }

    /**
     * Get user activity details
     */
    public function getUserActivity($userId)
    {
        try {
            $user = $this->userModel->find($userId);
            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User not found'
                ]);
            }

            $activities = $this->activityLogModel
                ->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit(50)
                ->findAll();

            foreach ($activities as &$activity) {
                $activity['icon'] = $this->getActivityIcon($activity['activity_type']);
                $activity['time_ago'] = $this->timeAgo($activity['created_at']);
                $activity['formatted_date'] = date('M d, Y H:i:s', strtotime($activity['created_at']));
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'activities' => $activities,
                    'stats' => [
                        'total_activities' => $this->activityLogModel->where('user_id', $userId)->countAllResults(),
                        'last_activity' => !empty($activities) ? $activities[0]['created_at'] : null
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting user activity: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading user activity'
            ]);
        }
    }

    /**
     * Export activity logs
     */
    public function exportActivityLogs()
    {
        try {
            $format = $this->request->getGet('format', 'excel');
            $dateFrom = $this->request->getGet('date_from');
            $dateTo = $this->request->getGet('date_to');
            $activityType = $this->request->getGet('activity_type');

            // Build query
            $builder = $this->activityLogModel
                ->select('user_activity_logs.*, users.username, users.full_name, users.role')
                ->join('users', 'users.id = user_activity_logs.user_id', 'left');

            // Apply filters
            if ($dateFrom) {
                $builder->where('user_activity_logs.created_at >=', $dateFrom . ' 00:00:00');
            }

            if ($dateTo) {
                $builder->where('user_activity_logs.created_at <=', $dateTo . ' 23:59:59');
            }

            if ($activityType) {
                $builder->where('user_activity_logs.activity_type', $activityType);
            }

            $activities = $builder->orderBy('user_activity_logs.created_at', 'DESC')->findAll();

            if ($format === 'csv') {
                return $this->exportToCsv($activities);
            } else {
                return $this->exportToExcel($activities);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error exporting activity logs: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export activity logs');
        }
    }

    /**
     * Export to CSV
     */
    private function exportToCsv($activities)
    {
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV Header
        fputcsv($output, [
            'ID',
            'User',
            'Role',
            'Activity Type',
            'Description',
            'IP Address',
            'User Agent',
            'Created At'
        ]);

        // Data rows
        foreach ($activities as $activity) {
            fputcsv($output, [
                $activity['id'],
                $activity['full_name'] . ' (' . $activity['username'] . ')',
                $activity['role'],
                $activity['activity_type'],
                $activity['activity_description'],
                $activity['ip_address'],
                $activity['user_agent'],
                $activity['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($activities)
    {
        require_once ROOTPATH . 'vendor/autoload.php';

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'User');
        $sheet->setCellValue('C1', 'Role');
        $sheet->setCellValue('D1', 'Activity Type');
        $sheet->setCellValue('E1', 'Description');
        $sheet->setCellValue('F1', 'IP Address');
        $sheet->setCellValue('G1', 'User Agent');
        $sheet->setCellValue('H1', 'Created At');

        // Style headers
        $headerStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E2E8F0']
            ]
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Add data
        $row = 2;
        foreach ($activities as $activity) {
            $sheet->setCellValue('A' . $row, $activity['id']);
            $sheet->setCellValue('B' . $row, $activity['full_name'] . ' (' . $activity['username'] . ')');
            $sheet->setCellValue('C' . $row, $activity['role']);
            $sheet->setCellValue('D' . $row, $activity['activity_type']);
            $sheet->setCellValue('E' . $row, $activity['activity_description']);
            $sheet->setCellValue('F' . $row, $activity['ip_address']);
            $sheet->setCellValue('G' . $row, $activity['user_agent']);
            $sheet->setCellValue('H' . $row, $activity['created_at']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Generate filename and output
        $filename = 'activity_logs_' . date('Y-m-d_H-i-s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    /**
     * Clear old activity logs
     */
    public function clearOldLogs()
    {
        try {
            $daysToKeep = (int) $this->request->getPost('days_to_keep', 90);
            $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysToKeep} days"));

            $deletedCount = $this->activityLogModel
                ->where('created_at <', $cutoffDate)
                ->delete();

            // Log cleanup activity
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'logs_cleanup',
                "Cleared {$deletedCount} old activity logs (older than {$daysToKeep} days)",
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Berhasil menghapus {$deletedCount} log aktivitas lama.",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error clearing old logs: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal menghapus log lama: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * View activity details
     */
    public function view($id)
    {
        try {
            $activity = $this->activityLogModel
                ->select('user_activity_logs.*, users.username, users.full_name, users.role, users.email')
                ->join('users', 'users.id = user_activity_logs.user_id', 'left')
                ->find($id);

            if (!$activity) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Activity log not found'
                ]);
            }

            // Format the activity data
            $activity['user_name'] = $activity['full_name'] ?: $activity['username'];
            $activity['activity'] = $activity['activity_description'];
            $activity['time_ago'] = $this->timeAgo($activity['created_at']);
            $activity['formatted_date'] = date('M d, Y H:i:s', strtotime($activity['created_at']));

            return $this->response->setJSON([
                'success' => true,
                'activity' => $activity
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting activity details: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading activity details'
            ]);
        }
    }

    /**
     * Export activity logs (alias method)
     */
    public function export()
    {
        return $this->exportActivityLogs();
    }

    /**
     * Cleanup old logs
     */
    public function cleanup()
    {
        return $this->clearOldLogs();
    }
}
