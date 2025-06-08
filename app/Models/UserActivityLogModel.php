<?php

namespace App\Models;

use CodeIgniter\Model;

class UserActivityLogModel extends Model
{
    protected $table = 'user_activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'user_id',
        'activity_type',
        'activity_description',
        'ip_address',
        'user_agent',
        'created_at'
    ];
    protected $useTimestamps = false;
    /**
     * Log user activity
     */
    public function logActivity($userId, $activityType, $description, $ipAddress = null, $userAgent = null)
    {
        // Validate user ID
        if (!$userId || !is_numeric($userId)) {
            log_message('warning', 'UserActivityLogModel: Invalid user ID provided: ' . $userId);
            return false;
        }

        // Check if user exists before logging activity
        $userModel = new \App\Models\UserModel();
        if (!$userModel->find($userId)) {
            log_message('warning', 'UserActivityLogModel: User not found for ID: ' . $userId);
            return false;
        }

        $data = [
            'user_id' => (int)$userId,
            'activity_type' => $activityType,
            'activity_description' => $description,
            'ip_address' => $ipAddress ?: ($_SERVER['REMOTE_ADDR'] ?? null),
            'user_agent' => $userAgent ?: ($_SERVER['HTTP_USER_AGENT'] ?? null),
            'created_at' => date('Y-m-d H:i:s')
        ];

        try {
            return $this->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'UserActivityLogModel: Failed to log activity: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user activity logs
     */
    public function getUserActivity($userId, $limit = 20)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get activity statistics for a user
     */
    public function getUserActivityStats($userId)
    {
        // Total activities
        $totalActivities = $this->where('user_id', $userId)->countAllResults();

        // Login count
        $loginCount = $this->where('user_id', $userId)
            ->where('activity_type', 'login')
            ->countAllResults();

        // Last login
        $lastLogin = $this->where('user_id', $userId)
            ->where('activity_type', 'login')
            ->orderBy('created_at', 'DESC')
            ->first();

        // Recent activities
        $recentActivities = $this->getUserActivity($userId, 10);

        return [
            'total_activities' => $totalActivities,
            'login_count' => $loginCount,
            'last_login' => $lastLogin ? $lastLogin['created_at'] : null,
            'recent_activities' => $recentActivities
        ];
    }

    /**
     * Clean old activity logs (older than specified days)
     */
    public function cleanOldLogs($days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        return $this->where('created_at <', $cutoffDate)->delete();
    }
}
