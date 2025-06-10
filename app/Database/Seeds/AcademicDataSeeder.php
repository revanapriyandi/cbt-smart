<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        $this->db->table('schedules')->truncate();
        $this->db->table('academic_years')->truncate();

        // Create Academic Years
        $academicYears = [
            [
                'name' => '2023-2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'is_active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => '2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => '2025-2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($academicYears as $year) {
            $this->db->table('academic_years')->insert($year);
        }

        // Get IDs for relationships
        $teacherId = $this->db->table('users')->where('role', 'teacher')->get()->getRow();
        if (!$teacherId) {
            echo "Please run InitialDataSeeder first to create teacher users.\n";
            return;
        }
        $teacherId = $teacherId->id;

        $activeAcademicYear = $this->db->table('academic_years')->where('is_active', 1)->get()->getRow();
        $academicYearId = $activeAcademicYear->id;

        // Get subject IDs
        $mathSubject = $this->db->table('subjects')->where('name', 'Matematika')->get()->getRow();
        $physicsSubject = $this->db->table('subjects')->where('name', 'Fisika')->get()->getRow();
        $chemistrySubject = $this->db->table('subjects')->where('name', 'Kimia')->get()->getRow();

        if (!$mathSubject || !$physicsSubject || !$chemistrySubject) {
            echo "Please run InitialDataSeeder first to create subjects.\n";
            return;
        }

        // Get class IDs
        $class7A = $this->db->table('classes')->where('name', 'Kelas 7A')->get()->getRow();
        $class7B = $this->db->table('classes')->where('name', 'Kelas 7B')->get()->getRow();
        $class8A = $this->db->table('classes')->where('name', 'Kelas 8A')->get()->getRow();

        if (!$class7A || !$class7B || !$class8A) {
            echo "Please run ClassSeeder first to create classes.\n";
            return;
        }

        // Create Sample Schedules
        $schedules = [
            // Monday schedules
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7A->id,
                'subject_id' => $mathSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 1, // Monday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Room 101',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7A->id,
                'subject_id' => $physicsSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 1, // Monday
                'start_time' => '09:45:00',
                'end_time' => '11:15:00',
                'room' => 'Lab Physics',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Tuesday schedules
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7B->id,
                'subject_id' => $mathSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 2, // Tuesday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Room 102',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class8A->id,
                'subject_id' => $chemistrySubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 2, // Tuesday
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'room' => 'Lab Chemistry',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Wednesday schedules
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7A->id,
                'subject_id' => $chemistrySubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 3, // Wednesday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Lab Chemistry',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Thursday schedules
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7B->id,
                'subject_id' => $physicsSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 4, // Thursday
                'start_time' => '09:00:00',
                'end_time' => '10:30:00',
                'room' => 'Lab Physics',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Friday schedules
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class8A->id,
                'subject_id' => $mathSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => 5, // Friday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Room 103',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            // Today's schedule (for testing "Today's Schedules" count)
            [
                'academic_year_id' => $academicYearId,
                'class_id' => $class7A->id,
                'subject_id' => $mathSubject->id,
                'teacher_id' => $teacherId,
                'day_of_week' => date('N'), // Today's day of week
                'start_time' => '14:00:00',
                'end_time' => '15:30:00',
                'room' => 'Room 104',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($schedules as $schedule) {
            $this->db->table('schedules')->insert($schedule);
        }

        echo "Academic years and schedules have been seeded successfully!\n";
        echo "- Created 3 academic years (2023-2024, 2024-2025 active, 2025-2026)\n";
        echo "- Created 8 sample schedules across different days and classes\n";
        echo "- Schedules include: Math, Physics, Chemistry for classes 7A, 7B, 8A\n";
    }
}
