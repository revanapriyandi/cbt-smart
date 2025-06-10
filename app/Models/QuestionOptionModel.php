<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionOptionModel extends Model
{
    protected $table = 'question_options';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'question_id',
        'option_text',
        'is_correct',
        'order_number',
        'explanation'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'question_id' => 'required|integer',
        'option_text' => 'required|min_length[1]',
        'is_correct' => 'required|in_list[0,1]',
        'order_number' => 'permit_empty|integer|greater_than[0]',
        'explanation' => 'permit_empty|max_length[500]'
    ];

    /**
     * Get options by question ID
     */
    public function getByQuestionId($questionId)
    {
        return $this->where('question_id', $questionId)
            ->orderBy('order_number', 'ASC')
            ->findAll();
    }

    /**
     * Get correct option for a question
     */
    public function getCorrectOption($questionId)
    {
        return $this->where('question_id', $questionId)
            ->where('is_correct', 1)
            ->first();
    }

    /**
     * Update options for a question
     */
    public function updateQuestionOptions($questionId, $options)
    {
        // Delete existing options
        $this->where('question_id', $questionId)->delete();

        // Insert new options
        foreach ($options as $index => $option) {
            $this->insert([
                'question_id' => $questionId,
                'option_text' => $option['text'],
                'is_correct' => $option['is_correct'] ? 1 : 0,
                'order_number' => $index + 1,
                'explanation' => $option['explanation'] ?? null
            ]);
        }

        return true;
    }
}
