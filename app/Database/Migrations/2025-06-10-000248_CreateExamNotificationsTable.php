<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExamNotificationsTable extends Migration
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
            'session_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['info', 'warning', 'alert', 'termination'],
                'default' => 'info',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'target' => [
                'type' => 'ENUM',
                'constraint' => ['all', 'individual'],
                'default' => 'all',
            ],
            'target_user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'sent_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'sent_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'is_read' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'read_at' => [
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

        $this->forge->addKey('id', true);
        $this->forge->addKey('session_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('sent_by');
        $this->forge->addKey('target_user_id');
        $this->forge->addKey(['type', 'sent_at']);

        $this->forge->addForeignKey('session_id', 'exam_sessions', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sent_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('target_user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('exam_notifications');
    }

    public function down()
    {
        $this->forge->dropTable('exam_notifications');
    }
}
