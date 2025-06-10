<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Kelas 7A',
                'level' => 'Grade 7',
                'academic_year' => '2024-2025',
                'capacity' => 30,
                'description' => 'Kelas 7A untuk tahun ajaran 2024-2025',
                'homeroom_teacher_id' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kelas 7B',
                'level' => 'Grade 7',
                'academic_year' => '2024-2025',
                'capacity' => 30,
                'description' => 'Kelas 7B untuk tahun ajaran 2024-2025',
                'homeroom_teacher_id' => 17,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kelas 8A',
                'level' => 'Grade 8',
                'academic_year' => '2024-2025',
                'capacity' => 28,
                'description' => 'Kelas 8A untuk tahun ajaran 2024-2025',
                'homeroom_teacher_id' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kelas 8B',
                'level' => 'Grade 8',
                'academic_year' => '2024-2025',
                'capacity' => 25,
                'description' => 'Kelas 8B untuk tahun ajaran 2024-2025',
                'homeroom_teacher_id' => 17,
                'is_active' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Kelas 9A',
                'level' => 'Grade 9',
                'academic_year' => '2024-2025',
                'capacity' => 32,
                'description' => 'Kelas 9A untuk tahun ajaran 2024-2025',
                'homeroom_teacher_id' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Insert data
        $this->db->table('classes')->insertBatch($data);
    }
}
