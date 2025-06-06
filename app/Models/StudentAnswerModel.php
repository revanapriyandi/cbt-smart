<?php

namespace App\Models;

use CodeIgniter\Model;

class StudentAnswerModel extends Model
{
    protected $table = 'student_answers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'exam_id',
        'student_id',
        'question_number',
        'answer_text',
        'ai_score',
        'ai_feedback',
        'manual_score',
        'manual_feedback',
        'final_score'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'exam_id' => 'required|integer',
        'student_id' => 'required|integer',
        'question_number' => 'required|integer|greater_than[0]',
        'answer_text' => 'required'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getAnswersByExamAndStudent($examId, $studentId)
    {
        return $this->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->orderBy('question_number', 'ASC')
            ->findAll();
    }

    public function getAnswersByExam($examId)
    {
        return $this->select('student_answers.*, users.full_name as student_name, users.username')
            ->join('users', 'users.id = student_answers.student_id')
            ->where('student_answers.exam_id', $examId)
            ->orderBy('users.full_name', 'ASC')
            ->orderBy('student_answers.question_number', 'ASC')
            ->findAll();
    }

    public function saveOrUpdateAnswer($data)
    {
        $existing = $this->where('exam_id', $data['exam_id'])
            ->where('student_id', $data['student_id'])
            ->where('question_number', $data['question_number'])
            ->first();

        if ($existing) {
            return $this->update($existing['id'], $data);
        } else {
            return $this->insert($data);
        }
    }

    public function hasAnswered($examId, $studentId, $questionNumber)
    {
        return $this->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->where('question_number', $questionNumber)
            ->countAllResults() > 0;
    }
}
