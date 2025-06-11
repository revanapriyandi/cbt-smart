<?php

namespace App\Models;

use CodeIgniter\Model;

class SecuritySettingModel extends Model
{
    protected $table = 'security_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'category',
        'description'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'setting_key' => 'required|max_length[100]',
        'setting_value' => 'required',
        'category' => 'required|in_list[general,password,session,network]'
    ];

    protected $validationMessages = [
        'setting_key' => [
            'required' => 'Setting key is required.'
        ],
        'category' => [
            'in_list' => 'Invalid category specified.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all security settings grouped by category
     */
    public function getAllSettings()
    {
        $settings = $this->findAll();
        $grouped = [];

        foreach ($settings as $setting) {
            $grouped[$setting['category']][$setting['setting_key']] = $setting['setting_value'];
        }

        return $grouped;
    }

    /**
     * Get specific setting value
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * Update or create a setting
     */
    public function updateSetting($key, $value, $category = 'general', $description = null)
    {
        $existing = $this->where('setting_key', $key)->first();

        $data = [
            'setting_key' => $key,
            'setting_value' => $value,
            'category' => $category,
            'description' => $description
        ];

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Get password policies
     */
    public function getPasswordPolicies()
    {
        $policies = $this->where('category', 'password')->findAll();
        $result = [];

        foreach ($policies as $policy) {
            $result[$policy['setting_key']] = $policy['setting_value'];
        }

        // Set defaults if not exists
        $defaults = [
            'min_length' => '8',
            'require_uppercase' => '1',
            'require_lowercase' => '1',
            'require_numbers' => '1',
            'require_symbols' => '0',
            'password_history' => '5',
            'password_expiry_days' => '90'
        ];

        return array_merge($defaults, $result);
    }

    /**
     * Update password policies
     */
    public function updatePasswordPolicies($policies)
    {
        foreach ($policies as $key => $value) {
            $this->updateSetting($key, $value, 'password');
        }
        return true;
    }

    /**
     * Get session settings
     */
    public function getSessionSettings()
    {
        $settings = $this->where('category', 'session')->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }

        // Set defaults if not exists
        $defaults = [
            'session_timeout' => '7200',
            'idle_timeout' => '1800',
            'concurrent_sessions' => '3',
            'remember_me_duration' => '2592000',
            'secure_cookies' => '1',
            'force_logout_on_password_change' => '1'
        ];

        return array_merge($defaults, $result);
    }

    /**
     * Update session settings
     */
    public function updateSessionSettings($settings)
    {
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value, 'session');
        }
        return true;
    }

    /**
     * Get recent security logs
     */    public function getRecentSecurityLogs($limit = 10)
    {
        $db = \Config\Database::connect();

        return $db->table('user_activity_logs')
            ->select('user_activity_logs.*, users.username')
            ->join('users', 'users.id = user_activity_logs.user_id', 'left')
            ->whereIn('activity_type', [
                'login_failed',
                'login_success',
                'password_changed',
                'account_locked',
                'security_settings_updated',
                'suspicious_activity'
            ])
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get failed login attempts
     */    public function getFailedLoginAttempts($hours = 24)
    {
        $db = \Config\Database::connect();

        return $db->table('user_activity_logs')
            ->select('ip_address, COUNT(*) as attempts, MAX(created_at) as last_attempt')
            ->where('activity_type', 'login_failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime("-{$hours} hours")))
            ->groupBy('ip_address')
            ->orderBy('attempts', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get blocked IPs
     */
    public function getBlockedIPs()
    {
        return $this->where('category', 'blocked_ips')
            ->where('setting_value', '1')
            ->findAll();
    }

    /**
     * Block IP address
     */
    public function blockIP($ipAddress, $reason = '')
    {
        return $this->updateSetting(
            'blocked_ip_' . str_replace('.', '_', $ipAddress),
            '1',
            'blocked_ips',
            'Blocked: ' . $reason
        );
    }

    /**
     * Unblock IP address
     */
    public function unblockIP($ipAddress)
    {
        $setting = $this->where('setting_key', 'blocked_ip_' . str_replace('.', '_', $ipAddress))->first();
        if ($setting) {
            return $this->delete($setting['id']);
        }
        return true;
    }

    /**
     * Check if IP is blocked
     */
    public function isIPBlocked($ipAddress)
    {
        $blocked = $this->getSetting('blocked_ip_' . str_replace('.', '_', $ipAddress), '0');
        return $blocked === '1';
    }

    /**
     * Get active sessions
     */
    public function getActiveSessions()
    {
        $db = \Config\Database::connect();

        return $db->table('ci_sessions')
            ->select('ip_address, user_agent, timestamp, data')
            ->where('timestamp >', time() - 7200) // Active in last 2 hours
            ->orderBy('timestamp', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Terminate session
     */
    public function terminateSession($sessionId)
    {
        $db = \Config\Database::connect();

        return $db->table('ci_sessions')
            ->where('id', $sessionId)
            ->delete();
    }

    /**
     * Get security dashboard data
     */
    public function getSecurityDashboard()
    {
        $db = \Config\Database::connect();

        $data = [];        // Failed login attempts in last 24 hours
        $data['failed_logins_24h'] = $db->table('user_activity_logs')
            ->where('activity_type', 'login_failed')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();

        // Successful logins in last 24 hours
        $data['successful_logins_24h'] = $db->table('user_activity_logs')
            ->where('activity_type', 'login_success')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();

        // Account lockouts in last 24 hours
        $data['account_lockouts_24h'] = $db->table('user_activity_logs')
            ->where('activity_type', 'account_locked')
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->countAllResults();

        // Active sessions
        $data['active_sessions'] = $db->table('ci_sessions')
            ->where('timestamp >', time() - 7200)
            ->countAllResults();

        // Blocked IPs
        $data['blocked_ips_count'] = $this->where('category', 'blocked_ips')
            ->where('setting_value', '1')
            ->countAllResults();        // Security events trend (last 7 days)
        $data['security_trends'] = $db->table('user_activity_logs')
            ->select('DATE(created_at) as date, activity_type, COUNT(*) as count')
            ->whereIn('activity_type', ['login_failed', 'login_success', 'account_locked'])
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-7 days')))
            ->groupBy('DATE(created_at), activity_type')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();

        return $data;
    }

    /**
     * Get security report data
     */
    public function getSecurityReportData($dateFrom, $dateTo)
    {
        $db = \Config\Database::connect();
        return $db->table('user_activity_logs')
            ->select('user_activity_logs.*, users.username')
            ->join('users', 'users.id = user_activity_logs.user_id', 'left')
            ->whereIn('activity_type', [
                'login_failed',
                'login_success',
                'password_changed',
                'account_locked',
                'security_settings_updated',
                'suspicious_activity'
            ])
            ->where('user_activity_logs.created_at >=', $dateFrom)
            ->where('user_activity_logs.created_at <=', $dateTo)
            ->orderBy('user_activity_logs.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Initialize default security settings
     */
    public function initializeDefaults()
    {
        $defaults = [
            // General settings
            ['setting_key' => 'two_factor_required', 'setting_value' => '0', 'category' => 'general', 'description' => 'Require two-factor authentication'],
            ['setting_key' => 'password_reset_required', 'setting_value' => '0', 'category' => 'general', 'description' => 'Force password reset on first login'],
            ['setting_key' => 'account_lockout_enabled', 'setting_value' => '1', 'category' => 'general', 'description' => 'Enable account lockout after failed attempts'],
            ['setting_key' => 'max_login_attempts', 'setting_value' => '5', 'category' => 'general', 'description' => 'Maximum failed login attempts'],
            ['setting_key' => 'lockout_duration', 'setting_value' => '900', 'category' => 'general', 'description' => 'Account lockout duration in seconds'],
            ['setting_key' => 'ip_whitelist_enabled', 'setting_value' => '0', 'category' => 'general', 'description' => 'Enable IP whitelist protection'],
            ['setting_key' => 'maintenance_mode', 'setting_value' => '0', 'category' => 'general', 'description' => 'Maintenance mode status'],

            // Password policies
            ['setting_key' => 'min_length', 'setting_value' => '8', 'category' => 'password', 'description' => 'Minimum password length'],
            ['setting_key' => 'require_uppercase', 'setting_value' => '1', 'category' => 'password', 'description' => 'Require uppercase letters'],
            ['setting_key' => 'require_lowercase', 'setting_value' => '1', 'category' => 'password', 'description' => 'Require lowercase letters'],
            ['setting_key' => 'require_numbers', 'setting_value' => '1', 'category' => 'password', 'description' => 'Require numbers'],
            ['setting_key' => 'require_symbols', 'setting_value' => '0', 'category' => 'password', 'description' => 'Require special symbols'],
            ['setting_key' => 'password_history', 'setting_value' => '5', 'category' => 'password', 'description' => 'Remember last N passwords'],
            ['setting_key' => 'password_expiry_days', 'setting_value' => '90', 'category' => 'password', 'description' => 'Password expires after N days'],

            // Session settings
            ['setting_key' => 'session_timeout', 'setting_value' => '7200', 'category' => 'session', 'description' => 'Session timeout in seconds'],
            ['setting_key' => 'idle_timeout', 'setting_value' => '1800', 'category' => 'session', 'description' => 'Idle timeout in seconds'],
            ['setting_key' => 'concurrent_sessions', 'setting_value' => '3', 'category' => 'session', 'description' => 'Maximum concurrent sessions'],
            ['setting_key' => 'remember_me_duration', 'setting_value' => '2592000', 'category' => 'session', 'description' => 'Remember me duration in seconds'],
            ['setting_key' => 'secure_cookies', 'setting_value' => '1', 'category' => 'session', 'description' => 'Use secure cookies'],
            ['setting_key' => 'force_logout_on_password_change', 'setting_value' => '1', 'category' => 'session', 'description' => 'Force logout when password changes']
        ];

        foreach ($defaults as $default) {
            $existing = $this->where('setting_key', $default['setting_key'])->first();
            if (!$existing) {
                $this->insert($default);
            }
        }

        return true;
    }

    /**
     * Add IP to whitelist
     */
    public function addToWhitelist($ipAddress)
    {
        return $this->updateSetting(
            'whitelist_ip_' . str_replace('.', '_', $ipAddress),
            '1',
            'whitelist',
            'Whitelisted IP: ' . $ipAddress
        );
    }

    /**
     * Remove IP from whitelist
     */
    public function removeFromWhitelist($ipAddress)
    {
        $setting = $this->where('setting_key', 'whitelist_ip_' . str_replace('.', '_', $ipAddress))->first();
        if ($setting) {
            return $this->delete($setting['id']);
        }
        return true;
    }

    /**
     * Get whitelisted IPs
     */
    public function getWhitelistedIPs()
    {
        return $this->where('category', 'whitelist')
            ->where('setting_value', '1')
            ->findAll();
    }

    /**
     * Terminate user session
     */
    public function terminateUserSession($sessionId, $userId)
    {
        $db = \Config\Database::connect();

        // First try to find and delete by session ID
        $result = $db->table('ci_sessions')
            ->where('id', $sessionId)
            ->delete();

        // If no direct session ID match, try to find by user data
        if (!$result) {
            $sessions = $db->table('ci_sessions')
                ->where('data LIKE', '%user_id";s:' . strlen($userId) . ':"' . $userId . '"%')
                ->get()
                ->getResultArray();

            foreach ($sessions as $session) {
                $db->table('ci_sessions')->where('id', $session['id'])->delete();
            }

            return count($sessions) > 0;
        }

        return $result;
    }

    /**
     * Count active sessions
     */
    public function countActiveSessions()
    {
        $db = \Config\Database::connect();

        return $db->table('ci_sessions')
            ->where('timestamp >', time() - 7200)
            ->countAllResults();
    }

    /**
     * Count failed login attempts today
     */
    public function countFailedAttemptsToday()
    {
        $db = \Config\Database::connect();

        return $db->table('user_activity_logs')
            ->where('activity_type', 'login_failed')
            ->where('created_at >=', date('Y-m-d 00:00:00'))
            ->countAllResults();
    }

    /**
     * Count blocked IPs
     */
    public function countBlockedIPs()
    {
        return $this->where('category', 'blocked_ips')
            ->where('setting_value', '1')
            ->countAllResults();
    }

    /**
     * Get security alerts
     */
    public function getSecurityAlerts($limit = 10)
    {
        $db = \Config\Database::connect();

        return $db->table('user_activity_logs')
            ->select('user_activity_logs.*, users.username')
            ->join('users', 'users.id = user_activity_logs.user_id', 'left')
            ->whereIn('activity_type', [
                'login_failed',
                'account_locked',
                'suspicious_activity',
                'security_violation'
            ])
            ->where('user_activity_logs.created_at >=', date('Y-m-d H:i:s', strtotime('-24 hours')))
            ->orderBy('user_activity_logs.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get login trends
     */
    public function getLoginTrends($days = 7)
    {
        $db = \Config\Database::connect();

        $trends = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));

            $successful = $db->table('user_activity_logs')
                ->where('activity_type', 'login_success')
                ->where('DATE(created_at)', $date)
                ->countAllResults();

            $failed = $db->table('user_activity_logs')
                ->where('activity_type', 'login_failed')
                ->where('DATE(created_at)', $date)
                ->countAllResults();

            $trends[] = [
                'date' => $date,
                'successful' => $successful,
                'failed' => $failed
            ];
        }

        return $trends;
    }

    /**
     * Calculate security score
     */
    public function calculateSecurityScore()
    {
        $score = 100;

        // Check password policies
        $policies = $this->getPasswordPolicies();
        if ($policies['min_length'] < 8) $score -= 10;
        if ($policies['require_uppercase'] == '0') $score -= 5;
        if ($policies['require_lowercase'] == '0') $score -= 5;
        if ($policies['require_numbers'] == '0') $score -= 5;
        if ($policies['require_symbols'] == '0') $score -= 10;

        // Check general settings
        $settings = $this->getAllSettings();
        if (($settings['general']['two_factor_required'] ?? '0') == '0') $score -= 15;
        if (($settings['general']['account_lockout_enabled'] ?? '0') == '0') $score -= 10;

        // Check recent security events
        $recentFailures = $this->countFailedAttemptsToday();
        if ($recentFailures > 10) $score -= 20;
        elseif ($recentFailures > 5) $score -= 10;

        return max(0, min(100, $score));
    }

    /**
     * Generate security report
     */
    public function generateSecurityReport($dateFrom, $dateTo)
    {
        $db = \Config\Database::connect();

        $report = [
            'period' => [
                'from' => $dateFrom,
                'to' => $dateTo
            ],
            'summary' => [
                'total_events' => 0,
                'failed_logins' => 0,
                'successful_logins' => 0,
                'account_lockouts' => 0,
                'security_violations' => 0
            ],
            'events' => [],
            'trends' => [],
            'top_ips' => []
        ];

        // Get all security events in the period
        $events = $this->getSecurityReportData($dateFrom, $dateTo);
        $report['events'] = $events;
        $report['summary']['total_events'] = count($events);

        // Calculate summary statistics
        foreach ($events as $event) {
            switch ($event['activity_type']) {
                case 'login_failed':
                    $report['summary']['failed_logins']++;
                    break;
                case 'login_success':
                    $report['summary']['successful_logins']++;
                    break;
                case 'account_locked':
                    $report['summary']['account_lockouts']++;
                    break;
                case 'suspicious_activity':
                case 'security_violation':
                    $report['summary']['security_violations']++;
                    break;
            }
        }

        // Get trends data
        $trendData = $db->table('user_activity_logs')
            ->select('DATE(created_at) as date, activity_type, COUNT(*) as count')
            ->whereIn('activity_type', ['login_failed', 'login_success', 'account_locked'])
            ->where('created_at >=', $dateFrom)
            ->where('created_at <=', $dateTo)
            ->groupBy('DATE(created_at), activity_type')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
        $report['trends'] = $trendData;

        // Get top IPs with failed attempts
        $topIPs = $db->table('user_activity_logs')
            ->select('ip_address, COUNT(*) as attempts')
            ->where('activity_type', 'login_failed')
            ->where('created_at >=', $dateFrom)
            ->where('created_at <=', $dateTo)
            ->groupBy('ip_address')
            ->orderBy('attempts', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        $report['top_ips'] = $topIPs;

        return $report;
    }
}
