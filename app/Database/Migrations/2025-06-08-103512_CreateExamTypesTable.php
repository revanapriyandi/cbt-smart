<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamTypesTable extends Migration
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'duration_minutes' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 60,
            ],
            'max_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 1,
            ],
            'passing_score' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'default' => 60.00,
            ],
            'show_result_immediately' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'allow_review' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'randomize_questions' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'randomize_options' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'auto_submit' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'instructions' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('created_by');
        $this->forge->addKey('updated_by');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('updated_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('exam_types');
    }

    public function down()
    {
        $this->forge->dropTable('exam_types');
    }
}
