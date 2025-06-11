<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'question_bank_id',
        'question_text',
        'question_type',
        'difficulty_level',
        'points',
        'time_limit',
        'explanation',
        'image_url',
        'order_number',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'question_bank_id' => 'required|integer',
        'question_text' => 'required|min_length[10]',
        'question_type' => 'required|in_list[multiple_choice,essay,true_false,fill_blank]',
        'difficulty_level' => 'required|in_list[easy,medium,hard]',
        'points' => 'required|numeric|greater_than[0]',
        'time_limit' => 'permit_empty|integer|greater_than[0]',
        'explanation' => 'permit_empty|max_length[1000]',
        'order_number' => 'permit_empty|integer|greater_than[0]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'question_bank_id' => [
            'required' => 'Bank soal harus dipilih',
            'integer' => 'ID bank soal tidak valid'
        ],
        'question_text' => [
            'required' => 'Teks soal harus diisi',
            'min_length' => 'Teks soal minimal 10 karakter'
        ],
        'question_type' => [
            'required' => 'Jenis soal harus dipilih',
            'in_list' => 'Jenis soal tidak valid'
        ],
        'difficulty_level' => [
            'required' => 'Tingkat kesulitan harus dipilih',
            'in_list' => 'Tingkat kesulitan tidak valid'
        ],
        'points' => [
            'required' => 'Poin soal harus diisi',
            'numeric' => 'Poin harus berupa angka',
            'greater_than' => 'Poin minimal 1'
        ],
        'time_limit' => [
            'integer' => 'Batas waktu harus berupa angka',
            'greater_than' => 'Batas waktu minimal 1 detik'
        ],
        'explanation' => [
            'max_length' => 'Penjelasan maksimal 1000 karakter'
        ],
        'order_number' => [
            'integer' => 'Nomor urut harus berupa angka',
            'greater_than' => 'Nomor urut minimal 1'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];

    /**
     * Get questions with bank details
     */
    public function getQuestionsWithDetails($filters = [])
    {
        $builder = $this->select('
            questions.*,
            question_banks.name as bank_name,
            subjects.name as subject_name,
            exam_types.name as exam_type_name,
            users.full_name as creator_name
        ')
            ->join('question_banks', 'question_banks.id = questions.question_bank_id', 'left')
            ->join('subjects', 'subjects.id = question_banks.subject_id', 'left')
            ->join('exam_types', 'exam_types.id = question_banks.exam_type_id', 'left')
            ->join('users', 'users.id = questions.created_by', 'left');

        // Apply filters
        if (!empty($filters['bank_id'])) {
            $builder->where('questions.question_bank_id', $filters['bank_id']);
        }
        if (!empty($filters['subject_id'])) {
            $builder->where('question_banks.subject_id', $filters['subject_id']);
        }
        if (!empty($filters['exam_type_id'])) {
            $builder->where('question_banks.exam_type_id', $filters['exam_type_id']);
        }
        if (!empty($filters['difficulty'])) {
            $builder->where('questions.difficulty_level', $filters['difficulty']);
        }
        if (!empty($filters['status'])) {
            $builder->where('questions.status', $filters['status']);
        }

        return $builder;
    }

    /**
     * Get questions by bank ID
     */
    public function getByBankId($bankId)
    {
        return $this->where('question_bank_id', $bankId)
            ->where('status', 'active')
            ->orderBy('order_number', 'ASC')
            ->findAll();
    }

    /**
     * Get question count by bank ID
     */
    public function getCountByBankId($bankId)
    {
        return $this->where('question_bank_id', $bankId)
            ->where('status', 'active')
            ->countAllResults();
    }

    /**
     * Update question orders
     */
    public function updateOrders($orders)
    {
        foreach ($orders as $order) {
            $this->update($order['id'], ['order_number' => $order['order']]);
        }
        return true;
    }

    /**
     * Duplicate question
     */
    public function duplicateQuestion($questionId)
    {
        $question = $this->find($questionId);
        if (!$question) {
            return false;
        }

        unset($question['id']);
        $question['question_text'] = $question['question_text'] . ' (Copy)';
        $question['created_by'] = session('user_id');
        $question['updated_by'] = session('user_id');

        return $this->insert($question);
    }

    /**
     * Get statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $active = $this->where('status', 'active')->countAllResults();
        $inactive = $this->where('status', 'inactive')->countAllResults();

        $byDifficulty = [
            'easy' => $this->where('difficulty_level', 'easy')->countAllResults(),
            'medium' => $this->where('difficulty_level', 'medium')->countAllResults(),
            'hard' => $this->where('difficulty_level', 'hard')->countAllResults()
        ];

        $byType = [
            'multiple_choice' => $this->where('question_type', 'multiple_choice')->countAllResults(),
            'essay' => $this->where('question_type', 'essay')->countAllResults(),
            'true_false' => $this->where('question_type', 'true_false')->countAllResults(),
            'fill_blank' => $this->where('question_type', 'fill_blank')->countAllResults()
        ];

        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $inactive,
            'by_difficulty' => $byDifficulty,
            'by_type' => $byType
        ];
    }
}
