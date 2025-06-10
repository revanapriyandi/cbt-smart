<?php

namespace App\Libraries;

use Smalot\PdfParser\Parser;

class PdfScrapingService
{
    private $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Extract text from PDF file
     */
    public function extractTextFromPdf($pdfPath)
    {
        try {
            $pdf = $this->parser->parseFile($pdfPath);
            $text = $pdf->getText();
            return $text;
        } catch (\Exception $e) {
            log_message('error', 'PDF parsing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract text from PDF content (binary)
     */
    public function extractTextFromContent($pdfContent)
    {
        try {
            $pdf = $this->parser->parseContent($pdfContent);
            $text = $pdf->getText();
            return $text;
        } catch (\Exception $e) {
            log_message('error', 'PDF parsing error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Parse questions from extracted text
     */
    public function parseQuestions($text)
    {
        $questions = [];

        // Pattern untuk mendeteksi soal multiple choice
        // Format: 1. Soal... A. pilihan B. pilihan C. pilihan D. pilihan
        $pattern = '/(\d+)\.\s*(.+?)(?=\d+\.|$)/s';

        preg_match_all($pattern, $text, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $questionNumber = trim($match[1]);
            $questionBlock = trim($match[2]);

            $question = $this->parseQuestionBlock($questionBlock);
            if ($question) {
                $question['order_number'] = (int)$questionNumber;
                $questions[] = $question;
            }
        }

        return $questions;
    }

    /**
     * Parse individual question block
     */
    private function parseQuestionBlock($questionBlock)
    {
        // Split question text and options
        $lines = explode("\n", $questionBlock);
        $questionText = '';
        $options = [];
        $currentOption = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Check if it's an option (A., B., C., D., etc.)
            if (preg_match('/^([A-E])\.\s*(.+)/', $line, $optionMatch)) {
                if ($currentOption) {
                    $options[] = $currentOption;
                }
                $currentOption = [
                    'letter' => $optionMatch[1],
                    'text' => trim($optionMatch[2]),
                    'is_correct' => false
                ];
            } elseif ($currentOption && !preg_match('/^[A-E]\./', $line)) {
                // Continue option text
                $currentOption['text'] .= ' ' . $line;
            } else {
                // Question text
                $questionText .= ' ' . $line;
            }
        }

        // Add last option
        if ($currentOption) {
            $options[] = $currentOption;
        }

        $questionText = trim($questionText);

        // Check if we have valid question and options
        if (empty($questionText) || count($options) < 2) {
            return null;
        }

        return [
            'question_text' => $questionText,
            'question_type' => 'multiple_choice',
            'difficulty_level' => 'medium', // Default
            'points' => 1, // Default
            'options' => $options,
            'status' => 'active'
        ];
    }

    /**
     * Auto-detect correct answers from text patterns
     */
    public function detectCorrectAnswers($text, &$questions)
    {
        // Pattern untuk jawaban: "Jawaban: 1.A 2.B 3.C"
        $answerPattern = '/(?:jawaban|kunci|answer)\s*:?\s*(.+?)(?:\n|$)/i';

        if (preg_match($answerPattern, $text, $matches)) {
            $answerText = $matches[1];

            // Parse individual answers: "1.A 2.B 3.C"
            preg_match_all('/(\d+)\.?\s*([A-E])/i', $answerText, $answerMatches, PREG_SET_ORDER);

            foreach ($answerMatches as $answerMatch) {
                $questionNum = (int)$answerMatch[1] - 1; // Convert to 0-based index
                $correctLetter = strtoupper($answerMatch[2]);

                if (isset($questions[$questionNum])) {
                    // Mark correct option
                    foreach ($questions[$questionNum]['options'] as &$option) {
                        if ($option['letter'] === $correctLetter) {
                            $option['is_correct'] = true;
                            break;
                        }
                    }
                }
            }
        }

        return $questions;
    }

    /**
     * Validate and clean parsed questions
     */
    public function validateQuestions($questions)
    {
        $validQuestions = [];

        foreach ($questions as $question) {
            // Check if question has at least one correct answer
            $hasCorrectAnswer = false;
            foreach ($question['options'] as $option) {
                if ($option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            // Skip questions without correct answers or with insufficient options
            if (!$hasCorrectAnswer || count($question['options']) < 2) {
                continue;
            }

            // Clean question text
            $question['question_text'] = $this->cleanText($question['question_text']);

            // Clean option texts
            foreach ($question['options'] as &$option) {
                $option['text'] = $this->cleanText($option['text']);
            }

            $validQuestions[] = $question;
        }

        return $validQuestions;
    }

    /**
     * Clean extracted text
     */
    private function cleanText($text)
    {
        // Remove extra whitespace
        $text = preg_replace('/\s+/', ' ', $text);

        // Remove special characters that might cause issues
        $text = preg_replace('/[^\p{L}\p{N}\p{P}\p{S}\s]/u', '', $text);

        return trim($text);
    }

    /**
     * Get extraction statistics
     */
    public function getExtractionStats($questions)
    {
        $stats = [
            'total_questions' => count($questions),
            'valid_questions' => 0,
            'questions_with_answers' => 0,
            'difficulty_distribution' => [
                'easy' => 0,
                'medium' => 0,
                'hard' => 0
            ],
            'type_distribution' => [
                'multiple_choice' => 0,
                'essay' => 0,
                'true_false' => 0,
                'fill_blank' => 0
            ]
        ];

        foreach ($questions as $question) {
            if (count($question['options']) >= 2) {
                $stats['valid_questions']++;
            }

            $hasCorrectAnswer = false;
            foreach ($question['options'] as $option) {
                if ($option['is_correct']) {
                    $hasCorrectAnswer = true;
                    break;
                }
            }

            if ($hasCorrectAnswer) {
                $stats['questions_with_answers']++;
            }

            $stats['difficulty_distribution'][$question['difficulty_level']]++;
            $stats['type_distribution'][$question['question_type']]++;
        }

        return $stats;
    }
}
