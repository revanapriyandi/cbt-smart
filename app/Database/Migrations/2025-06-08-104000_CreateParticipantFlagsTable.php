<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantFlagsTable extends Migration
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
            'participant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'flag_type' => [
                'type' => 'ENUM',
                'constraint' => ['suspicious_activity', 'cheating_attempt', 'technical_issue', 'time_violation', 'browser_switching', 'manual_flag'],
                'default' => 'manual_flag',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'severity' => [
                'type' => 'ENUM',
                'constraint' => ['low', 'medium', 'high', 'critical'],
                'default' => 'medium',
            ],
            'flagged_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'flagged_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'resolved' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'resolved_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'resolved_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'resolution_notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('participant_id');
        $this->forge->addKey('flagged_by');
        $this->forge->addKey('flagged_at');
        $this->forge->addKey(['flag_type', 'severity']);
        $this->forge->addForeignKey('participant_id', 'exam_participants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('flagged_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('resolved_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('participant_flags');
    }

    public function down()
    {
        $this->forge->dropTable('participant_flags');
    }
}
