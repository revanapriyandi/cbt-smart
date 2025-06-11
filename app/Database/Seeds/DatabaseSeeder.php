<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Clear existing data
        $this->db->table('exam_results')->truncate();
        $this->db->table('student_answers')->truncate();
        $this->db->table('exam_questions')->truncate();
        $this->db->table('exams')->truncate();
        $this->db->table('subjects')->truncate();
        $this->db->table('users')->truncate();

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        // Insert users
        $users = [
            [
                'username' => 'admin',
                'email' => 'admin@cbt.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'full_name' => 'Administrator',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher1',
                'email' => 'teacher1@cbt.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role' => 'teacher',
                'full_name' => 'Teacher One',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'teacher2',
                'email' => 'teacher2@cbt.com',
                'password' => password_hash('teacher123', PASSWORD_DEFAULT),
                'role' => 'teacher',
                'full_name' => 'Teacher Two',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Add 5 students
        for ($i = 1; $i <= 5; $i++) {
            $users[] = [
                'username' => "student{$i}",
                'email' => "student{$i}@cbt.com",
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'student',
                'full_name' => "Student {$i}",
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        $this->db->table('users')->insertBatch($users);

        // Insert subjects
        $subjects = [
            [
                'name' => 'Matematika',
                'description' => 'Mata pelajaran Matematika',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bahasa Indonesia',
                'description' => 'Mata pelajaran Bahasa Indonesia',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bahasa Inggris',
                'description' => 'Mata pelajaran Bahasa Inggris',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'IPA',
                'description' => 'Mata pelajaran Ilmu Pengetahuan Alam',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('subjects')->insertBatch($subjects);

        // Insert sample exam
        $exam = [
            'title' => 'Ujian Matematika - Latihan 1',
            'subject_id' => 1, // Matematika
            'teacher_id' => 2, // teacher1
            'description' => 'Ujian latihan matematika untuk mengukur pemahaman siswa',
            'duration' => 60, // 60 minutes
            'start_time' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            'end_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'instructions' => 'Jawablah semua pertanyaan dengan lengkap dan jelas. Waktu pengerjaan 60 menit.',
            'status' => 'scheduled',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('exams')->insert($exam);

        // Insert sample questions
        $questions = [
            [
                'exam_id' => 1,
                'question_text' => 'Jelaskan konsep limit dalam matematika dan berikan contoh penerapannya dalam kehidupan sehari-hari.',
                'max_score' => 25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'exam_id' => 1,
                'question_text' => 'Selesaikan persamaan kuadrat berikut: x² - 5x + 6 = 0. Tunjukkan langkah-langkah penyelesaiannya.',
                'max_score' => 25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'exam_id' => 1,
                'question_text' => 'Buatlah grafik fungsi f(x) = 2x + 3 dan jelaskan karakteristik grafik tersebut.',
                'max_score' => 25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'exam_id' => 1,
                'question_text' => 'Hitunglah luas dan keliling lingkaran dengan jari-jari 7 cm. Gunakan π = 22/7.',
                'max_score' => 25,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('exam_questions')->insertBatch($questions);

        // Clear and create Academic Years
        $this->db->table('schedules')->truncate();
        $this->db->table('academic_years')->truncate();

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

        echo "Database seeded successfully!\n";
        echo "Login credentials:\n";
        echo "Admin: admin / admin123\n";
        echo "Teacher: teacher1 / teacher123 or teacher2 / teacher123\n";
        echo "Student: student1 / student123 (up to student5)\n";
        echo "Academic years: 2023-2024, 2024-2025 (active), 2025-2026\n";
    }
}
