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
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'code' => 'required|alpha_numeric_punct|max_length[20]|is_unique[subjects.code,id,{id}]',
        'teacher_id' => 'permit_empty|integer'
    ];

    protected $validationMessages = [
        'code' => [
            'is_unique' => 'Kode mata pelajaran sudah digunakan.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    public function getSubjectsWithTeacher()
    {
        return $this->select('subjects.*, users.full_name as teacher_name')
            ->join('users', 'users.id = subjects.teacher_id', 'left')
            ->findAll();
    }

    public function getSubjectsByTeacher($teacherId)
    {
        return $this->where('teacher_id', $teacherId)->findAll();
    }
}
