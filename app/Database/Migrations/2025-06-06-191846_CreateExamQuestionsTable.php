<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamQuestionsTable extends Migration
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
            'question_number' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'question_text' => [
                'type' => 'LONGTEXT',
            ],
            'max_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 10.00,
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
        $this->forge->createTable('exam_questions');
    }

    public function down()
    {
        $this->forge->dropTable('exam_questions');
    }
}
