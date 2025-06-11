<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * CompleteExamDataSeeder - Seeder lengkap untuk demo sistem CBT
 * 
 * Seeder ini membuat data lengkap untuk demo sistem CBT termasuk:
 * - Users (admin, teacher, student)  
 * - Academic Years
 * - Classes
 * - Subjects
 * - Exam Types
 * - Question Banks
 * - Questions & Options
 * - Exams
 * - Exam Sessions
 * - Participants & Results
 * - Sample answers & activity logs
 * 
 * @author CBT Smart Team
 * @version 1.0
 */
class CompleteExamDataSeeder extends Seeder
{
    public function run()
    {
        echo "Starting Complete Exam Data Seeder...\n";
        echo str_repeat("=", 60) . "\n";

        // Check if basic data exists
        if ($this->hasBasicData()) {
            echo "Basic data already exists, creating exam-specific data only...\n";
        } else {
            echo "Creating basic data first...\n";
            $this->createBasicData();
        }

        // Always create exam-related data
        $this->createExamData();

        echo str_repeat("=", 60) . "\n";
        echo "Complete Exam Data Seeder finished!\n";
        echo str_repeat("=", 60) . "\n";
    }

    /**
     * Check if basic data exists
     */
    private function hasBasicData()
    {
        $userCount = $this->db->table('users')->countAllResults();
        $subjectCount = $this->db->table('subjects')->countAllResults();
        $classCount = $this->db->table('classes')->countAllResults();

        return $userCount > 0 && $subjectCount > 0 && $classCount > 0;
    }

    /**
     * Create basic data (users, subjects, classes, etc.)
     */
    private function createBasicData()
    {
        // Clear existing data first
        $this->clearExistingData();

        // Create Academic Years
        $this->createAcademicYears();

        // Create Users
        $this->createUsers();

        // Create Subjects
        $this->createSubjects();

        // Create Classes
        $this->createClasses();

        // Assign students to classes
        $this->assignStudentsToClasses();
    }
    /**
     * Create exam-specific data
     */
    private function createExamData()
    {
        // Clear exam data
        $this->clearExamData();

        // Create exam types
        $this->createExamTypes();

        // Create question banks
        $this->createQuestionBanks();

        // Create questions and options
        $this->createQuestionsAndOptions();

        // Create exams
        $this->createExams();

        // Create exam sessions
        $this->createExamSessions();

        // Create exam participants
        $this->createExamParticipants();

        // Create sample results and activity logs
        $this->createSampleResults();
    }

    /**
     * Clear existing data
     */
    private function clearExistingData()
    {
        echo "Clearing existing data...\n";

        // Disable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        // Clear all tables
        $tables = [
            'exam_activity_logs',
            'student_answers',
            'exam_results',
            'exam_participants',
            'exam_sessions',
            'exam_questions',
            'exams',
            'question_options',
            'questions',
            'question_banks',
            'exam_types',
            'user_classes',
            'classes',
            'subjects',
            'users',
            'academic_years'
        ];

        foreach ($tables as $table) {
            $this->db->table($table)->truncate();
        }

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "Data cleared successfully\n";
    }

    /**
     * Clear only exam-related data
     */
    private function clearExamData()
    {
        echo "Clearing exam data...\n";

        // Disable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0');

        $examTables = [
            'exam_activity_logs',
            'student_answers',
            'exam_results',
            'exam_participants',
            'exam_sessions',
            'exam_questions',
            'exams',
            'question_options',
            'questions',
            'question_banks',
            'exam_types'
        ];

        foreach ($examTables as $table) {
            $this->db->table($table)->truncate();
        }

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1');

        echo "Exam data cleared\n";
    }

    /**
     * Create Academic Years
     */
    private function createAcademicYears()
    {
        echo "Creating Academic Years...\n";

        $academicYears = [
            [
                'name' => 'Tahun Ajaran 2024/2025',
                'code' => '2024-2025',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'is_active' => 1,
                'is_current' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Tahun Ajaran 2023/2024',
                'code' => '2023-2024',
                'start_date' => '2023-07-01',
                'end_date' => '2024-06-30',
                'is_active' => 0,
                'is_current' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($academicYears as $year) {
            $this->db->table('academic_years')->insert($year);
        }

        echo "Created " . count($academicYears) . " academic years\n";
    }

    /**
     * Create Users (Admin, Teachers, Students)
     */
    private function createUsers()
    {
        echo "Creating Users...\n";

        // Admin user
        $adminData = [
            'username' => 'admin',
            'email' => 'admin@cbt.test',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'full_name' => 'Administrator System',
            'role' => 'admin',
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('users')->insert($adminData);

        // Teachers
        $teachers = [
            [
                'username' => 'guru_matematika',
                'email' => 'matematika@cbt.test',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Budi Santoso, S.Pd',
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'guru_bahasa',
                'email' => 'bahasa@cbt.test',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Siti Rahayu, S.Pd',
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'guru_ipa',
                'email' => 'ipa@cbt.test',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Ahmad Wijaya, S.Si',
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'username' => 'guru_ips',
                'email' => 'ips@cbt.test',
                'password' => password_hash('guru123', PASSWORD_DEFAULT),
                'full_name' => 'Rina Marlina, S.Sos',
                'role' => 'teacher',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($teachers as $teacher) {
            $this->db->table('users')->insert($teacher);
        }

        // Students
        $studentNames = [
            'Andi Pratama',
            'Sari Dewi',
            'Roni Setiawan',
            'Maya Sari',
            'Dedi Kurniawan',
            'Lisa Anggraini',
            'Tono Wijaya',
            'Fitri Handayani',
            'Bram Sutrisno',
            'Indah Permata',
            'Joko Susilo',
            'Ratna Sari',
            'Hendra Gunawan',
            'Eka Putri',
            'Fajar Nugroho',
            'Dewi Lestari',
            'Agus Setiawan',
            'Nur Hasanah',
            'Rizki Ramadan',
            'Ayu Lestari',
            'Bayu Pratama',
            'Sinta Maharani',
            'Dika Saputra',
            'Lina Marlina',
            'Ferry Kurniawan'
        ];

        foreach ($studentNames as $index => $name) {
            $studentNum = str_pad($index + 1, 3, '0', STR_PAD_LEFT);
            $studentData = [
                'username' => 'siswa' . $studentNum,
                'email' => 'siswa' . $studentNum . '@cbt.test',
                'password' => password_hash('siswa123', PASSWORD_DEFAULT),
                'full_name' => $name,
                'role' => 'student',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->table('users')->insert($studentData);
        }

        echo "Created 1 admin, " . count($teachers) . " teachers, and " . count($studentNames) . " students\n";
    }

    /**
     * Create Subjects
     */
    private function createSubjects()
    {
        echo "Creating Subjects...\n";

        // Get teacher IDs
        $teachers = $this->db->table('users')->where('role', 'teacher')->get()->getResult();

        $subjects = [
            [
                'name' => 'Matematika',
                'code' => 'MTK',
                'description' => 'Mata pelajaran Matematika untuk melatih kemampuan logika dan analisis',
                'teacher_id' => $teachers[0]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Bahasa Indonesia',
                'code' => 'BID',
                'description' => 'Mata pelajaran Bahasa Indonesia untuk meningkatkan kemampuan komunikasi',
                'teacher_id' => $teachers[1]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ilmu Pengetahuan Alam',
                'code' => 'IPA',
                'description' => 'Mata pelajaran IPA untuk memahami fenomena alam dan sains',
                'teacher_id' => $teachers[2]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ilmu Pengetahuan Sosial',
                'code' => 'IPS',
                'description' => 'Mata pelajaran IPS untuk memahami kehidupan sosial dan bermasyarakat',
                'teacher_id' => $teachers[3]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($subjects as $subject) {
            $this->db->table('subjects')->insert($subject);
        }

        echo "Created " . count($subjects) . " subjects\n";
    }

    /**
     * Create Classes
     */
    private function createClasses()
    {
        echo "Creating Classes...\n";

        // Get teacher IDs for homeroom teachers
        $teachers = $this->db->table('users')->where('role', 'teacher')->get()->getResult();

        $classes = [
            [
                'name' => 'VII A',
                'level' => 7,
                'capacity' => 30,
                'description' => 'Kelas 7A - Kelas unggulan',
                'is_active' => 1,
                'academic_year' => '2024/2025',
                'homeroom_teacher_id' => $teachers[0]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'VII B',
                'level' => 7,
                'capacity' => 30,
                'description' => 'Kelas 7B - Kelas reguler',
                'is_active' => 1,
                'academic_year' => '2024/2025',
                'homeroom_teacher_id' => $teachers[1]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'VIII A',
                'level' => 8,
                'capacity' => 30,
                'description' => 'Kelas 8A - Kelas unggulan',
                'is_active' => 1,
                'academic_year' => '2024/2025',
                'homeroom_teacher_id' => $teachers[2]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'VIII B',
                'level' => 8,
                'capacity' => 30,
                'description' => 'Kelas 8B - Kelas reguler',
                'is_active' => 1,
                'academic_year' => '2024/2025',
                'homeroom_teacher_id' => $teachers[3]->id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($classes as $class) {
            $this->db->table('classes')->insert($class);
        }

        echo "Created " . count($classes) . " classes\n";
    }

    /**
     * Assign Students to Classes
     */
    private function assignStudentsToClasses()
    {
        echo "Assigning Students to Classes...\n";

        $students = $this->db->table('users')->where('role', 'student')->get()->getResult();
        $classes = $this->db->table('classes')->get()->getResult();

        $assignmentCount = 0;

        // Distribute students evenly across classes
        foreach ($students as $index => $student) {
            $classIndex = $index % count($classes);
            $class = $classes[$classIndex];

            $assignmentData = [
                'user_id' => $student->id,
                'class_id' => $class->id,
                'status' => 'active',
                'enrolled_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('user_classes')->insert($assignmentData);
            $assignmentCount++;
        }

        echo "Assigned {$assignmentCount} students to classes\n";
    }

    /**
     * Create Exam Types
     */
    private function createExamTypes()
    {
        echo "Creating Exam Types...\n";
        $examTypes = [
            [
                'name' => 'Ulangan Harian',
                'category' => 'daily_test',
                'description' => 'Ulangan harian untuk mengukur pemahaman materi per bab',
                'duration_minutes' => 60,
                'max_attempts' => 1,
                'passing_score' => 75.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 0,
                'randomize_options' => 0,
                'auto_submit' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ujian Tengah Semester',
                'category' => 'midterm',
                'description' => 'Ujian tengah semester untuk evaluasi setengah semester',
                'duration_minutes' => 90,
                'max_attempts' => 1,
                'passing_score' => 70.00,
                'show_result_immediately' => 0,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ujian Akhir Semester',
                'category' => 'final',
                'description' => 'Ujian akhir semester untuk evaluasi seluruh materi semester',
                'duration_minutes' => 120,
                'max_attempts' => 1,
                'passing_score' => 65.00,
                'show_result_immediately' => 0,
                'allow_review' => 0,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Try Out',
                'category' => 'practice',
                'description' => 'Try out untuk persiapan ujian nasional',
                'duration_minutes' => 180,
                'max_attempts' => 3,
                'passing_score' => 60.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Remedial',
                'category' => 'remedial',
                'description' => 'Ujian remedial untuk siswa yang belum mencapai KKM',
                'duration_minutes' => 60,
                'max_attempts' => 2,
                'passing_score' => 75.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 0,
                'randomize_options' => 0,
                'auto_submit' => 1,
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($examTypes as $type) {
            $this->db->table('exam_types')->insert($type);
        }

        echo "Created " . count($examTypes) . " exam types\n";
    }

    /**
     * Create Question Banks
     */
    private function createQuestionBanks()
    {
        echo "Creating Question Banks...\n";

        $subjects = $this->db->table('subjects')->get()->getResult();
        $teachers = $this->db->table('users')->where('role', 'teacher')->get()->getResult();

        $questionBanks = [];

        foreach ($subjects as $subject) {
            // Find teacher for this subject
            $teacher = null;
            foreach ($teachers as $t) {
                if ($t->id == $subject->teacher_id) {
                    $teacher = $t;
                    break;
                }
            }
            $questionBanks[] = [
                'name' => 'Bank Soal ' . $subject->name . ' Kelas VII',
                'description' => 'Kumpulan soal-soal ' . $subject->name . ' untuk kelas VII',
                'subject_id' => $subject->id,
                'exam_type_id' => null,
                'difficulty_level' => 'medium',
                'instructions' => 'Bacalah setiap soal dengan teliti sebelum menjawab',
                'status' => 'active',
                'created_by' => $teacher ? $teacher->id : 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $questionBanks[] = [
                'name' => 'Bank Soal ' . $subject->name . ' Kelas VIII',
                'description' => 'Kumpulan soal-soal ' . $subject->name . ' untuk kelas VIII',
                'subject_id' => $subject->id,
                'exam_type_id' => null,
                'difficulty_level' => 'medium',
                'instructions' => 'Bacalah setiap soal dengan teliti sebelum menjawab',
                'status' => 'active',
                'created_by' => $teacher ? $teacher->id : 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
        }

        foreach ($questionBanks as $bank) {
            $this->db->table('question_banks')->insert($bank);
        }

        echo "Created " . count($questionBanks) . " question banks\n";
    }

    /**
     * Create Questions and Options
     */
    private function createQuestionsAndOptions()
    {
        echo "Creating Questions and Options...\n";

        $questionBanks = $this->db->table('question_banks')->get()->getResult();
        $subjects = $this->db->table('subjects')->get()->getResult();

        $questionCount = 0;
        $optionCount = 0;

        foreach ($questionBanks as $bank) {
            // Find subject for this bank
            $subject = null;
            foreach ($subjects as $s) {
                if ($s->id == $bank->subject_id) {
                    $subject = $s;
                    break;
                }
            }

            if (!$subject) continue;            // Create questions based on subject
            $level = strpos($bank->name, 'VII') !== false ? 'VII' : 'VIII';
            $questions = $this->getQuestionsForSubject($subject->code, $level);

            foreach ($questions as $questionData) {                // Insert question
                $questionInsert = [
                    'question_bank_id' => $bank->id,
                    'question_text' => $questionData['question'],
                    'question_type' => 'multiple_choice',
                    'difficulty_level' => $questionData['difficulty'],
                    'points' => $questionData['points'],
                    'explanation' => $questionData['explanation'] ?? null,
                    'status' => 'active',
                    'created_by' => $bank->created_by,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('questions')->insert($questionInsert);
                $questionId = $this->db->insertID();
                $questionCount++;

                // Insert options
                foreach ($questionData['options'] as $index => $option) {
                    $optionInsert = [
                        'question_id' => $questionId,
                        'option_text' => $option,
                        'is_correct' => ($index == $questionData['correct_answer']) ? 1 : 0,
                        'order_number' => $index + 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->table('question_options')->insert($optionInsert);
                    $optionCount++;
                }
            }
        }

        echo "Created {$questionCount} questions with {$optionCount} options\n";
    }

    /**
     * Get sample questions for each subject
     */
    private function getQuestionsForSubject($subjectCode, $level)
    {
        switch ($subjectCode) {
            case 'MTK':
                return $this->getMathQuestions($level);
            case 'BID':
                return $this->getIndonesianQuestions($level);
            case 'IPA':
                return $this->getScienceQuestions($level);
            case 'IPS':
                return $this->getSocialQuestions($level);
            default:
                return [];
        }
    }

    /**
     * Math questions
     */
    private function getMathQuestions($level)
    {
        if ($level == 'VII') {
            return [
                [
                    'question' => 'Hasil dari 15 + (-8) × 3 adalah...',
                    'options' => ['9', '-9', '21', '-21'],
                    'correct_answer' => 1,
                    'difficulty' => 'medium',
                    'points' => 10,
                    'explanation' => 'Operasi perkalian dikerjakan terlebih dahulu: 15 + (-24) = -9'
                ],
                [
                    'question' => 'Jika x + 5 = 12, maka nilai x adalah...',
                    'options' => ['7', '17', '6', '8'],
                    'correct_answer' => 0,
                    'difficulty' => 'easy',
                    'points' => 5,
                    'explanation' => 'x = 12 - 5 = 7'
                ],
                [
                    'question' => 'Keliling persegi dengan sisi 8 cm adalah...',
                    'options' => ['16 cm', '24 cm', '32 cm', '64 cm'],
                    'correct_answer' => 2,
                    'difficulty' => 'easy',
                    'points' => 5,
                    'explanation' => 'Keliling persegi = 4 × sisi = 4 × 8 = 32 cm'
                ],
                [
                    'question' => 'Bentuk sederhana dari 2x + 3x - x adalah...',
                    'options' => ['4x', '5x', '6x', '3x'],
                    'correct_answer' => 0,
                    'difficulty' => 'medium',
                    'points' => 10,
                    'explanation' => '2x + 3x - x = (2 + 3 - 1)x = 4x'
                ],
                [
                    'question' => 'Nilai dari 2³ × 2² adalah...',
                    'options' => ['2⁵', '2⁶', '4⁵', '4⁶'],
                    'correct_answer' => 0,
                    'difficulty' => 'medium',
                    'points' => 10,
                    'explanation' => '2³ × 2² = 2³⁺² = 2⁵'
                ]
            ];
        } else {
            return [
                [
                    'question' => 'Hasil dari ∛64 adalah...',
                    'options' => ['4', '8', '16', '32'],
                    'correct_answer' => 0,
                    'difficulty' => 'medium',
                    'points' => 10,
                    'explanation' => '∛64 = ∛(4³) = 4'
                ],
                [
                    'question' => 'Gradien garis yang melalui titik (2,3) dan (4,7) adalah...',
                    'options' => ['1', '2', '3', '4'],
                    'correct_answer' => 1,
                    'difficulty' => 'hard',
                    'points' => 15,
                    'explanation' => 'm = (y₂-y₁)/(x₂-x₁) = (7-3)/(4-2) = 4/2 = 2'
                ]
            ];
        }
    }

    /**
     * Indonesian language questions
     */
    private function getIndonesianQuestions($level)
    {
        return [
            [
                'question' => 'Manakah yang merupakan kalimat efektif?',
                'options' => [
                    'Dia membeli buku di toko buku',
                    'Dia membeli buku di toko',
                    'Dia beli buku di toko buku',
                    'Dia membeli buku pada toko buku'
                ],
                'correct_answer' => 1,
                'difficulty' => 'medium',
                'points' => 10,
                'explanation' => 'Kalimat efektif tidak menggunakan kata yang berlebihan'
            ],
            [
                'question' => 'Kata yang mengalami proses afiksasi adalah...',
                'options' => ['rumah', 'berlari', 'buku', 'meja'],
                'correct_answer' => 1,
                'difficulty' => 'easy',
                'points' => 5,
                'explanation' => 'Berlari = ber- + lari (mendapat awalan ber-)'
            ]
        ];
    }

    /**
     * Science questions
     */
    private function getScienceQuestions($level)
    {
        return [
            [
                'question' => 'Proses fotosintesis terjadi pada bagian tumbuhan yaitu...',
                'options' => ['akar', 'batang', 'daun', 'bunga'],
                'correct_answer' => 2,
                'difficulty' => 'easy',
                'points' => 5,
                'explanation' => 'Fotosintesis terjadi di daun karena mengandung klorofil'
            ],
            [
                'question' => 'Satuan SI untuk gaya adalah...',
                'options' => ['Joule', 'Newton', 'Watt', 'Pascal'],
                'correct_answer' => 1,
                'difficulty' => 'medium',
                'points' => 10,
                'explanation' => 'Satuan gaya dalam SI adalah Newton (N)'
            ]
        ];
    }

    /**
     * Social studies questions
     */
    private function getSocialQuestions($level)
    {
        return [
            [
                'question' => 'Proklamasi kemerdekaan Indonesia dibacakan pada tanggal...',
                'options' => ['17 Agustus 1945', '17 Agustus 1944', '18 Agustus 1945', '16 Agustus 1945'],
                'correct_answer' => 0,
                'difficulty' => 'easy',
                'points' => 5,
                'explanation' => 'Proklamasi kemerdekaan Indonesia dibacakan pada 17 Agustus 1945'
            ],
            [
                'question' => 'Ibukota provinsi Jawa Tengah adalah...',
                'options' => ['Surabaya', 'Bandung', 'Semarang', 'Yogyakarta'],
                'correct_answer' => 2,
                'difficulty' => 'easy',
                'points' => 5,
                'explanation' => 'Semarang adalah ibukota provinsi Jawa Tengah'
            ]
        ];
    }

    /**
     * Create Exams
     */
    private function createExams()
    {
        echo "Creating Exams...\n";

        $subjects = $this->db->table('subjects')->get()->getResult();
        $examTypes = $this->db->table('exam_types')->get()->getResult();
        $teachers = $this->db->table('users')->where('role', 'teacher')->get()->getResult();

        $exams = [];

        foreach ($subjects as $subject) {
            // Find teacher for this subject
            $teacher = null;
            foreach ($teachers as $t) {
                if ($t->id == $subject->teacher_id) {
                    $teacher = $t;
                    break;
                }
            }            // Create UH exam
            $uhType = null;
            foreach ($examTypes as $type) {
                if ($type->category == 'daily_test') {
                    $uhType = $type;
                    break;
                }
            }

            if ($uhType && $teacher) {
                $exams[] = [
                    'title' => 'Ulangan Harian ' . $subject->name . ' Bab 1',
                    'description' => 'Ulangan harian ' . $subject->name . ' untuk mengukur pemahaman materi bab 1',
                    'subject_id' => $subject->id,
                    'exam_type_id' => $uhType->id,
                    'created_by' => $teacher->id,
                    'duration_minutes' => 60,
                    'total_questions' => 20,
                    'passing_score' => 75,
                    'max_attempts' => 1,
                    'is_active' => 1,
                    'is_published' => 1,
                    'instructions' => 'Bacalah soal dengan teliti sebelum menjawab. Pilih jawaban yang paling tepat.',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }

            // Create UTS exam
            $utsType = null;
            foreach ($examTypes as $type) {
                if ($type->category == 'midterm') {
                    $utsType = $type;
                    break;
                }
            }

            if ($utsType && $teacher) {
                $exams[] = [
                    'title' => 'Ujian Tengah Semester ' . $subject->name,
                    'description' => 'Ujian tengah semester ' . $subject->name . ' semester ganjil',
                    'subject_id' => $subject->id,
                    'exam_type_id' => $utsType->id,
                    'created_by' => $teacher->id,
                    'duration_minutes' => 90,
                    'total_questions' => 30,
                    'passing_score' => 70,
                    'max_attempts' => 1,
                    'is_active' => 1,
                    'is_published' => 1,
                    'instructions' => 'Ujian terdiri dari 30 soal pilihan ganda. Waktu pengerjaan 90 menit.',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        foreach ($exams as $exam) {
            $this->db->table('exams')->insert($exam);
        }

        echo "Created " . count($exams) . " exams\n";
    }

    /**
     * Create Exam Sessions
     */
    private function createExamSessions()
    {
        echo "Creating Exam Sessions...\n";

        $exams = $this->db->table('exams')->get()->getResult();
        $classes = $this->db->table('classes')->get()->getResult();

        $sessions = [];

        foreach ($exams as $exam) {
            foreach ($classes as $class) {
                // Create session for each class
                $startTime = date('Y-m-d H:i:s', strtotime('+' . rand(1, 7) . ' days +' . rand(8, 14) . ' hours'));
                $endTime = date('Y-m-d H:i:s', strtotime($startTime . ' +' . $exam->duration_minutes . ' minutes'));

                $sessions[] = [
                    'exam_id' => $exam->id,
                    'session_name' => $exam->title . ' - ' . $class->name,
                    'class_id' => $class->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => rand(0, 1) ? 'scheduled' : 'active',
                    'max_participants' => $class->capacity,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        foreach ($sessions as $session) {
            $this->db->table('exam_sessions')->insert($session);
        }

        echo "Created " . count($sessions) . " exam sessions\n";
    }

    /**
     * Create Exam Participants
     */
    private function createExamParticipants()
    {
        echo "Creating Exam Participants...\n";

        $examSessions = $this->db->table('exam_sessions')->get()->getResult();
        $participantCount = 0;

        foreach ($examSessions as $session) {
            // Get students in this class
            $students = $this->db->table('user_classes uc')
                ->join('users u', 'u.id = uc.user_id')
                ->where('uc.class_id', $session->class_id)
                ->where('u.role', 'student')
                ->get()
                ->getResult();

            foreach ($students as $student) {
                $participantData = [
                    'exam_session_id' => $session->id,
                    'student_id' => $student->user_id,
                    'status' => $this->getRandomParticipantStatus(),
                    'started_at' => null,
                    'finished_at' => null,
                    'registered_at' => date('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                // Set times based on status
                if ($participantData['status'] == 'completed') {
                    $participantData['started_at'] = date('Y-m-d H:i:s', strtotime($session->start_time . ' +' . rand(0, 10) . ' minutes'));
                    $participantData['finished_at'] = date('Y-m-d H:i:s', strtotime($participantData['started_at'] . ' +' . rand(30, 60) . ' minutes'));
                } elseif ($participantData['status'] == 'in_progress') {
                    $participantData['started_at'] = date('Y-m-d H:i:s', strtotime($session->start_time . ' +' . rand(0, 10) . ' minutes'));
                }

                $this->db->table('exam_participants')->insert($participantData);
                $participantCount++;
            }
        }

        echo "Created {$participantCount} exam participants\n";
    }

    /**
     * Get random participant status
     */
    private function getRandomParticipantStatus()
    {
        $statuses = ['registered', 'in_progress', 'completed', 'absent'];
        $weights = [20, 10, 60, 10]; // Percentage weights

        $rand = rand(1, 100);
        $cumulative = 0;

        for ($i = 0; $i < count($statuses); $i++) {
            $cumulative += $weights[$i];
            if ($rand <= $cumulative) {
                return $statuses[$i];
            }
        }

        return 'registered';
    }

    /**
     * Create Sample Results and Activity Logs
     */
    private function createSampleResults()
    {
        echo "Creating Sample Results and Activity Logs...\n";

        // Get completed participants
        $completedParticipants = $this->db->table('exam_participants ep')
            ->join('exam_sessions es', 'es.id = ep.exam_session_id')
            ->join('exams e', 'e.id = es.exam_id')
            ->where('ep.status', 'completed')
            ->select('ep.*, e.total_questions, e.passing_score, es.exam_id')
            ->get()
            ->getResult();

        $resultCount = 0;
        $answerCount = 0;
        $logCount = 0;

        foreach ($completedParticipants as $participant) {
            // Create exam result
            $correctAnswers = rand(10, $participant->total_questions);
            $score = round(($correctAnswers / $participant->total_questions) * 100, 2);
            $grade = $this->calculateGrade($score);

            $resultData = [
                'exam_participant_id' => $participant->id,
                'student_id' => $participant->student_id,
                'exam_id' => $participant->exam_id,
                'total_questions' => $participant->total_questions,
                'answered_questions' => $correctAnswers + rand(0, $participant->total_questions - $correctAnswers),
                'correct_answers' => $correctAnswers,
                'wrong_answers' => $participant->total_questions - $correctAnswers,
                'score' => $score,
                'grade' => $grade,
                'passed' => $score >= $participant->passing_score ? 1 : 0,
                'time_spent_minutes' => rand(30, 90),
                'submitted_at' => $participant->finished_at,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('exam_results')->insert($resultData);
            $resultCount++;

            // Create sample student answers
            $this->createSampleAnswers($participant->exam_id, $participant->student_id, $participant->id, $correctAnswers);
            $answerCount += $participant->total_questions;

            // Create activity logs
            $this->createActivityLogs($participant->id, $participant->student_id);
            $logCount += rand(5, 15);
        }

        echo "Created {$resultCount} exam results, {$answerCount} student answers, and {$logCount} activity logs\n";
    }

    /**
     * Calculate grade based on score
     */
    private function calculateGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B';
        if ($score >= 70) return 'C';
        if ($score >= 60) return 'D';
        return 'E';
    }

    /**
     * Create sample student answers
     */
    private function createSampleAnswers($examId, $studentId, $participantId, $correctAnswers)
    {
        // Get questions for this exam
        $questions = $this->db->table('exam_questions eq')
            ->join('questions q', 'q.id = eq.question_id')
            ->where('eq.exam_id', $examId)
            ->select('q.id as question_id')
            ->get()
            ->getResult();

        if (empty($questions)) {
            // If no exam_questions exist, get questions from question bank
            $exam = $this->db->table('exams')->where('id', $examId)->get()->getRow();
            if ($exam) {
                $questions = $this->db->table('questions q')
                    ->join('question_banks qb', 'qb.id = q.question_bank_id')
                    ->where('qb.subject_id', $exam->subject_id)
                    ->select('q.id as question_id')
                    ->limit($exam->total_questions)
                    ->get()
                    ->getResult();
            }
        }

        $correctCount = 0;
        foreach ($questions as $index => $question) {
            // Get options for this question
            $options = $this->db->table('question_options')
                ->where('question_id', $question->question_id)
                ->get()
                ->getResult();

            if (empty($options)) continue;

            // Determine if this answer should be correct
            $shouldBeCorrect = $correctCount < $correctAnswers;

            if ($shouldBeCorrect) {
                // Find correct option
                $selectedOption = null;
                foreach ($options as $option) {
                    if ($option->is_correct) {
                        $selectedOption = $option->id;
                        break;
                    }
                }
                $correctCount++;
            } else {
                // Select random wrong option
                $wrongOptions = array_filter($options, function ($opt) {
                    return !$opt->is_correct;
                });
                if (!empty($wrongOptions)) {
                    $selectedOption = $wrongOptions[array_rand($wrongOptions)]->id;
                } else {
                    $selectedOption = $options[0]->id;
                }
            }

            if ($selectedOption) {
                $answerData = [
                    'exam_participant_id' => $participantId,
                    'student_id' => $studentId,
                    'question_id' => $question->question_id,
                    'selected_option_id' => $selectedOption,
                    'is_correct' => $shouldBeCorrect ? 1 : 0,
                    'answered_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' minutes')),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('student_answers')->insert($answerData);
            }
        }
    }

    /**
     * Create activity logs
     */
    private function createActivityLogs($participantId, $studentId)
    {
        $activities = [
            'exam_started' => 'Siswa memulai ujian',
            'question_viewed' => 'Siswa melihat soal nomor ' . rand(1, 20),
            'answer_selected' => 'Siswa memilih jawaban untuk soal nomor ' . rand(1, 20),
            'answer_changed' => 'Siswa mengubah jawaban soal nomor ' . rand(1, 20),
            'question_flagged' => 'Siswa menandai soal nomor ' . rand(1, 20) . ' untuk direview',
            'exam_submitted' => 'Siswa menyelesaikan dan mengumpulkan ujian'
        ];

        foreach ($activities as $activity => $description) {
            if (rand(0, 1)) { // 50% chance to create each activity
                $logData = [
                    'exam_participant_id' => $participantId,
                    'student_id' => $studentId,
                    'activity_type' => $activity,
                    'description' => $description,
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 120) . ' minutes')),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('exam_activity_logs')->insert($logData);
            }
        }
    }

    /**
     * Generate random IP address
     */
    private function generateRandomIP()
    {
        return rand(192, 203) . '.' . rand(168, 199) . '.' . rand(1, 254) . '.' . rand(1, 254);
    }
}
