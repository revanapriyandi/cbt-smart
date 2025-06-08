<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastLoginToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'last_login' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'is_active'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_login');
    }
}
