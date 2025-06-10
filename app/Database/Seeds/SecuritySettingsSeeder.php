<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SecuritySettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General settings
            [
                'setting_key' => 'two_factor_required',
                'setting_value' => '0',
                'category' => 'general',
                'description' => 'Require two-factor authentication',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'password_reset_required',
                'setting_value' => '0',
                'category' => 'general',
                'description' => 'Force password reset on first login',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'account_lockout_enabled',
                'setting_value' => '1',
                'category' => 'general',
                'description' => 'Enable account lockout after failed attempts',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'max_login_attempts',
                'setting_value' => '5',
                'category' => 'general',
                'description' => 'Maximum failed login attempts',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'lockout_duration',
                'setting_value' => '900',
                'category' => 'general',
                'description' => 'Account lockout duration in seconds',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'ip_whitelist_enabled',
                'setting_value' => '0',
                'category' => 'general',
                'description' => 'Enable IP whitelist protection',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => '0',
                'category' => 'general',
                'description' => 'Maintenance mode status',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Password policies
            [
                'setting_key' => 'min_length',
                'setting_value' => '8',
                'category' => 'password',
                'description' => 'Minimum password length',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'require_uppercase',
                'setting_value' => '1',
                'category' => 'password',
                'description' => 'Require uppercase letters',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'require_lowercase',
                'setting_value' => '1',
                'category' => 'password',
                'description' => 'Require lowercase letters',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'require_numbers',
                'setting_value' => '1',
                'category' => 'password',
                'description' => 'Require numbers',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'require_symbols',
                'setting_value' => '0',
                'category' => 'password',
                'description' => 'Require special symbols',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'password_history',
                'setting_value' => '5',
                'category' => 'password',
                'description' => 'Remember last N passwords',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'password_expiry_days',
                'setting_value' => '90',
                'category' => 'password',
                'description' => 'Password expires after N days',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            
            // Session settings
            [
                'setting_key' => 'session_timeout',
                'setting_value' => '7200',
                'category' => 'session',
                'description' => 'Session timeout in seconds',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'idle_timeout',
                'setting_value' => '1800',
                'category' => 'session',
                'description' => 'Idle timeout in seconds',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'concurrent_sessions',
                'setting_value' => '3',
                'category' => 'session',
                'description' => 'Maximum concurrent sessions',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'remember_me_duration',
                'setting_value' => '2592000',
                'category' => 'session',
                'description' => 'Remember me duration in seconds',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'secure_cookies',
                'setting_value' => '1',
                'category' => 'session',
                'description' => 'Use secure cookies',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'force_logout_on_password_change',
                'setting_value' => '1',
                'category' => 'session',
                'description' => 'Force logout when password changes',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert the default settings
        $this->db->table('security_settings')->insertBatch($settings);
    }
}
