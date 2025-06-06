<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamQuestionModel extends Model
{
    protected $table = 'exam_questions';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'exam_id',
        'question_number',
        'question_text',
        'max_score'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'exam_id' => 'required|integer',
        'question_number' => 'required|integer|greater_than[0]',
        'question_text' => 'required',
        'max_score' => 'required|decimal|greater_than[0]'
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getQuestionsByExam($examId)
    {
        return $this->where('exam_id', $examId)
            ->orderBy('question_number', 'ASC')
            ->findAll();
    }

    public function deleteByExamId($examId)
    {
        return $this->where('exam_id', $examId)->delete();
    }
}
