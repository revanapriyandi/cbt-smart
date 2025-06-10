<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ComprehensiveDataSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Disable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Clear existing data
        $db->table('schedules')->truncate();
        $db->table('academic_years')->truncate();
        $db->table('classes')->truncate();
        $db->table('subjects')->truncate();

        // Clear users but keep admin
        $db->query('DELETE FROM users WHERE role != "admin"');

        // Re-enable foreign key checks
        $db->query('SET FOREIGN_KEY_CHECKS = 1');

        // 1. Insert subjects
        $subjects = [
            [
                'code' => 'MTK',
                'name' => 'Matematika',
                'description' => 'Mata pelajaran Matematika',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'BIN',
                'name' => 'Bahasa Indonesia',
                'description' => 'Mata pelajaran Bahasa Indonesia',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'IPA',
                'name' => 'Ilmu Pengetahuan Alam',
                'description' => 'Mata pelajaran IPA',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'IPS',
                'name' => 'Ilmu Pengetahuan Sosial',
                'description' => 'Mata pelajaran IPS',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('subjects')->insertBatch($subjects);

        // 2. Insert classes
        $classes = [
            [
                'code' => 'X-IPA-1',
                'name' => 'X IPA 1',
                'description' => 'Kelas X IPA 1',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'X-IPA-2',
                'name' => 'X IPA 2',
                'description' => 'Kelas X IPA 2',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => 'X-IPS-1',
                'name' => 'X IPS 1',
                'description' => 'Kelas X IPS 1',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('classes')->insertBatch($classes);

        // 3. Insert teachers
        $teachers = [
            [
                'username' => 'teacher1',
                'email' => 'teacher1@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Budi Santoso, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher2',
                'email' => 'teacher2@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Siti Nurhaliza, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher3',
                'email' => 'teacher3@cbt-smart.com',
                'password' => password_hash('password123', PASSWORD_DEFAULT),
                'full_name' => 'Ahmad Wijaya, S.Pd',
                'role' => 'teacher',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('users')->insertBatch($teachers);

        // 4. Insert academic years
        $academicYears = [
            [
                'code' => '2023-2024',
                'name' => '2023-2024',
                'start_date' => '2023-08-01',
                'end_date' => '2024-07-31',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => '2024-2025',
                'name' => '2024-2025',
                'start_date' => '2024-08-01',
                'end_date' => '2025-07-31',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'code' => '2025-2026',
                'name' => '2025-2026',
                'start_date' => '2025-08-01',
                'end_date' => '2026-07-31',
                'is_active' => false,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('academic_years')->insertBatch($academicYears);

        // Get the active academic year
        $activeAcademicYear = $db->table('academic_years')->where('is_active', true)->get()->getRowArray();
        $academicYearId = $activeAcademicYear['id'];

        // 5. Insert schedules
        $schedules = [
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 1, // Matematika
                'teacher_id' => 1, // Budi Santoso
                'day_of_week' => 1, // Monday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas A',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 2, // Bahasa Indonesia
                'teacher_id' => 2, // Siti Nurhaliza
                'day_of_week' => 2, // Tuesday
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'room' => 'Ruang Kelas A',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 2, // X IPA 2
                'subject_id' => 1, // Matematika
                'teacher_id' => 1, // Budi Santoso
                'day_of_week' => 3, // Wednesday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas B',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 2, // X IPA 2
                'subject_id' => 3, // IPA
                'teacher_id' => 3, // Ahmad Wijaya
                'day_of_week' => 4, // Thursday
                'start_time' => '10:00:00',
                'end_time' => '11:30:00',
                'room' => 'Lab IPA',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 3, // X IPS 1
                'subject_id' => 4, // IPS
                'teacher_id' => 2, // Siti Nurhaliza
                'day_of_week' => 5, // Friday
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'room' => 'Ruang Kelas C',
                'is_active' => true,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'academic_year_id' => $academicYearId,
                'class_id' => 1, // X IPA 1
                'subject_id' => 3, // IPA
                'teacher_id' => 3, // Ahmad Wijaya
                'day_of_week' => 1, // Monday
                'start_time' => '13:00:00',
                'end_time' => '14:30:00',
                'room' => 'Lab IPA',
                'is_active' => false, // Inactive for testing
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $db->table('schedules')->insertBatch($schedules);

        echo "Comprehensive data seeded successfully!\n";
        echo "- Subjects: " . count($subjects) . "\n";
        echo "- Classes: " . count($classes) . "\n";
        echo "- Teachers: " . count($teachers) . "\n";
        echo "- Academic Years: " . count($academicYears) . "\n";
        echo "- Schedules: " . count($schedules) . "\n";
    }
}
