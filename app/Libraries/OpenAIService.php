<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class OpenAIService
{
    private $client;
    private $apiKey;
    private $model;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = getenv('OPENAI_API_KEY');
        $this->model = getenv('OPENAI_MODEL') ?: 'gpt-4o-mini';
    }

    public function gradeEssayAnswer($question, $answer, $maxScore = 10)
    {
        $prompt = $this->buildGradingPrompt($question, $answer, $maxScore);

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah seorang guru yang ahli dalam menilai jawaban esai siswa. Berikan penilaian yang objektif, konstruktif, dan detail.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 1000
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['choices'][0]['message']['content'])) {
                return $this->parseGradingResponse($body['choices'][0]['message']['content'], $maxScore);
            }

            return [
                'score' => 0,
                'feedback' => 'Terjadi kesalahan dalam proses penilaian otomatis.',
                'success' => false
            ];
        } catch (RequestException $e) {
            log_message('error', 'OpenAI API Error: ' . $e->getMessage());
            return [
                'score' => 0,
                'feedback' => 'Terjadi kesalahan dalam koneksi ke layanan penilaian otomatis.',
                'success' => false
            ];
        }
    }

    private function buildGradingPrompt($question, $answer, $maxScore)
    {
        return "
Silakan nilai jawaban esai berikut ini:

SOAL:
{$question}

JAWABAN SISWA:
{$answer}

KRITERIA PENILAIAN:
- Skor maksimal: {$maxScore}
- Nilai berdasarkan: ketepatan konsep, kelengkapan jawaban, logika penjelasan, dan penggunaan bahasa

INSTRUKSI:
1. Berikan skor dalam bentuk angka (contoh: 7.5)
2. Berikan feedback yang konstruktif dan spesifik
3. Sebutkan poin-poin yang sudah benar dan yang perlu diperbaiki
4. Format respons:
   SKOR: [angka]
   FEEDBACK: [penjelasan detail]

Pastikan penilaian objektif dan membantu pembelajaran siswa.
        ";
    }

    private function parseGradingResponse($response, $maxScore)
    {
        // Extract score
        preg_match('/SKOR:\s*([0-9]+(?:\.[0-9]+)?)/i', $response, $scoreMatches);
        $score = isset($scoreMatches[1]) ? floatval($scoreMatches[1]) : 0;

        // Ensure score doesn't exceed max score
        $score = min($score, $maxScore);
        $score = max($score, 0);
        $score = (float) $score;

        // Extract feedback
        preg_match('/FEEDBACK:\s*(.*?)$/is', $response, $feedbackMatches);
        $feedback = isset($feedbackMatches[1]) ? trim($feedbackMatches[1]) : $response;

        return [
            'score' => $score,
            'feedback' => $feedback,
            'success' => true
        ];
    }

    public function extractTextFromPDF($pdfUrl)
    {
        // This method would integrate with PDF parsing
        // For now, we'll use a placeholder that works with the PDF parser
        try {
            $pdfContent = file_get_contents($pdfUrl);

            if ($pdfContent === false) {
                throw new \Exception('Unable to fetch PDF content');
            }

            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();

            return [
                'text' => $text,
                'success' => true
            ];
        } catch (\Exception $e) {
            log_message('error', 'PDF Parsing Error: ' . $e->getMessage());
            return [
                'text' => '',
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function generateQuestionsFromPDF($pdfText, $questionCount = 5)
    {
        $prompt = "
Berdasarkan teks PDF berikut, buatlah {$questionCount} soal esai yang berkualitas:

TEKS PDF:
{$pdfText}

INSTRUKSI:
1. Buat soal yang menguji pemahaman konsep, analisis, dan aplikasi
2. Soal harus jelas, spesifik, dan dapat dijawab berdasarkan materi
3. Hindari soal yang terlalu mudah atau terlalu sulit
4. Format setiap soal dengan:
   SOAL [nomor]: [pertanyaan]

Contoh format:
SOAL 1: Jelaskan konsep utama yang dibahas dalam materi ini!
SOAL 2: Analisislah hubungan antara X dan Y berdasarkan penjelasan dalam teks!
        ";

        try {
            $response = $this->client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah seorang guru berpengalaman yang ahli dalam membuat soal esai berkualitas tinggi.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'max_tokens' => 2000
                ]
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['choices'][0]['message']['content'])) {
                return $this->parseGeneratedQuestions($body['choices'][0]['message']['content']);
            }

            return [
                'questions' => [],
                'success' => false
            ];
        } catch (RequestException $e) {
            log_message('error', 'OpenAI API Error: ' . $e->getMessage());
            return [
                'questions' => [],
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function parseGeneratedQuestions($response)
    {
        $questions = [];
        preg_match_all('/SOAL\s+(\d+):\s*(.*?)(?=SOAL\s+\d+:|$)/is', $response, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $questions[] = [
                'number' => intval($match[1]),
                'text' => trim($match[2])
            ];
        }

        return [
            'questions' => $questions,
            'success' => true
        ];
    }
}
