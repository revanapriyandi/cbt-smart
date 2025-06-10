<?php

namespace App\Models;

use CodeIgniter\Model;

class SystemSettingModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'is_required',
        'default_value',
        'validation_rules'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'category' => 'required|max_length[50]',
        'setting_key' => 'required|max_length[100]',
        'setting_value' => 'permit_empty',
        'setting_type' => 'required|in_list[string,integer,boolean,json,text]',
        'is_required' => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'category' => [
            'required' => 'Category is required',
            'max_length' => 'Category cannot exceed 50 characters'
        ],
        'setting_key' => [
            'required' => 'Setting key is required',
            'max_length' => 'Setting key cannot exceed 100 characters'
        ],
        'setting_type' => [
            'required' => 'Setting type is required',
            'in_list' => 'Setting type must be one of: string, integer, boolean, json, text'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get settings by category
     */
    public function getSettingsByCategory($category)
    {
        $settings = $this->where('category', $category)->findAll();
        $result = [];

        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $this->formatSettingValue($setting['setting_value'], $setting['setting_type']);
        }

        return $result;
    }

    /**
     * Get a single setting value
     */
    public function getSetting($category, $key, $default = null)
    {
        $setting = $this->where('category', $category)
            ->where('setting_key', $key)
            ->first();

        if (!$setting) {
            return $default;
        }

        return $this->formatSettingValue($setting['setting_value'], $setting['setting_type']);
    }

    /**
     * Update or create a setting
     */
    public function updateSetting($category, $key, $value, $type = 'string', $description = null)
    {
        $existingSetting = $this->where('category', $category)
            ->where('setting_key', $key)
            ->first();

        $data = [
            'category' => $category,
            'setting_key' => $key,
            'setting_value' => $this->prepareSettingValue($value, $type),
            'setting_type' => $type
        ];

        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($existingSetting) {
            return $this->update($existingSetting['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Format setting value based on type
     */
    private function formatSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'json':
                return json_decode($value, true);
            case 'string':
            case 'text':
            default:
                return $value;
        }
    }

    /**
     * Prepare setting value for storage
     */
    private function prepareSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'integer':
                return (string) intval($value);
            case 'json':
                return json_encode($value);
            case 'string':
            case 'text':
            default:
                return (string) $value;
        }
    }

    /**
     * Get all settings grouped by category
     */
    public function getAllSettings()
    {
        $settings = $this->orderBy('category, setting_key')->findAll();
        $result = [];

        foreach ($settings as $setting) {
            if (!isset($result[$setting['category']])) {
                $result[$setting['category']] = [];
            }

            $result[$setting['category']][$setting['setting_key']] = [
                'value' => $this->formatSettingValue($setting['setting_value'], $setting['setting_type']),
                'type' => $setting['setting_type'],
                'description' => $setting['description'],
                'is_required' => (bool) $setting['is_required'],
                'default_value' => $setting['default_value']
            ];
        }

        return $result;
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaultSettings()
    {
        $defaultSettings = $this->getDefaultSettings();

        foreach ($defaultSettings as $category => $settings) {
            foreach ($settings as $key => $config) {
                $existing = $this->where('category', $category)
                    ->where('setting_key', $key)
                    ->first();

                if (!$existing) {
                    $this->insert([
                        'category' => $category,
                        'setting_key' => $key,
                        'setting_value' => $this->prepareSettingValue($config['value'], $config['type']),
                        'setting_type' => $config['type'],
                        'description' => $config['description'] ?? '',
                        'is_required' => $config['required'] ?? 0,
                        'default_value' => $this->prepareSettingValue($config['value'], $config['type']),
                        'validation_rules' => $config['validation'] ?? null
                    ]);
                }
            }
        }
    }

    /**
     * Reset category settings to default
     */
    public function resetCategoryToDefault($category)
    {
        $defaultSettings = $this->getDefaultSettings();

        if (!isset($defaultSettings[$category])) {
            throw new \Exception("Category '{$category}' not found in default settings");
        }

        foreach ($defaultSettings[$category] as $key => $config) {
            $this->updateSetting(
                $category,
                $key,
                $config['value'],
                $config['type'],
                $config['description'] ?? null
            );
        }

        return true;
    }

    /**
     * Get default system settings
     */
    private function getDefaultSettings()
    {
        return [
            'general' => [
                'site_name' => [
                    'value' => 'CBT Smart',
                    'type' => 'string',
                    'description' => 'Name of the application',
                    'required' => 1
                ],
                'site_description' => [
                    'value' => 'Computer Based Testing System',
                    'type' => 'text',
                    'description' => 'Description of the application'
                ],
                'site_url' => [
                    'value' => base_url(),
                    'type' => 'string',
                    'description' => 'Base URL of the application'
                ],
                'admin_email' => [
                    'value' => 'admin@cbtsmart.com',
                    'type' => 'string',
                    'description' => 'Administrator email address',
                    'validation' => 'valid_email'
                ],
                'timezone' => [
                    'value' => 'Asia/Jakarta',
                    'type' => 'string',
                    'description' => 'Default timezone'
                ],
                'date_format' => [
                    'value' => 'Y-m-d',
                    'type' => 'string',
                    'description' => 'Date format for display'
                ],
                'time_format' => [
                    'value' => 'H:i:s',
                    'type' => 'string',
                    'description' => 'Time format for display'
                ],
                'language' => [
                    'value' => 'id',
                    'type' => 'string',
                    'description' => 'Default language'
                ],
                'logo_path' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'Path to application logo'
                ]
            ],
            'email' => [
                'smtp_host' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'SMTP server hostname'
                ],
                'smtp_port' => [
                    'value' => '587',
                    'type' => 'integer',
                    'description' => 'SMTP server port'
                ],
                'smtp_user' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'SMTP username'
                ],
                'smtp_pass' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'SMTP password'
                ],
                'smtp_crypto' => [
                    'value' => 'tls',
                    'type' => 'string',
                    'description' => 'SMTP encryption method'
                ],
                'from_email' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'From email address'
                ],
                'from_name' => [
                    'value' => 'CBT Smart',
                    'type' => 'string',
                    'description' => 'From name for emails'
                ],
                'email_enabled' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Enable email functionality'
                ]
            ],
            'exam' => [
                'default_exam_duration' => [
                    'value' => '60',
                    'type' => 'integer',
                    'description' => 'Default exam duration in minutes'
                ],
                'max_exam_duration' => [
                    'value' => '180',
                    'type' => 'integer',
                    'description' => 'Maximum exam duration in minutes'
                ],
                'auto_submit_enabled' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Auto submit exam when time expires'
                ],
                'shuffle_questions' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Shuffle question order'
                ],
                'shuffle_options' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Shuffle answer options'
                ],
                'show_results_immediately' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Show results immediately after exam'
                ],
                'allow_review_answers' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Allow students to review answers'
                ],
                'max_attempts' => [
                    'value' => '1',
                    'type' => 'integer',
                    'description' => 'Maximum exam attempts per student'
                ],
                'passing_score' => [
                    'value' => '60',
                    'type' => 'integer',
                    'description' => 'Minimum passing score percentage'
                ],
                'lockdown_enabled' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Enable exam lockdown mode'
                ]
            ],
            'notification' => [
                'email_notifications' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Enable email notifications'
                ],
                'exam_start_notification' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Send notification when exam starts'
                ],
                'exam_end_notification' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Send notification when exam ends'
                ],
                'result_notification' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Send notification when results are available'
                ],
                'system_alert_notification' => [
                    'value' => true,
                    'type' => 'boolean',
                    'description' => 'Send system alert notifications'
                ],
                'notification_delay' => [
                    'value' => '5',
                    'type' => 'integer',
                    'description' => 'Notification delay in minutes'
                ],
                'digest_frequency' => [
                    'value' => 'daily',
                    'type' => 'string',
                    'description' => 'Digest email frequency'
                ]
            ],
            'maintenance' => [
                'maintenance_mode' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Enable maintenance mode'
                ],
                'maintenance_message' => [
                    'value' => 'System sedang dalam maintenance. Silakan coba lagi nanti.',
                    'type' => 'text',
                    'description' => 'Maintenance mode message'
                ],
                'maintenance_start_time' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'Scheduled maintenance start time'
                ],
                'maintenance_end_time' => [
                    'value' => '',
                    'type' => 'string',
                    'description' => 'Scheduled maintenance end time'
                ],
                'allowed_ips' => [
                    'value' => '',
                    'type' => 'text',
                    'description' => 'IP addresses allowed during maintenance (comma separated)'
                ],
                'auto_backup_enabled' => [
                    'value' => false,
                    'type' => 'boolean',
                    'description' => 'Enable automatic backup'
                ],
                'backup_frequency' => [
                    'value' => 'daily',
                    'type' => 'string',
                    'description' => 'Automatic backup frequency'
                ],
                'log_retention_days' => [
                    'value' => '90',
                    'type' => 'integer',
                    'description' => 'Log retention period in days'
                ]
            ]
        ];
    }

    /**
     * Validate setting value
     */
    public function validateSetting($category, $key, $value)
    {
        $setting = $this->where('category', $category)
            ->where('setting_key', $key)
            ->first();

        if (!$setting || !$setting['validation_rules']) {
            return true;
        }

        $validation = \Config\Services::validation();
        $validation->setRules(['value' => $setting['validation_rules']]);

        return $validation->run(['value' => $value]);
    }

    /**
     * Get setting schema for forms
     */
    public function getSettingSchema($category)
    {
        $defaultSettings = $this->getDefaultSettings();

        if (!isset($defaultSettings[$category])) {
            return [];
        }

        return $defaultSettings[$category];
    }

    /**
     * Export settings
     */
    public function exportSettings($categories = null)
    {
        $builder = $this->builder();

        if ($categories) {
            $builder->whereIn('category', $categories);
        }

        return $builder->orderBy('category, setting_key')->get()->getResultArray();
    }

    /**
     * Import settings
     */
    public function importSettings($settings)
    {
        $imported = 0;
        $errors = [];

        foreach ($settings as $setting) {
            try {
                $this->updateSetting(
                    $setting['category'],
                    $setting['setting_key'],
                    $setting['setting_value'],
                    $setting['setting_type'],
                    $setting['description'] ?? null
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Error importing {$setting['category']}.{$setting['setting_key']}: " . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'errors' => $errors
        ];
    }
}
