<?php

namespace App\Controllers\Admin;

use App\Models\SystemSettingModel;

class AdminSystemSettingsController extends BaseAdminController
{
    protected $systemSettingModel;

    public function __construct()
    {
        parent::__construct();
        $this->systemSettingModel = new SystemSettingModel();
    }

    /**
     * Display system settings page
     */
    public function index()
    {
        $data = [
            'generalSettings' => $this->systemSettingModel->getSettingsByCategory('general'),
            'emailSettings' => $this->systemSettingModel->getSettingsByCategory('email'),
            'examSettings' => $this->systemSettingModel->getSettingsByCategory('exam'),
            'notificationSettings' => $this->systemSettingModel->getSettingsByCategory('notification'),
            'maintenanceSettings' => $this->systemSettingModel->getSettingsByCategory('maintenance'),
            'systemInfo' => $this->getSystemInfo()
        ];

        return view('admin/system-settings/index', $data);
    }

    /**
     * Update general settings
     */
    public function updateGeneralSettings()
    {
        try {
            $settings = [
                'site_name' => $this->request->getPost('site_name'),
                'site_description' => $this->request->getPost('site_description'),
                'site_url' => $this->request->getPost('site_url'),
                'admin_email' => $this->request->getPost('admin_email'),
                'timezone' => $this->request->getPost('timezone'),
                'date_format' => $this->request->getPost('date_format'),
                'time_format' => $this->request->getPost('time_format'),
                'language' => $this->request->getPost('language'),
                'logo_path' => $this->request->getPost('logo_path')
            ];

            foreach ($settings as $key => $value) {
                $this->systemSettingModel->updateSetting('general', $key, $value);
            }

            // Log settings update
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_update',
                'Updated general system settings',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan umum berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating general settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings()
    {
        try {
            $settings = [
                'smtp_host' => $this->request->getPost('smtp_host'),
                'smtp_port' => $this->request->getPost('smtp_port'),
                'smtp_user' => $this->request->getPost('smtp_user'),
                'smtp_pass' => $this->request->getPost('smtp_pass'),
                'smtp_crypto' => $this->request->getPost('smtp_crypto'),
                'from_email' => $this->request->getPost('from_email'),
                'from_name' => $this->request->getPost('from_name'),
                'email_enabled' => $this->request->getPost('email_enabled') ? '1' : '0'
            ];

            foreach ($settings as $key => $value) {
                $this->systemSettingModel->updateSetting('email', $key, $value);
            }

            // Log settings update
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_update',
                'Updated email system settings',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan email berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating email settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update exam settings
     */
    public function updateExamSettings()
    {
        try {
            $settings = [
                'default_exam_duration' => $this->request->getPost('default_exam_duration'),
                'max_exam_duration' => $this->request->getPost('max_exam_duration'),
                'auto_submit_enabled' => $this->request->getPost('auto_submit_enabled') ? '1' : '0',
                'shuffle_questions' => $this->request->getPost('shuffle_questions') ? '1' : '0',
                'shuffle_options' => $this->request->getPost('shuffle_options') ? '1' : '0',
                'show_results_immediately' => $this->request->getPost('show_results_immediately') ? '1' : '0',
                'allow_review_answers' => $this->request->getPost('allow_review_answers') ? '1' : '0',
                'max_attempts' => $this->request->getPost('max_attempts'),
                'passing_score' => $this->request->getPost('passing_score'),
                'lockdown_enabled' => $this->request->getPost('lockdown_enabled') ? '1' : '0'
            ];

            foreach ($settings as $key => $value) {
                $this->systemSettingModel->updateSetting('exam', $key, $value);
            }

            // Log settings update
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_update',
                'Updated exam system settings',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan ujian berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating exam settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan ujian: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update notification settings
     */
    public function updateNotificationSettings()
    {
        try {
            $settings = [
                'email_notifications' => $this->request->getPost('email_notifications') ? '1' : '0',
                'exam_start_notification' => $this->request->getPost('exam_start_notification') ? '1' : '0',
                'exam_end_notification' => $this->request->getPost('exam_end_notification') ? '1' : '0',
                'result_notification' => $this->request->getPost('result_notification') ? '1' : '0',
                'system_alert_notification' => $this->request->getPost('system_alert_notification') ? '1' : '0',
                'notification_delay' => $this->request->getPost('notification_delay'),
                'digest_frequency' => $this->request->getPost('digest_frequency')
            ];

            foreach ($settings as $key => $value) {
                $this->systemSettingModel->updateSetting('notification', $key, $value);
            }

            // Log settings update
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_update',
                'Updated notification system settings',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan notifikasi berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating notification settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan notifikasi: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update maintenance settings
     */
    public function updateMaintenanceSettings()
    {
        try {
            $settings = [
                'maintenance_mode' => $this->request->getPost('maintenance_mode') ? '1' : '0',
                'maintenance_message' => $this->request->getPost('maintenance_message'),
                'maintenance_start_time' => $this->request->getPost('maintenance_start_time'),
                'maintenance_end_time' => $this->request->getPost('maintenance_end_time'),
                'allowed_ips' => $this->request->getPost('allowed_ips'),
                'auto_backup_enabled' => $this->request->getPost('auto_backup_enabled') ? '1' : '0',
                'backup_frequency' => $this->request->getPost('backup_frequency'),
                'log_retention_days' => $this->request->getPost('log_retention_days')
            ];

            foreach ($settings as $key => $value) {
                $this->systemSettingModel->updateSetting('maintenance', $key, $value);
            }

            // Log settings update
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_update',
                'Updated maintenance system settings',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Pengaturan maintenance berhasil diperbarui!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error updating maintenance settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memperbarui pengaturan maintenance: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Test email configuration
     */
    public function testEmailConfig()
    {
        try {
            $testEmail = $this->request->getPost('test_email');

            if (!$testEmail) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Email tujuan diperlukan untuk test!'
                ]);
            }

            // Get email settings
            $emailSettings = $this->systemSettingModel->getSettingsByCategory('email');

            // Configure email
            $email = \Config\Services::email();
            $config = [
                'protocol' => 'smtp',
                'SMTPHost' => $emailSettings['smtp_host'] ?? '',
                'SMTPUser' => $emailSettings['smtp_user'] ?? '',
                'SMTPPass' => $emailSettings['smtp_pass'] ?? '',
                'SMTPPort' => $emailSettings['smtp_port'] ?? 587,
                'SMTPCrypto' => $emailSettings['smtp_crypto'] ?? 'tls'
            ];

            $email->initialize($config);

            $email->setFrom($emailSettings['from_email'] ?? '', $emailSettings['from_name'] ?? '');
            $email->setTo($testEmail);
            $email->setSubject('Test Email Configuration - CBT Smart');
            $email->setMessage('This is a test email to verify your email configuration is working correctly.');

            if ($email->send()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Email test berhasil dikirim!'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengirim email test: ' . $email->printDebugger()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error testing email config: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error testing email: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get system information
     */
    public function getSystemInfo()
    {
        try {
            return [
                'php_version' => PHP_VERSION,
                'codeigniter_version' => \CodeIgniter\CodeIgniter::CI_VERSION,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
                'database_version' => $this->getDatabaseVersion(),
                'max_execution_time' => ini_get('max_execution_time'),
                'memory_limit' => ini_get('memory_limit'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'timezone' => date_default_timezone_get(),
                'disk_space' => $this->getDiskSpace(),
                'writable_paths' => $this->checkWritablePaths()
            ];
        } catch (\Exception $e) {
            log_message('error', 'Error getting system info: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get database version
     */
    private function getDatabaseVersion()
    {
        try {
            $query = $this->db->query('SELECT VERSION() as version');
            $result = $query->getRow();
            return $result->version ?? 'Unknown';
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    /**
     * Get disk space information
     */
    private function getDiskSpace()
    {
        try {
            $bytes = disk_free_space(ROOTPATH);
            $total = disk_total_space(ROOTPATH);

            return [
                'free' => $this->formatBytes($bytes),
                'total' => $this->formatBytes($total),
                'used_percentage' => round((($total - $bytes) / $total) * 100, 2)
            ];
        } catch (\Exception $e) {
            return [
                'free' => 'Unknown',
                'total' => 'Unknown',
                'used_percentage' => 0
            ];
        }
    }

    /**
     * Check writable paths
     */
    private function checkWritablePaths()
    {
        $paths = [
            'writable' => WRITEPATH,
            'uploads' => WRITEPATH . 'uploads/',
            'cache' => WRITEPATH . 'cache/',
            'logs' => WRITEPATH . 'logs/',
            'session' => WRITEPATH . 'session/'
        ];

        $results = [];
        foreach ($paths as $name => $path) {
            $results[$name] = [
                'path' => $path,
                'writable' => is_writable($path),
                'exists' => file_exists($path)
            ];
        }

        return $results;
    }

    /**
     * Format bytes
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }

        return round($size, $precision) . ' ' . $units[$i];
    }

    /**
     * Clear system cache
     */
    public function clearCache()
    {
        try {
            // Clear file cache
            $cacheDir = WRITEPATH . 'cache/';
            if (is_dir($cacheDir)) {
                $this->deleteDirectory($cacheDir);
                mkdir($cacheDir, 0755, true);
            }

            // Clear view cache
            $viewCacheDir = WRITEPATH . 'cache/views/';
            if (is_dir($viewCacheDir)) {
                $this->deleteDirectory($viewCacheDir);
                mkdir($viewCacheDir, 0755, true);
            }

            // Log cache clear
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_cache_clear',
                'System cache cleared',
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cache sistem berhasil dibersihkan!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error clearing cache: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal membersihkan cache: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Delete directory recursively
     */
    private function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return false;
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->deleteDirectory($path);
            } else {
                unlink($path);
            }
        }

        return rmdir($dir);
    }

    /**
     * Upload logo
     */
    public function uploadLogo()
    {
        try {
            $file = $this->request->getFile('logo_file');

            if (!$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid!'
                ]);
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Hanya file gambar (JPEG, PNG, GIF) yang diizinkan!'
                ]);
            }

            // Validate file size (max 2MB)
            if ($file->getSize() > 2048 * 1024) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Ukuran file maksimal 2MB!'
                ]);
            }

            $uploadPath = FCPATH . 'uploads/logos/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            $fileName = 'logo_' . time() . '.' . $file->getExtension();

            if ($file->move($uploadPath, $fileName)) {
                $logoPath = 'uploads/logos/' . $fileName;

                // Update logo path in settings
                $this->systemSettingModel->updateSetting('general', 'logo_path', $logoPath);

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Logo berhasil diupload!',
                    'logo_path' => base_url($logoPath)
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal mengupload logo!'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error uploading logo: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengupload logo: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Reset settings to default
     */
    public function resetToDefault()
    {
        try {
            $category = $this->request->getPost('category');

            if (!$category) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kategori setting diperlukan!'
                ]);
            }

            // Reset settings to default values
            $this->systemSettingModel->resetCategoryToDefault($category);

            // Log reset action
            $this->userActivityLogModel->logActivity(
                session()->get('user_id'),
                'system_settings_reset',
                "Reset {$category} settings to default",
                $this->request->getIPAddress(),
                $this->request->getUserAgent()
            );

            return $this->response->setJSON([
                'success' => true,
                'message' => "Pengaturan {$category} berhasil direset ke default!"
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error resetting settings: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mereset pengaturan: ' . $e->getMessage()
            ]);
        }
    }
}
