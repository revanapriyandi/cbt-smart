<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ExamQuestionsSeeder - Seeder khusus untuk soal-soal ujian
 * 
 * Seeder ini membuat soal-soal ujian yang lebih detail dan realistis
 * berdasarkan exam yang sudah ada di database
 * 
 * @author CBT Smart Team
 * @version 1.0
 */
class ExamQuestionsSeeder extends Seeder
{
    public function run()
    {
        echo "Creating Exam Questions...\n";

        // Ambil semua exam yang ada
        $exams = $this->db->table('exams e')
            ->select('e.*, s.name as subject_name')
            ->join('subjects s', 's.id = e.subject_id')
            ->get()
            ->getResult();

        if (empty($exams)) {
            echo "No exams found. Please run ExamSeeder first.\n";
            return;
        }

        $totalQuestions = 0;

        foreach ($exams as $exam) {
            // Buat soal-soal untuk setiap exam
            $questionCount = $exam->question_count ?? 15;

            for ($i = 1; $i <= $questionCount; $i++) {
                $questionData = [
                    'exam_id' => $exam->id,
                    'question_number' => $i,
                    'question_text' => $this->generateQuestionText($exam->subject_name, $i),
                    'max_score' => rand(5, 10),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $this->db->table('exam_questions')->insert($questionData);
                $totalQuestions++;
            }
        }

        echo "Created {$totalQuestions} exam questions\n";
    }

    /**
     * Generate question text berdasarkan mata pelajaran
     */
    private function generateQuestionText($subjectName, $questionNumber)
    {
        $questionTemplates = [
            'Matematika' => [
                "Soal {$questionNumber}: Hitunglah hasil dari operasi berikut: 15 + 28 - 12 = ...",
                "Soal {$questionNumber}: Jika sebuah persegi panjang memiliki panjang 12 cm dan lebar 8 cm, berapakah kelilingnya?",
                "Soal {$questionNumber}: Pak Ahmad membeli 5 kg beras seharga Rp 60.000. Berapa harga per kg beras tersebut?",
                "Soal {$questionNumber}: Dalam sebuah kelas terdapat 25 siswa. Jika 3/5 dari siswa adalah perempuan, berapa jumlah siswa laki-laki?",
                "Soal {$questionNumber}: Hitunglah luas segitiga yang memiliki alas 10 cm dan tinggi 6 cm!",
                "Soal {$questionNumber}: Hasil dari 8² - 3² adalah...",
                "Soal {$questionNumber}: Jika x + 15 = 32, maka nilai x adalah...",
                "Soal {$questionNumber}: Konversikan 2,5 jam ke dalam menit!",
                "Soal {$questionNumber}: Dalam sebuah kantong terdapat 120 kelereng. Jika 25% adalah kelereng merah, berapa jumlah kelereng merah?",
                "Soal {$questionNumber}: Hitunglah volume kubus yang memiliki sisi 4 cm!"
            ],
            'Bahasa Indonesia' => [
                "Soal {$questionNumber}: Tentukan kata baku yang tepat dari kata 'apotek' dalam kalimat berikut!",
                "Soal {$questionNumber}: Manakah di antara pilihan berikut yang merupakan contoh kalimat efektif?",
                "Soal {$questionNumber}: Apa sinonim yang tepat untuk kata 'gembira'?",
                "Soal {$questionNumber}: Tentukan jenis kalimat berikut: 'Apakah kamu sudah mengerjakan PR?'",
                "Soal {$questionNumber}: Dalam pantun, baris yang berisi pesan atau nasihat adalah...",
                "Soal {$questionNumber}: Kata 'membaca' termasuk kata kerja...",
                "Soal {$questionNumber}: Penulisan alamat surat yang benar adalah...",
                "Soal {$questionNumber}: Fungsi utama paragraf pembuka dalam surat resmi adalah...",
                "Soal {$questionNumber}: Antonim dari kata 'optimis' adalah...",
                "Soal {$questionNumber}: Dalam karya sastra, amanat adalah..."
            ],
            'Ilmu Pengetahuan Alam' => [
                "Soal {$questionNumber}: Proses fotosintesis pada tumbuhan memerlukan bantuan sinar matahari dan...",
                "Soal {$questionNumber}: Organ pernapasan utama pada manusia adalah...",
                "Soal {$questionNumber}: Planet yang memiliki cincin di tata surya adalah...",
                "Soal {$questionNumber}: Zat yang diperlukan untuk pembakaran adalah...",
                "Soal {$questionNumber}: Metamorfosis sempurna terjadi pada...",
                "Soal {$questionNumber}: Alat gerak aktif pada manusia adalah...",
                "Soal {$questionNumber}: Sumber energi terbesar di bumi adalah...",
                "Soal {$questionNumber}: Bagian tumbuhan yang berfungsi untuk menyerap air dan mineral adalah...",
                "Soal {$questionNumber}: Hewan yang mengalami hibernasi adalah...",
                "Soal {$questionNumber}: Bunyi dapat merambat melalui..."
            ],
            'Ilmu Pengetahuan Sosial' => [
                "Soal {$questionNumber}: Proklamasi kemerdekaan Indonesia dibacakan pada tanggal...",
                "Soal {$questionNumber}: Presiden pertama Republik Indonesia adalah...",
                "Soal {$questionNumber}: Pulau terbesar di Indonesia adalah...",
                "Soal {$questionNumber}: Mata uang resmi Indonesia adalah...",
                "Soal {$questionNumber}: Ibu kota provinsi Jawa Tengah adalah...",
                "Soal {$questionNumber}: Organisasi pergerakan nasional pertama di Indonesia adalah...",
                "Soal {$questionNumber}: Negara tetangga Indonesia di sebelah utara adalah...",
                "Soal {$questionNumber}: Sila pertama Pancasila adalah...",
                "Soal {$questionNumber}: Candi Borobudur terletak di provinsi...",
                "Soal {$questionNumber}: Hari Pendidikan Nasional diperingati setiap tanggal..."
            ],
            'Bahasa Inggris' => [
                "Soal {$questionNumber}: Choose the correct form: 'She _____ to school every day.' (go/goes/going)",
                "Soal {$questionNumber}: What is the plural form of 'child'?",
                "Soal {$questionNumber}: Complete the sentence: 'I _____ my homework yesterday.' (do/did/does)",
                "Soal {$questionNumber}: Which is the correct greeting for the afternoon?",
                "Soal {$questionNumber}: What does 'beautiful' mean in Indonesian?",
                "Soal {$questionNumber}: Choose the correct pronoun: '_____ is my friend.' (He/Him/His)",
                "Soal {$questionNumber}: What is the opposite of 'big'?",
                "Soal {$questionNumber}: Complete: 'There _____ many books on the table.' (is/are/am)",
                "Soal {$questionNumber}: What time is it? It's _____ o'clock. (choose the correct format)",
                "Soal {$questionNumber}: Which sentence is grammatically correct?"
            ]
        ];

        $templates = $questionTemplates[$subjectName] ?? $questionTemplates['Matematika'];

        if ($questionNumber <= count($templates)) {
            return $templates[$questionNumber - 1];
        } else {
            // Jika questionNumber melebihi template yang tersedia, ambil secara random
            return $templates[array_rand($templates)];
        }
    }
}
