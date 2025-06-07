<?php

namespace App\Models;

use CodeIgniter\Model;

class ExamActivityLogModel extends Model
{
    protected $table = 'exam_activity_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = [
        'exam_id',
        'student_id',
        'event_type',
        'details',
        'created_at',
    ];
    protected $useTimestamps = false;
}
