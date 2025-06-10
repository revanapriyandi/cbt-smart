<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamTypeModel extends Model
{
    protected $table = 'exam_types';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'category',
        'description',
        'duration_minutes',
        'max_attempts',
        'passing_score',
        'show_result_immediately',
        'allow_review',
        'randomize_questions',
        'randomize_options',
        'auto_submit',
        'instructions',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'category' => 'required|in_list[daily,mid_semester,final_semester,national,practice,simulation]',
        'description' => 'permit_empty|max_length[500]',
        'duration_minutes' => 'required|integer|greater_than[0]|less_than_equal_to[480]',
        'max_attempts' => 'permit_empty|integer|greater_than[0]|less_than_equal_to[10]',
        'passing_score' => 'required|numeric|greater_than[0]|less_than_equal_to[100]',
        'show_result_immediately' => 'permit_empty|in_list[0,1]',
        'allow_review' => 'permit_empty|in_list[0,1]',
        'randomize_questions' => 'permit_empty|in_list[0,1]',
        'randomize_options' => 'permit_empty|in_list[0,1]',
        'auto_submit' => 'permit_empty|in_list[0,1]',
        'instructions' => 'permit_empty|max_length[2000]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama jenis ujian harus diisi',
            'max_length' => 'Nama jenis ujian maksimal 100 karakter'
        ],
        'category' => [
            'required' => 'Kategori harus dipilih',
            'in_list' => 'Kategori tidak valid'
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 500 karakter'
        ],
        'duration_minutes' => [
            'required' => 'Durasi ujian harus diisi',
            'integer' => 'Durasi harus berupa angka',
            'greater_than' => 'Durasi minimal 1 menit',
            'less_than_equal_to' => 'Durasi maksimal 480 menit (8 jam)'
        ],
        'max_attempts' => [
            'integer' => 'Maksimal percobaan harus berupa angka',
            'greater_than' => 'Maksimal percobaan minimal 1',
            'less_than_equal_to' => 'Maksimal percobaan maksimal 10'
        ],
        'passing_score' => [
            'required' => 'Nilai kelulusan harus diisi',
            'numeric' => 'Nilai kelulusan harus berupa angka',
            'greater_than' => 'Nilai kelulusan minimal 1',
            'less_than_equal_to' => 'Nilai kelulusan maksimal 100'
        ],
        'instructions' => [
            'max_length' => 'Instruksi maksimal 2000 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status harus active atau inactive'
        ]
    ];

    public function getMostUsedExamType()
    {
        $result = $this->select('exam_types.name, COUNT(exams.id) as usage_count')
            ->join('exams', 'exams.exam_type_id = exam_types.id', 'left')
            ->groupBy('exam_types.id')
            ->orderBy('usage_count', 'DESC')
            ->first();

        return $result ? $result['name'] : 'Belum ada';
    }

    /**
     * Check if exam type is being used in any exams
     */
    public function isBeingUsed($id)
    {
        $examModel = new \App\Models\ExamModel();
        return $examModel->where('exam_type_id', $id)->countAllResults() > 0;
    }

    public function getExamTypeStatistics($id)
    {
        $stats = [
            'total_exams' => 0,
            'active_exams' => 0,
            'total_participants' => 0,
            'avg_score' => 0
        ];

        // Count total exams
        $stats['total_exams'] = $this->db->table('exams')
            ->where('exam_type_id', $id)
            ->countAllResults();

        // Count active exams
        $stats['active_exams'] = $this->db->table('exams')
            ->where('exam_type_id', $id)
            ->where('status', 'active')
            ->countAllResults();

        // Count total participants
        $stats['total_participants'] = $this->db->table('exam_participants ep')
            ->join('exams e', 'e.id = ep.exam_id')
            ->where('e.exam_type_id', $id)
            ->countAllResults();

        // Calculate average score
        $avgResult = $this->db->table('exam_results er')
            ->join('exams e', 'e.id = er.exam_id')
            ->where('e.exam_type_id', $id)
            ->selectAvg('er.score')
            ->get()
            ->getRowArray();

        $stats['avg_score'] = $avgResult['score'] ?? 0;
        return $stats;
    }

    public function getRecentExamsByType($examTypeId, $limit = 10)
    {
        return $this->db->table('exams e')
            ->select('e.id, e.title, e.status, e.start_date, e.end_date, 
                     u.full_name as created_by_name')
            ->join('users u', 'u.id = e.created_by', 'left')
            ->where('e.exam_type_id', $examTypeId)
            ->orderBy('e.created_at', 'DESC')
            ->limit($limit)
            ->get()
            ->getResultArray();
    }

    public function getPopularExamTypes($limit = 5)
    {
        return $this->select('exam_types.*, COUNT(exams.id) as exam_count')
            ->join('exams', 'exams.exam_type_id = exam_types.id', 'left')
            ->where('exam_types.status', 'active')
            ->groupBy('exam_types.id')
            ->orderBy('exam_count', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    public function getExamTypesByCategory($category = null)
    {
        $builder = $this->where('status', 'active');

        if ($category) {
            $builder->where('category', $category);
        }

        return $builder->orderBy('name', 'ASC')->findAll();
    }

    public function getExamTypeUsageStats()
    {
        return $this->select('
                exam_types.category,
                COUNT(DISTINCT exam_types.id) as type_count,
                COUNT(exams.id) as exam_count
            ')
            ->join('exams', 'exams.exam_type_id = exam_types.id', 'left')
            ->where('exam_types.status', 'active')
            ->groupBy('exam_types.category')
            ->findAll();
    }

    public function duplicateExamType($sourceId, $newName, $userId)
    {
        $sourceExamType = $this->find($sourceId);

        if (!$sourceExamType) {
            return false;
        }

        // Remove ID and update fields
        unset($sourceExamType['id']);
        $sourceExamType['name'] = $newName;
        $sourceExamType['created_by'] = $userId;
        $sourceExamType['updated_by'] = null;
        $sourceExamType['created_at'] = date('Y-m-d H:i:s');
        $sourceExamType['updated_at'] = date('Y-m-d H:i:s');

        return $this->insert($sourceExamType);
    }

    public function getExamTypeTemplate($category)
    {
        $templates = [
            'daily' => [
                'duration_minutes' => 45,
                'max_attempts' => 1,
                'passing_score' => 75,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => 'Instruksi untuk ujian harian:\n1. Baca setiap soal dengan teliti\n2. Pilih jawaban yang paling tepat\n3. Periksa kembali jawaban sebelum submit'
            ],
            'mid_semester' => [
                'duration_minutes' => 90,
                'max_attempts' => 1,
                'passing_score' => 70,
                'show_result_immediately' => 0,
                'allow_review' => 0,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => 'Instruksi untuk Ujian Tengah Semester:\n1. Ujian berlangsung selama 90 menit\n2. Tidak dapat mengulang ujian\n3. Hasil akan diumumkan kemudian\n4. Periksa jawaban sebelum submit'
            ],
            'final_semester' => [
                'duration_minutes' => 120,
                'max_attempts' => 1,
                'passing_score' => 65,
                'show_result_immediately' => 0,
                'allow_review' => 0,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => 'Instruksi untuk Ujian Akhir Semester:\n1. Ujian berlangsung selama 120 menit\n2. Tidak dapat mengulang ujian\n3. Hasil akan diumumkan kemudian\n4. Bacalah petunjuk setiap soal dengan cermat'
            ],
            'practice' => [
                'duration_minutes' => 30,
                'max_attempts' => 3,
                'passing_score' => 60,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 0,
                'instructions' => 'Instruksi untuk latihan:\n1. Anda dapat mengulang hingga 3 kali\n2. Hasil akan langsung ditampilkan\n3. Gunakan untuk persiapan ujian\n4. Review jawaban yang salah'
            ]
        ];

        return $templates[$category] ?? [];
    }

    public function validateExamTypeConfiguration($data)
    {
        $errors = [];

        // Custom validation rules
        if (isset($data['max_attempts']) && $data['max_attempts'] > 1 && $data['show_result_immediately'] == 0) {
            $errors[] = 'Jika izin mengulang lebih dari 1 kali, sebaiknya tampilkan hasil segera';
        }

        if (isset($data['duration_minutes']) && $data['duration_minutes'] < 15) {
            $errors[] = 'Durasi ujian terlalu singkat, minimal 15 menit';
        }

        if (isset($data['passing_score']) && $data['category'] == 'practice' && $data['passing_score'] > 80) {
            $errors[] = 'Nilai kelulusan untuk latihan sebaiknya tidak terlalu tinggi';
        }

        return $errors;
    }

    public function getExamTypeRecommendations($examTypeId)
    {
        $examType = $this->find($examTypeId);
        if (!$examType) {
            return [];
        }

        $recommendations = [];

        // Duration recommendations
        if ($examType['duration_minutes'] < 30 && $examType['category'] != 'practice') {
            $recommendations[] = [
                'type' => 'warning',
                'message' => 'Durasi ujian mungkin terlalu singkat untuk kategori ' . $examType['category']
            ];
        }

        // Attempts recommendations
        if ($examType['max_attempts'] > 1 && in_array($examType['category'], ['mid_semester', 'final_semester'])) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Ujian formal biasanya hanya membolehkan 1 kali percobaan'
            ];
        }

        // Result display recommendations
        if ($examType['show_result_immediately'] && in_array($examType['category'], ['mid_semester', 'final_semester'])) {
            $recommendations[] = [
                'type' => 'info',
                'message' => 'Pertimbangkan untuk tidak menampilkan hasil segera pada ujian formal'
            ];
        }

        return $recommendations;
    }

    /**
     * Log activity for exam type operations
     */
    public function logActivity($examTypeId, $action, $description, $userId)
    {
        $userActivityModel = new \App\Models\UserActivityLogModel();

        return $userActivityModel->insert([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'table_name' => 'exam_types',
            'record_id' => $examTypeId,
            'ip_address' => \Config\Services::request()->getIPAddress(),
            'user_agent' => \Config\Services::request()->getUserAgent()->getAgentString()
        ]);
    }

    public function getCategoryStatistics()
    {
        return $this->select('
                category,
                COUNT(*) as total_types,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active_types,
                AVG(duration_minutes) as avg_duration,
                AVG(passing_score) as avg_passing_score
            ')
            ->groupBy('category')
            ->findAll();
    }

    public function getExamTypesBySettings($settings = [])
    {
        $builder = $this->where('status', 'active');

        if (isset($settings['show_result_immediately'])) {
            $builder->where('show_result_immediately', $settings['show_result_immediately']);
        }

        if (isset($settings['allow_review'])) {
            $builder->where('allow_review', $settings['allow_review']);
        }

        if (isset($settings['randomize_questions'])) {
            $builder->where('randomize_questions', $settings['randomize_questions']);
        }

        if (isset($settings['min_duration'])) {
            $builder->where('duration_minutes >=', $settings['min_duration']);
        }

        if (isset($settings['max_duration'])) {
            $builder->where('duration_minutes <=', $settings['max_duration']);
        }

        return $builder->orderBy('name', 'ASC')->findAll();
    }
}
