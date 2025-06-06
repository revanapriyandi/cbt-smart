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
        'teacher_id',
        'pdf_url',
        'pdf_content',
        'question_count',
        'duration_minutes',
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
}
