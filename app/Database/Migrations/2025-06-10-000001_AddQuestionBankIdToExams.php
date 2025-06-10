<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQuestionBankIdToExams extends Migration
{
    public function up()
    {
        // Add question_bank_id column to exams table
        $fields = [
            'question_bank_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'exam_type_id'
            ],
            'max_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'after' => 'is_active'
            ],
            'passing_score' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 60,
                'after' => 'max_attempts'
            ],
            'shuffle_questions' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'passing_score'
            ],
            'show_results' => [
                'type' => 'ENUM',
                'constraint' => ['immediately', 'after_exam', 'manual'],
                'default' => 'immediately',
                'after' => 'shuffle_questions'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published', 'completed', 'archived'],
                'default' => 'draft',
                'after' => 'show_results'
            ]
        ];

        $this->forge->addColumn('exams', $fields);

        // Add foreign key constraint for question_bank_id
        $this->forge->addForeignKey('question_bank_id', 'question_banks', 'id', 'SET NULL', 'CASCADE', 'exams');
    }

    public function down()
    {
        // Drop foreign key constraint first
        $this->forge->dropForeignKey('exams', 'exams_question_bank_id_foreign');

        // Drop columns
        $this->forge->dropColumn('exams', [
            'question_bank_id',
            'max_attempts',
            'passing_score',
            'shuffle_questions',
            'show_results',
            'status'
        ]);
    }
}
