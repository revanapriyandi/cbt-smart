<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamResultModel extends Model
{
    protected $table = 'exam_results';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'exam_id',
        'student_id',
        'total_score',
        'max_total_score',
        'percentage',
        'status',
        'started_at',
        'submitted_at',
        'graded_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'exam_id' => 'required|integer',
        'student_id' => 'required|integer'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getResultsByExam($examId)
    {
        return $this->select('exam_results.*, users.full_name as student_name, users.username')
            ->join('users', 'users.id = exam_results.student_id')
            ->where('exam_results.exam_id', $examId)
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    public function getResultsByStudent($studentId)
    {
        return $this->select('exam_results.*, exams.title as exam_title, subjects.name as subject_name')
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->where('exam_results.student_id', $studentId)
            ->orderBy('exam_results.created_at', 'DESC')
            ->findAll();
    }

    public function getOrCreateResult($examId, $studentId)
    {
        $result = $this->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->first();

        if (!$result) {
            $data = [
                'exam_id' => $examId,
                'student_id' => $studentId,
                'status' => EXAM_STATUS_ONGOING,
                'started_at' => date('Y-m-d H:i:s')
            ];
            $this->insert($data);
            $result = $this->where('exam_id', $examId)
                ->where('student_id', $studentId)
                ->first();
        }

        return $result;
    }

    public function submitExam($examId, $studentId)
    {
        return $this->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->set([
                'status' => EXAM_STATUS_SUBMITTED,
                'submitted_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    public function updateScores($examId, $studentId, $totalScore, $maxTotalScore)
    {
        $percentage = $maxTotalScore > 0 ? ($totalScore / $maxTotalScore) * 100 : 0;

        return $this->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->set([
                'total_score' => $totalScore,
                'max_total_score' => $maxTotalScore,
                'percentage' => $percentage,
                'status' => EXAM_STATUS_GRADED,
                'graded_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    public function getAverageScore($examId)
    {
        $result = $this->select('AVG(percentage) as average_score')
            ->where('exam_id', $examId)
            ->where('status', EXAM_STATUS_GRADED)
            ->where('percentage IS NOT NULL')
            ->first();

        return $result ? round($result['average_score'], 1) : null;
    }
    public function getResultsWithDetails($filters = [])
    {
        $builder = $this->select('
                exam_results.*,
                users.full_name as student_name,
                users.username as student_username,
                users.email as student_email,
                exams.title as exam_title,
                exams.duration as exam_duration,
                subjects.name as subject_name,
                classes.name as class_name,
                exam_sessions.session_name,
                exam_sessions.start_time as session_start_time,
                exam_sessions.end_time as session_end_time
            ')
            ->join('users', 'users.id = exam_results.student_id')
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('user_classes', 'user_classes.user_id = users.id', 'left')
            ->join('classes', 'classes.id = user_classes.class_id', 'left')
            ->join('exam_sessions', 'exam_sessions.exam_id = exams.id', 'left');

        // Apply filters
        if (!empty($filters['exam_id'])) {
            $builder->where('exam_results.exam_id', $filters['exam_id']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('user_classes.class_id', $filters['class_id']);
        }

        if (!empty($filters['session_id'])) {
            $builder->where('exam_sessions.id', $filters['session_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('exam_results.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('exam_results.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('exam_results.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['score_min'])) {
            $builder->where('exam_results.percentage >=', $filters['score_min']);
        }

        if (!empty($filters['score_max'])) {
            $builder->where('exam_results.percentage <=', $filters['score_max']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('users.full_name', $filters['search'])
                ->orLike('users.username', $filters['search'])
                ->orLike('users.email', $filters['search'])
                ->orLike('exams.title', $filters['search'])
                ->orLike('subjects.name', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('exam_results.created_at', 'DESC')->findAll();
    }
    public function getResultWithDetails($id)
    {
        return $this->select('
                exam_results.*,
                users.full_name as student_name,
                users.username as student_username,
                users.email as student_email,
                users.phone as student_phone,
                exams.title as exam_title,
                exams.description as exam_description,
                exams.duration as exam_duration,
                exams.total_questions,
                subjects.name as subject_name,
                classes.name as class_name,
                exam_sessions.session_name,
                exam_sessions.start_time as session_start_time,
                exam_sessions.end_time as session_end_time
            ')
            ->join('users', 'users.id = exam_results.student_id')
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('user_classes', 'user_classes.user_id = users.id', 'left')
            ->join('classes', 'classes.id = user_classes.class_id', 'left')
            ->join('exam_sessions', 'exam_sessions.exam_id = exams.id', 'left')
            ->where('exam_results.id', $id)
            ->first();
    }
    public function getResultStatistics($filters = [])
    {
        $builder = $this->select(
                "COUNT(*) as total_results,
                COUNT(CASE WHEN exam_results.status = '" . EXAM_STATUS_GRADED . "' THEN 1 END) as graded_count,
                COUNT(CASE WHEN exam_results.status = '" . EXAM_STATUS_ONGOING . "' THEN 1 END) as ongoing_count,
                COUNT(CASE WHEN exam_results.status = '" . EXAM_STATUS_SUBMITTED . "' THEN 1 END) as submitted_count,
                AVG(CASE WHEN exam_results.status = '" . EXAM_STATUS_GRADED . "' AND exam_results.percentage IS NOT NULL THEN exam_results.percentage END) as average_score,
                MAX(CASE WHEN exam_results.status = '" . EXAM_STATUS_GRADED . "' AND exam_results.percentage IS NOT NULL THEN exam_results.percentage END) as highest_score,
                MIN(CASE WHEN exam_results.status = '" . EXAM_STATUS_GRADED . "' AND exam_results.percentage IS NOT NULL THEN exam_results.percentage END) as lowest_score,
                COUNT(CASE WHEN exam_results.percentage >= 80 THEN 1 END) as excellent_count,
                COUNT(CASE WHEN exam_results.percentage >= 70 AND exam_results.percentage < 80 THEN 1 END) as good_count,
                COUNT(CASE WHEN exam_results.percentage >= 60 AND exam_results.percentage < 70 THEN 1 END) as satisfactory_count,
                COUNT(CASE WHEN exam_results.percentage < 60 THEN 1 END) as needs_improvement_count"
            )->join('users', 'users.id = exam_results.student_id')
            ->join('exams', 'exams.id = exam_results.exam_id')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('user_classes', 'user_classes.user_id = users.id', 'left')
            ->join('classes', 'classes.id = user_classes.class_id', 'left')
            ->join('exam_sessions', 'exam_sessions.exam_id = exams.id', 'left');

        // Apply same filters as getResultsWithDetails
        if (!empty($filters['exam_id'])) {
            $builder->where('exam_results.exam_id', $filters['exam_id']);
        }
        if (!empty($filters['class_id'])) {
            $builder->where('user_classes.class_id', $filters['class_id']);
        }

        if (!empty($filters['session_id'])) {
            $builder->where('exam_sessions.id', $filters['session_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('exam_results.status', $filters['status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('exam_results.created_at >=', $filters['date_from'] . ' 00:00:00');
        }

        if (!empty($filters['date_to'])) {
            $builder->where('exam_results.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        if (!empty($filters['score_min'])) {
            $builder->where('exam_results.percentage >=', $filters['score_min']);
        }

        if (!empty($filters['score_max'])) {
            $builder->where('exam_results.percentage <=', $filters['score_max']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('users.full_name', $filters['search'])
                ->orLike('users.username', $filters['search'])
                ->orLike('users.email', $filters['search'])
                ->orLike('exams.title', $filters['search'])
                ->orLike('subjects.name', $filters['search'])
                ->groupEnd();
        }

        $result = $builder->first();

        // Convert to more readable format and add calculated fields
        return [
            'total_results' => (int) $result['total_results'],
            'graded_count' => (int) $result['graded_count'],
            'ongoing_count' => (int) $result['ongoing_count'],
            'submitted_count' => (int) $result['submitted_count'],
            'average_score' => $result['average_score'] ? round($result['average_score'], 1) : 0,
            'highest_score' => $result['highest_score'] ? round($result['highest_score'], 1) : 0,
            'lowest_score' => $result['lowest_score'] ? round($result['lowest_score'], 1) : 0,
            'excellent_count' => (int) $result['excellent_count'],
            'good_count' => (int) $result['good_count'],
            'satisfactory_count' => (int) $result['satisfactory_count'],
            'needs_improvement_count' => (int) $result['needs_improvement_count'],
            'completion_rate' => $result['total_results'] > 0 ? round(($result['graded_count'] / $result['total_results']) * 100, 1) : 0
        ];
    }

    /**
     * Get detailed answers for a specific result
     */
    public function getDetailedAnswers($resultId)
    {
        $result = $this->find($resultId);
        if (!$result) {
            return [];
        }

        $builder = $this->db->table('student_answers sa');
        $builder->select('
            sa.*,
            q.question_text,
            q.question_type,
            q.max_score,
            q.options,
            q.correct_answer
        ');
        $builder->join('questions q', 'q.id = sa.question_id', 'left');
        $builder->where('sa.exam_id', $result['exam_id']);
        $builder->where('sa.student_id', $result['student_id']);
        $builder->orderBy('sa.question_number', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get performance analysis for a specific result
     */
    public function getPerformanceAnalysis($resultId)
    {
        $result = $this->find($resultId);
        if (!$result) {
            return [];
        }

        $answers = $this->getDetailedAnswers($resultId);

        $analysis = [
            'total_questions' => count($answers),
            'answered_questions' => count(array_filter($answers, fn($a) => !empty($a['answer_text']))),
            'correct_answers' => 0,
            'by_type' => [
                'multiple_choice' => ['total' => 0, 'correct' => 0],
                'essay' => ['total' => 0, 'graded' => 0],
                'true_false' => ['total' => 0, 'correct' => 0],
                'fill_blank' => ['total' => 0, 'correct' => 0]
            ],
            'score_distribution' => [],
            'time_spent' => 0
        ];

        foreach ($answers as $answer) {
            $type = $answer['question_type'] ?? 'multiple_choice';
            if (isset($analysis['by_type'][$type])) {
                $analysis['by_type'][$type]['total']++;

                if ($type === 'essay') {
                    if (!empty($answer['final_score'])) {
                        $analysis['by_type'][$type]['graded']++;
                    }
                } else {
                    if ($answer['is_correct'] ?? false) {
                        $analysis['by_type'][$type]['correct']++;
                        $analysis['correct_answers']++;
                    }
                }
            }
        }

        // Calculate time spent if timestamps are available
        if ($result['started_at'] && $result['submitted_at']) {
            $start = new \DateTime($result['started_at']);
            $end = new \DateTime($result['submitted_at']);
            $analysis['time_spent'] = $end->getTimestamp() - $start->getTimestamp();
        }

        return $analysis;
    }

    /**
     * Get analytics data for admin dashboard
     */
    public function getAnalyticsData($filters = [])
    {
        $builder = $this->db->table('exam_results er');
        $builder->select('
            COUNT(*) as total_attempts,
            AVG(CASE WHEN er.status = "graded" THEN er.percentage END) as avg_score,
            COUNT(CASE WHEN er.status = "graded" THEN 1 END) as completed_count,
            COUNT(CASE WHEN er.percentage >= 80 THEN 1 END) as excellent_count,
            COUNT(CASE WHEN er.percentage >= 60 AND er.percentage < 80 THEN 1 END) as good_count,
            COUNT(CASE WHEN er.percentage < 60 THEN 1 END) as poor_count
        ');

        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('users u', 'u.id = er.student_id');

        // Apply filters
        if (!empty($filters['exam_id'])) {
            $builder->where('er.exam_id', $filters['exam_id']);
        }
        if (!empty($filters['class_id'])) {
            $builder->join('user_classes uc', 'uc.user_id = u.id');
            $builder->where('uc.class_id', $filters['class_id']);
        }
        if (!empty($filters['date_from'])) {
            $builder->where('er.created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('er.created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $result = $builder->get()->getRowArray();

        return [
            'total_attempts' => (int) $result['total_attempts'],
            'avg_score' => $result['avg_score'] ? round($result['avg_score'], 1) : 0,
            'completed_count' => (int) $result['completed_count'],
            'excellent_count' => (int) $result['excellent_count'],
            'good_count' => (int) $result['good_count'],
            'poor_count' => (int) $result['poor_count'],
            'completion_rate' => $result['total_attempts'] > 0 ? round(($result['completed_count'] / $result['total_attempts']) * 100, 1) : 0
        ];
    }

    /**
     * Get comparison data for analytics
     */
    public function getComparisonData($filters = [])
    {
        // Get monthly comparison data
        $builder = $this->db->table('exam_results er');
        $builder->select('
            DATE_FORMAT(er.created_at, "%Y-%m") as month,
            COUNT(*) as total_attempts,
            AVG(CASE WHEN er.status = "graded" THEN er.percentage END) as avg_score,
            COUNT(CASE WHEN er.status = "graded" THEN 1 END) as completed_count
        ');

        $builder->join('exams e', 'e.id = er.exam_id');
        $builder->join('users u', 'u.id = er.student_id');

        // Apply filters
        if (!empty($filters['exam_id'])) {
            $builder->where('er.exam_id', $filters['exam_id']);
        }
        if (!empty($filters['class_id'])) {
            $builder->join('user_classes uc', 'uc.user_id = u.id');
            $builder->where('uc.class_id', $filters['class_id']);
        }

        $builder->where('er.created_at >=', date('Y-m-d', strtotime('-6 months')) . ' 00:00:00');
        $builder->groupBy('DATE_FORMAT(er.created_at, "%Y-%m")');
        $builder->orderBy('month', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get results that need grading (essay questions)
     */
    public function getPendingGrading()
    {
        $builder = $this->db->table('student_answers sa');
        $builder->select('
            sa.*,
            er.id as result_id,
            u.full_name as student_name,
            u.username as student_username,
            e.title as exam_title,
            q.question_text,
            q.max_score
        ');

        $builder->join('exam_results er', 'er.exam_id = sa.exam_id AND er.student_id = sa.student_id');
        $builder->join('users u', 'u.id = sa.student_id');
        $builder->join('exams e', 'e.id = sa.exam_id');
        $builder->join('questions q', 'q.id = sa.question_id', 'left');

        $builder->where('q.question_type', 'essay');
        $builder->where('sa.final_score IS NULL');
        $builder->where('sa.answer_text IS NOT NULL');
        $builder->where('sa.answer_text !=', '');

        $builder->orderBy('er.submitted_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get essay answers for a specific result
     */
    public function getEssayAnswers($resultId)
    {
        $result = $this->find($resultId);
        if (!$result) {
            return [];
        }

        $builder = $this->db->table('student_answers sa');
        $builder->select('
            sa.*,
            q.question_text,
            q.max_score,
            q.question_number
        ');

        $builder->join('questions q', 'q.id = sa.question_id', 'left');
        $builder->where('sa.exam_id', $result['exam_id']);
        $builder->where('sa.student_id', $result['student_id']);
        $builder->where('q.question_type', 'essay');
        $builder->orderBy('q.question_number', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Update answer grade (for essay grading)
     */
    public function updateAnswerGrade($answerId, $updateData)
    {
        return $this->db->table('student_answers')
            ->where('id', $answerId)
            ->update($updateData);
    }

    /**
     * Recalculate total score for a result
     */
    public function recalculateScore($resultId)
    {
        $result = $this->find($resultId);
        if (!$result) {
            return false;
        }

        // Get all answers for this result
        $builder = $this->db->table('student_answers sa');
        $builder->select('
            COALESCE(sa.final_score, sa.ai_score, sa.manual_score, 0) as score,
            q.max_score
        ');
        $builder->join('questions q', 'q.id = sa.question_id', 'left');
        $builder->where('sa.exam_id', $result['exam_id']);
        $builder->where('sa.student_id', $result['student_id']);

        $answers = $builder->get()->getResultArray();

        $totalScore = 0;
        $maxTotalScore = 0;

        foreach ($answers as $answer) {
            $totalScore += (float) $answer['score'];
            $maxTotalScore += (float) $answer['max_score'];
        }

        $percentage = $maxTotalScore > 0 ? ($totalScore / $maxTotalScore) * 100 : 0;

        return $this->update($resultId, [
            'total_score' => $totalScore,
            'max_total_score' => $maxTotalScore,
            'percentage' => round($percentage, 2),
            'status' => EXAM_STATUS_GRADED,
            'graded_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Bulk delete results
     */
    public function bulkDelete($resultIds)
    {
        if (empty($resultIds)) {
            return false;
        }

        return $this->whereIn('id', $resultIds)->delete();
    }

    /**
     * Bulk publish results
     */
    public function bulkPublish($resultIds)
    {
        if (empty($resultIds)) {
            return false;
        }

        return $this->whereIn('id', $resultIds)
            ->set(['is_published' => 1, 'published_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    /**
     * Bulk unpublish results
     */
    public function bulkUnpublish($resultIds)
    {
        if (empty($resultIds)) {
            return false;
        }

        return $this->whereIn('id', $resultIds)
            ->set(['is_published' => 0, 'published_at' => null])
            ->update();
    }

    /**
     * Bulk recalculate scores
     */
    public function bulkRecalculate($resultIds)
    {
        if (empty($resultIds)) {
            return false;
        }

        $success = true;
        foreach ($resultIds as $resultId) {
            if (!$this->recalculateScore($resultId)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Generate report data
     */
    public function generateReport($examId, $classId = null, $reportType = 'summary')
    {
        $builder = $this->select('
            exam_results.*,
            users.full_name as student_name,
            users.username as student_username,
            exams.title as exam_title,
            subjects.name as subject_name
        ');

        $builder->join('users', 'users.id = exam_results.student_id');
        $builder->join('exams', 'exams.id = exam_results.exam_id');
        $builder->join('subjects', 'subjects.id = exams.subject_id');

        $builder->where('exam_results.exam_id', $examId);

        if ($classId) {
            $builder->join('user_classes', 'user_classes.user_id = users.id');
            $builder->where('user_classes.class_id', $classId);
        }

        $builder->orderBy('users.full_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * Publish session results
     */
    public function publishSessionResults($sessionId, $message = null)
    {
        $builder = $this->db->table('exam_results er');
        $builder->join('exam_sessions es', 'es.exam_id = er.exam_id');
        $builder->where('es.id', $sessionId);

        return $builder->update([
            'is_published' => 1,
            'published_at' => date('Y-m-d H:i:s'),
            'publish_message' => $message
        ]);
    }

    /**
     * Get session results
     */
    public function getSessionResults($sessionId)
    {
        $builder = $this->select('
            exam_results.*,
            users.full_name as student_name,
            users.username as student_username
        ');

        $builder->join('users', 'users.id = exam_results.student_id');
        $builder->join('exam_sessions es', 'es.exam_id = exam_results.exam_id');
        $builder->where('es.id', $sessionId);
        $builder->orderBy('users.full_name', 'ASC');

        return $builder->get()->getResultArray();
    }
}
