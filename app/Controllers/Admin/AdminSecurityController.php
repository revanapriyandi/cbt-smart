<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SecuritySettingModel;
use App\Models\UserModel;
use App\Models\UserActivityLogModel;

class AdminSecurityController extends BaseController
{
    protected $securityModel;
    protected $userModel;
    protected $activityLogModel;
    protected $session;

    public function __construct()
    {
        $this->securityModel = new SecuritySettingModel();
        $this->userModel = new UserModel();
        $this->activityLogModel = new UserActivityLogModel();
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        $data = [
            'title' => 'Security Settings - CBT Admin',
            'current_page' => 'security_settings',
            'breadcrumb' => [
                ['name' => 'Dashboard', 'url' => '/admin/dashboard'],
                ['name' => 'Security Settings', 'url' => '']
            ]
        ];        // Get current security settings
        $data['settings'] = $this->securityModel->getAllSettings();
        $data['password_policies'] = $this->securityModel->getPasswordPolicies();
        $data['session_settings'] = $this->securityModel->getSessionSettings();
        $data['security_logs'] = $this->securityModel->getRecentSecurityLogs(10);
        $data['failed_attempts'] = $this->securityModel->getFailedLoginAttempts();
        $data['blocked_ips'] = $this->securityModel->getBlockedIPs();
        $data['active_sessions'] = $this->securityModel->getActiveSessions();
        $data['security_dashboard'] = $this->securityModel->getSecurityDashboard();

        return view('admin/security/index', $data);
    }

    public function updateGeneralSettings()
    {
        $rules = [
            'two_factor_required' => 'required|in_list[0,1]',
            'password_reset_required' => 'required|in_list[0,1]',
            'account_lockout_enabled' => 'required|in_list[0,1]',
            'max_login_attempts' => 'required|integer|greater_than[0]',
            'lockout_duration' => 'required|integer|greater_than[0]',
            'ip_whitelist_enabled' => 'required|in_list[0,1]',
            'maintenance_mode' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $settings = $this->request->getPost();

            foreach ($settings as $key => $value) {
                $this->securityModel->updateSetting($key, $value);
            }

            // Log security setting change
            $this->activityLogModel->logActivity(
                session('admin_id'),
                'security_settings_updated',
                'Security settings updated',
                json_encode($settings)
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Security settings updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Security settings update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update security settings'
            ]);
        }
    }

    public function updatePasswordPolicies()
    {

        $rules = [
            'min_length' => 'required|integer|greater_than[5]',
            'require_uppercase' => 'required|in_list[0,1]',
            'require_lowercase' => 'required|in_list[0,1]',
            'require_numbers' => 'required|in_list[0,1]',
            'require_symbols' => 'required|in_list[0,1]',
            'password_history' => 'required|integer|greater_than_equal_to[0]',
            'password_expiry_days' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $policies = $this->request->getPost();

            $this->securityModel->updatePasswordPolicies($policies);

            // Log password policy change
            $this->activityLogModel->logActivity(
                session('admin_id'),
                'password_policies_updated',
                'Password policies updated',
                json_encode($policies)
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Password policies updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Password policies update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update password policies'
            ]);
        }
    }

    public function updateSessionSettings()
    {

        $rules = [
            'session_timeout' => 'required|integer|greater_than[0]',
            'idle_timeout' => 'required|integer|greater_than[0]',
            'concurrent_sessions' => 'required|integer|greater_than[0]',
            'remember_me_duration' => 'required|integer|greater_than[0]',
            'secure_cookies' => 'required|in_list[0,1]',
            'force_logout_on_password_change' => 'required|in_list[0,1]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $sessionSettings = $this->request->getPost();

            $this->securityModel->updateSessionSettings($sessionSettings);

            // Log session settings change
            $this->activityLogModel->logActivity(
                session('admin_id'),
                'session_settings_updated',
                'Session settings updated',
                json_encode($sessionSettings)
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Session settings updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Session settings update failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update session settings'
            ]);
        }
    }

    public function manageIPWhitelist()
    {

        $action = $this->request->getPost('action');
        $ipAddress = $this->request->getPost('ip_address');

        if (!in_array($action, ['add', 'remove'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid action'
            ]);
        }

        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid IP address format'
            ]);
        }

        try {
            if ($action === 'add') {
                $result = $this->securityModel->addToWhitelist($ipAddress);
                $message = 'IP address added to whitelist successfully';
            } else {
                $result = $this->securityModel->removeFromWhitelist($ipAddress);
                $message = 'IP address removed from whitelist successfully';
            }

            if ($result) {
                // Log IP whitelist change
                $this->activityLogModel->logActivity(
                    session('admin_id'),
                    'ip_whitelist_modified',
                    "IP {$ipAddress} {$action}ed to/from whitelist",
                    json_encode(['action' => $action, 'ip' => $ipAddress])
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Operation failed'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'IP whitelist management failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to manage IP whitelist'
            ]);
        }
    }

    public function unblockIP()
    {

        $ipAddress = $this->request->getPost('ip_address');

        if (!filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid IP address format'
            ]);
        }

        try {
            $result = $this->securityModel->unblockIP($ipAddress);

            if ($result) {
                // Log IP unblock
                $this->activityLogModel->logActivity(
                    session('admin_id'),
                    'ip_unblocked',
                    "IP {$ipAddress} unblocked manually",
                    json_encode(['ip' => $ipAddress])
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'IP address unblocked successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to unblock IP address'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'IP unblock failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to unblock IP address'
            ]);
        }
    }

    public function terminateSession()
    {

        $sessionId = $this->request->getPost('session_id');
        $userId = $this->request->getPost('user_id');

        if (empty($sessionId) || empty($userId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Missing required parameters'
            ]);
        }

        try {
            $result = $this->securityModel->terminateUserSession($sessionId, $userId);

            if ($result) {
                // Log session termination
                $this->activityLogModel->logActivity(
                    session('admin_id'),
                    'session_terminated',
                    "User session terminated (User ID: {$userId})",
                    json_encode(['session_id' => $sessionId, 'user_id' => $userId])
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Session terminated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to terminate session'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Session termination failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to terminate session'
            ]);
        }
    }

    public function getSecurityDashboard()
    {

        try {
            $dashboard = [
                'total_users' => $this->userModel->countAllResults(),
                'active_sessions' => $this->securityModel->countActiveSessions(),
                'failed_attempts_today' => $this->securityModel->countFailedAttemptsToday(),
                'blocked_ips' => $this->securityModel->countBlockedIPs(),
                'security_alerts' => $this->securityModel->getSecurityAlerts(),
                'login_trends' => $this->securityModel->getLoginTrends(7),
                'security_score' => $this->securityModel->calculateSecurityScore()
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $dashboard
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Security dashboard data failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to load dashboard data'
            ]);
        }
    }

    public function exportSecurityReport()
    {
        if (!$this->checkAdminPermission()) {
            return redirect()->to('/admin/security')->with('error', 'Access denied');
        }

        $format = $this->request->getGet('format') ?? 'pdf';
        $dateFrom = $this->request->getGet('date_from') ?? date('Y-m-01');
        $dateTo = $this->request->getGet('date_to') ?? date('Y-m-d');

        try {
            $reportData = $this->securityModel->generateSecurityReport($dateFrom, $dateTo);

            if ($format === 'excel') {
                return $this->exportToExcel($reportData, $dateFrom, $dateTo);
            } elseif ($format === 'csv') {
                return $this->exportToCSV($reportData, $dateFrom, $dateTo);
            } else {
                return $this->exportToPDF($reportData, $dateFrom, $dateTo);
            }
        } catch (\Exception $e) {
            log_message('error', 'Security report export failed: ' . $e->getMessage());
            return redirect()->to('/admin/security')->with('error', 'Failed to export security report');
        }
    }

    private function exportToPDF($data, $dateFrom, $dateTo)
    {
        // Implementation for PDF export using a library like TCPDF or mPDF
        // This is a placeholder - you would implement actual PDF generation
        $filename = "security_report_{$dateFrom}_to_{$dateTo}.pdf";

        // Log export activity
        $this->activityLogModel->logActivity(
            session('admin_id'),
            'security_report_exported',
            "Security report exported (PDF) for period {$dateFrom} to {$dateTo}",
            json_encode(['format' => 'pdf', 'date_from' => $dateFrom, 'date_to' => $dateTo])
        );

        // Return PDF download response
        return $this->response->download($filename, null);
    }

    private function exportToExcel($data, $dateFrom, $dateTo)
    {
        // Implementation for Excel export using PhpSpreadsheet
        // This is a placeholder - you would implement actual Excel generation
        $filename = "security_report_{$dateFrom}_to_{$dateTo}.xlsx";

        // Log export activity
        $this->activityLogModel->logActivity(
            session('admin_id'),
            'security_report_exported',
            "Security report exported (Excel) for period {$dateFrom} to {$dateTo}",
            json_encode(['format' => 'excel', 'date_from' => $dateFrom, 'date_to' => $dateTo])
        );

        // Return Excel download response
        return $this->response->download($filename, null);
    }

    private function exportToCSV($data, $dateFrom, $dateTo)
    {
        $filename = "security_report_{$dateFrom}_to_{$dateTo}.csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, ['Date', 'Event Type', 'User', 'IP Address', 'Description', 'Status']);

        // Add data rows
        foreach ($data['events'] as $event) {
            fputcsv($output, [
                $event['created_at'],
                $event['event_type'],
                $event['username'] ?? 'N/A',
                $event['ip_address'],
                $event['description'],
                $event['status']
            ]);
        }

        fclose($output);

        // Log export activity
        $this->activityLogModel->logActivity(
            session('admin_id'),
            'security_report_exported',
            "Security report exported (CSV) for period {$dateFrom} to {$dateTo}",
            json_encode(['format' => 'csv', 'date_from' => $dateFrom, 'date_to' => $dateTo])
        );

        exit;
    }

    private function checkAdminPermission()
    {
        return session('user_type') === 'admin' && session('is_logged_in');
    }
}
