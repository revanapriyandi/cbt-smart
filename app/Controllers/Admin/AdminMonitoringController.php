<?php

namespace App\Controllers\Admin;

use App\Models\ExamSessionModel;
use App\Models\ExamModel;
use App\Models\UserModel;
use App\Models\ExamParticipantModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminMonitoringController extends BaseAdminController
{
    protected $examSessionModel;
    protected $examModel;
    protected $userModel;
    protected $examParticipantModel;

    public function __construct()
    {
        parent::__construct();
        $this->examSessionModel = new ExamSessionModel();
        $this->examModel = new ExamModel();
        $this->userModel = new UserModel();
        $this->examParticipantModel = new ExamParticipantModel();
    }

    public function index()
    {
        $data = [
            'title' => 'Live Monitoring',
            'breadcrumb' => [
                ['title' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['title' => 'Live Monitoring', 'url' => '/admin/monitoring/live']
            ]
        ];        // Get active sessions
        try {
            $activeSessions = $this->examSessionModel->getActiveSessions() ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get active sessions: ' . $e->getMessage());
            $activeSessions = [];
        }

        // Get real-time statistics
        try {
            $statistics = $this->getMonitoringStatistics() ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get monitoring statistics: ' . $e->getMessage());
            $statistics = [
                'active_sessions' => 0,
                'active_participants' => 0,
                'completed_today' => 0,
                'flagged_participants' => 0
            ];
        }

        // Get recent activities
        try {
            $recentActivities = $this->getRecentActivities() ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get recent activities: ' . $e->getMessage());
            $recentActivities = [];
        }

        // Get system alerts
        try {
            $systemAlerts = $this->getSystemAlerts() ?? [];
        } catch (\Exception $e) {
            log_message('error', 'Failed to get system alerts: ' . $e->getMessage());
            $systemAlerts = [];
        }

        $data['activeSessions'] = $activeSessions;
        $data['statistics'] = $statistics;
        $data['recentActivities'] = $recentActivities;
        $data['systemAlerts'] = $systemAlerts;

        return view('admin/monitoring/live', $data);
    }
    /**
     * Live monitoring page - alias for index method
     */
    public function live()
    {
        return $this->index();
    }

    /**
     * Get monitoring statistics
     */
    private function getMonitoringStatistics()
    {
        $stats = [];

        // Active sessions
        $stats['active_sessions'] = $this->examSessionModel->where('status', 'active')->countAllResults();

        // Active participants
        $stats['active_participants'] = $this->examParticipantModel->where('status', 'in_progress')->countAllResults();

        // Completed today
        $stats['completed_today'] = $this->examParticipantModel
            ->where('status', 'completed')
            ->where('DATE(updated_at)', date('Y-m-d'))
            ->countAllResults();

        // Total sessions
        $stats['total_sessions'] = $this->examSessionModel->countAllResults();

        // Online users (approximate)
        $stats['online_users'] = $this->getActiveConnections();

        return $stats;
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 20)
    {
        // Get recent participant activities
        $activities = $this->db->table('exam_activity_logs')
            ->select('exam_activity_logs.*, users.full_name as student_name, exams.title as exam_title')
            ->join('users', 'users.id = exam_activity_logs.student_id', 'left')
            ->join('exams', 'exams.id = exam_activity_logs.exam_id', 'left')
            ->orderBy('exam_activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();

        // Format activities
        foreach ($activities as &$activity) {
            $activity['time_ago'] = $this->timeAgo($activity['created_at']);
            $activity['icon'] = $this->getActivityIcon($activity['event_type']);
        }

        return $activities;
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts()
    {
        $alerts = [];

        // Check for system issues
        $activeParticipants = $this->examParticipantModel->where('status', 'in_progress')->countAllResults();

        if ($activeParticipants > 100) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "High load: {$activeParticipants} active participants",
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }

        // Check for flagged participants
        $flaggedCount = $this->db->table('participant_flags')
            ->where('DATE(flagged_at)', date('Y-m-d'))
            ->countAllResults();

        if ($flaggedCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$flaggedCount} participants flagged today",
                'timestamp' => date('Y-m-d H:i:s')
            ];
        }

        return $alerts;
    }

    public function getRealtimeData()
    {
        $data = [
            'activeSessions' => $this->examSessionModel->getActiveSessions(),
            'statistics' => $this->getMonitoringStatistics(),
            'recentActivities' => $this->getRecentActivities(20),
            'systemAlerts' => $this->getSystemAlerts(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $data
        ]);
    }

    public function getData()
    {
        return $this->getRealtimeData();
    }

    public function getSessionDetail($sessionId)
    {
        $session = $this->examSessionModel->getSessionWithDetails($sessionId);
        if (!$session) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi ujian tidak ditemukan'
            ]);
        }

        $participants = $this->examSessionModel->getSessionParticipants($sessionId);
        $progress = $this->examSessionModel->getSessionProgress($sessionId);
        $activities = $this->examSessionModel->getSessionActivities($sessionId, 50);

        // Get detailed participant status
        $participantDetails = [];
        foreach ($participants as $participant) {
            $participantDetails[] = [
                'id' => $participant->id,
                'name' => $participant->student_name,
                'student_id' => $participant->student_id,
                'status' => $participant->status,
                'start_time' => $participant->start_time,
                'current_question' => $participant->current_question,
                'answered_count' => $participant->answered_count,
                'time_remaining' => $this->calculateTimeRemaining($participant),
                'last_activity' => $participant->last_activity,
                'ip_address' => $participant->ip_address,
                'user_agent' => $participant->user_agent
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'session' => $session,
                'participants' => $participantDetails,
                'progress' => $progress,
                'activities' => $activities
            ]
        ]);
    }

    public function session($sessionId)
    {
        return $this->getSessionDetail($sessionId);
    }

    public function endSession($sessionId)
    {
        if (!$sessionId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session ID tidak valid'
            ]);
        }

        try {
            $session = $this->examSessionModel->find($sessionId);
            if (!$session) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sesi tidak ditemukan'
                ]);
            }

            // Update session status to ended
            $data = [
                'status' => 'ended',
                'end_time' => date('Y-m-d H:i:s'),
                'ended_by' => session()->get('user_id')
            ];

            if ($this->examSessionModel->update($sessionId, $data)) {
                // Also update all active participants in this session
                $this->examParticipantModel
                    ->where('session_id', $sessionId)
                    ->where('status', 'in_progress')
                    ->set(['status' => 'ended', 'end_time' => date('Y-m-d H:i:s')])
                    ->update();

                // Log activity
                $this->logActivity('session_ended', "Sesi ID: {$sessionId} diakhiri oleh administrator");

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Sesi berhasil diakhiri'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengakhiri sesi'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to end session: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ]);
        }
    }

    public function getParticipantActivity($participantId)
    {
        $participant = $this->examParticipantModel->find($participantId);
        if (!$participant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peserta tidak ditemukan'
            ]);
        }

        // Get participant's detailed activity log
        $activities = $this->getParticipantDetailedActivity($participantId);

        // Get current screen and browser info
        $currentStatus = $this->getParticipantCurrentStatus($participantId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'participant' => $participant,
                'activities' => $activities,
                'currentStatus' => $currentStatus
            ]
        ]);
    }

    public function sendMessage()
    {
        $rules = [
            'session_id' => 'required|is_natural_no_zero',
            'message' => 'required|max_length[500]',
            'type' => 'required|in_list[info,warning,alert]',
            'target' => 'required|in_list[all,individual]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'session_id' => $this->request->getPost('session_id'),
            'message' => $this->request->getPost('message'),
            'type' => $this->request->getPost('type'),
            'target' => $this->request->getPost('target'),
            'target_user_id' => $this->request->getPost('target_user_id'),
            'sent_by' => session()->get('user_id'),
            'sent_at' => date('Y-m-d H:i:s')
        ];

        if ($this->sendMessageToParticipants($data)) {
            // Log activity
            $this->logActivity('monitoring_message_sent', "Pesan monitoring dikirim ke sesi ID: {$data['session_id']}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pesan berhasil dikirim'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal mengirim pesan'
        ]);
    }

    public function terminateParticipant()
    {
        $participantId = $this->request->getPost('participant_id');
        $reason = $this->request->getPost('reason');

        if (!$participantId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID peserta tidak valid'
            ]);
        }

        $participant = $this->examParticipantModel->find($participantId);
        if (!$participant) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Peserta tidak ditemukan'
            ]);
        }

        // Terminate participant session
        $data = [
            'status' => 'terminated',
            'end_time' => date('Y-m-d H:i:s'),
            'termination_reason' => $reason,
            'terminated_by' => session()->get('user_id')
        ];

        if ($this->examParticipantModel->update($participantId, $data)) {
            // Log termination
            $this->logActivity('participant_terminated', "Peserta ID: {$participantId} diterminasi. Alasan: {$reason}");

            // Send termination notification to participant
            $this->sendTerminationNotification($participant, $reason);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta berhasil diterminasi'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal meterminasi peserta'
        ]);
    }

    public function flagParticipant()
    {
        $participantId = $this->request->getPost('participant_id');
        $flagType = $this->request->getPost('flag_type');
        $notes = $this->request->getPost('notes');

        if (!$participantId || !$flagType) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak lengkap'
            ]);
        }

        $flagData = [
            'participant_id' => $participantId,
            'flag_type' => $flagType,
            'notes' => $notes,
            'flagged_by' => session()->get('user_id'),
            'flagged_at' => date('Y-m-d H:i:s')
        ];

        if ($this->addParticipantFlag($flagData)) {
            // Log flag
            $this->logActivity('participant_flagged', "Peserta ID: {$participantId} ditandai dengan flag: {$flagType}");

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Peserta berhasil ditandai'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menandai peserta'
        ]);
    }

    public function getSystemHealth()
    {
        $health = [
            'server_status' => $this->checkServerStatus(),
            'database_status' => $this->checkDatabaseStatus(),
            'active_connections' => $this->getActiveConnections(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage(),
            'disk_usage' => $this->getDiskUsage(),
            'response_time' => $this->getAverageResponseTime()
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => $health
        ]);
    }

    private function calculateTimeRemaining($participant)
    {
        if (!$participant->start_time || !$participant->end_time) {
            return 0;
        }

        $endTime = strtotime($participant->end_time);
        $currentTime = time();

        return max(0, $endTime - $currentTime);
    }

    private function getParticipantDetailedActivity($participantId)
    {
        return $this->db->table('exam_activity_logs')
            ->select('*')
            ->where('participant_id', $participantId)
            ->orderBy('created_at', 'DESC')
            ->limit(100)
            ->get()
            ->getResultArray();
    }

    private function getParticipantCurrentStatus($participantId)
    {
        $participant = $this->examParticipantModel->find($participantId);

        return [
            'is_online' => $this->isParticipantOnline($participantId),
            'current_question' => $participant['current_question'] ?? 0,
            'answered_questions' => $participant['answered_questions'] ?? 0,
            'time_remaining' => $this->calculateTimeRemaining($participant),
            'last_activity' => $participant['last_activity'] ?? null,
            'ip_address' => $participant['ip_address'] ?? null
        ];
    }

    private function isParticipantOnline($participantId)
    {
        // Check if participant has activity in last 30 seconds
        $lastActivity = $this->db->table('exam_activity_logs')
            ->where('participant_id', $participantId)
            ->where('created_at >', date('Y-m-d H:i:s', strtotime('-30 seconds')))
            ->countAllResults();

        return $lastActivity > 0;
    }

    private function sendMessageToParticipants($data)
    {
        // Insert message into notifications table
        return $this->db->table('exam_notifications')->insert([
            'session_id' => $data['session_id'],
            'message' => $data['message'],
            'type' => $data['type'],
            'target' => $data['target'],
            'target_user_id' => $data['target_user_id'] ?? null,
            'sent_by' => $data['sent_by'],
            'sent_at' => $data['sent_at'],
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    private function sendTerminationNotification($participant, $reason)
    {
        // Send notification to participant about termination
        $notification = [
            'user_id' => $participant['student_id'],
            'type' => 'termination',
            'title' => 'Sesi Ujian Diterminasi',
            'message' => "Sesi ujian Anda telah diterminasi oleh administrator. Alasan: {$reason}",
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Insert into notifications table if exists
        try {
            $this->db->table('notifications')->insert($notification);
        } catch (\Exception $e) {
            log_message('error', 'Failed to send termination notification: ' . $e->getMessage());
        }
    }

    private function addParticipantFlag($flagData)
    {
        try {
            return $this->db->table('participant_flags')->insert($flagData);
        } catch (\Exception $e) {
            log_message('error', 'Failed to add participant flag: ' . $e->getMessage());
            return false;
        }
    }

    private function getAverageResponseTime()
    {
        // This would typically be measured by application performance monitoring
        return rand(50, 200); // Placeholder: 50-200ms
    }

    private function checkServerStatus()
    {
        // Check basic server health indicators
        $loadAverage = null;
        if (function_exists('sys_getloadavg')) {
            $loadAverage = sys_getloadavg();
        }

        $status = [
            'uptime' => $this->getServerUptime(),
            'load_average' => $loadAverage,
            'memory_usage' => $this->getMemoryUsage(),
            'disk_space' => $this->getDiskUsage(),
            'status' => 'running'
        ];

        return $status;
    }

    private function getServerUptime()
    {
        // Get server uptime (works on Unix systems)
        if (function_exists('shell_exec') && stripos(PHP_OS, 'WIN') === false) {
            $uptime = shell_exec('uptime');
            return trim($uptime);
        }

        // Fallback for Windows or when shell_exec is disabled
        return 'Uptime information not available';
    }

    private function checkDatabaseStatus()
    {
        try {
            $this->db->query('SELECT 1');
            return 'connected';
        } catch (\Exception $e) {
            return 'disconnected';
        }
    }
    private function getActiveConnections()
    {
        // Count active user sessions
        return $this->examParticipantModel->where('status', 'in_progress')->countAllResults();
    }

    private function getMemoryUsage()
    {
        return round(memory_get_usage(true) / 1024 / 1024, 2); // MB
    }

    private function getCpuUsage()
    {
        // Check if sys_getloadavg is available (Unix/Linux systems)
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return $load ? round($load[0] * 100, 2) : 0;
        }

        // Windows fallback - use WMI if available
        if (stripos(PHP_OS, 'WIN') === 0 && function_exists('shell_exec')) {
            try {
                $cmd = 'wmic cpu get loadpercentage /value';
                $output = shell_exec($cmd);
                if ($output && preg_match('/LoadPercentage=(\d+)/', $output, $matches)) {
                    return (float) $matches[1];
                }
            } catch (\Exception $e) {
                // Fallback to random value for demo purposes
            }
        }

        // Fallback - simulate CPU usage between 10-30%
        return rand(10, 30);
    }

    private function getDiskUsage()
    {
        $free = disk_free_space('/');
        $total = disk_total_space('/');
        return round((($total - $free) / $total) * 100, 2);
    }
}
