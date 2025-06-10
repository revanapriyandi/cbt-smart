<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'full_name',
        'role',
        'is_active',
        'last_login'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';    // Validation
    protected $validationRules = [
        'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'full_name' => 'required|max_length[100]',
        'role' => 'required|in_list[admin,teacher,student]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Email sudah digunakan oleh user lain.'
        ],
        'username' => [
            'is_unique' => 'Username sudah digunakan oleh user lain.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function getTeachers()
    {
        return $this->where('role', 'teacher')
            ->where('is_active', 1)
            ->findAll();
    }

    public function getStudents()
    {
        return $this->where('role', 'student')
            ->where('is_active', 1)
            ->findAll();
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Update user with custom validation rules
     */
    public function updateUser($id, $data)
    {
        // Set up validation rules for update
        $rules = [
            'username' => "required|alpha_numeric_punct|min_length[3]|max_length[50]|is_unique[users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'full_name' => 'required|max_length[100]',
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        // Add password validation only if password is provided
        if (isset($data['password']) && !empty($data['password'])) {
            $rules['password'] = 'required|min_length[6]';
        }

        $this->setValidationRules($rules);

        return $this->update($id, $data);
    }

    /**
     * Get students by class ID
     */
    public function getStudentsByClass($classId)
    {
        return $this->select('users.*, user_classes.enrolled_at, user_classes.status')
            ->join('user_classes', 'user_classes.user_id = users.id')
            ->where('user_classes.class_id', $classId)
            ->where('users.role', 'student')
            ->where('user_classes.status', 'active')
            ->orderBy('users.full_name', 'ASC')
            ->findAll();
    }

    /**
     * Add student to class
     */
    public function addStudentToClass($userId, $classId)
    {
        $db = \Config\Database::connect();

        // Check if already enrolled
        $existing = $db->table('user_classes')
            ->where('user_id', $userId)
            ->where('class_id', $classId)
            ->get()
            ->getRow();

        if ($existing) {
            return false; // Already enrolled
        }

        return $db->table('user_classes')->insert([
            'user_id' => $userId,
            'class_id' => $classId,
            'enrolled_at' => date('Y-m-d H:i:s'),
            'status' => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Remove student from class
     */
    public function removeStudentFromClass($userId, $classId)
    {
        $db = \Config\Database::connect();
        return $db->table('user_classes')
            ->where('user_id', $userId)
            ->where('class_id', $classId)
            ->delete();
    }

    /**
     * Get classes for a student
     */
    public function getStudentClasses($userId)
    {
        $db = \Config\Database::connect();
        return $db->table('user_classes uc')
            ->select('c.*, uc.enrolled_at, uc.status, u.full_name as homeroom_teacher_name')
            ->join('classes c', 'c.id = uc.class_id')
            ->join('users u', 'u.id = c.homeroom_teacher_id', 'left')
            ->where('uc.user_id', $userId)
            ->where('uc.status', 'active')
            ->orderBy('c.name', 'ASC')
            ->get()
            ->getResultArray();
    }
}
