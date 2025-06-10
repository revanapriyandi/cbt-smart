<?php

namespace App\Models;

use CodeIgniter\Model;

class ClassModel extends Model
{
    protected $table = 'classes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'name',
        'level',
        'capacity',
        'description',
        'is_active',
        'academic_year',
        'homeroom_teacher_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'level' => 'required|integer|greater_than[0]',
        'capacity' => 'required|integer|greater_than[0]',
        'description' => 'permit_empty|max_length[500]',
        'is_active' => 'in_list[0,1]',
        'academic_year' => 'required|max_length[20]',
        'homeroom_teacher_id' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama kelas wajib diisi.',
            'max_length' => 'Nama kelas maksimal 100 karakter.',
            'is_unique' => 'Nama kelas sudah digunakan.'
        ],
        'level' => [
            'required' => 'Tingkat kelas wajib diisi.',
            'integer' => 'Tingkat kelas harus berupa angka.',
            'greater_than' => 'Tingkat kelas harus lebih dari 0.'
        ],
        'capacity' => [
            'required' => 'Kapasitas kelas wajib diisi.',
            'integer' => 'Kapasitas kelas harus berupa angka.',
            'greater_than' => 'Kapasitas kelas harus lebih dari 0.'
        ],
        'description' => [
            'max_length' => 'Deskripsi maksimal 500 karakter.'
        ],
        'is_active' => [
            'in_list' => 'Status aktif harus berupa 0 atau 1.'
        ],
        'academic_year' => [
            'required' => 'Tahun akademik wajib diisi.',
            'max_length' => 'Tahun akademik maksimal 20 karakter.'
        ],
        'homeroom_teacher_id' => [
            'integer' => 'ID wali kelas harus berupa angka.'
        ]
    ];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get all classes with additional details
     */
    public function getClassesWithDetails()
    {
        return $this->select('classes.*, users.full_name as homeroom_teacher_name, 
                           (SELECT COUNT(*) FROM user_classes WHERE user_classes.class_id = classes.id) as student_count')
            ->join('users', 'users.id = classes.homeroom_teacher_id', 'left')
            ->where('users.role', 'teacher')
            ->orWhere('classes.homeroom_teacher_id IS NULL')
            ->orderBy('classes.level', 'ASC')->orderBy('classes.name', 'ASC')
            ->findAll();
    }

    /**
     * Get class with teacher and student count
     */
    public function getClassWithDetails($id)
    {
        return $this->select('classes.*, users.full_name as homeroom_teacher_name, users.email as homeroom_teacher_email,
                           (SELECT COUNT(*) FROM user_classes WHERE user_classes.class_id = classes.id) as student_count')
            ->join('users', 'users.id = classes.homeroom_teacher_id', 'left')->where('classes.id', $id)
            ->first();
    }

    /**
     * Get available teachers for homeroom assignment
     */
    public function getAvailableTeachers()
    {
        $userModel = new \App\Models\UserModel();
        return $userModel->select('id, full_name as name, email')
            ->where('role', 'teacher')
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
    }

    /**
     * Get classes by academic year
     */
    public function getClassesByAcademicYear($academicYear)
    {
        return $this->where('academic_year', $academicYear)
            ->where('is_active', 1)
            ->orderBy('level', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Get classes by level
     */
    public function getClassesByLevel($level)
    {
        return $this->where('level', $level)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
    }

    /**
     * Check if class name is unique (for validation)
     */
    public function isNameUnique($name, $excludeId = null)
    {
        $builder = $this->where('name', $name);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() === 0;
    }

    /**
     * Get student count for a class
     */
    public function getStudentCount($classId)
    {
        $db = \Config\Database::connect();
        return $db->table('user_classes')
            ->where('class_id', $classId)
            ->countAllResults();
    }

    /**
     * Check if class can be deleted (no students enrolled)
     */
    public function canDelete($classId)
    {
        return $this->getStudentCount($classId) === 0;
    }

    /**
     * Get classes statistics
     */
    public function getStatistics()
    {
        $db = \Config\Database::connect();

        // Total classes
        $totalClasses = $this->countAllResults();

        // Active classes
        $activeClasses = $this->where('is_active', 1)->countAllResults();

        // Total students across all classes
        $totalStudents = $db->table('user_classes uc')
            ->join('users u', 'u.id = uc.user_id')
            ->where('u.role', 'student')
            ->where('u.is_active', 1)
            ->countAllResults();

        // Classes with assigned teachers
        $classTeachers = $this->where('homeroom_teacher_id IS NOT NULL')->countAllResults();
        return [
            'total' => $totalClasses,
            'active' => $activeClasses,
            'students' => $totalStudents,
            'teachers' => $classTeachers,
            'average_capacity' => $this->selectAvg('capacity')->first()['capacity'] ?? 0
        ];
    }

    /**
     * Get classes data for DataTables with enhanced information
     */
    public function getClassesForDataTable($start = 0, $length = 10, $search = '')
    {
        $builder = $this->select('classes.*, 
                                users.full_name as teacher_name,
                                users.email as teacher_email,
                                (SELECT COUNT(*) FROM user_classes WHERE user_classes.class_id = classes.id) as student_count')
            ->join('users', 'users.id = classes.homeroom_teacher_id', 'left');

        // Apply search filter if provided
        if (!empty($search)) {
            $builder->groupStart()
                ->like('classes.name', $search)
                ->orLike('classes.level', $search)
                ->orLike('classes.academic_year', $search)
                ->orLike('classes.description', $search)
                ->orLike('users.full_name', $search)
                ->groupEnd();
        }

        // Count total records
        $totalRecords = $this->countAllResults();

        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Get paginated results
        $data = $builder->orderBy('classes.level', 'ASC')
            ->orderBy('classes.name', 'ASC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return [
            'data' => $data,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords
        ];
    }
}
