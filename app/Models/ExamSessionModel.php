<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamSessionModel extends Model
{
    protected $table = 'exam_sessions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'exam_id',
        'class_id',
        'session_name',
        'start_time',
        'end_time',
        'max_participants',
        'room_location',
        'instructions',
        'security_settings',
        'status',
        'actual_start_time',
        'actual_end_time',
        'created_by',
        'created_at',
        'updated_at'
    ];
    protected $useTimestamps = false;
    protected $validationRules = [
        'exam_id' => 'required|is_natural_no_zero',
        'class_id' => 'required|is_natural_no_zero',
        'session_name' => 'required|max_length[100]',
        'start_time' => 'required|valid_date[Y-m-d H:i:s]',
        'end_time' => 'required|valid_date[Y-m-d H:i:s]',
        'max_participants' => 'required|is_natural_no_zero',
        'status' => 'required|in_list[scheduled,active,completed,cancelled]'
    ];

    public function getSessionsWithDetails($filters = [])
    {
        $builder = $this->db->table($this->table . ' es');
        $builder->select('
            es.*,
            e.title as exam_title,
            e.duration as exam_duration,
            c.name as class_name,
            c.level as class_level,
            u.full_name as creator_name,
            COUNT(DISTINCT ep.id) as participant_count,
            COUNT(DISTINCT CASE WHEN ep.status = "completed" THEN ep.id END) as completed_count
        ');
        $builder->join('exams e', 'e.id = es.exam_id', 'left');
        $builder->join('classes c', 'c.id = es.class_id', 'left');
        $builder->join('users u', 'u.id = es.created_by', 'left');
        $builder->join('exam_participants ep', 'ep.exam_session_id = es.id', 'left');

        // Apply filters
        if (!empty($filters['status'])) {
            $builder->where('es.status', $filters['status']);
        }

        if (!empty($filters['exam_id'])) {
            $builder->where('es.exam_id', $filters['exam_id']);
        }

        if (!empty($filters['class_id'])) {
            $builder->where('es.class_id', $filters['class_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('DATE(es.start_time) >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('DATE(es.start_time) <=', $filters['date_to']);
        }

        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('es.session_name', $filters['search']);
            $builder->orLike('e.title', $filters['search']);
            $builder->orLike('c.name', $filters['search']);
            $builder->orLike('es.room_location', $filters['search']);
            $builder->groupEnd();
        }

        $builder->groupBy('es.id');
        $builder->orderBy('es.start_time', 'DESC');

        return $builder->get()->getResult();
    }

    public function getSessionWithDetails($id)
    {
        $builder = $this->db->table($this->table . ' es');
        $builder->select('
            es.*,
            e.title as exam_title,
            e.description as exam_description,
            e.duration as exam_duration,
            e.total_questions,
            c.name as class_name,
            c.level as class_level,
            u.full_name as creator_name,
            COUNT(DISTINCT ep.id) as participant_count,
            COUNT(DISTINCT CASE WHEN ep.status = "completed" THEN ep.id END) as completed_count,
            COUNT(DISTINCT CASE WHEN ep.status = "in_progress" THEN ep.id END) as active_count
        ');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->join('classes c', 'c.id = es.class_id');
        $builder->join('users u', 'u.id = es.created_by');
        $builder->join('exam_participants ep', 'ep.exam_session_id = es.id', 'left');
        $builder->where('es.id', $id);
        $builder->groupBy('es.id');

        return $builder->get()->getRow();
    }

    public function getSessionStatistics()
    {
        $builder = $this->db->table($this->table);

        // Total sessions
        $totalSessions = $builder->countAllResults(false);

        // Sessions by status
        $scheduled = $builder->where('status', 'scheduled')->countAllResults(false);
        $active = $builder->where('status', 'active')->countAllResults(false);
        $completed = $builder->where('status', 'completed')->countAllResults(false);

        // Today's sessions
        $todaySessions = $builder->where('DATE(start_time)', date('Y-m-d'))->countAllResults(false);

        // This week's sessions
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));
        $weekSessions = $builder->where('DATE(start_time) >=', $weekStart)
            ->where('DATE(start_time) <=', $weekEnd)
            ->countAllResults();

        return [
            'total_sessions' => $totalSessions,
            'scheduled' => $scheduled,
            'active' => $active,
            'completed' => $completed,
            'today_sessions' => $todaySessions,
            'week_sessions' => $weekSessions
        ];
    }

    public function getSessionParticipants($sessionId)
    {
        $builder = $this->db->table('exam_participants ep');
        $builder->select('
            ep.*,
            u.full_name as student_name,
            u.username as student_nis,
            COALESCE(
                (SELECT COUNT(*) FROM student_answers sa 
                    WHERE sa.exam_id = ep.exam_id AND sa.student_id = ep.user_id AND TRIM(sa.answer_text) != "") * 100 / 
                (SELECT COUNT(*) FROM exam_questions WHERE exam_id = ep.exam_id), 0
            ) as progress
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->where('ep.exam_session_id', $sessionId);
        $builder->orderBy('u.full_name', 'ASC');

        return $builder->get()->getResult();
    }

    public function getSessionProgress($sessionId)
    {
        $builder = $this->db->table('exam_participants ep');
        $builder->select('
            COUNT(*) as total_participants,
            COUNT(CASE WHEN ep.status = "not_started" THEN 1 END) as not_started,
            COUNT(CASE WHEN ep.status = "in_progress" THEN 1 END) as in_progress,
            COUNT(CASE WHEN ep.status = "completed" THEN 1 END) as completed,
            COUNT(CASE WHEN ep.status = "absent" THEN 1 END) as absent,            ROUND(AVG(
                COALESCE(
                    (SELECT COUNT(*) FROM student_answers sa 
                        WHERE sa.exam_id = ep.exam_id AND sa.student_id = ep.user_id AND TRIM(sa.answer_text) != "") * 100 / 
                    (SELECT COUNT(*) FROM exam_questions WHERE exam_id = ep.exam_id), 0
                )
            ), 2) as overall_progress
        ');
        $builder->where('ep.exam_session_id', $sessionId);

        $result = $builder->get()->getRow();

        return $result ?: (object)[
            'total_participants' => 0,
            'not_started' => 0,
            'in_progress' => 0,
            'completed' => 0,
            'absent' => 0,
            'overall_progress' => 0
        ];
    }

    public function getSessionActivities($sessionId, $limit = 50)
    {
        $builder = $this->db->table('user_activity_logs ual');
        $builder->select('
            ual.*,
            u.full_name as user_name
        ');
        $builder->join('users u', 'u.id = ual.user_id', 'left');
        $builder->where('ual.details LIKE', "%session_id:{$sessionId}%");
        $builder->orWhere('ual.activity LIKE', "%session%");
        $builder->orderBy('ual.created_at', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResult();
    }

    public function getParticipantDetails($sessionId, $participantId)
    {
        $builder = $this->db->table('exam_participants ep');
        $builder->select('
            ep.*,
            u.full_name as student_name,
            u.username as student_nis,
            u.email as student_email,            COALESCE(
                (SELECT COUNT(*) FROM student_answers sa 
                    WHERE sa.exam_id = ep.exam_id AND sa.student_id = ep.user_id AND TRIM(sa.answer_text) != "") * 100 / 
                (SELECT COUNT(*) FROM exam_questions WHERE exam_id = ep.exam_id), 0
            ) as progress
        ');
        $builder->join('users u', 'u.id = ep.user_id');
        $builder->where('ep.exam_session_id', $sessionId);
        $builder->where('ep.user_id', $participantId);

        $participant = $builder->get()->getRow();

        if ($participant) {
            // Get participant's answers
            $answersBuilder = $this->db->table('student_answers sa');
            $answersBuilder->select('
                sa.*,
                eq.question_text,
                eq.correct_answer,
                CASE WHEN sa.answer = eq.correct_answer THEN 1 ELSE 0 END as is_correct
            ');
            $answersBuilder->join('exam_questions eq', 'eq.id = sa.question_id');
            $answersBuilder->where('sa.exam_participant_id', $participant->id);
            $answersBuilder->orderBy('eq.question_order', 'ASC');

            $participant->answers = $answersBuilder->get()->getResult();
        }

        return $participant;
    }

    public function forceSubmitParticipant($sessionId, $participantId)
    {
        $builder = $this->db->table('exam_participants');
        $data = [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'is_force_submitted' => 1
        ];

        $builder->where('exam_session_id', $sessionId);
        $builder->where('user_id', $participantId);
        $builder->where('status', 'in_progress');

        return $builder->update($data);
    }

    public function bulkDelete($sessionIds)
    {
        if (empty($sessionIds)) {
            return false;
        }

        // Check if any sessions are active
        $builder = $this->db->table($this->table);
        $builder->whereIn('id', $sessionIds);
        $builder->where('status', 'active');
        $activeSessions = $builder->countAllResults();

        if ($activeSessions > 0) {
            return false; // Cannot delete active sessions
        }

        // Delete sessions
        $builder = $this->db->table($this->table);
        $builder->whereIn('id', $sessionIds);
        return $builder->delete();
    }

    public function bulkStart($sessionIds)
    {
        if (empty($sessionIds)) {
            return false;
        }

        $builder = $this->db->table($this->table);
        $data = [
            'status' => 'active',
            'actual_start_time' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $builder->whereIn('id', $sessionIds);
        $builder->where('status', 'scheduled');
        return $builder->update($data);
    }

    public function bulkEnd($sessionIds)
    {
        if (empty($sessionIds)) {
            return false;
        }

        $builder = $this->db->table($this->table);
        $data = [
            'status' => 'completed',
            'actual_end_time' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $builder->whereIn('id', $sessionIds);
        $builder->where('status', 'active');
        return $builder->update($data);
    }

    /**
     * Get active exam sessions for monitoring
     */
    public function getActiveSessions()
    {
        $builder = $this->db->table($this->table . ' es');
        $builder->select('
            es.*,
            e.title as exam_title,
            e.duration_minutes,
            c.name as class_name,
            u.full_name as creator_name,
            COUNT(DISTINCT ep.id) as participant_count,
            COUNT(DISTINCT CASE WHEN ep.status = "in_progress" THEN ep.id END) as active_participants,
            COUNT(DISTINCT CASE WHEN ep.status = "completed" THEN ep.id END) as completed_participants
        ');
        $builder->join('exams e', 'e.id = es.exam_id');
        $builder->join('classes c', 'c.id = es.class_id', 'left');
        $builder->join('users u', 'u.id = es.created_by', 'left');
        $builder->join('exam_participants ep', 'ep.exam_session_id = es.id', 'left');
        $builder->where('es.status', 'active');
        $builder->groupBy('es.id');
        $builder->orderBy('es.start_time', 'ASC');

        return $builder->get()->getResult();
    }
}
