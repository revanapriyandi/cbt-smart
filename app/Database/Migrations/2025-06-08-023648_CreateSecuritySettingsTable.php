<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSecuritySettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'setting_value' => [
                'type' => 'TEXT',
            ],
            'category' => [
                'type'       => 'ENUM',
                'constraint' => ['general', 'password', 'session', 'network', 'blocked_ips'],
                'default'    => 'general',
            ],
            'description' => [
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
        $this->forge->addUniqueKey('setting_key');
        $this->forge->addKey('category');
        $this->forge->createTable('security_settings');
    }

    public function down()
    {
        $this->forge->dropTable('security_settings');
    }
}
