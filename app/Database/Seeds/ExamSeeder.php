<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ExamSeeder - Seeder lengkap untuk sistem ujian CBT
 * 
 * Seeder ini akan membuat data lengkap untuk sistem ujian meliputi:
 * - Exam Types (jenis ujian)
 * - Question Banks (bank soal)
 * - Questions dengan pilihan jawaban
 * - Exams (ujian)
 * - Exam Questions (soal ujian)
 * - Exam Sessions (sesi ujian)
 * - Exam Participants (peserta ujian)
 * - Exam Results (hasil ujian)
 * - Student Answers (jawaban siswa)
 * - Exam Activity Logs (log aktivitas)
 * 
 * @author CBT Smart Team
 * @version 1.0
 */
class ExamSeeder extends Seeder
{
    private $subjectIds = [];
    private $teacherIds = [];
    private $studentIds = [];
    private $classIds = [];
    private $examTypeIds = [];
    private $questionBankIds = [];
    private $examIds = [];
    private $sessionIds = [];

    public function run()
    {
        $this->loadExistingData();
        $this->createExamTypes();
        $this->createQuestionBanks();
        $this->createQuestions();
        $this->createExams();
        $this->createExamSessions();
        $this->createExamParticipants();
        $this->createExamResults();
        $this->createStudentAnswers();
        $this->createExamActivityLogs();

        $this->displaySummary();
    }

    /**
     * Load existing data yang diperlukan
     */
    private function loadExistingData()
    {
        // Load subjects
        $subjects = $this->db->table('subjects')->get()->getResult();
        foreach ($subjects as $subject) {
            $this->subjectIds[] = $subject->id;
        }

        // Load teachers
        $teachers = $this->db->table('users')->where('role', 'teacher')->get()->getResult();
        foreach ($teachers as $teacher) {
            $this->teacherIds[] = $teacher->id;
        }

        // Load students
        $students = $this->db->table('users')->where('role', 'student')->get()->getResult();
        foreach ($students as $student) {
            $this->studentIds[] = $student->id;
        }

        // Load classes
        $classes = $this->db->table('classes')->get()->getResult();
        foreach ($classes as $class) {
            $this->classIds[] = $class->id;
        }

        // Check if required data exists
        if (empty($this->subjectIds) || empty($this->teacherIds) || empty($this->studentIds) || empty($this->classIds)) {
            throw new \Exception("Required data tidak lengkap. Pastikan subjects, teachers, students, dan classes sudah ada.");
        }
    }

    /**
     * Create Exam Types
     */
    private function createExamTypes()
    {
        echo "Creating Exam Types...\n";

        $examTypes = [
            [
                'name' => 'Ujian Harian',
                'category' => 'daily',
                'description' => 'Ujian harian untuk evaluasi materi pembelajaran sehari-hari',
                'duration_minutes' => 45,
                'max_attempts' => 1,
                'passing_score' => 75.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => "Instruksi Ujian Harian:\n1. Baca setiap soal dengan teliti\n2. Pilih jawaban yang paling tepat\n3. Waktu pengerjaan 45 menit\n4. Hasil akan langsung ditampilkan",
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ujian Tengah Semester',
                'category' => 'mid_semester',
                'description' => 'Ujian tengah semester untuk evaluasi pemahaman materi setengah semester',
                'duration_minutes' => 90,
                'max_attempts' => 1,
                'passing_score' => 70.00,
                'show_result_immediately' => 0,
                'allow_review' => 0,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => "Instruksi UTS:\n1. Ujian berlangsung 90 menit\n2. Tidak dapat mengulang ujian\n3. Hasil akan diumumkan kemudian\n4. Periksa jawaban sebelum submit",
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Ujian Akhir Semester',
                'category' => 'final_semester',
                'description' => 'Ujian akhir semester untuk evaluasi pemahaman materi seluruh semester',
                'duration_minutes' => 120,
                'max_attempts' => 1,
                'passing_score' => 65.00,
                'show_result_immediately' => 0,
                'allow_review' => 0,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => "Instruksi UAS:\n1. Ujian berlangsung 120 menit\n2. Tidak dapat mengulang ujian\n3. Hasil akan diumumkan kemudian\n4. Bacalah petunjuk setiap soal dengan cermat",
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Latihan Soal',
                'category' => 'practice',
                'description' => 'Latihan soal untuk persiapan ujian',
                'duration_minutes' => 30,
                'max_attempts' => 3,
                'passing_score' => 60.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 0,
                'instructions' => "Instruksi Latihan:\n1. Anda dapat mengulang hingga 3 kali\n2. Hasil akan langsung ditampilkan\n3. Gunakan untuk persiapan ujian\n4. Review jawaban yang salah",
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Simulasi Ujian',
                'category' => 'simulation',
                'description' => 'Simulasi ujian untuk persiapan ujian sesungguhnya',
                'duration_minutes' => 60,
                'max_attempts' => 2,
                'passing_score' => 70.00,
                'show_result_immediately' => 1,
                'allow_review' => 1,
                'randomize_questions' => 1,
                'randomize_options' => 1,
                'auto_submit' => 1,
                'instructions' => "Instruksi Simulasi:\n1. Simulasi kondisi ujian sesungguhnya\n2. Dapat diulang maksimal 2 kali\n3. Evaluasi kesiapan ujian\n4. Gunakan waktu dengan efektif",
                'status' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($examTypes as $examType) {
            $examTypeId = $this->db->table('exam_types')->insert($examType);
            $this->examTypeIds[] = $this->db->insertID();
        }

        echo "Created " . count($examTypes) . " exam types\n";
    }

    /**
     * Create Question Banks
     */
    private function createQuestionBanks()
    {
        echo "Creating Question Banks...\n";

        $questionBanks = [];

        foreach ($this->subjectIds as $subjectId) {
            foreach ($this->examTypeIds as $examTypeId) {
                $subjectName = $this->getSubjectName($subjectId);
                $examTypeName = $this->getExamTypeName($examTypeId);

                $questionBanks[] = [
                    'name' => "Bank Soal {$subjectName} - {$examTypeName}",
                    'subject_id' => $subjectId,
                    'exam_type_id' => $examTypeId,
                    'difficulty_level' => 'medium',
                    'description' => "Kumpulan soal {$subjectName} untuk {$examTypeName}",
                    'instructions' => "Petunjuk mengerjakan:\n1. Baca soal dengan teliti\n2. Pilih jawaban yang paling tepat\n3. Pastikan semua soal terjawab",
                    'time_per_question' => 60, // 1 menit per soal
                    'negative_marking' => 0,
                    'negative_marks' => 0,
                    'randomize_questions' => 1,
                    'show_correct_answer' => 1,
                    'allow_calculator' => $subjectName === 'Matematika' ? 1 : 0,
                    'tags' => strtolower(str_replace(' ', ',', $subjectName)) . ',' . strtolower(str_replace(' ', ',', $examTypeName)),
                    'status' => 'active',
                    'created_by' => $this->teacherIds[0],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        foreach ($questionBanks as $questionBank) {
            $this->db->table('question_banks')->insert($questionBank);
            $this->questionBankIds[] = $this->db->insertID();
        }

        echo "Created " . count($questionBanks) . " question banks\n";
    }

    /**
     * Create Questions with Options
     */
    private function createQuestions()
    {
        echo "Creating Questions and Options...\n";

        $questionTemplates = $this->getQuestionTemplates();
        $totalQuestions = 0;
        $totalOptions = 0;

        foreach ($this->questionBankIds as $questionBankId) {
            $bankInfo = $this->getQuestionBankInfo($questionBankId);
            $subjectName = $bankInfo['subject_name'];

            // Create 20 questions per bank
            for ($i = 1; $i <= 20; $i++) {
                $template = $questionTemplates[$subjectName][array_rand($questionTemplates[$subjectName])];

                $questionData = [
                    'question_bank_id' => $questionBankId,
                    'question_text' => str_replace('{number}', $i, $template['question']),
                    'question_type' => 'multiple_choice',
                    'difficulty_level' => ['easy', 'medium', 'hard'][rand(0, 2)],
                    'points' => rand(5, 10),
                    'time_limit' => 60,
                    'explanation' => $template['explanation'],
                    'order_number' => $i,
                    'status' => 'active',
                    'created_by' => $this->teacherIds[array_rand($this->teacherIds)],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('questions')->insert($questionData);
                $questionId = $this->db->insertID();
                $totalQuestions++;

                // Create options for each question
                foreach ($template['options'] as $index => $option) {
                    $optionData = [
                        'question_id' => $questionId,
                        'option_letter' => chr(65 + $index), // A, B, C, D
                        'option_text' => $option,
                        'is_correct' => ($index === $template['correct_answer']) ? 1 : 0,
                        'explanation' => ($index === $template['correct_answer']) ? 'Jawaban benar' : 'Jawaban salah',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                    $this->db->table('question_options')->insert($optionData);
                    $totalOptions++;
                }
            }
        }

        echo "Created {$totalQuestions} questions with {$totalOptions} options\n";
    }

    /**
     * Create Exams
     */
    private function createExams()
    {
        echo "Creating Exams...\n";

        $exams = [];

        foreach ($this->subjectIds as $subjectId) {
            foreach ($this->examTypeIds as $examTypeId) {
                $subjectName = $this->getSubjectName($subjectId);
                $examTypeName = $this->getExamTypeName($examTypeId);
                $questionBankId = $this->getQuestionBankId($subjectId, $examTypeId);

                $examData = [
                    'title' => "{$examTypeName} {$subjectName}",
                    'description' => "Ujian {$examTypeName} mata pelajaran {$subjectName}",
                    'subject_id' => $subjectId,
                    'exam_type_id' => $examTypeId,
                    'question_bank_id' => $questionBankId,
                    'teacher_id' => $this->teacherIds[array_rand($this->teacherIds)],
                    'pdf_url' => '', // Optional
                    'pdf_content' => null,
                    'question_count' => 15,
                    'total_questions' => 15,
                    'duration_minutes' => $this->getExamTypeDuration($examTypeId),
                    'duration' => $this->getExamTypeDuration($examTypeId),
                    'start_time' => date('Y-m-d H:i:s', strtotime('+1 day')),
                    'end_time' => date('Y-m-d H:i:s', strtotime('+7 days')),
                    'max_attempts' => $this->getExamTypeMaxAttempts($examTypeId),
                    'passing_score' => $this->getExamTypePassingScore($examTypeId),
                    'shuffle_questions' => 1,
                    'show_results' => $this->getExamTypeShowResults($examTypeId),
                    'status' => 'active',
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('exams')->insert($examData);
                $this->examIds[] = $this->db->insertID();
            }
        }

        echo "Created " . count($this->examIds) . " exams\n";
    }

    /**
     * Create Exam Sessions
     */
    private function createExamSessions()
    {
        echo "Creating Exam Sessions...\n";

        foreach ($this->examIds as $examId) {
            foreach ($this->classIds as $classId) {
                $examInfo = $this->getExamInfo($examId);

                $sessionData = [
                    'exam_id' => $examId,
                    'class_id' => $classId,
                    'session_name' => "Sesi {$examInfo['title']} - Kelas {$this->getClassName($classId)}",
                    'start_time' => date('Y-m-d H:i:s', strtotime('+2 days')),
                    'end_time' => date('Y-m-d H:i:s', strtotime('+2 days +' . $examInfo['duration'] . ' minutes')),
                    'max_participants' => 50,
                    'room_location' => 'Lab Komputer ' . rand(1, 5),
                    'instructions' => "Petunjuk sesi ujian:\n1. Hadir 15 menit sebelum ujian\n2. Bawa alat tulis dan kartu identitas\n3. Tidak diperkenankan membawa HP\n4. Ikuti instruksi pengawas",
                    'security_settings' => json_encode([
                        'prevent_copy_paste' => true,
                        'prevent_right_click' => true,
                        'fullscreen_mode' => true,
                        'disable_browser_back' => true,
                        'monitor_tab_switch' => true
                    ]),
                    'status' => ['scheduled', 'active', 'completed'][rand(0, 2)],
                    'actual_start_time' => null,
                    'actual_end_time' => null,
                    'created_by' => $this->teacherIds[array_rand($this->teacherIds)],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('exam_sessions')->insert($sessionData);
                $this->sessionIds[] = $this->db->insertID();
            }
        }

        echo "Created " . count($this->sessionIds) . " exam sessions\n";
    }

    /**
     * Create Exam Participants
     */
    private function createExamParticipants()
    {
        echo "Creating Exam Participants...\n";

        $totalParticipants = 0;

        foreach ($this->sessionIds as $sessionId) {
            $sessionInfo = $this->getSessionInfo($sessionId);

            // Add random students to each session
            $selectedStudents = array_rand(array_flip($this->studentIds), min(10, count($this->studentIds)));
            if (!is_array($selectedStudents)) {
                $selectedStudents = [$selectedStudents];
            }

            foreach ($selectedStudents as $studentId) {
                $status = ['not_started', 'in_progress', 'completed', 'absent'][rand(0, 3)];
                $startTime = null;
                $endTime = null;
                $score = null;

                if ($status === 'completed') {
                    $startTime = date('Y-m-d H:i:s', strtotime('-1 hour'));
                    $endTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));
                    $score = rand(60, 100);
                } elseif ($status === 'in_progress') {
                    $startTime = date('Y-m-d H:i:s', strtotime('-30 minutes'));
                }

                $participantData = [
                    'exam_session_id' => $sessionId,
                    'exam_id' => $sessionInfo['exam_id'],
                    'user_id' => $studentId,
                    'status' => $status,
                    'started_at' => $startTime,
                    'completed_at' => $endTime,
                    'submission_time' => $endTime,
                    'total_time_spent' => $endTime ? rand(1800, 3600) : null, // 30-60 minutes
                    'score' => $score,
                    'total_questions' => 15,
                    'answered_questions' => $status === 'completed' ? 15 : rand(0, 15),
                    'correct_answers' => $status === 'completed' ? rand(8, 15) : 0,
                    'wrong_answers' => $status === 'completed' ? rand(0, 7) : 0,
                    'unanswered_questions' => $status === 'completed' ? 0 : rand(0, 15),
                    'is_force_submitted' => 0,
                    'browser_info' => json_encode([
                        'browser' => 'Chrome',
                        'version' => '91.0.4472.124',
                        'os' => 'Windows 10'
                    ]),
                    'ip_address' => '192.168.1.' . rand(10, 254),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('exam_participants')->insert($participantData);
                $totalParticipants++;
            }
        }

        echo "Created {$totalParticipants} exam participants\n";
    }

    /**
     * Create Exam Results
     */
    private function createExamResults()
    {
        echo "Creating Exam Results...\n";

        $participants = $this->db->table('exam_participants')
            ->where('status', 'completed')
            ->get()
            ->getResult();

        foreach ($participants as $participant) {
            $totalScore = rand(60, 100);
            $maxTotalScore = 100;
            $percentage = $totalScore;

            $resultData = [
                'exam_id' => $participant->exam_id,
                'student_id' => $participant->user_id,
                'total_score' => $totalScore,
                'max_total_score' => $maxTotalScore,
                'percentage' => $percentage,
                'status' => 'graded',
                'started_at' => $participant->started_at,
                'submitted_at' => $participant->completed_at,
                'graded_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $this->db->table('exam_results')->insert($resultData);
        }

        echo "Created " . count($participants) . " exam results\n";
    }

    /**
     * Create Student Answers
     */
    private function createStudentAnswers()
    {
        echo "Creating Student Answers...\n";

        $participants = $this->db->table('exam_participants')
            ->whereIn('status', ['completed', 'in_progress'])
            ->get()
            ->getResult();

        $totalAnswers = 0;

        foreach ($participants as $participant) {
            $questionsToAnswer = $participant->status === 'completed' ? 15 : rand(5, 15);

            for ($i = 1; $i <= $questionsToAnswer; $i++) {
                $isCorrect = rand(0, 1);
                $score = $isCorrect ? rand(8, 10) : rand(0, 5);

                $answerData = [
                    'exam_id' => $participant->exam_id,
                    'student_id' => $participant->user_id,
                    'question_number' => $i,
                    'answer_text' => $isCorrect ? 'Jawaban yang benar' : 'Jawaban yang salah',
                    'ai_score' => $score,
                    'ai_feedback' => $isCorrect ? 'Jawaban sudah benar' : 'Jawaban perlu diperbaiki',
                    'manual_score' => null,
                    'manual_feedback' => null,
                    'final_score' => $score,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('student_answers')->insert($answerData);
                $totalAnswers++;
            }
        }

        echo "Created {$totalAnswers} student answers\n";
    }

    /**
     * Create Exam Activity Logs
     */
    private function createExamActivityLogs()
    {
        echo "Creating Exam Activity Logs...\n";

        $participants = $this->db->table('exam_participants')
            ->get()
            ->getResult();

        $totalLogs = 0;
        $eventTypes = ['exam_started', 'question_answered', 'exam_submitted', 'tab_switched', 'window_focused'];

        foreach ($participants as $participant) {
            // Create 5-10 activity logs per participant
            $logCount = rand(5, 10);

            for ($i = 0; $i < $logCount; $i++) {
                $eventType = $eventTypes[array_rand($eventTypes)];

                $logData = [
                    'exam_id' => $participant->exam_id,
                    'student_id' => $participant->user_id,
                    'event_type' => $eventType,
                    'details' => json_encode([
                        'event' => $eventType,
                        'timestamp' => date('Y-m-d H:i:s'),
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                        'ip_address' => '192.168.1.' . rand(10, 254)
                    ]),
                    'created_at' => date('Y-m-d H:i:s', strtotime("-" . rand(1, 120) . " minutes"))
                ];

                $this->db->table('exam_activity_logs')->insert($logData);
                $totalLogs++;
            }
        }

        echo "Created {$totalLogs} activity logs\n";
    }

    /**
     * Helper Methods
     */
    private function getSubjectName($subjectId)
    {
        $subject = $this->db->table('subjects')->where('id', $subjectId)->get()->getRow();
        return $subject ? $subject->name : 'Unknown Subject';
    }

    private function getExamTypeName($examTypeId)
    {
        $examType = $this->db->table('exam_types')->where('id', $examTypeId)->get()->getRow();
        return $examType ? $examType->name : 'Unknown Exam Type';
    }

    private function getClassName($classId)
    {
        $class = $this->db->table('classes')->where('id', $classId)->get()->getRow();
        return $class ? $class->name : 'Unknown Class';
    }

    private function getQuestionBankId($subjectId, $examTypeId)
    {
        $bank = $this->db->table('question_banks')
            ->where('subject_id', $subjectId)
            ->where('exam_type_id', $examTypeId)
            ->get()
            ->getRow();

        return $bank ? $bank->id : null;
    }

    private function getQuestionBankInfo($questionBankId)
    {
        $bank = $this->db->table('question_banks qb')
            ->select('qb.*, s.name as subject_name, et.name as exam_type_name')
            ->join('subjects s', 's.id = qb.subject_id')
            ->join('exam_types et', 'et.id = qb.exam_type_id')
            ->where('qb.id', $questionBankId)
            ->get()
            ->getRow();

        return $bank ? (array)$bank : [];
    }

    private function getExamInfo($examId)
    {
        $exam = $this->db->table('exams')->where('id', $examId)->get()->getRow();
        return $exam ? (array)$exam : [];
    }

    private function getSessionInfo($sessionId)
    {
        $session = $this->db->table('exam_sessions')->where('id', $sessionId)->get()->getRow();
        return $session ? (array)$session : [];
    }

    private function getExamTypeDuration($examTypeId)
    {
        $examType = $this->db->table('exam_types')->where('id', $examTypeId)->get()->getRow();
        return $examType ? $examType->duration_minutes : 60;
    }

    private function getExamTypeMaxAttempts($examTypeId)
    {
        $examType = $this->db->table('exam_types')->where('id', $examTypeId)->get()->getRow();
        return $examType ? $examType->max_attempts : 1;
    }

    private function getExamTypePassingScore($examTypeId)
    {
        $examType = $this->db->table('exam_types')->where('id', $examTypeId)->get()->getRow();
        return $examType ? $examType->passing_score : 70;
    }

    private function getExamTypeShowResults($examTypeId)
    {
        $examType = $this->db->table('exam_types')->where('id', $examTypeId)->get()->getRow();
        return $examType ? $examType->show_result_immediately : 1;
    }

    /**
     * Get Question Templates berdasarkan mata pelajaran
     */
    private function getQuestionTemplates()
    {
        return [
            'Matematika' => [
                [
                    'question' => 'Berapakah hasil dari 25 + 37?',
                    'options' => ['60', '62', '65', '67'],
                    'correct_answer' => 1,
                    'explanation' => '25 + 37 = 62'
                ],
                [
                    'question' => 'Hasil dari 8 × 9 adalah...',
                    'options' => ['71', '72', '73', '74'],
                    'correct_answer' => 1,
                    'explanation' => '8 × 9 = 72'
                ],
                [
                    'question' => 'Berapakah akar kuadrat dari 144?',
                    'options' => ['10', '11', '12', '13'],
                    'correct_answer' => 2,
                    'explanation' => '√144 = 12'
                ]
            ],
            'Bahasa Indonesia' => [
                [
                    'question' => 'Kata baku yang benar dari kata "ijin" adalah...',
                    'options' => ['izin', 'ijin', 'idzin', 'ijhin'],
                    'correct_answer' => 0,
                    'explanation' => 'Kata baku yang benar adalah "izin"'
                ],
                [
                    'question' => 'Sinonim dari kata "indah" adalah...',
                    'options' => ['jelek', 'cantik', 'buruk', 'rusak'],
                    'correct_answer' => 1,
                    'explanation' => 'Sinonim dari indah adalah cantik'
                ]
            ],
            'Ilmu Pengetahuan Alam' => [
                [
                    'question' => 'Planet yang paling dekat dengan matahari adalah...',
                    'options' => ['Venus', 'Merkurius', 'Mars', 'Bumi'],
                    'correct_answer' => 1,
                    'explanation' => 'Merkurius adalah planet terdekat dengan matahari'
                ],
                [
                    'question' => 'Rumus kimia air adalah...',
                    'options' => ['H2O', 'CO2', 'NaCl', 'O2'],
                    'correct_answer' => 0,
                    'explanation' => 'Rumus kimia air adalah H2O'
                ]
            ]
        ];
    }

    /**
     * Display summary of created data
     */
    private function displaySummary()
    {
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "EXAM SEEDER SUMMARY\n";
        echo str_repeat("=", 60) . "\n";
        echo "✓ Exam Types: " . count($this->examTypeIds) . "\n";
        echo "✓ Question Banks: " . count($this->questionBankIds) . "\n";
        echo "✓ Exams: " . count($this->examIds) . "\n";
        echo "✓ Exam Sessions: " . count($this->sessionIds) . "\n";

        // Count other entities
        $questionsCount = $this->db->table('questions')->countAllResults();
        $optionsCount = $this->db->table('question_options')->countAllResults();
        $participantsCount = $this->db->table('exam_participants')->countAllResults();
        $resultsCount = $this->db->table('exam_results')->countAllResults();
        $answersCount = $this->db->table('student_answers')->countAllResults();
        $logsCount = $this->db->table('exam_activity_logs')->countAllResults();

        echo "✓ Questions: {$questionsCount}\n";
        echo "✓ Question Options: {$optionsCount}\n";
        echo "✓ Exam Participants: {$participantsCount}\n";
        echo "✓ Exam Results: {$resultsCount}\n";
        echo "✓ Student Answers: {$answersCount}\n";
        echo "✓ Activity Logs: {$logsCount}\n";
        echo str_repeat("=", 60) . "\n";
        echo "✓ Exam seeder completed successfully!\n";
        echo str_repeat("=", 60) . "\n\n";
    }
}
