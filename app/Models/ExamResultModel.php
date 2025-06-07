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
                'status' => 'ongoing',
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
                'status' => 'submitted',
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
                'status' => 'graded',
                'graded_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }

    public function getAverageScore($examId)
    {
        $result = $this->select('AVG(percentage) as average_score')
            ->where('exam_id', $examId)
            ->where('status', 'graded')
            ->where('percentage IS NOT NULL')
            ->first();

        return $result ? round($result['average_score'], 1) : null;
    }
}
