<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddAcademicYearsSeeder extends Seeder
{
    public function run()
    {
        // Check if academic years already exist
        $existingAcademicYear = $this->db->table('academic_years')->where('name', '2024-2025')->get()->getRow();

        if ($existingAcademicYear) {
            echo "Academic years already exist. Skipping seeding.\n";
            return;
        }

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

        $this->db->table('academic_years')->insertBatch($academicYears);

        // Get IDs for relationships
        $teacherId = $this->db->table('users')->where('role', 'teacher')->get()->getRow();
        if (!$teacherId) {
            echo "No teacher found. Academic years created but no schedules.\n";
            return;
        }
        $teacherId = $teacherId->id;

        $activeAcademicYear = $this->db->table('academic_years')->where('is_active', 1)->get()->getRow();
        $academicYearId = $activeAcademicYear->id;

        // Get subject IDs
        $mathSubject = $this->db->table('subjects')->where('name', 'Matematika')->get()->getRow();
        $physicsSubject = $this->db->table('subjects')->where('name', 'Fisika')->get()->getRow();

        if (!$mathSubject || !$physicsSubject) {
            echo "Academic years created, but subjects not found for schedules.\n";
            return;
        }

        // Get class IDs
        $classes = $this->db->table('classes')->get()->getResult();

        if (empty($classes)) {
            echo "Academic years created, but no classes found for schedules.\n";
            return;
        }

        // Create Sample Schedules for each class
        $scheduleData = [];
        $days = [1, 2, 3, 4, 5]; // Monday to Friday
        $timeSlots = [
            ['08:00:00', '09:30:00'],
            ['09:45:00', '11:15:00'],
            ['13:00:00', '14:30:00'],
            ['14:45:00', '16:15:00']
        ];

        $subjects = [$mathSubject, $physicsSubject];
        $rooms = ['Room 101', 'Room 102', 'Lab Physics', 'Lab Math'];

        foreach ($classes as $index => $class) {
            foreach ($days as $dayIndex => $day) {
                if ($dayIndex < 2) { // Only create 2 schedules per class to avoid too much data
                    $subject = $subjects[$dayIndex % count($subjects)];
                    $timeSlot = $timeSlots[$dayIndex % count($timeSlots)];
                    $room = $rooms[$dayIndex % count($rooms)];

                    $scheduleData[] = [
                        'academic_year_id' => $academicYearId,
                        'class_id' => $class->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacherId,
                        'day_of_week' => $day,
                        'start_time' => $timeSlot[0],
                        'end_time' => $timeSlot[1],
                        'room' => $room,
                        'is_active' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                }
            }
        }

        if (!empty($scheduleData)) {
            $this->db->table('schedules')->insertBatch($scheduleData);
            echo "Academic years and " . count($scheduleData) . " schedules created successfully!\n";
        } else {
            echo "Academic years created, but no schedules were generated.\n";
        }
    }
}
