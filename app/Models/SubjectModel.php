<?php

namespace App\Models;

use CodeIgniter\Model;

class SubjectModel extends Model
{
    protected $table = 'subjects';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'code',
        'description',
        'teacher_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'code' => 'required|alpha_numeric_punct|max_length[20]',
        'teacher_id' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama mata pelajaran wajib diisi.',
            'max_length' => 'Nama mata pelajaran maksimal 100 karakter.'
        ],
        'code' => [
            'required' => 'Kode mata pelajaran wajib diisi.',
            'alpha_numeric_punct' => 'Kode mata pelajaran hanya boleh mengandung huruf, angka, dan tanda baca.',
            'max_length' => 'Kode mata pelajaran maksimal 20 karakter.',
            'is_unique' => 'Kode mata pelajaran sudah digunakan.'
        ],
        'teacher_id' => [
            'integer' => 'Teacher ID harus berupa angka.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Custom validation for unique subject code
     */
    public function validateUniqueCode($code, $excludeId = null)
    {
        $builder = $this->where('code', $code);

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() == 0;
    }

    /**
     * Create subject with custom validation
     */
    public function createSubject($data)
    {
        // Validate unique code
        if (!$this->validateUniqueCode($data['code'])) {
            return [
                'success' => false,
                'errors' => ['code' => 'Kode mata pelajaran sudah digunakan.']
            ];
        }

        // Insert data
        $result = $this->insert($data);

        if ($result === false) {
            return [
                'success' => false,
                'errors' => $this->errors()
            ];
        }

        return [
            'success' => true,
            'id' => $result
        ];
    }

    /**
     * Update subject with custom validation
     */
    public function updateSubject($id, $data)
    {
        // Validate unique code (excluding current record)
        if (isset($data['code']) && !$this->validateUniqueCode($data['code'], $id)) {
            return [
                'success' => false,
                'errors' => ['code' => 'Kode mata pelajaran sudah digunakan.']
            ];
        }

        // Update data
        $result = $this->update($id, $data);

        if ($result === false) {
            return [
                'success' => false,
                'errors' => $this->errors()
            ];
        }

        return [
            'success' => true
        ];
    }

    public function getSubjectsWithTeacher()
    {
        return $this->select('subjects.*, users.full_name as teacher_name')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->findAll();
    }

    public function getSubjectsWithDetails()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subjects s');

        return $builder->select('
            s.*,
            u.full_name as teacher_name,
            u.email as teacher_email,
            COUNT(DISTINCT e.id) as exam_count,
            COUNT(DISTINCT CASE WHEN e.is_active = 1 THEN e.id END) as active_exam_count,
            AVG(er.percentage) as average_score,
            COUNT(DISTINCT er.student_id) as total_students
        ')
            ->join('users u', 'u.id = s.teacher_id', 'left')
            ->join('exams e', 'e.subject_id = s.id', 'left')
            ->join('exam_results er', 'er.exam_id = e.id AND er.status = "graded"', 'left')
            ->groupBy('s.id')
            ->orderBy('s.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function getSubjectsByTeacher($teacherId)
    {
        return $this->where('teacher_id', $teacherId)->findAll();
    }

    public function getSubjectStatistics($subjectId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subjects s');
        return $builder->select('
            s.*,
            u.full_name as teacher_name,
            u.email as teacher_email,
            COUNT(DISTINCT e.id) as total_exams,
            COUNT(DISTINCT CASE WHEN e.is_active = 1 THEN e.id END) as active_exams,
            COUNT(DISTINCT CASE WHEN e.is_active = 0 THEN e.id END) as draft_exams,
            COUNT(DISTINCT er.student_id) as enrolled_students,
            COUNT(DISTINCT CASE WHEN er.status = "graded" THEN er.id END) as completed_attempts,
            AVG(CASE WHEN er.status = "graded" THEN er.percentage END) as average_score,
            MAX(CASE WHEN er.status = "graded" THEN er.percentage END) as highest_score,
            MIN(CASE WHEN er.status = "graded" THEN er.percentage END) as lowest_score
        ')
            ->join('users u', 'u.id = s.teacher_id', 'left')
            ->join('exams e', 'e.subject_id = s.id', 'left')
            ->join('exam_results er', 'er.exam_id = e.id', 'left')
            ->where('s.id', $subjectId)
            ->groupBy('s.id')
            ->get()
            ->getRowArray();
    }
}
