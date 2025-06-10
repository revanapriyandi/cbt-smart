<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Models\UserActivityLogModel;

class QuestionBankModel extends Model
{
    protected $table = 'question_banks';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'subject_id',
        'exam_type_id',
        'difficulty_level',
        'description',
        'instructions',
        'time_per_question',
        'negative_marking',
        'negative_marks',
        'randomize_questions',
        'show_correct_answer',
        'allow_calculator',
        'tags',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[200]',
        'subject_id' => 'required|integer',
        'exam_type_id' => 'required|integer',
        'difficulty_level' => 'required|in_list[easy,medium,hard]',
        'description' => 'permit_empty|max_length[1000]',
        'instructions' => 'permit_empty|max_length[2000]',
        'time_per_question' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[300]',
        'negative_marks' => 'permit_empty|numeric|less_than[0]',
        'tags' => 'permit_empty|max_length[500]',
        'status' => 'required|in_list[active,draft,archived]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama bank soal harus diisi',
            'max_length' => 'Nama bank soal maksimal 200 karakter'
        ],
        'subject_id' => [
            'required' => 'Mata pelajaran harus dipilih',
            'integer' => 'Mata pelajaran tidak valid'
        ],
        'exam_type_id' => [
            'required' => 'Jenis ujian harus dipilih',
            'integer' => 'Jenis ujian tidak valid'
        ],
        'difficulty_level' => [
            'required' => 'Tingkat kesulitan harus dipilih',
            'in_list' => 'Tingkat kesulitan tidak valid'
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 1000 karakter'
        ],
        'instructions' => [
            'max_length' => 'Instruksi maksimal 2000 karakter'
        ],
        'time_per_question' => [
            'integer' => 'Waktu per soal harus berupa angka',
            'greater_than' => 'Waktu per soal minimal 1 detik',
            'less_than_equal_to' => 'Waktu per soal maksimal 300 detik'
        ],
        'negative_marks' => [
            'numeric' => 'Nilai negatif harus berupa angka',
            'less_than' => 'Nilai negatif harus kurang dari 0'
        ],
        'tags' => [
            'max_length' => 'Tags maksimal 500 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status tidak valid'
        ]
    ];
    public function getQuestionBanksWithDetails()
    {
        return $this->select('
                question_banks.*,
                subjects.name as subject_name,
                exam_types.name as exam_type_name,
                users.full_name as created_by_name,
                (SELECT COUNT(*) FROM questions WHERE questions.question_bank_id = question_banks.id) as question_count,
                (SELECT COUNT(DISTINCT e.id) FROM exams e WHERE e.question_bank_id = question_banks.id) as used_count
            ')
            ->join('subjects', 'subjects.id = question_banks.subject_id', 'left')
            ->join('exam_types', 'exam_types.id = question_banks.exam_type_id', 'left')
            ->join('users', 'users.id = question_banks.created_by', 'left');
    }

    public function getQuestionBankWithDetails($id)
    {
        return $this->getQuestionBanksWithDetails()
            ->where('question_banks.id', $id)
            ->first();
    }
    public function getTotalQuestions()
    {
        return $this->db->table('questions')
            ->join('question_banks', 'question_banks.id = questions.question_bank_id')
            ->countAllResults();
    }
    public function getQuestionsByBank($bankId, $limit = null)
    {
        // Since there's no QuestionModel, we'll use direct database queries
        $builder = $this->db->table('questions')
            ->select('questions.*, 
                (SELECT COUNT(DISTINCT e.id) FROM exams e WHERE e.question_bank_id = questions.question_bank_id) as usage_count')
            ->where('question_bank_id', $bankId)
            ->orderBy('questions.created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    public function getBankStatistics($bankId)
    {
        // Get question count by type
        $questionStats = $this->db->table('questions')
            ->select('question_type, COUNT(*) as count, AVG(points) as avg_points')
            ->where('question_bank_id', $bankId)
            ->groupBy('question_type')
            ->get()
            ->getResultArray();        // Get usage statistics
        $usageStats = $this->db->table('exams')
            ->select('COUNT(*) as total_usage')
            ->where('question_bank_id', $bankId)
            ->get()
            ->getRow(); // Get difficulty distribution
        $difficultyStats = $this->db->table('questions')
            ->select('difficulty_level, COUNT(*) as count')
            ->where('question_bank_id', $bankId)
            ->groupBy('difficulty_level')
            ->get()
            ->getResultArray();

        return [
            'question_stats' => $questionStats,
            'usage_stats' => $usageStats,
            'total_usage' => $usageStats->total_usage ?? 0,
            'difficulty_stats' => $difficultyStats
        ];
    }
    public function isBeingUsed($bankId)
    {
        // Check if this question bank is used in any exams
        $usageCount = $this->db->table('exams')
            ->where('question_bank_id', $bankId)
            ->countAllResults();

        return $usageCount > 0;
    }

    public function deleteQuestionsByBank($bankId)
    {
        // Delete all questions in this bank
        return $this->db->table('questions')
            ->where('question_bank_id', $bankId)
            ->delete();
    }
    public function getBankUsageHistory($bankId, $limit = 10)
    {
        // Get recent usage history for this bank
        return $this->db->table('exam_sessions es')
            ->select('es.*, e.title as exam_title, u.full_name as student_name')
            ->join('exams e', 'e.id = es.exam_id')
            ->join('users u', 'u.id = es.student_id')
            ->where('e.question_bank_id', $bankId)
            ->orderBy('es.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function duplicateBank($sourceId, $userId)
    {
        $sourceBank = $this->find($sourceId);
        if (!$sourceBank) {
            return ['success' => false, 'message' => 'Bank soal sumber tidak ditemukan'];
        }

        $this->db->transStart();

        try {
            // Duplicate bank
            unset($sourceBank['id']);
            $sourceBank['name'] = 'Copy of ' . $sourceBank['name'];
            $sourceBank['created_by'] = $userId;
            $sourceBank['updated_by'] = null;
            $sourceBank['created_at'] = date('Y-m-d H:i:s');
            $sourceBank['updated_at'] = date('Y-m-d H:i:s');

            if (!$this->insert($sourceBank)) {
                throw new \Exception('Gagal membuat bank soal baru');
            }

            $newBankId = $this->getInsertID();

            // Duplicate questions
            $questionModel = new \App\Models\QuestionModel();
            $questions = $questionModel->where('question_bank_id', $sourceId)->findAll();

            foreach ($questions as $question) {
                $oldQuestionId = $question['id'];
                unset($question['id']);
                $question['question_bank_id'] = $newBankId;
                $question['created_at'] = date('Y-m-d H:i:s');
                $question['updated_at'] = date('Y-m-d H:i:s');

                if (!$questionModel->insert($question)) {
                    throw new \Exception('Gagal menduplikasi soal');
                }

                $newQuestionId = $questionModel->getInsertID();

                // Duplicate question options
                $options = $this->db->table('question_options')
                    ->where('question_id', $oldQuestionId)
                    ->get()
                    ->getResultArray();

                foreach ($options as $option) {
                    unset($option['id']);
                    $option['question_id'] = $newQuestionId;
                    $option['created_at'] = date('Y-m-d H:i:s');
                    $option['updated_at'] = date('Y-m-d H:i:s');

                    $this->db->table('question_options')->insert($option);
                }
            }

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \Exception('Transaksi database gagal');
            }

            // Log activity
            $this->logActivity(
                $newBankId,
                'duplicate',
                'Bank soal diduplikasi dari: ' . $sourceBank['name'],
                $userId
            );

            return [
                'success' => true,
                'message' => 'Bank soal berhasil diduplikasi',
                'new_bank_id' => $newBankId
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function importFromFile($file, $data, $userId)
    {
        // Implementation for importing question banks from file
        // This would be a complex method to handle Excel/CSV imports
        // For now, return a placeholder response
        return [
            'success' => false,
            'message' => 'Import functionality not yet implemented',
            'errors' => []
        ];
    }

    public function getQuestionBanksBySubject($subjectId)
    {
        return $this->where('subject_id', $subjectId)
            ->where('status', 'active')
            ->orderBy('name', 'ASC')
            ->findAll();
    }
    public function getPopularBanks($limit = 5)
    {
        return $this->select('question_banks.*, subjects.name as subject_name,
                COUNT(e.id) as usage_count')
            ->join('subjects', 'subjects.id = question_banks.subject_id', 'left')
            ->join('exams e', 'e.question_bank_id = question_banks.id', 'left')
            ->where('question_banks.status', 'active')
            ->groupBy('question_banks.id')
            ->orderBy('usage_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function searchBanks($keyword, $filters = [])
    {
        $builder = $this->getQuestionBanksWithDetails();

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('question_banks.name', $keyword)
                ->orLike('question_banks.description', $keyword)
                ->orLike('question_banks.tags', $keyword)
                ->groupEnd();
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('question_banks.subject_id', $filters['subject_id']);
        }

        if (!empty($filters['exam_type_id'])) {
            $builder->where('question_banks.exam_type_id', $filters['exam_type_id']);
        }

        if (!empty($filters['difficulty_level'])) {
            $builder->where('question_banks.difficulty_level', $filters['difficulty_level']);
        }

        if (!empty($filters['status'])) {
            $builder->where('question_banks.status', $filters['status']);
        }

        return $builder->orderBy('question_banks.name', 'ASC')->findAll();
    }
    public function logActivity($bankId, $action, $description, $userId)
    {
        $logModel = new UserActivityLogModel();

        return $logModel->logActivity(
            $userId,
            'question_bank_' . $action,
            $description,
            \Config\Services::request()->getIPAddress(),
            \Config\Services::request()->getUserAgent()->getAgentString()
        );
    }
    public function getQuestionBanksWithStats()
    {
        return $this->select('
                question_banks.*,
                subjects.name as subject_name,
                exam_types.name as exam_type_name,
                users.full_name as created_by_name,
                (SELECT COUNT(*) FROM questions WHERE questions.question_bank_id = question_banks.id AND questions.status = "active") as question_count,
                (SELECT COUNT(*) FROM exams WHERE exams.question_bank_id = question_banks.id) as exam_count
            ')
            ->join('subjects', 'subjects.id = question_banks.subject_id', 'left')
            ->join('exam_types', 'exam_types.id = question_banks.exam_type_id', 'left')
            ->join('users', 'users.id = question_banks.created_by', 'left')
            ->where('question_banks.status', 'active')
            ->orderBy('question_banks.name', 'ASC')
            ->findAll();
    }
}
