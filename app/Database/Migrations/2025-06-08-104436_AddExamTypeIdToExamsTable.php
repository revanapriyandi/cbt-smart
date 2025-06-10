<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExamTypeIdToExamsTable extends Migration
{
    public function up()
    {
        $fields = [
            'exam_type_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'subject_id',
            ],
        ];

        $this->forge->addColumn('exams', $fields);
        
        // Add foreign key constraint
        $this->forge->addForeignKey('exam_type_id', 'exam_types', 'id', 'SET NULL', 'CASCADE', 'exams');
    }

    public function down()
    {
        $this->forge->dropForeignKey('exams', 'exams_exam_type_id_foreign');
        $this->forge->dropColumn('exams', 'exam_type_id');
    }
}
