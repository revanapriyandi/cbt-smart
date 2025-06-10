<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\SubjectModel;
use App\Models\ExamModel;
use App\Models\ExamResultModel;
use App\Models\UserActivityLogModel;

abstract class BaseAdminController extends BaseController
{
    protected $userModel;
    protected $subjectModel;
    protected $examModel;
    protected $examResultModel;
    protected $userActivityLogModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->subjectModel = new SubjectModel();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
        $this->userActivityLogModel = new UserActivityLogModel();
        $this->db = \Config\Database::connect();
    }
    /**
     * Helper function to get activity icon based on type
     */
    protected function getActivityIcon($activityType)
    {
        $icons = [
            'login' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-secondary',
            'exam_start' => 'fas fa-play text-primary',
            'exam_submit' => 'fas fa-check text-success',
            'profile_update' => 'fas fa-user-edit text-info',
            'password_change' => 'fas fa-key text-warning',
            'user_create' => 'fas fa-user-plus text-success',
            'user_update' => 'fas fa-user-edit text-info',
            'user_delete' => 'fas fa-user-minus text-danger',
            'default' => 'fas fa-circle text-muted'
        ];

        return $icons[$activityType] ?? $icons['default'];
    }

    /**
     * Helper function to calculate time ago
     */
    protected function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);

        if ($time < 60) return 'just now';
        if ($time < 3600) return floor($time / 60) . ' minutes ago';
        if ($time < 86400) return floor($time / 3600) . ' hours ago';
        if ($time < 2592000) return floor($time / 86400) . ' days ago';
        if ($time < 31536000) return floor($time / 2592000) . ' months ago';

        return floor($time / 31536000) . ' years ago';
    }
    /**
     * Check if current user has admin role and can access exam results
     * Admin and teachers can access all exam results for management purposes
     * Students can only access their own exam results
     */
    protected function canAccessExamResults($examId = null, $studentId = null)
    {
        $userRole = session()->get('role');
        $currentUserId = session()->get('user_id');

        // Admin and teachers can access all exam results for management purposes
        if (in_array($userRole, ['admin', 'teacher'])) {
            return true;
        }

        // Students can only access their own exam results
        if ($userRole === 'student') {
            if ($studentId && $currentUserId != $studentId) {
                return false; // Student trying to access another student's results
            }
            return true;
        }

        return false;
    }
    /**
     * Log activity for security and audit purposes
     */
    protected function logActivity($activityType, $description, $additionalData = null)
    {
        $data = [
            'user_id' => session()->get('user_id'),
            'activity_type' => $activityType,
            'description' => $description,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'additional_data' => $additionalData ? json_encode($additionalData) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->userActivityLogModel->insert($data);
    }
}
