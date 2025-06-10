<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table = 'reports';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'report_type',
        'title',
        'description',
        'filters',
        'options',
        'file_path',
        'file_size',
        'format',
        'status',
        'generated_by',
        'scheduled_at',
        'completed_at',
        'error_message',
        'download_count',
        'expires_at',
        'is_public',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'report_type' => 'required|in_list[exam_results,student_performance,exam_analytics,attendance,system_usage,progress_tracking]',
        'title' => 'required|max_length[255]',
        'format' => 'required|in_list[pdf,excel,csv]',
        'status' => 'required|in_list[pending,generating,completed,failed,expired]',
        'generated_by' => 'required|integer'
    ];

    protected $validationMessages = [
        'report_type' => [
            'required' => 'Tipe laporan harus diisi',
            'in_list' => 'Tipe laporan tidak valid'
        ],
        'title' => [
            'required' => 'Judul laporan harus diisi',
            'max_length' => 'Judul laporan maksimal 255 karakter'
        ],
        'format' => [
            'required' => 'Format laporan harus diisi',
            'in_list' => 'Format laporan tidak valid'
        ],
        'status' => [
            'required' => 'Status laporan harus diisi',
            'in_list' => 'Status laporan tidak valid'
        ],
        'generated_by' => [
            'required' => 'ID pembuat laporan harus diisi',
            'integer' => 'ID pembuat laporan harus berupa angka'
        ]
    ];

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    /**
     * Before insert callback
     */
    protected function beforeInsert(array $data)
    {
        // Encode filters and options as JSON if they're arrays
        if (isset($data['data']['filters']) && is_array($data['data']['filters'])) {
            $data['data']['filters'] = json_encode($data['data']['filters']);
        }

        if (isset($data['data']['options']) && is_array($data['data']['options'])) {
            $data['data']['options'] = json_encode($data['data']['options']);
        }

        // Set default status if not provided
        if (!isset($data['data']['status'])) {
            $data['data']['status'] = 'pending';
        }

        return $data;
    }

    /**
     * Before update callback
     */
    protected function beforeUpdate(array $data)
    {
        // Encode filters and options as JSON if they're arrays
        if (isset($data['data']['filters']) && is_array($data['data']['filters'])) {
            $data['data']['filters'] = json_encode($data['data']['filters']);
        }

        if (isset($data['data']['options']) && is_array($data['data']['options'])) {
            $data['data']['options'] = json_encode($data['data']['options']);
        }

        return $data;
    }

    /**
     * After find callback to decode JSON fields
     */
    protected function afterFind(array $data)
    {
        if (isset($data['data'])) {
            // Single row
            $data['data'] = $this->decodeJsonFields($data['data']);
        } else {
            // Multiple rows
            foreach ($data as &$row) {
                $row = $this->decodeJsonFields($row);
            }
        }

        return $data;
    }

    /**
     * Decode JSON fields
     */
    private function decodeJsonFields($row)
    {
        if (isset($row['filters']) && is_string($row['filters'])) {
            $row['filters'] = json_decode($row['filters'], true) ?? [];
        }

        if (isset($row['options']) && is_string($row['options'])) {
            $row['options'] = json_decode($row['options'], true) ?? [];
        }

        return $row;
    }

    /**
     * Get reports by user
     */
    public function getReportsByUser($userId, $limit = null)
    {
        $builder = $this->builder();
        $builder->where('generated_by', $userId);
        $builder->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get reports by type
     */
    public function getReportsByType($reportType, $limit = null)
    {
        $builder = $this->builder();
        $builder->where('report_type', $reportType);
        $builder->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get reports by status
     */
    public function getReportsByStatus($status, $limit = null)
    {
        $builder = $this->builder();
        $builder->where('status', $status);
        $builder->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get pending reports for processing
     */
    public function getPendingReports($limit = 10)
    {
        return $this->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get expired reports
     */
    public function getExpiredReports()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))
            ->where('status', 'completed')
            ->findAll();
    }

    /**
     * Mark report as completed
     */
    public function markAsCompleted($reportId, $filePath, $fileSize = null)
    {
        $updateData = [
            'status' => 'completed',
            'file_path' => $filePath,
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if ($fileSize !== null) {
            $updateData['file_size'] = $fileSize;
        }

        return $this->update($reportId, $updateData);
    }

    /**
     * Mark report as failed
     */
    public function markAsFailed($reportId, $errorMessage = null)
    {
        $updateData = [
            'status' => 'failed',
            'completed_at' => date('Y-m-d H:i:s')
        ];

        if ($errorMessage) {
            $updateData['error_message'] = $errorMessage;
        }

        return $this->update($reportId, $updateData);
    }

    /**
     * Increment download count
     */
    public function incrementDownloadCount($reportId)
    {
        $builder = $this->builder();
        $builder->set('download_count', 'download_count + 1', false);
        $builder->where('id', $reportId);
        return $builder->update();
    }

    /**
     * Set expiration date
     */
    public function setExpirationDate($reportId, $days = 30)
    {
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$days} days"));
        return $this->update($reportId, ['expires_at' => $expiresAt]);
    }

    /**
     * Clean up expired reports
     */
    public function cleanupExpiredReports()
    {
        $expiredReports = $this->getExpiredReports();
        $deletedCount = 0;

        foreach ($expiredReports as $report) {
            // Delete file if exists
            if ($report['file_path']) {
                $filePath = WRITEPATH . $report['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }
            }

            // Delete from database
            if ($this->delete($report['id'])) {
                $deletedCount++;
            }
        }

        return $deletedCount;
    }

    /**
     * Get report statistics
     */
    public function getReportStatistics($startDate = null, $endDate = null)
    {
        $builder = $this->builder();

        if ($startDate) {
            $builder->where('created_at >=', $startDate);
        }

        if ($endDate) {
            $builder->where('created_at <=', $endDate);
        }

        // Total reports
        $totalReports = $builder->countAllResults(false);

        // Reports by status
        $statusStats = $builder
            ->select('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->getResultArray();

        // Reports by type
        $builder = $this->builder();
        if ($startDate) $builder->where('created_at >=', $startDate);
        if ($endDate) $builder->where('created_at <=', $endDate);

        $typeStats = $builder
            ->select('report_type, COUNT(*) as count')
            ->groupBy('report_type')
            ->get()
            ->getResultArray();

        // Reports by format
        $builder = $this->builder();
        if ($startDate) $builder->where('created_at >=', $startDate);
        if ($endDate) $builder->where('created_at <=', $endDate);

        $formatStats = $builder
            ->select('format, COUNT(*) as count')
            ->groupBy('format')
            ->get()
            ->getResultArray();

        // Average generation time for completed reports
        $builder = $this->builder();
        if ($startDate) $builder->where('created_at >=', $startDate);
        if ($endDate) $builder->where('created_at <=', $endDate);

        $avgGenerationTime = $builder
            ->select('AVG(TIMESTAMPDIFF(SECOND, created_at, completed_at)) as avg_seconds')
            ->where('status', 'completed')
            ->where('completed_at IS NOT NULL')
            ->get()
            ->getRowArray();

        return [
            'total_reports' => $totalReports,
            'by_status' => $statusStats,
            'by_type' => $typeStats,
            'by_format' => $formatStats,
            'avg_generation_time' => $avgGenerationTime['avg_seconds'] ?? 0
        ];
    }

    /**
     * Get popular report types
     */
    public function getPopularReportTypes($limit = 5, $days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->builder()
            ->select('report_type, COUNT(*) as count, SUM(download_count) as total_downloads')
            ->where('created_at >=', $startDate)
            ->groupBy('report_type')
            ->orderBy('count', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    /**
     * Get user report activity
     */
    public function getUserReportActivity($userId, $days = 30)
    {
        $startDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->builder()
            ->select('DATE(created_at) as date, COUNT(*) as reports_generated')
            ->where('generated_by', $userId)
            ->where('created_at >=', $startDate)
            ->groupBy('DATE(created_at)')
            ->orderBy('date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Schedule report generation
     */
    public function scheduleReport($reportData, $scheduledAt)
    {
        $reportData['status'] = 'pending';
        $reportData['scheduled_at'] = $scheduledAt;

        return $this->insert($reportData);
    }

    /**
     * Get scheduled reports ready for processing
     */
    public function getScheduledReportsReady()
    {
        return $this->where('status', 'pending')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->where('scheduled_at IS NOT NULL')
            ->orderBy('scheduled_at', 'ASC')
            ->findAll();
    }

    /**
     * Duplicate report with new filters
     */
    public function duplicateReport($reportId, $newFilters = null, $newOptions = null)
    {
        $originalReport = $this->find($reportId);

        if (!$originalReport) {
            return false;
        }

        $newReport = [
            'report_type' => $originalReport['report_type'],
            'title' => $originalReport['title'] . ' (Copy)',
            'description' => $originalReport['description'],
            'filters' => $newFilters ?? $originalReport['filters'],
            'options' => $newOptions ?? $originalReport['options'],
            'format' => $originalReport['format'],
            'status' => 'pending',
            'generated_by' => session()->get('user_id'),
            'is_public' => false
        ];

        return $this->insert($newReport);
    }

    /**
     * Get file size in human readable format
     */
    public function getHumanReadableFileSize($reportId)
    {
        $report = $this->find($reportId);

        if (!$report || !$report['file_size']) {
            return 'Unknown';
        }

        $bytes = $report['file_size'];
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if report file exists
     */
    public function reportFileExists($reportId)
    {
        $report = $this->find($reportId);

        if (!$report || !$report['file_path']) {
            return false;
        }

        $filePath = WRITEPATH . $report['file_path'];
        return file_exists($filePath);
    }

    /**
     * Update report file size
     */
    public function updateFileSize($reportId)
    {
        $report = $this->find($reportId);

        if (!$report || !$report['file_path']) {
            return false;
        }

        $filePath = WRITEPATH . $report['file_path'];

        if (file_exists($filePath)) {
            $fileSize = filesize($filePath);
            return $this->update($reportId, ['file_size' => $fileSize]);
        }

        return false;
    }
}
