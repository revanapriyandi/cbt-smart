<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamModel extends Model
{
    protected $table = 'exams';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'title',
        'description',
        'subject_id',
        'exam_type_id',
        'question_bank_id',
        'teacher_id',
        'pdf_url',
        'pdf_content',
        'question_count',
        'total_questions',
        'duration_minutes',
        'duration',
        'start_time',
        'end_time',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|max_length[200]',
        'subject_id' => 'required|integer',
        'teacher_id' => 'required|integer',
        'pdf_url' => 'required|valid_url|max_length[500]',
        'question_count' => 'required|integer|greater_than[0]',
        'duration_minutes' => 'required|integer|greater_than[0]',
        'start_time' => 'required|valid_date',
        'end_time' => 'required|valid_date'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getExamsWithDetails()
    {
        return $this->select('exams.*, subjects.name as subject_name, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('users', 'users.id = exams.teacher_id')
            ->orderBy('exams.created_at', 'DESC')
            ->findAll();
    }

    public function getExamsByTeacher($teacherId)
    {
        return $this->select('exams.*, subjects.name as subject_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->where('exams.teacher_id', $teacherId)
            ->orderBy('exams.created_at', 'DESC')
            ->findAll();
    }

    public function getActiveExams()
    {
        return $this->select('exams.*, subjects.name as subject_name, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('users', 'users.id = exams.teacher_id')
            ->where('exams.is_active', 1)
            ->where('exams.start_time <=', date('Y-m-d H:i:s'))
            ->where('exams.end_time >=', date('Y-m-d H:i:s'))
            ->orderBy('exams.start_time', 'ASC')
            ->findAll();
    }

    public function getUpcomingExams()
    {
        return $this->select('exams.*, subjects.name as subject_name, users.full_name as teacher_name')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('users', 'users.id = exams.teacher_id')
            ->where('exams.is_active', 1)
            ->where('exams.start_time >', date('Y-m-d H:i:s'))
            ->orderBy('exams.start_time', 'ASC')
            ->findAll();
    }

    /**
     * Get exams with full details including question bank
     */
    public function getExamsWithFullDetails($filters = [])
    {
        $builder = $this->select('
            exams.*,
            subjects.name as subject_name,
            subjects.code as subject_code,
            exam_types.name as exam_type_name,
            exam_types.category as exam_category,
            question_banks.name as question_bank_name,
            users.full_name as teacher_name,
            users.email as teacher_email
        ')
            ->join('subjects', 'subjects.id = exams.subject_id')
            ->join('exam_types', 'exam_types.id = exams.exam_type_id', 'left')
            ->join('question_banks', 'question_banks.id = exams.question_bank_id', 'left')
            ->join('users', 'users.id = exams.teacher_id');

        // Apply filters
        if (!empty($filters['subject_id'])) {
            $builder->where('exams.subject_id', $filters['subject_id']);
        }
        if (!empty($filters['exam_type_id'])) {
            $builder->where('exams.exam_type_id', $filters['exam_type_id']);
        }
        if (!empty($filters['teacher_id'])) {
            $builder->where('exams.teacher_id', $filters['teacher_id']);
        }
        if (!empty($filters['status'])) {
            $builder->where('exams.is_active', $filters['status'] === 'active' ? 1 : 0);
        }

        return $builder->orderBy('exams.created_at', 'DESC')->findAll();
    }

    /**
     * Get available question banks for exam creation
     */
    public function getAvailableQuestionBanks($subjectId = null, $examTypeId = null)
    {
        $questionBankModel = new \App\Models\QuestionBankModel();
        $builder = $questionBankModel->select('
            question_banks.*,
            subjects.name as subject_name,
            exam_types.name as exam_type_name,
            (SELECT COUNT(*) FROM questions WHERE question_bank_id = question_banks.id AND status = "active") as question_count
        ')
            ->join('subjects', 'subjects.id = question_banks.subject_id')
            ->join('exam_types', 'exam_types.id = question_banks.exam_type_id')
            ->where('question_banks.status', 'active');

        if ($subjectId) {
            $builder->where('question_banks.subject_id', $subjectId);
        }
        if ($examTypeId) {
            $builder->where('question_banks.exam_type_id', $examTypeId);
        }

        return $builder->orderBy('question_banks.name', 'ASC')->findAll();
    }

    /**
     * Create exam from question bank
     */
    public function createFromQuestionBank($examData, $questionBankId = null)
    {
        if ($questionBankId) {
            $examData['question_bank_id'] = $questionBankId;

            // Get question count from the bank
            $questionModel = new \App\Models\QuestionModel();
            $questionCount = $questionModel->getCountByBankId($questionBankId);

            if ($questionCount > 0) {
                $examData['question_count'] = min($examData['question_count'], $questionCount);
            }
        }

        return $this->insert($examData);
    }
}
