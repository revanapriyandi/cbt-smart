<?php

namespace App\Models;

use CodeIgniter\Model;

class AcademicYearModel extends Model
{
    protected $table = 'academic_years';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'name',
        'code',
        'start_date',
        'end_date',
        'is_active',
        'is_current'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Basic validation rules
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[100]',
        'code' => 'required|min_length[2]|max_length[20]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Academic year name is required',
            'min_length' => 'Academic year name must be at least 3 characters',
            'max_length' => 'Academic year name cannot exceed 100 characters'
        ],
        'code' => [
            'required' => 'Academic year code is required',
            'min_length' => 'Academic year code must be at least 2 characters',
            'max_length' => 'Academic year code cannot exceed 20 characters'
        ],
        'start_date' => [
            'required' => 'Start date is required',
            'valid_date' => 'Start date format is invalid'
        ],
        'end_date' => [
            'required' => 'End date is required',
            'valid_date' => 'End date format is invalid'
        ]
    ];

    /**
     * Get current academic year
     */
    public function getCurrentAcademicYear()
    {
        return $this->where('is_current', 1)->where('is_active', 1)->first();
    }

    /**
     * Get active academic years
     */
    public function getActiveAcademicYears()
    {
        return $this->where('is_active', 1)->orderBy('start_date', 'DESC')->findAll();
    }

    /**
     * Get academic years for dropdown
     */
    public function getAcademicYearsForDropdown()
    {
        $years = $this->select('id, name, code, is_current')
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();

        $dropdown = [];
        foreach ($years as $year) {
            $label = $year['name'];
            if ($year['is_current']) {
                $label .= ' (Current)';
            }
            $dropdown[$year['id']] = $label;
        }

        return $dropdown;
    }
}
