<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingColumnsToExamsTable extends Migration
{
    public function up()
    {
        $fields = [
            'total_questions' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'after' => 'question_count',
            ],
            'duration' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 60,
                'after' => 'total_questions',
            ],
        ];

        $this->forge->addColumn('exams', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('exams', ['total_questions', 'duration']);
    }
}
