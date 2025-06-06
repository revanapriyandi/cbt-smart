<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentAnswersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'exam_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'student_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'question_number' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'answer_text' => [
                'type' => 'LONGTEXT',
            ],
            'ai_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'ai_feedback' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'manual_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'manual_feedback' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'final_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('exam_id', 'exams', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('student_answers');
    }

    public function down()
    {
        $this->forge->dropTable('student_answers');
    }
}
