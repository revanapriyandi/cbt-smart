<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamResultsTable extends Migration
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
            'total_score' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'default' => 0.00,
            ],
            'max_total_score' => [
                'type' => 'DECIMAL',
                'constraint' => '8,2',
                'default' => 0.00,
            ],
            'percentage' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 0.00,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['ongoing', 'submitted', 'graded'],
                'default' => 'ongoing',
            ],
            'started_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'graded_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addUniqueKey(['exam_id', 'student_id']);
        $this->forge->createTable('exam_results');
    }

    public function down()
    {
        $this->forge->dropTable('exam_results');
    }
}
