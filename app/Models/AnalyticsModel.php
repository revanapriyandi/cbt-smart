<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalyticsModel extends Model
{
    protected $table = 'analytics_cache';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'cache_key',
        'cache_type',
        'cache_data',
        'parameters',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'cache_key' => 'required|max_length[255]',
        'cache_type' => 'required|in_list[dashboard,exam,user,system]',
        'cache_data' => 'required',
        'expires_at' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'cache_key' => [
            'required' => 'Cache key harus diisi',
            'max_length' => 'Cache key maksimal 255 karakter'
        ],
        'cache_type' => [
            'required' => 'Tipe cache harus diisi',
            'in_list' => 'Tipe cache tidak valid'
        ],
        'cache_data' => [
            'required' => 'Data cache harus diisi'
        ],
        'expires_at' => [
            'required' => 'Waktu kedaluwarsa harus diisi',
            'valid_date' => 'Format waktu tidak valid'
        ]
    ];

    protected $beforeInsert = ['beforeInsert'];
    protected $beforeUpdate = ['beforeUpdate'];

    /**
     * Before insert callback
     */
    protected function beforeInsert(array $data)
    {
        if (isset($data['data']['cache_data']) && is_array($data['data']['cache_data'])) {
            $data['data']['cache_data'] = json_encode($data['data']['cache_data']);
        }

        if (isset($data['data']['parameters']) && is_array($data['data']['parameters'])) {
            $data['data']['parameters'] = json_encode($data['data']['parameters']);
        }

        return $data;
    }

    /**
     * Before update callback
     */
    protected function beforeUpdate(array $data)
    {
        if (isset($data['data']['cache_data']) && is_array($data['data']['cache_data'])) {
            $data['data']['cache_data'] = json_encode($data['data']['cache_data']);
        }

        if (isset($data['data']['parameters']) && is_array($data['data']['parameters'])) {
            $data['data']['parameters'] = json_encode($data['data']['parameters']);
        }

        return $data;
    }

    /**
     * Get cached analytics data
     */
    public function getCachedData($cacheKey)
    {
        $cached = $this->where('cache_key', $cacheKey)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if ($cached) {
            $cached['cache_data'] = json_decode($cached['cache_data'], true);
            $cached['parameters'] = json_decode($cached['parameters'], true);
            return $cached;
        }

        return null;
    }

    /**
     * Store analytics data in cache
     */
    public function setCachedData($cacheKey, $cacheType, $data, $parameters = [], $ttl = 3600)
    {
        $expiresAt = date('Y-m-d H:i:s', time() + $ttl);

        // Delete existing cache with same key
        $this->where('cache_key', $cacheKey)->delete();

        // Insert new cache
        return $this->insert([
            'cache_key' => $cacheKey,
            'cache_type' => $cacheType,
            'cache_data' => $data,
            'parameters' => $parameters,
            'expires_at' => $expiresAt
        ]);
    }

    /**
     * Clear expired cache entries
     */
    public function clearExpiredCache()
    {
        return $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }

    /**
     * Clear cache by type
     */
    public function clearCacheByType($cacheType)
    {
        return $this->where('cache_type', $cacheType)->delete();
    }

    /**
     * Clear all cache
     */
    public function clearAllCache()
    {
        return $this->truncate();
    }

    /**
     * Get cache statistics
     */
    public function getCacheStats()
    {
        $builder = $this->builder();

        $stats = $builder
            ->select('cache_type, COUNT(*) as count, 
                     SUM(CASE WHEN expires_at > NOW() THEN 1 ELSE 0 END) as active_count,
                     SUM(CASE WHEN expires_at <= NOW() THEN 1 ELSE 0 END) as expired_count')
            ->groupBy('cache_type')
            ->get()
            ->getResultArray();

        return $stats;
    }

    /**
     * Performance analytics helper methods
     */

    /**
     * Calculate exam completion rates over time
     */
    public function getCompletionRatesTrend($startDate, $endDate, $groupBy = 'day')
    {
        $db = \Config\Database::connect();

        $dateFormat = match ($groupBy) {
            'hour' => '%Y-%m-%d %H:00:00',
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $query = "
            SELECT 
                DATE_FORMAT(ep.created_at, '{$dateFormat}') as period,
                COUNT(*) as total_attempts,
                SUM(CASE WHEN ep.status = 'completed' THEN 1 ELSE 0 END) as completed_attempts,
                (SUM(CASE WHEN ep.status = 'completed' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as completion_rate
            FROM exam_participants ep
            WHERE ep.created_at BETWEEN ? AND ?
            GROUP BY DATE_FORMAT(ep.created_at, '{$dateFormat}')
            ORDER BY period ASC
        ";

        return $db->query($query, [$startDate, $endDate])->getResultArray();
    }

    /**
     * Get score distribution analysis
     */
    public function getScoreDistribution($examId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('exam_results er');

        $builder->select("
            CASE 
                WHEN er.score >= 90 THEN 'A (90-100)'
                WHEN er.score >= 80 THEN 'B (80-89)'
                WHEN er.score >= 70 THEN 'C (70-79)'
                WHEN er.score >= 60 THEN 'D (60-69)'
                ELSE 'E (0-59)'
            END as grade_range,
            COUNT(*) as count,
            ROUND(AVG(er.score), 2) as avg_score_in_range
        ");

        if ($examId) {
            $builder->where('er.exam_id', $examId);
        }

        if ($startDate && $endDate) {
            $builder->where('er.created_at >=', $startDate);
            $builder->where('er.created_at <=', $endDate);
        }

        $builder->where('er.status', EXAM_STATUS_GRADED);
        $builder->groupBy('grade_range');
        $builder->orderBy('AVG(er.score)', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get user performance analytics
     */
    public function getUserPerformanceAnalytics($userId = null, $classId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->db->table('exam_results er');

        $builder->select("
            u.id as user_id,
            u.name as user_name,
            u.email as user_email,
            c.name as class_name,
            COUNT(er.id) as total_exams,
            AVG(er.score) as average_score,
            MAX(er.score) as highest_score,
            MIN(er.score) as lowest_score,
            STDDEV(er.score) as score_deviation,
            SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) as passed_exams,
            (SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) / COUNT(er.id)) * 100 as pass_rate
        ");

        $builder->join('users u', 'u.id = er.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');

        if ($userId) {
            $builder->where('u.id', $userId);
        }

        if ($classId) {
            $builder->where('u.class_id', $classId);
        }

        if ($startDate && $endDate) {
            $builder->where('er.created_at >=', $startDate);
            $builder->where('er.created_at <=', $endDate);
        }

        $builder->where('er.status', EXAM_STATUS_GRADED);
        $builder->groupBy(['u.id', 'u.name', 'u.email', 'c.name']);
        $builder->orderBy('average_score', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get subject performance comparison
     */
    public function getSubjectPerformanceComparison($startDate = null, $endDate = null)
    {
        $builder = $this->db->table('exam_results er');

        $builder->select("
            s.id as subject_id,
            s.name as subject_name,
            s.code as subject_code,
            COUNT(er.id) as total_attempts,
            AVG(er.score) as average_score,
            MAX(er.score) as highest_score,
            MIN(er.score) as lowest_score,
            STDDEV(er.score) as score_deviation,
            COUNT(DISTINCT er.user_id) as unique_students,
            SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) as passed_attempts,
            (SUM(CASE WHEN er.score >= 75 THEN 1 ELSE 0 END) / COUNT(er.id)) * 100 as pass_rate
        ");

        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('subjects s', 's.id = e.subject_id');

        if ($startDate && $endDate) {
            $builder->where('er.created_at >=', $startDate);
            $builder->where('er.created_at <=', $endDate);
        }

        $builder->where('er.status', EXAM_STATUS_GRADED);
        $builder->groupBy(['s.id', 's.name', 's.code']);
        $builder->orderBy('average_score', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get exam difficulty analysis
     */
    public function getExamDifficultyAnalysis($examId = null)
    {
        $builder = $this->db->table('exam_results er');

        $builder->select("
            e.id as exam_id,
            e.title as exam_title,
            e.total_questions,
            e.duration_minutes,
            COUNT(er.id) as total_attempts,
            AVG(er.score) as average_score,
            STDDEV(er.score) as score_deviation,
            MIN(er.score) as lowest_score,
            MAX(er.score) as highest_score,
            CASE 
                WHEN AVG(er.score) >= 85 THEN 'Easy'
                WHEN AVG(er.score) >= 70 THEN 'Moderate'
                WHEN AVG(er.score) >= 55 THEN 'Hard'
                ELSE 'Very Hard'
            END as difficulty_level
        ");

        $builder->join('exams e', 'e.id = er.exam_id');

        if ($examId) {
            $builder->where('e.id', $examId);
        }

        $builder->where('er.status', EXAM_STATUS_GRADED);
        $builder->groupBy(['e.id', 'e.title', 'e.total_questions', 'e.duration_minutes']);
        $builder->orderBy('average_score', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get time-based performance trends
     */
    public function getPerformanceTrends($period = 'daily', $startDate = null, $endDate = null)
    {
        $dateFormat = match ($period) {
            'hourly' => '%Y-%m-%d %H:00:00',
            'daily' => '%Y-%m-%d',
            'weekly' => '%Y-%u',
            'monthly' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        $builder = $this->db->table('exam_results er');

        $builder->select("
            DATE_FORMAT(er.created_at, '{$dateFormat}') as period,
            COUNT(er.id) as total_attempts,
            AVG(er.score) as average_score,
            MAX(er.score) as highest_score,
            MIN(er.score) as lowest_score,
            COUNT(DISTINCT er.user_id) as unique_users,
            COUNT(DISTINCT er.exam_id) as unique_exams
        ");

        if ($startDate && $endDate) {
            $builder->where('er.created_at >=', $startDate);
            $builder->where('er.created_at <=', $endDate);
        }

        $builder->where('er.status', EXAM_STATUS_GRADED);
        $builder->groupBy("DATE_FORMAT(er.created_at, '{$dateFormat}')");
        $builder->orderBy('period', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get system usage statistics
     */
    public function getSystemUsageStats($startDate = null, $endDate = null)
    {
        $stats = [];

        // Exam session statistics
        $builder = $this->db->table('exam_sessions es');
        $builder->select("
            COUNT(*) as total_sessions,
            SUM(CASE WHEN es.status = 'active' THEN 1 ELSE 0 END) as active_sessions,
            SUM(CASE WHEN es.status = 'completed' THEN 1 ELSE 0 END) as completed_sessions,
            SUM(CASE WHEN es.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_sessions,
            AVG(TIMESTAMPDIFF(MINUTE, es.started_at, es.ended_at)) as avg_session_duration
        ");

        if ($startDate && $endDate) {
            $builder->where('es.created_at >=', $startDate);
            $builder->where('es.created_at <=', $endDate);
        }

        $stats['sessions'] = $builder->get()->getRowArray();

        // User activity statistics
        $builder = $this->db->table('exam_participants ep');
        $builder->select("
            COUNT(*) as total_participants,
            COUNT(DISTINCT ep.user_id) as unique_users,
            SUM(CASE WHEN ep.status = 'completed' THEN 1 ELSE 0 END) as completed_participants,
            AVG(TIMESTAMPDIFF(MINUTE, ep.started_at, ep.finished_at)) as avg_exam_duration
        ");

        if ($startDate && $endDate) {
            $builder->where('ep.created_at >=', $startDate);
            $builder->where('ep.created_at <=', $endDate);
        }

        $stats['participants'] = $builder->get()->getRowArray();

        return $stats;
    }

    /**
     * Generate comprehensive analytics report
     */
    public function generateComprehensiveReport($filters = [])
    {
        $startDate = $filters['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $filters['end_date'] ?? date('Y-m-d');

        $report = [
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ],
            'overview' => $this->getSystemUsageStats($startDate, $endDate),
            'performance_trends' => $this->getPerformanceTrends('daily', $startDate, $endDate),
            'subject_performance' => $this->getSubjectPerformanceComparison($startDate, $endDate),
            'user_performance' => $this->getUserPerformanceAnalytics(null, null, $startDate, $endDate),
            'score_distribution' => $this->getScoreDistribution(null, $startDate, $endDate),
            'exam_difficulty' => $this->getExamDifficultyAnalysis(),
            'completion_rates' => $this->getCompletionRatesTrend($startDate, $endDate)
        ];

        return $report;
    }
}
