<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Check if admin user already exists
        $existingAdmin = $this->db->table('users')->where('username', 'admin')->get()->getRow();

        if ($existingAdmin) {
            echo "Sample data already exists. Skipping seeding.\n";
            echo "Login credentials:\n";
            echo "Admin: admin / admin123\n";
            echo "Teacher: teacher1 / teacher123\n";
            echo "Student: student1 / student123 (up to student5)\n";
            return;
        }

        // Create admin user
        $this->db->table('users')->insert([
            'username' => 'admin',
            'email' => 'admin@cbt.com',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'Administrator',
            'role' => 'admin',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create teacher user
        $this->db->table('users')->insert([
            'username' => 'teacher1',
            'email' => 'teacher@cbt.com',
            'password' => password_hash('teacher123', PASSWORD_DEFAULT),
            'full_name' => 'Guru Matematika',
            'role' => 'teacher',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create student users
        for ($i = 1; $i <= 5; $i++) {
            $this->db->table('users')->insert([
                'username' => "student{$i}",
                'email' => "student{$i}@cbt.com",
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'full_name' => "Siswa {$i}",
                'role' => 'student',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create subjects
        $teacherId = $this->db->table('users')->where('username', 'teacher1')->get()->getRow()->id;

        $subjects = [
            ['name' => 'Matematika', 'code' => 'MAT'],
            ['name' => 'Fisika', 'code' => 'FIS'],
            ['name' => 'Kimia', 'code' => 'KIM'],
            ['name' => 'Bahasa Indonesia', 'code' => 'BIND'],
            ['name' => 'Bahasa Inggris', 'code' => 'BING']
        ];

        foreach ($subjects as $subject) {
            $this->db->table('subjects')->insert([
                'name' => $subject['name'],
                'code' => $subject['code'],
                'description' => "Mata pelajaran {$subject['name']}",
                'teacher_id' => $teacherId,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Create sample exam
        $subjectId = $this->db->table('subjects')->where('name', 'Matematika')->get()->getRow()->id;

        $this->db->table('exams')->insert([
            'title' => 'Ujian Matematika - Aljabar',
            'description' => 'Ujian essay tentang konsep aljabar dasar',
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
            'pdf_url' => 'https://example.com/aljabar.pdf',
            'pdf_content' => 'Konsep dasar aljabar meliputi variabel, konstanta, dan operasi matematika...',
            'question_count' => 3,
            'duration_minutes' => 60,
            'start_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
            'end_time' => date('Y-m-d H:i:s', strtotime('+2 days')),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // Create sample exam questions
        $examId = $this->db->table('exams')->where('title', 'Ujian Matematika - Aljabar')->get()->getRow()->id;

        $questions = [
            'Jelaskan perbedaan antara variabel dan konstanta dalam aljabar, berikan contoh masing-masing!',
            'Selesaikan persamaan linear berikut: 3x + 5 = 20. Tunjukkan langkah-langkah penyelesaiannya!',
            'Faktorkan bentuk aljabar x² + 5x + 6 dan jelaskan metode yang digunakan!'
        ];

        foreach ($questions as $index => $question) {
            $this->db->table('exam_questions')->insert([
                'exam_id' => $examId,
                'question_number' => $index + 1,
                'question_text' => $question,
                'max_score' => 10.0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        echo "Sample data has been seeded successfully!\n";
        echo "Login credentials:\n";
        echo "Admin: admin / admin123\n";
        echo "Teacher: teacher1 / teacher123\n";
        echo "Student: student1 / student123 (up to student5)\n";
    }
}
