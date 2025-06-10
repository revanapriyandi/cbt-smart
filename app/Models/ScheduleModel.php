<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'academic_year_id',
        'class_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'duration',
        'room',
        'notes',
        'status',
        'created_by',
        'updated_by'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'academic_year_id' => 'required|integer',
        'class_id' => 'required|integer',
        'subject_id' => 'required|integer',
        'teacher_id' => 'required|integer',
        'day_of_week' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[7]',
        'start_time' => 'required',
        'end_time' => 'required',
        'duration' => 'permit_empty|integer',
        'room' => 'permit_empty|max_length[100]',
        'notes' => 'permit_empty|max_length[500]',
        'status' => 'required|in_list[active,inactive,suspended]'
    ];

    protected $validationMessages = [
        'academic_year_id' => [
            'required' => 'Tahun ajaran harus dipilih',
            'integer' => 'Tahun ajaran tidak valid'
        ],
        'class_id' => [
            'required' => 'Kelas harus dipilih',
            'integer' => 'Kelas tidak valid'
        ],
        'subject_id' => [
            'required' => 'Mata pelajaran harus dipilih',
            'integer' => 'Mata pelajaran tidak valid'
        ],
        'teacher_id' => [
            'required' => 'Guru harus dipilih',
            'integer' => 'Guru tidak valid'
        ],
        'day_of_week' => [
            'required' => 'Hari harus dipilih',
            'integer' => 'Hari tidak valid',
            'greater_than_equal_to' => 'Hari harus antara 1-7',
            'less_than_equal_to' => 'Hari harus antara 1-7'
        ],
        'start_time' => [
            'required' => 'Waktu mulai harus diisi'
        ],
        'end_time' => [
            'required' => 'Waktu selesai harus diisi'
        ],
        'room' => [
            'max_length' => 'Nama ruangan maksimal 100 karakter'
        ],
        'notes' => [
            'max_length' => 'Catatan maksimal 500 karakter'
        ],
        'status' => [
            'required' => 'Status harus dipilih',
            'in_list' => 'Status harus active atau inactive'
        ]
    ];

    public function getSchedulesWithDetails()
    {
        return $this->select('
                schedules.*,
                academic_years.name as academic_year_name,
                classes.name as class_name,
                subjects.name as subject_name,
                users.full_name as teacher_name
            ')
            ->join('academic_years', 'academic_years.id = schedules.academic_year_id', 'left')
            ->join('classes', 'classes.id = schedules.class_id', 'left')
            ->join('subjects', 'subjects.id = schedules.subject_id', 'left')
            ->join('users', 'users.id = schedules.teacher_id', 'left');
    }

    public function getScheduleWithDetails($id)
    {
        return $this->getSchedulesWithDetails()
            ->where('schedules.id', $id)
            ->first();
    }

    public function getTodaySchedulesCount()
    {
        $today = date('N'); // 1 (Monday) to 7 (Sunday)
        return $this->where('day_of_week', $today)
            ->where('status', 'active')
            ->countAllResults();
    }

    public function getThisWeekSchedulesCount()
    {
        return $this->where('status', 'active')
            ->countAllResults();
    }

    public function hasConflict($data, $excludeId = null)
    {
        $builder = $this->where('teacher_id', $data['teacher_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('status', 'active');

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        // Check for time overlap
        $builder->groupStart()
            ->where('start_time <', $data['end_time'])
            ->where('end_time >', $data['start_time'])
            ->groupEnd();

        $teacherConflict = $builder->countAllResults() > 0;

        // Check for class conflict
        $builder = $this->where('class_id', $data['class_id'])
            ->where('day_of_week', $data['day_of_week'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('status', 'active');

        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }

        $builder->groupStart()
            ->where('start_time <', $data['end_time'])
            ->where('end_time >', $data['start_time'])
            ->groupEnd();

        $classConflict = $builder->countAllResults() > 0;

        // Check for room conflict if room is specified
        $roomConflict = false;
        if (!empty($data['room'])) {
            $builder = $this->where('room', $data['room'])
                ->where('day_of_week', $data['day_of_week'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->where('status', 'active');

            if ($excludeId) {
                $builder->where('id !=', $excludeId);
            }

            $builder->groupStart()
                ->where('start_time <', $data['end_time'])
                ->where('end_time >', $data['start_time'])
                ->groupEnd();

            $roomConflict = $builder->countAllResults() > 0;
        }

        return $teacherConflict || $classConflict || $roomConflict;
    }

    public function getWeeklySchedule($classId = null, $teacherId = null, $date = null)
    {
        $builder = $this->getSchedulesWithDetails()
            ->where('schedules.status', 'active')
            ->orderBy('schedules.day_of_week ASC, schedules.start_time ASC');

        if ($classId) {
            $builder->where('schedules.class_id', $classId);
        }

        if ($teacherId) {
            $builder->where('schedules.teacher_id', $teacherId);
        }

        // Get current academic year if no specific date provided
        if (!$date) {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->where('is_current', 1)->first();
            if ($currentYear) {
                $builder->where('schedules.academic_year_id', $currentYear['id']);
            }
        }

        $schedules = $builder->findAll();

        // Group by day
        $weeklySchedule = [
            1 => [], // Monday
            2 => [], // Tuesday
            3 => [], // Wednesday
            4 => [], // Thursday
            5 => [], // Friday
            6 => [], // Saturday
            7 => []  // Sunday
        ];

        foreach ($schedules as $schedule) {
            $weeklySchedule[$schedule['day_of_week']][] = $schedule;
        }

        return $weeklySchedule;
    }

    public function getRelatedSchedules($currentId, $classId, $teacherId, $limit = 5)
    {
        return $this->getSchedulesWithDetails()
            ->where('schedules.id !=', $currentId)
            ->groupStart()
            ->where('schedules.class_id', $classId)
            ->orWhere('schedules.teacher_id', $teacherId)
            ->groupEnd()
            ->where('schedules.status', 'active')
            ->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->limit($limit)
            ->findAll();
    }

    public function getTeacherSchedules($teacherId, $academicYearId = null)
    {
        $builder = $this->getSchedulesWithDetails()
            ->where('schedules.teacher_id', $teacherId)
            ->where('schedules.status', 'active');

        if ($academicYearId) {
            $builder->where('schedules.academic_year_id', $academicYearId);
        }

        return $builder->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->findAll();
    }

    public function getClassSchedules($classId, $academicYearId = null)
    {
        $builder = $this->getSchedulesWithDetails()
            ->where('schedules.class_id', $classId)
            ->where('schedules.status', 'active');

        if ($academicYearId) {
            $builder->where('schedules.academic_year_id', $academicYearId);
        }

        return $builder->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->findAll();
    }

    public function getSubjectSchedules($subjectId, $academicYearId = null)
    {
        $builder = $this->getSchedulesWithDetails()
            ->where('schedules.subject_id', $subjectId)
            ->where('schedules.status', 'active');

        if ($academicYearId) {
            $builder->where('schedules.academic_year_id', $academicYearId);
        }

        return $builder->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->findAll();
    }

    public function getScheduleStatistics($academicYearId = null)
    {
        $builder = $this->select('
                COUNT(*) as total_schedules,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active_schedules,
                COUNT(CASE WHEN status = "inactive" THEN 1 END) as inactive_schedules,
                COUNT(DISTINCT class_id) as total_classes,
                COUNT(DISTINCT teacher_id) as total_teachers,
                COUNT(DISTINCT subject_id) as total_subjects,
                AVG(duration) as avg_duration
            ');

        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        }

        return $builder->get()->getRowArray();
    }

    public function duplicateScheduleForAcademicYear($fromYearId, $toYearId, $userId)
    {
        $schedules = $this->where('academic_year_id', $fromYearId)
            ->where('status', 'active')
            ->findAll();

        $duplicatedCount = 0;
        $errors = [];

        foreach ($schedules as $schedule) {
            try {
                $newSchedule = $schedule;
                unset($newSchedule['id']);
                $newSchedule['academic_year_id'] = $toYearId;
                $newSchedule['created_by'] = $userId;
                $newSchedule['updated_by'] = null;
                $newSchedule['created_at'] = date('Y-m-d H:i:s');
                $newSchedule['updated_at'] = date('Y-m-d H:i:s');

                if ($this->insert($newSchedule)) {
                    $duplicatedCount++;
                } else {
                    $errors[] = "Gagal menduplikasi jadwal: " . implode(', ', $this->errors());
                }
            } catch (\Exception $e) {
                $errors[] = "Error: " . $e->getMessage();
            }
        }

        return [
            'success' => $duplicatedCount > 0,
            'duplicated_count' => $duplicatedCount,
            'errors' => $errors
        ];
    }
    public function logActivity($scheduleId, $action, $description, $userId)
    {
        $logModel = new \App\Models\UserActivityLogModel();

        return $logModel->insert([
            'user_id' => $userId,
            'module' => 'schedules',
            'action' => $action,
            'description' => $description,
            'reference_id' => $scheduleId,
            'ip_address' => \Config\Services::request()->getIPAddress(),
            'user_agent' => \Config\Services::request()->getUserAgent()->getAgentString()
        ]);
    }

    public function validateTimeRange($startTime, $endTime)
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);

        if ($start >= $end) {
            return [
                'valid' => false,
                'message' => 'Waktu selesai harus lebih besar dari waktu mulai'
            ];
        }

        $duration = ($end - $start) / 60; // in minutes

        if ($duration < 30) {
            return [
                'valid' => false,
                'message' => 'Durasi minimal 30 menit'
            ];
        }

        if ($duration > 480) { // 8 hours
            return [
                'valid' => false,
                'message' => 'Durasi maksimal 8 jam'
            ];
        }

        return [
            'valid' => true,
            'duration' => $duration
        ];
    }

    public function getSchedulesByDateRange($startDate, $endDate, $filters = [])
    {
        $builder = $this->getSchedulesWithDetails();

        // Apply filters
        if (!empty($filters['class_id'])) {
            $builder->where('schedules.class_id', $filters['class_id']);
        }

        if (!empty($filters['teacher_id'])) {
            $builder->where('schedules.teacher_id', $filters['teacher_id']);
        }

        if (!empty($filters['subject_id'])) {
            $builder->where('schedules.subject_id', $filters['subject_id']);
        }

        if (!empty($filters['status'])) {
            $builder->where('schedules.status', $filters['status']);
        }

        return $builder->orderBy('schedules.day_of_week ASC, schedules.start_time ASC')
            ->findAll();
    }
}
