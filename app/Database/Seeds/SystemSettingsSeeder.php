<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            // General Settings
            [
                'setting_key'   => 'site_name',
                'setting_value' => 'CBT Smart System',
                'setting_type'  => 'string',
                'category'      => 'general',
                'description'   => 'Name of the website/system',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'site_description',
                'setting_value' => 'Computer Based Testing System',
                'setting_type'  => 'string',
                'category'      => 'general',
                'description'   => 'Description of the website/system',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'admin_email',
                'setting_value' => 'admin@example.com',
                'setting_type'  => 'string',
                'category'      => 'general',
                'description'   => 'Primary admin email address',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'timezone',
                'setting_value' => 'Asia/Jakarta',
                'setting_type'  => 'string',
                'category'      => 'general',
                'description'   => 'System timezone',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'date_format',
                'setting_value' => 'Y-m-d',
                'setting_type'  => 'string',
                'category'      => 'general',
                'description'   => 'Date display format',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Email Settings
            [
                'setting_key'   => 'smtp_host',
                'setting_value' => 'smtp.gmail.com',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'SMTP server hostname',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'smtp_port',
                'setting_value' => '587',
                'setting_type'  => 'integer',
                'category'      => 'email',
                'description'   => 'SMTP server port',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'smtp_username',
                'setting_value' => '',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'SMTP username',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'smtp_password',
                'setting_value' => '',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'SMTP password',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'smtp_encryption',
                'setting_value' => 'tls',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'SMTP encryption method',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'from_email',
                'setting_value' => 'noreply@example.com',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'Default from email address',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'from_name',
                'setting_value' => 'CBT Smart System',
                'setting_type'  => 'string',
                'category'      => 'email',
                'description'   => 'Default from name',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Exam Settings
            [
                'setting_key'   => 'default_exam_duration',
                'setting_value' => '120',
                'setting_type'  => 'integer',
                'category'      => 'exam',
                'description'   => 'Default exam duration in minutes',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'auto_submit_buffer',
                'setting_value' => '5',
                'setting_type'  => 'integer',
                'category'      => 'exam',
                'description'   => 'Auto submit buffer time in minutes',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'passing_score',
                'setting_value' => '60',
                'setting_type'  => 'integer',
                'category'      => 'exam',
                'description'   => 'Default passing score percentage',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'max_attempts',
                'setting_value' => '1',
                'setting_type'  => 'integer',
                'category'      => 'exam',
                'description'   => 'Maximum exam attempts allowed',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'allow_review',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'category'      => 'exam',
                'description'   => 'Allow students to review questions',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'shuffle_questions',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'category'      => 'exam',
                'description'   => 'Shuffle questions by default',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Notification Settings
            [
                'setting_key'   => 'email_notifications',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'category'      => 'notification',
                'description'   => 'Enable email notifications',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'exam_start_notification',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'category'      => 'notification',
                'description'   => 'Send notification when exam starts',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'exam_end_notification',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'category'      => 'notification',
                'description'   => 'Send notification when exam completes',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'user_registration_notification',
                'setting_value' => '1',
                'setting_type'  => 'boolean',
                'category'      => 'notification',
                'description'   => 'Send notification for new user registrations',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Maintenance Settings
            [
                'setting_key'   => 'maintenance_mode',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'category'      => 'maintenance',
                'description'   => 'Enable maintenance mode',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'maintenance_message',
                'setting_value' => 'System is under maintenance. Please try again later.',
                'setting_type'  => 'string',
                'category'      => 'maintenance',
                'description'   => 'Maintenance mode message',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'backup_retention_days',
                'setting_value' => '30',
                'setting_type'  => 'integer',
                'category'      => 'maintenance',
                'description'   => 'Days to retain backup files',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'log_retention_days',
                'setting_value' => '90',
                'setting_type'  => 'integer',
                'category'      => 'maintenance',
                'description'   => 'Days to retain log files',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        // Insert data only if not exists
        foreach ($settings as $setting) {
            $existingSetting = $this->db->table('system_settings')
                ->where('setting_key', $setting['setting_key'])
                ->get()
                ->getRow();

            if (!$existingSetting) {
                $this->db->table('system_settings')->insert($setting);
            }
        }
    }
}
