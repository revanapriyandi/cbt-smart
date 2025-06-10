<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamParticipantModel extends Model
{
    protected $table = 'exam_participants';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'exam_session_id',
        'user_id',
        'start_time',
        'end_time',
        'status',
        'current_question',
        'answered_count',
        'score',
        'last_activity',
        'ip_address',
        'user_agent',
        'termination_reason',
        'terminated_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;
    protected $validationRules = [
        'exam_session_id' => 'required|is_natural_no_zero',
        'user_id' => 'required|is_natural_no_zero',
        'status' => 'required|in_list[not_started,in_progress,completed,terminated,submitted]'
    ];

    public function getParticipantWithDetails($id)
    {
        $builder = $this->db->table($this->table . ' ep');
        $builder->select('
            ep.*,
            u.name as student_name,
            u.email as student_email,
            u.student_id,
            es.session_name,
            es.start_time as session_start,
            es.end_time as session_end,
            e.title as exam_title,
            e.duration as exam_duration,
            c.name as class_name
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->join('classes c', 'c.id = es.class_id');
        $builder->where('ep.id', $id);

        return $builder->get()->getRow();
    }

    public function getParticipantsBySession($sessionId)
    {
        $builder = $this->db->table($this->table . ' ep');
        $builder->select('
            ep.*,
            u.name as student_name,
            u.email as student_email,
            u.student_id,
            c.name as class_name
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('classes c', 'c.id = u.class_id', 'left');
        $builder->where('ep.exam_session_id', $sessionId);
        $builder->orderBy('u.name', 'ASC');

        return $builder->get()->getResult();
    }

    public function getActiveParticipants()
    {
        $builder = $this->db->table($this->table . ' ep');
        $builder->select('
            ep.*,
            u.name as student_name,
            u.student_id,
            es.session_name,
            e.title as exam_title
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->where('ep.status', 'in_progress');
        $builder->orderBy('ep.last_activity', 'DESC');

        return $builder->get()->getResult();
    }

    public function getCompletedParticipants($dateFrom = null, $dateTo = null)
    {
        $builder = $this->db->table($this->table . ' ep');
        $builder->select('
            ep.*,
            u.name as student_name,
            u.student_id,
            es.session_name,
            e.title as exam_title
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->where('ep.status', 'completed');

        if ($dateFrom) {
            $builder->where('DATE(ep.end_time) >=', $dateFrom);
        }

        if ($dateTo) {
            $builder->where('DATE(ep.end_time) <=', $dateTo);
        }

        $builder->orderBy('ep.end_time', 'DESC');

        return $builder->get()->getResult();
    }

    public function getParticipantStatistics($participantId)
    {
        $participant = $this->find($participantId);
        if (!$participant) {
            return null;
        }

        $stats = [];

        // Basic info
        $stats['basic'] = $participant;

        // Time statistics
        if ($participant['start_time'] && $participant['end_time']) {
            $startTime = strtotime($participant['start_time']);
            $endTime = strtotime($participant['end_time']);
            $stats['duration'] = $endTime - $startTime; // in seconds
        } else {
            $stats['duration'] = 0;
        }

        // Answer statistics
        $builder = $this->db->table('exam_answers');
        $builder->where('exam_participant_id', $participantId);
        $stats['total_answers'] = $builder->countAllResults(false);
        $stats['correct_answers'] = $builder->where('is_correct', 1)->countAllResults();

        // Activity count
        $builder = $this->db->table('exam_activities');
        $builder->where('exam_participant_id', $participantId);
        $stats['activity_count'] = $builder->countAllResults();

        // Flags
        $builder = $this->db->table('participant_flags');
        $builder->where('participant_id', $participantId);
        $stats['flags'] = $builder->get()->getResult();

        return $stats;
    }

    public function updateLastActivity($participantId, $activityType = 'general')
    {
        $data = [
            'last_activity' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Log activity
        $activityData = [
            'exam_participant_id' => $participantId,
            'activity_type' => $activityType,
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('exam_activities')->insert($activityData);

        return $this->update($participantId, $data);
    }

    public function startExam($participantId, $ipAddress, $userAgent)
    {
        $data = [
            'status' => 'in_progress',
            'start_time' => date('Y-m-d H:i:s'),
            'last_activity' => date('Y-m-d H:i:s'),
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($participantId, $data);
    }

    public function finishExam($participantId, $finalScore)
    {
        $data = [
            'status' => 'completed',
            'end_time' => date('Y-m-d H:i:s'),
            'score' => $finalScore,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($participantId, $data);
    }

    public function terminateExam($participantId, $reason, $terminatedBy)
    {
        $data = [
            'status' => 'terminated',
            'end_time' => date('Y-m-d H:i:s'),
            'termination_reason' => $reason,
            'terminated_by' => $terminatedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        return $this->update($participantId, $data);
    }

    public function getDashboardStats()
    {
        $stats = [];

        // Active participants
        $stats['active'] = $this->where('status', 'in_progress')->countAllResults();

        // Completed today
        $stats['completed_today'] = $this->where('status', 'completed')
            ->where('DATE(end_time)', date('Y-m-d'))
            ->countAllResults();

        // Average score today
        $builder = $this->db->table($this->table);
        $builder->selectAvg('score');
        $builder->where('status', 'completed');
        $builder->where('DATE(end_time)', date('Y-m-d'));
        $result = $builder->get()->getRow();
        $stats['avg_score_today'] = $result ? round($result->score, 2) : 0;

        // Terminated today
        $stats['terminated_today'] = $this->where('status', 'terminated')
            ->where('DATE(end_time)', date('Y-m-d'))
            ->countAllResults();

        return $stats;
    }

    public function getParticipantsByScoreRange($minScore, $maxScore, $sessionId = null)
    {
        $builder = $this->db->table($this->table . ' ep');
        $builder->select('
            ep.*,
            u.name as student_name,
            u.student_id,
            es.session_name
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->where('ep.status', 'completed');
        $builder->where('ep.score >=', $minScore);
        $builder->where('ep.score <=', $maxScore);

        if ($sessionId) {
            $builder->where('ep.exam_session_id', $sessionId);
        }

        $builder->orderBy('ep.score', 'DESC');

        return $builder->get()->getResult();
    }

    public function getTimeBasedStatistics($sessionId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('
            AVG(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as avg_duration,
            MIN(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as min_duration,
            MAX(TIMESTAMPDIFF(MINUTE, start_time, end_time)) as max_duration,
            COUNT(*) as total_completed
        ');
        $builder->where('exam_session_id', $sessionId);
        $builder->where('status', 'completed');
        $builder->where('start_time IS NOT NULL');
        $builder->where('end_time IS NOT NULL');

        return $builder->get()->getRow();
    }

    public function getFlaggedParticipants($sessionId = null)
    {
        $builder = $this->db->table('participant_flags pf');
        $builder->select('
            pf.*,
            ep.user_id,
            u.name as student_name,
            u.student_id,
            es.session_name,
            flagged_by_user.name as flagged_by_name
        ');
        $builder->join($this->table . ' ep', 'ep.id = pf.participant_id');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->join('users flagged_by_user', 'flagged_by_user.id = pf.flagged_by');

        if ($sessionId) {
            $builder->where('ep.exam_session_id', $sessionId);
        }

        $builder->orderBy('pf.flagged_at', 'DESC');

        return $builder->get()->getResult();
    }

    public function getSuspiciousActivities($timeRange = '1 hour')
    {
        $builder = $this->db->table('exam_activities ea');
        $builder->select('
            ea.*,
            ep.user_id,
            u.name as student_name,
            u.student_id,
            es.session_name
        ');
        $builder->join($this->table . ' ep', 'ep.id = ea.exam_participant_id');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->join('exam_sessions es', 'es.id = ep.exam_session_id');
        $builder->where('ea.created_at >=', date('Y-m-d H:i:s', strtotime("-{$timeRange}")));
        $builder->whereIn('ea.activity_type', ['tab_switch', 'window_blur', 'copy_attempt', 'paste_attempt', 'right_click']);
        $builder->orderBy('ea.created_at', 'DESC');

        return $builder->get()->getResult();
    }

    public function getParticipantAnswerHistory($participantId)
    {
        $builder = $this->db->table('exam_answers ea');
        $builder->select('
            ea.*,
            q.question_text,
            q.question_type,
            qo.option_text as selected_option
        ');
        $builder->join('questions q', 'q.id = ea.question_id');
        $builder->join('question_options qo', 'qo.id = ea.selected_option_id', 'left');
        $builder->where('ea.exam_participant_id', $participantId);
        $builder->orderBy('ea.answered_at', 'ASC');

        return $builder->get()->getResult();
    }

    public function bulkUpdateStatus($participantIds, $status, $reason = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($status === 'terminated' && $reason) {
            $data['termination_reason'] = $reason;
            $data['terminated_by'] = session()->get('user_id');
            $data['end_time'] = date('Y-m-d H:i:s');
        }

        return $this->whereIn('id', $participantIds)->set($data)->update();
    }

    public function getParticipantProgress($participantId)
    {
        $participant = $this->find($participantId);
        if (!$participant) {
            return null;
        }

        // Get total questions for this exam
        $builder = $this->db->table('exam_sessions es');
        $builder->select('e.total_questions');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->where('es.id', $participant['exam_session_id']);
        $examInfo = $builder->get()->getRow();

        $totalQuestions = $examInfo ? $examInfo->total_questions : 0;

        // Get answered questions count
        $builder = $this->db->table('exam_answers');
        $builder->where('exam_participant_id', $participantId);
        $answeredCount = $builder->countAllResults();

        // Calculate progress percentage
        $progressPercentage = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100, 2) : 0;

        return [
            'total_questions' => $totalQuestions,
            'answered_count' => $answeredCount,
            'current_question' => $participant['current_question'],
            'progress_percentage' => $progressPercentage,
            'status' => $participant['status']
        ];
    }
}
